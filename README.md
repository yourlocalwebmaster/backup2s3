# DESCRIPTION #

This script should backup the files in any directory, as well as X databases, and add them to a ZIP and shoot off to S3


##AWS CONFIGURATION##
1. Create a USER (IAM)
2. Assign that user a IAM POLICY (below)
3. Create a Bucket for that users site.
4. Get the Users credentials and add to the Child class of Backup

## Example Backup Policy ##
**Create a user with the PutObject IAM policy** 

The policy should look like this: (Where BUCKETNAME is the lowercase name of bucket)
```
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
```

##INSTALLATION ON SITE

1. clone this repo or `composer require "gkimball/backup2s3":"^1.0"` 

##USAGE

1. Create an entry point like "examples/backup.php" and configure credentials.
2. Add to a secure directory and run via CRON every month or so in the middle of the night.

