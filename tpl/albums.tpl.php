<ul data-role="listview" data-inset="true" id="covers">
    <?php

    foreach($albums as $album) {
        
        echo '
        <li class="album" data-id="'.$album['id'].'">
            <a href="#" onclick="loadSongs('.$album['id'].')">
            	<img src="img/'.$album['imId'].'-128x128/album.'.$album['ext'].'" alt="cover">
            	<h2>'.$album['prefix'].' '.$album['name'].'</h2>
                <!-- p>'.$album['year'].'</p -->
                <!-- p class="ui-li-aside">123</p -->
            </a>
        </li>';
    }
?>
</ul>