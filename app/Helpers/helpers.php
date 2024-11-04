<?php
function getTime($timezone = 'Europe/Dublin', $datetime = "now")
{
    $given = new DateTime($datetime, new DateTimeZone("UTC"));
    $given->setTimezone(new DateTimeZone($timezone));
    $output = $given->format("H:i"); //can change as per your requirement
    return $output;
}

function ytId($url)
{
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    if (count($match) >= 2) {
        return $match[1];
    }
    else{
        return null;
    }

}

function getUniqueToken($length){
     $token = "";
     $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
     $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
     $codeAlphabet.= "0123456789";
     $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max-1)];
    }

    return $token;
}

function is_JSON($string) {

  return (is_null(json_decode($string, TRUE))) ? FALSE : TRUE;

}
