<?php

require 'vendor/autoload.php';

		$s3 = new Aws\S3\S3Client([
			'region'  => 'ap-southeast-1',
			'version' => 'latest',
			'credentials' => [
				'key'    => "AKIA4YXAPMXBTXNUKZEB",
				'secret' => "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",
			]
		]);	
		
		$result = $s3->getObject([
			'Bucket' => 'amic-bot-storage',
			'Key'    => 'n.jpg'		
		]);
		var_dump($result);

?>