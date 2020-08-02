<?php

header('Content-Type: text/html; charset=utf-8');
require_once('connect.php');
if(isset($_POST['submit'])) 
{ 
    $countid = 0;
	$ContentData="";
	$FindWord = $_POST['findID'];
	$sql = "SELECT * FROM news ORDER BY News_id DESC";
    $result = $conn->query($sql);
	
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if (strpos($row["NewsContent"],$FindWord)!==false) {
                $countid = $countid+1;
                $ContentData=$ContentData.$row["NewsDate"]." [".$row["News_id"]."] => ".$row["NewsHadline"]."\n".?><br><?php;
            }
		}	
	}	
	echo"Found :: ".$countid." News";
	echo $ContentData;
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   ค้นหาคำว่า :: <input type="text" name="findID"><br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>