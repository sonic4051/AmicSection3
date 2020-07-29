<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

define('AWS_KEY', 'AKIA4YXAPMXBTXNUKZEB');
define('AWS_SECRET_KEY', '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');
define('HOST', 'https://amic-bot-storage.s3-ap-southeast-1.amazonaws.com');
define('REGION', 'ap-southeast-1');

// Establish connection with DreamObjects with an S3 client.
$client = new Aws\S3\S3Client([
    'version'     => '2006-03-01',
    'region'      => REGION,
    'endpoint'    => HOST,
        'credentials' => [
        'key'      => AWS_KEY,
        'secret'   => AWS_SECRET_KEY,
    ]
]);

$plain_url = $client->getObjectUrl('amic-bot-storage', 'test.PNG');
echo $plain_url . "\n";

$cmd = $client->getCommand('GetObject', [
        'Bucket' => 'amic-bot-storage',
        'Key'    => 'test.PNG'
]);
$signed_url = $client->createPresignedRequest($cmd, '+1 hour');
echo $signed_url->getUri() . "\n";
?>