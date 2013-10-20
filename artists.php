<?php

require_once 'lib/init.php';

/* Switch on Action */
switch ($_REQUEST['action']) {
    case 'getAll':
        $artists = Artist::get_all();
        include('tpl/artists.tpl.php');
    break;
}