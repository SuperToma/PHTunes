<ul data-role="listview" data-inset="true" data-theme="c">
    <li data-role="divider" data-theme="a"><?php echo $album->full_name ?></li>
    <?php
    foreach($songs as $song) {
        
        $timeFormat = 'i:s';
        if($song['time'] >= 3600) {
            $timeFormat = 'H:i:s';
        }
        echo '
        <li data-icon="false" class="songName" data-id="'.$song['id'].'">
            <a href="#">
                '.$song['track'].'. 
                <span class="name">'.$song['title'].'</span>
                <span class="artist">'.$song['name'].'</span>
                <span class="ui-li-count" data-duration="'.$song['time'].'">'.gmdate($timeFormat , $song['time']).'</span>
            </a>
        </li>';
    }
    ?>
</ul>