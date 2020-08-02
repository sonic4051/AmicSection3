<?php

header('Content-Type: text/html; charset=utf-8');
require_once('connect.php');
if(isset($_POST['submit'])) 
{ 
    
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   ค้นหาคำว่า :: <input type="text" name="findID"><br>
   <input type="submit" name="submit" value="ส่งระหัส"><br>
</form>