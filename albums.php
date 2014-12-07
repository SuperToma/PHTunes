<?php

require_once 'lib/init.php';

/* Switch on Action */
switch ($_REQUEST['action']) {
    case 'getAll':
        
        if(!isset($_REQUEST['id'])) {
            $_REQUEST['id'] = null;
        }
        
        if(!isset($_REQUEST['artistId'])) {
            $_REQUEST['artistId'] = null;
        }

        $albums = Album::get_all($_REQUEST['artistId'], $_REQUEST['id']);
        
        include('tpl/albums.tpl.php');
    break;
    
    case 'getLast':
        $albums = Album::get_last(100);
        include('tpl/albums.tpl.php');
    break;    

    case 'getSongs':
       
        //Sear album name
        $album = new Album((int)$_GET['id']);
        
        $songs = $album->get_songs_with_artist();
        
        include('tpl/songs.tpl.php');
    break;
}
