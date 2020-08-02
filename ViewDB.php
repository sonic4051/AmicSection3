<?php
header('Content-Type: text/html; charset=utf-8');
require_once('connect.php');
if(isset($_POST['submit'])) 
{ 
	$NewsID = $_POST['NewsID'];
	$sql = "SELECT * FROM news WHERE News_id = '$NewsID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) 
	{
        while($row = $result->fetch_assoc()) 
		{
            $NewContent = $row["NewsContent"];// เนื้อหาข่าว
            $NewHeader = $row["NewsHadline"];
            $Newdate =$row["NewsDate"]." , ".$row["NewsTime"];
            $IDReporter = $row["NewsReporter"];
		}
	}
	mb_internal_encoding('utf-8');
	$countStr = mb_strlen($NewContent, 'utf-8');
	$NewContent1 = mb_substr($NewContent, 0, 1500,'utf-8');
	echo $countStr."<br>";	
	echo"หัวข้อข่าว :: ".$NewHeader."<br>";
	echo $NewContent1;
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   <input type="text" name="NewsID"><br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>  