<?php

if(isset($_REQUEST['tpl']) && file_exists('tpl/'.$_REQUEST['tpl'].'.tpl.php')) {
    echo file_get_contents('tpl/'.$_REQUEST['tpl'].'.tpl.php');
} else {
    echo file_get_contents('tpl/player.tpl');
}
