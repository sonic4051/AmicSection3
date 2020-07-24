<?
$headers = get_headers('https://ift.tt/2ZVZFoe' , true);
print_r $headers;
echo "<br>";
echo "test";
echo "<br>";
echo $headers['Location'];
?>