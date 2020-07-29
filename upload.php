<?php

require 'vendor/autoload.php';
use Aws\S3\S3Client;
 
$bucketName = 'amic-bot-storage';
$client = new S3Client([
    'version' => 'latest',
    'region' => 'ap-southeast-1',
    'credentials' => [
        'key'    => 'AKIA4YXAPMXBTXNUKZEB',
        'secret' => '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'
    ]
]); 

$file_Path = 'item.png';
$key = basename($file_Path);
 
// Upload a publicly accessible file. The file size and type are determined by the SDK.
try {
    $result = $s3->putObject([
        'Bucket' => $bucketName,
        'Key'    => $key,
        'Body'   => fopen($file_Path, 'r'),
        'ACL'    => 'public-read',
    ]);
    echo $result->get('ObjectURL');
} catch (Aws\S3\Exception\S3Exception $e) {
    echo "There was an error uploading the file.\n";
    echo $e->getMessage();
}
?>