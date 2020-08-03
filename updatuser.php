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
	 
	}	
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   กดเพื่อเริ่มอับเดท<br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>  