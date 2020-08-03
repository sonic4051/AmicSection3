<?php
header('Content-Type: text/html; charset=utf-8');
require_once('connect.php');
if(isset($_POST['submit'])) 
{
	$sql = "SELECT * FROM news";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) 
	{
	 $NewsID = $row["News_id"];
	 if($row["NewsReporter"]=='U9c53dfeda7747d656d6ddb4a2e4fd599') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'พ.ท.ศักรินทร์'  WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	 }
	 else if($row["NewsReporter"]=='U5be89a7c6b71c03e19b271b15d08ff22') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'ร.อ.อุเทน'  WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	 }
	 else if($row["NewsReporter"]=='U7723a267e3ac3294a99c96db72fdbe48') 
	 {
		 $sql2 = "UPDATE		 news SET NewsReporter = 'จ.ส.อ.เกศฎา' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}
	 else if($row["NewsReporter"]=='U1558932bd360e271db982f5946c0ae35') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'พ.ต.กิตติพงษ์' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}
	 else if($row["NewsReporter"]=='Ubf8a16f2dc7b302e61a095f383508e91') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'จ.ส.อ.พิพัฒน์ทนัน' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}
	 else if($row["NewsReporter"]=='U8a79a7684e8752f9b96f0d626d9998cc') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'ร.อ.ชัยพันธุ์' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}
	 else if($row["NewsReporter"]=='U973698b9ac474608e23ce7519d2b2621') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'ร.อ.ทองอินทร์' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}
	  else if($row["NewsReporter"]=='Ub736246d429f003bf3f0256113745d65') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'ร.อ.อนุพงษ์​' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}
	else if($row["NewsReporter"]=='Uc576ffbaf058896279283bcb52211af1') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'ร.อ.วัชร์ชัยนันท์​' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}	
	else if($row["NewsReporter"]=='U6126911eea396446e0919385e74ee6f1') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'จ.ส.ต.อธิพันธ์​' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}	
	else if($row["NewsReporter"]=='U018d37636ec93191f4e6433d710fc844') 
	 {
		 $sql2 = "UPDATE news SET NewsReporter = 'ส.อ.ทนงศักดิ์​' WHERE News_id='$NewsID'";
		 $result2 = $conn->query($sql2);
	}	
	}	
    }
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   กดเพื่อเริ่มอับเดท<br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>  