<?php

// Include the SDK using the composer autoloader
require 'vendor/autoload.php';

$s3 = new Aws\S3\S3Client([
	'region'  => 'ap-southeast-1',
	'version' => 'latest',
	'credentials' => [
	    'key'    => "AKIA4YXAPMXBTXNUKZEB",
	    'secret' => "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",
	]
]);

// Send a PutObject request and get the result object.
$key = 'sonicboom';

$result = $s3->putObject([
	'Bucket' => 'amic-bot-storage',
	'Key'    => $key,
	'Body'   => 'this is the body!',
	//'SourceFile' => 'c:\samplefile.png' -- use this if you want to upload a file from a local location
]);

// Print the body of the result by indexing into the result object.
var_dump($result);
?>