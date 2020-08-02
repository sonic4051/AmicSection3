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
	$countStr = utf8_strlen($NewContent);
    if ($countStr>=1000) {
		//$NewContent1=substr($NewContent,0,100);
	echo $countStr;
	$NewContent1 = $NewContent;
	}	
	echo"หัวข้อข่าว :: ".$NewHeader;
	
	echo $NewContent1;
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   <input type="text" name="NewsID"><br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>  