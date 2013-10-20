<?php

################################################################################
#                             Download Album                                   #
################################################################################
if(isset($_GET['downloadAlbum'], $_GET['id']) && is_numeric($_GET['id'])) {
    
    require_once 'lib/init.php';

    //Search files
    $sql    = 'SELECT file FROM song WHERE album = '.(int)$_GET['id'];
    $result = Dba::read($sql);
    
    if(Dba::num_rows($result) == 0) {
        die('No file found');
    }

    //Create zip
    $zip = new ZipArchive();
    $zipName = "tmp.".  uniqid().".zip";

    if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
        exit("Impossible d'ouvrir le fichier $zipName\n");
    }
    
    //Put files in the zip
    while($mp3 = Dba::fetch_assoc($result)) { 
        $zip->addFile($mp3['file'], basename($mp3['file']));
    }
    $zip->close();

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
    //header("Content-Type: application/download");
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

require_once 'lib/init.php';

$sql = 'SELECT id, file FROM song WHERE id = '.(int)$_GET['id'].' LIMIT 1';
$result = Dba::read($sql);
$infos =  Dba::fetch_assoc($result);

if(!isset($infos['id']) || empty($infos['file'])) {
    header("HTTP/1.0 404 Not Found");
    exit();
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