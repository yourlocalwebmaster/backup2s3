<?php

/**
 * Backup Policy
> Create a user with the PutObject IAM policy
The policy should look like this: (Where BUCKETNAME is the lowercase name of bucket)
{
    "Version": "2012-10-17",
    "Statement": [
        {
        "Effect": "Allow",
        "Action": [
            "s3:PutObject"
        ],
        "Resource": "arn:aws:s3:::BUCKETNAME/*”
        }
    ]
}
 */

namespace Backup;
use Aws\Resource\Aws;
use Comodojo\Zip\Zip;
use Spatie\DbDumper\Databases\MySql;


class Backup
{

    ## AWS User stuff (client specific)
    // TODO: AWS STUFF
    protected $bucket;
    protected $config;

    ## Filesystem
    protected $directories_to_zip; // i.e. /var/www, /etc/apache2/sites-available/

    ## Database (Array of databases);
    /**
     * @var Array
     * array( ( array ('host' => '', 'name' => '',  'user' => '', 'pass' =>'', 'port' => '' ) )
     */
    protected $dbs = [];

    //garbage collection
    protected $tempfiles = [];

    public function __construct()
    {

    }

    /**
     * @param $local
     * @param $remote
     * Slaps something into to the validated bucket.
     */
    public function putSomethingInBucket($local, $remote){
        $aws = new Aws($this->config);
        $bucket = $aws->s3->bucket($this->bucket);
        $bucket->putObject([
            'Key'  => $remote,
            'Body' => fopen($local, 'r'),
        ]);
    }

    /**
     * The main entry point for the crontask
     */
    public function runBackup(){
        $name = 'backup_cron.zip';
        $this->zipDirsAndDbs($name);
        $zipdate = date('y_m_d');

        //TODO: I still need to add the DATABASE to the backup zip as well...

        $this->putSomethingInBucket($name,'backup-'.$zipdate);

        $this->cleanUp();
    }

    /**
     * @param $name
     * Zips each directory listed in the property "directories_to_zip"
     */
    private function zipDirsAndDbs($name){

        $zip = Zip::create($name,true);
        foreach ($this->directories_to_zip as $dir){
            $zip->add($dir);
        }


        foreach($this->dbs as $db){
            $sql_name = 'db_'.$db['name'].'_.sql';
            MySql::create()->setDbName($db['name'])->setHost($db['host'])->setPassword($db['password'])->setUserName($db['user'])->dumpToFile($sql_name);
            $zip->add($sql_name); // add the SQL file to the zip..
            $this->tempfiles[] = $sql_name;
        }

        $zip->close();

        //TODO: This won't delete the zip because of permissions...
        $this->tempfiles[] = $name; //add the actual zip..
    }

    /**
     * Once the zip is complete, delete the sql files etc..
     */
    protected function cleanUp(){
        foreach($this->tempfiles as $file){
            unlink($file);
        }
    }

}
