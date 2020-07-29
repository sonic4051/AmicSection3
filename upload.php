<?php
// Require the Composer autoloader.
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

$s3 = new S3Client([
'version' => 'latest',
'region' => 'ap-southeast-1',
'credentials' => [
'key' => 'AKIA4YXAPMXBTXNUKZEB',
'secret' => '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'
]
]);
// Upload an object to Amazon S3
$bucket = 'amic-bot-storage';
try {
//$s3->createBucket(['Bucket' => 'gangimg']);
//echo 'Create bucket';
// Upload an object to Amazon S3
$filename = explode(".", $_FILES['img']["name"]);
$filenameext = $filename[count($filename) — 1];
$filename = $_POST['path'].'img_' . time() . "." . $filenameext;
$result = $s3->putObject(array(
'Bucket' => $bucket,
'Key' => $filename,
'ACL' => 'public-read',
'SourceFile' => $_FILES['img']['tmp_name'],
));
// Access parts of the result object
echo $result['Expiration'] . "\n";
echo $result['ServerSideEncryption'] . "\n";
echo $result['ETag'] . "\n";
echo $result['VersionId'] . "\n";
echo $result['RequestId'] . "\n";
// Get the URL the object can be downloaded from
echo $result['ObjectURL'] . "\n";
echo '<img src="' . $result['ObjectURL'] . '">';
} catch (S3Exception $e) {
// Catch an S3 specific exception.
echo $e->getMessage();
} catch (AwsException $e) {
// This catches the more generic AwsException. You can grab information
// from the exception using methods of the exception object.
echo $e->getAwsRequestId() . "\n";
echo $e->getAwsErrorType() . "\n";
echo $e->getAwsErrorCode() . "\n";
}
?>