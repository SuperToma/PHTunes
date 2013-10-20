<?php

require_once 'lib/init.php';

/* Switch on Action */
switch ($_REQUEST['action']) {
    case 'search':
        
        $search = $_POST['search'];
        
        $max = 10;
        $artists = Search::artistsLike($search, $max);
        
        $max = $max - count($artists);
        $albums = Search::albumsLike($search, $max);
        
        $max = $max - count($albums);
        $songs = Search::songsLike($search, $max);

        include('tpl/search.tpl.php');
    break;
}

//Artist

//Albums


//songs