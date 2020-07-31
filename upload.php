<?php
	if(isset($_FILES['image'])){
		$file_name = $_FILES['image']['name'];   
		$temp_file_location = $_FILES['image']['tmp_name']; 

		require 'vendor/autoload.php';

		$s3 = new Aws\S3\S3Client([
			'region'  => 'ap-southeast-1',
			'version' => 'latest',
			'credentials' => [
				'key'    => "AKIA4YXAPMXBSJCZDMQ4",
				'secret' => "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",
			]
		]);		

		$result = $s3->putObject([
			'Bucket' => 'amic-bot-storage',
			'Key'    => $file_name,
			'SourceFile' => $temp_file_location,	
			'ACL' => 'public-read',
		]);
		//var_dump($result);
		//echo $result["@metadata"]["statusCode"];
	}
?>

<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">         
	<input type="file" name="image" />
	<input type="submit"/>
</form>      
