<?php
header('Content-Type: text/html; charset=utf-8');
require_once('connect.php');
if(isset($_POST['submit'])) 
{
	$sql = "SELECT * FROM news";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) 
	{
	 if($row["NewsReporter"]='U9c53dfeda7747d656d6ddb4a2e4fd599') 
	 {UPDATE news SET NewsReporter = 'พ.ท.ศักรินทร์' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='U5be89a7c6b71c03e19b271b15d08ff22') 
	 {UPDATE news SET NewsReporter = 'ร.อ.อุเทน' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='Ua7a7bec0f7f8d3b2c31396674abfee2d') 
	 {UPDATE news SET NewsReporter = 'ร.ท.ทวี' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='U397397f56447399ad60169cb958704d7') 
	 {UPDATE news SET NewsReporter = 'จ.ส.อ.ไพรวัลย์' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='U7723a267e3ac3294a99c96db72fdbe48') 
	 {UPDATE news SET NewsReporter = 'จ.ส.อ.เกศฎา' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='U1558932bd360e271db982f5946c0ae35') 
	 {UPDATE news SET NewsReporter = 'พ.ต.กิตติพงษ์' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='Ubf8a16f2dc7b302e61a095f383508e91') 
	 {UPDATE news SET NewsReporter = 'จ.ส.อ.พิพัฒน์ทนัน' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='U8a79a7684e8752f9b96f0d626d9998cc') 
	 {UPDATE news SET NewsReporter = 'ร.อ.ชัยพันธุ์' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='Uc576ffbaf058896279283bcb52211af1') 
	 {UPDATE news SET NewsReporter = 'ร.อ.วัชร์ชัยนันท์' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='U973698b9ac474608e23ce7519d2b2621') 
	 {UPDATE news SET NewsReporter = 'ร.อ.ทองอินทร์' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='U440d7905ad0c12aad5ec4f923aba0bdc') 
	 {UPDATE news SET NewsReporter = 'ร.ท.สุวิทย์' WHERE $row["News_id"];}
	 else if($row["NewsReporter"]='Ub736246d429f003bf3f0256113745d65') 
	 {UPDATE news SET NewsReporter = 'ร.อ.อนุพงษ์​' WHERE $row["News_id"];}		 
	}	
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   กดเพื่อเริ่มอับเดท<br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>  