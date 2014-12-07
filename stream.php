<?php

header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'lib/init.php';

if( !vauth::check_session() ) {
	header("HTTP/1.0 401 Unauthorized");
    die('Unauthorized');
}

################################################################################
#                             Download Album                                   #
################################################################################
if(isset($_GET['downloadAlbum'], $_GET['id']) && is_numeric($_GET['id'])) {

    if( !class_exists('Phar') && !class_exists('ZipArchive')) {
        die('No PHP zip extension found (ZipArchive or Phar');
    }

    if( !class_exists('ZipArchive') && ( is_null(ini_get('phar.readonly')) || ini_set('phar.readonly') === 1 ) ) {
        die('Phar extention is installed but can\'t create zip files. Verify the parameter phar.readonly in your php.ini');
    }
    
    //Search files
    $sql    = 'SELECT file, size FROM song WHERE album = '.(int)$_GET['id'];
    $result = Dba::read($sql);

    if(Dba::num_rows($result) == 0) {
        die('No file found');
    }
    
    //Search mp3s
    $mp3s = array();
    $maxMemory = memory_get_usage(true);

    //Get all files and set memory limit else crash
    while($mp3 = Dba::fetch_assoc($result)) { 
        $mp3s[] = $mp3['file'];
        $maxMemory += $mp3['size'];
    }

    set_memory_limit($maxMemory+32);

    //Create zip file
    
    if(class_exists('Phar')) {
        $pharName = "tmp_".  uniqid().".phar";
        
        $zip = new Phar(dirname(__FILE__).'/'.$pharName, 0, $pharName);
        
        //Put files in the archive
        $zip->startBuffering();

        foreach($mp3s as $mp3) {
            $fileName = removeAccents(basename($mp3));
            $zip[$fileName] = file_get_contents($mp3);
        }

        $zip->stopBuffering();
        $zip->convertToData(Phar::ZIP); //Create a .zip file

        $zipName = str_replace('.phar', '.zip', $pharName);
        unlink($pharName);
    }
    //With ZipArchive
    else {
        $zipName = "tmp_".  uniqid().".zip";
        
        $zip = new ZipArchive();
        
        if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
            exit("Impossible d'ouvrir le fichier $zipName\n");
        }

        //Put files in the zip
        foreach($mp3s as $mp3) {
            $zip->addFile($mp3, basename($mp3));
        }
        $zip->close();
    }

    //Search Album name
    $sql = "SELECT album.name AS albumName, CONCAT_WS(' ', artist.prefix, artist.name) AS artistName
            FROM album 
            LEFT JOIN song ON song.album = album.id
            LEFT JOIN artist ON artist.id = song.artist
            WHERE album.id = ".(int)$_GET['id']."
            GROUP BY artistName";
    $result2 = Dba::read($sql);

    //If only one artist in the album
    $infosAlbum = Dba::fetch_assoc($result2);
    $albumName = $infosAlbum['albumName'];
    
    if(Dba::num_rows($result2) == 1) {
        $artistName = $infosAlbum['artistName'];
    } else {
        $artistName = 'Album';
    }
    
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"".$artistName." - ".$albumName.".zip\";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($zipName));

    //echo file_get_contents($zipName); //BUG PHP 5.3 : no memory mapping : make memory limit error

    //Send archive
    $file_handle = fopen($zipName, "r");

    while (!feof($file_handle)) {
       $line = fgets($file_handle);
       echo $line;
    }
    fclose($file_handle);
    
    //Delete archive
    unlink($zipName);
    exit();
}

################################################################################
#                               Playing song                                   #
################################################################################
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

$sql = 'SELECT id, file FROM song WHERE id = '.(int)$_GET['id'].' LIMIT 1';
$result = Dba::read($sql);
$infos =  Dba::fetch_assoc($result);

if(!isset($infos['id']) || empty($infos['file'])) {
    header("HTTP/1.0 404 Not Found");
    die('File not found on the server');
}

Dba::query('UPDATE song SET played=played+1 WHERE id = '.$infos['id']);

header('Content-type: '.mime_content_type($infos['file']));
//header("Content-Type: application/force-download");
//header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=\"".basename($infos['file'])."\";");
//header("Content-Transfer-Encoding: binary"); //Doesn't work on FF
//header("Content-Type: application/octet-stream"); //Doesn't work on FF
header("Content-Length: ".filesize($infos['file']));
header('Accept-Ranges: bytes');

readfile($infos['file']);