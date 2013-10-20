<?php

if(!empty($artists)) {
    echo '<li data-role="list-divider">Artists</li>';
    foreach($artists as $artist) {
        echo '
        <li data-icon="false" data-icon="false">
            <a href="javascript:loadPage(\'albums.php?action=getAll&artistId='.$artist['id'].'\');">'.$artist['name'].'</a>
        </li>';
    }
}

if(!empty($albums)) {
    echo '<li data-role="list-divider">Albums</li>';
    foreach($albums as $album) {
        echo '
        <li data-icon="false" data-icon="false">
            <a href="javascript:loadPage(\'albums.php?action=getAll&id='.$album['id'].'\');">'.$album['name'].'</a>
        </li>';
    }
}

if(!empty($songs)) {
    echo '<li data-role="list-divider">Songs</li>';
    foreach($songs as $song) {
        echo '
        <li data-icon="false" data-icon="false">
            <a href="javascript:loadPage(\'albums.php?action=getAll&id='.$song['id'].'\');">'.$song['title'].'</a>
        </li>';
    }
}
