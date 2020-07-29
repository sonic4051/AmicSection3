<?php

require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$bucket = 'amic-bot-storage';
$keyname = 'n.jpg';

	$s3 = new Aws\S3\S3Client([
		'region'  => 'ap-southeast-1',
		'version' => 'latest',
		'credentials' => [
			'key'    => "AKIA4YXAPMXBTXNUKZEB",
			'secret' => "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",
			]
		]);	
		
	try {
    // Get the object.
    $result = $s3->getObject([
        'Bucket' => $bucket,
        'Key'    => $keyname
    ]);
    // Display the object in the browser.
    header("Content-Type: {$result['ContentType']}");
	//echo $result['Body'];
	?>
	<img src="<?php echo $result['Body'];  ?>" alt="Girl in a jacket" width="500" height="500">
	<?php
	} 
	catch (S3Exception $e) {
		echo $e->getMessage(). PHP_EOL;
	}
?>
