<?php 

$url="https://ift.tt/393BoPZ";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$a = curl_exec($ch); // $a will contain all headers

$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL

echo $url; // Redirected url
//https://www.google.com/maps?q=18.79171955,98.96891115&z=13
?>

