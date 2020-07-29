<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$bucket = 'amic-bot-storage';
$key = 'AKIA4YXAPMXBTXNUKZEB';
$secret = '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';

$s3 = new AmazonS3($key, $secret);
$objInfo = $s3->get_object_headers($bucket, 'test.PNG');
$obj = $s3->get_object($bucket, 'test.PNG');
header('Content-type: ' . $objInfo->header['_info']['content_type']);
echo $obj->body;

?>