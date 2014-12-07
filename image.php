<?php

require_once 'lib/init.php';

if( !isset($_GET['id'], $_GET['w'], $_GET['h'], $_GET['ext']) || 
    !is_numeric($_GET['id']) || 
    !is_numeric($_GET['w']) || 
    !is_numeric($_GET['h']) || 
    strlen($_GET['ext']) != 3) 
{
    throw new Exception("Wrong parameters\n");
}

$dir = 'cache/'.$_GET['w'].'x'.$_GET['h'];
$file = $dir.'/'.$_GET['id'].'.'.$_GET['ext'];

if( file_exists($file) && !is_dir($file) ) 
{
    if( isset($_SERVER["SERVER_SOFTWARE"]) && stripos($_SERVER["SERVER_SOFTWARE"], 'apache') !== false ) {
        readfile('./img/configure_htaccess.gif');
    } else {
        readfile($file);
    }
    die;
}

if( !is_dir($dir) ) {
    mkdir($dir, 0755);
}

if(!Image::resize($_GET['id'], $file, $_GET['w'], $_GET['h'])) {
    throw new Exception("Error Image::resize : ".$file."\n");
} else {
    //Load
    readfile($file);
    die;
}
