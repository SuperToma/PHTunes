
<ul data-role="listview" data-inset="true" id="covers">
    <?php

    foreach($artists as $artist) {
        
        echo '<li>
            <a href="javascript:loadPage(\'albums.php?action=getAll&artistId='.$artist['id'].'\');" id="album'.$artist['id'].'">
            	<img src="img/'.$artist['imId'].'-128x128/album.'.$artist['ext'].'" alt="cover">
            	<h2>'.$artist['name'].'</h2>
                <!-- p>'.$artist['nbAlbums'].'</p -->
                '.($artist['nbAlbums'] > 1?'<p class="ui-li-aside">'.$artist['nbAlbums'].'</p>':'').'
            </a>
        </li>';
    }
?>
</ul>
