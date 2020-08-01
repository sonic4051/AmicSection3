<?php

header('Content-Type: text/html; charset=utf-8');
require_once('connect.php');
if(isset($_POST['submit'])) 
{ 
    $NewsID = $_POST['NewsID'];
    echo "User Has submitted the form and entered this name : <b> $NewsID </b>";
    echo "<br>You can use the following form again to enter a new name."; 
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   <input type="text" name="NewsID"><br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>  