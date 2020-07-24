<?
$headers = get_headers('https://ift.tt/2ZVZFoe' , true);
echo $headers;
echo "<br>";
echo "test";
echo $headers['Location'];
?>