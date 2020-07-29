<?php
	if(isset($_FILES['image'])){
		$file_name = $_FILES['image']['name'];   
		$temp_file_location = $_FILES['image']['tmp_name']; 

		require 'vendor/autoload.php';

		$s3 = new Aws\S3\S3Client([
			'region'  => 'ap-southeast-1',
			'version' => 'latest',
			'credentials' => [
				'key'    => "AKIA4YXAPMXBTXNUKZEB",
				'secret' => "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",
			]
		]);		

		$result = $s3->putObject([
			'Bucket' => 'amic-bot-storage',
			'Key'    => $file_name,
			'SourceFile' => $temp_file_location			
		]);
		//var_dump($result);
		if($result!=NULL) echo"อับโหลดสำเร็จ";
	}
?>

<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">         
	<input type="file" name="image" />
	<input type="submit"/>
</form>      
