<?php

require 'vendor/autoload.php';
 
use Aws\S3\S3Client;
 
// Instantiate an Amazon S3 client.
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'ap-southeast-1',
    'credentials' => [
        'key'    => 'AKIA4YXAPMXBTXNUKZEB',
        'secret' => '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'
    ]
]);
 
?>