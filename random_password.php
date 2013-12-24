<?php
function randomPassword($length_passw) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = '';                           //password is a string
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length_passw; $i++) {
        $n = mt_rand(0, $alphaLength);    
        $pass = $pass.$alphabet[$n];      //append a random character
    }
    return ($pass); 
}

$passw = randomPassword(10);
echo "<br>password = $passw";
?>