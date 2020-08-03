<?php
header('Content-Type: text/html; charset=utf-8');
require_once('connect.php');
if(isset($_POST['submit'])) 
{
	$sql = "SELECT * FROM news WHERE NewsReporter = 'ร.อ.อนุพงษ์​'";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) 
	{
		 $sql2 = "UPDATE news SET NewsReporter = 'ร.อ.​'  ";
		 $result2 = $conn->query($sql2);
	}	
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   กดเพื่อเริ่มอับเดท<br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>  