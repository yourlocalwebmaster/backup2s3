<?php

    namespace App;

    require('vendor/autoload.php');

    use Backup\Backup;

    class DemoClient extends Backup{

        // Name of the bucket in AWS
        protected $bucket = '{AWS BUCKET NAME}';

        // AWS API information
        protected $config = [
            "region" => 'us-west-2',
            "version" => "2006-03-01",
            "credentials" => [
                "key" => '{AWS KEY}',
                "secret" => '{AWS SECRET}'
            ]
        ];

        // The directories on the server you would like to zip and store.
        protected $directories_to_zip = [
           '/var/www',
           '/etc/apache2/sites-available'
        ];

        // list all the DB's that you would like to backup and store as SQL dumps.
        protected $dbs = [
            [
                'host'=>'localhost',
                'user'=>'root',
                'name'=>'test_db',
                'password'=>'hJBV9hd4mowREN',
            ],
            // ...
        ];

    }

$Demo = new DemoClient();

$Demo->runBackup();
