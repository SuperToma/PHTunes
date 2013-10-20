<?php
require_once 'lib/init.php';

$installFile = './config/install.txt';
$sqlFile = './config/install.sql';

$installMode = false;
if( file_exists($installFile) ) {
    $installMode = true;
}

switch ($_REQUEST['action']) {
    
    case 'testConfig':

        $username = Config::get('database_username');
        $hostname = Config::get('database_hostname');
        $password = Config::get('database_password');
        $database = Config::get('database_name');
        
        $errors = 0;
        $msgs = array();
        
        //Tests config
        if ((int)phpversion() < 5) {
            $errors++;
            $msgs[] = 'PHP 5 : <span class="error">ERROR: PHTunes requires PHP 5</span>';
        } else {
            $msgs[] = 'PHP 5 : <span class="ok">OK</span>';
        }
        
        $dsn = 'mysql:dbname='.$database.';host='.$hostname;
        try {
            $dbh = new PDO($dsn, $username, $password);
            $msgs[] = 'Connection MySQL : <span class="ok">OK</span>';
        } catch (PDOException $e) {
            $errors++;
            $msgs[] = 'Connection MySQL : <span class="error">ERROR: '.$e->getMessage().'</span>';
        }
        
        //Install requested
        if(isset($_REQUEST['install']) && $_REQUEST['install'] == 1 && empty($errors)) {
            if(!file_exists($sqlFile)) {
                die('ERROR : File '.$sqlFile.' not found');
            }
            
            $queries = file_get_contents($sqlFile);
            
            try {
                $dbh->exec($queries);
                die('Installation succeful');
            } catch(PDOException $e) {
                $msgs[] = 'Connection MySQL : <span class="error">ERROR: '.$e->getMessage().'</span>';
            }
        } 

        //Show messages
        foreach($msgs as $msg) {
            echo $msg.'<br />';
        }

        
    break;
    case 'upload_zip':
        
        if(!isset($_REQUEST['catalog']) || !is_numeric($_REQUEST['catalog'])) {
            die('Request error');
        }
        
        if(!isset($_FILES['zip'])) {
            die('No zip found');
        }
        
        $zip = new ZipArchive;
        $resZip = $zip->open($_FILES['zip']['tmp_name']);
        if ( $resZip !== true ) {
            echo 'The file is not a correct ZIP : ';
            print_r($zip);
            exit();
        }

        //Check only one directory in the archive
        $i = 0;
        $nbMp3Files = 0;
        while ($info = $zip->statIndex($i)) {
            if($i == 0) {
                if(substr($info['name'], -1) != '/' || $info['size'] != 0) {
                    die('First entry in the zip file is not a directory');
                }
                $rootFolder = $info['name'];
            } else {
                if(strpos($info['name'], $rootFolder) !== 0) {
                    die('Zip file does not content only one root directory');
                }
                if(substr($info['name'], -strlen('.mp3')) === '.mp3') {
                    $nbMp3Files++;
                }
            }
            $i++;
        }
        
        if($nbMp3Files == 0) {
            die('No mp3 files found in the zip');
        }
        
        $catalog = new Catalog((int)$_REQUEST['catalog']);

        if(is_dir($catalog->path.'/upload')) {
            if(!is_writable($catalog->path.'/upload')) {
                die('Can\'t write in the upload directory');
            }
        } else {
            if(!is_writable($catalog->path)) {
                die('Can\'t create an upload directory in the catalog');
            } else {
                mkdir($catalog->path.'/upload');
            }
        }
        
        $zip->extractTo($catalog->path.'/upload');
        
        die('ok');
    break;
    case 'verify':
        if (isset($_REQUEST['catalogs'])) {
            foreach ($_REQUEST['catalogs'] as $catalog_id) {
                $catalog = new Catalog($catalog_id);
                $catalog->verify_catalog();
            }
        }
    break;
    case 'add':
        if (isset($_REQUEST['catalogs'])) {
            foreach ($_REQUEST['catalogs'] as $catalog_id) {
                $catalog = new Catalog($catalog_id);
                $catalog->add_to_catalog();
            }
        }
    break;
    case 'full_service':
        if (!$_REQUEST['catalogs']) {
            $_REQUEST['catalogs'] = Catalog::get_catalog_ids();
        }

        /* This runs the clean/verify/add in that order */
        foreach ($_REQUEST['catalogs'] as $catalog_id) {
            $catalog = new Catalog($catalog_id);
            $catalog->clean_catalog();
            $catalog->count = 0;
            $catalog->verify_catalog();
            $catalog->count = 0;
            $catalog->add_to_catalog();
        }
        Catalog::optimize_tables();
        $url    = Config::get('web_path') . '/admin/catalog.php';
        $title  = _('Catalog Updated');
        $body   = '';
        show_confirmation($title,$body,$url);
        toggle_visible('ajax-loading');
    break;
    case 'clean_catalog':
        // Make sure they checked something
        if (isset($_REQUEST['catalogs'])) {
            $error = false;
            foreach($_REQUEST['catalogs'] as $catalog_id) {
                $catalog = new Catalog($catalog_id);
                $status = $catalog->clean_catalog();
                
                if($status === false) {
                    break;
                }
            } // end foreach catalogs
            
            if($status !== false) {
                Catalog::optimize_tables();
            }
        }
    break;
    case 'delete_catalog':
        if (!Core::form_verify('delete_catalog', 'get')) {
            access_denied();
            exit;
        }
        Catalog::delete($_GET['catalog_id']);
    break;
    case 'gather_album_art':
        $catalogs = $_REQUEST['catalogs'] ? $_REQUEST['catalogs'] : Catalog::get_catalogs();

        // Itterate throught the catalogs and gather as needed
        foreach ($catalogs as $catalog_id) {
            $catalog = new Catalog($catalog_id);
            $catalog->get_art('',1);
        }
    break;
    case 'add_catalog': 
        if (!strlen($_POST['path']) || !strlen($_POST['name'])) {
                Error::add('general',_('Error: Name and path not specified'));
        }

        if (substr($_POST['path'],0,7) != 'http://' && $_POST['type'] == 'remote') {
                Error::add('general',_('Error: Remote selected, but path is not a URL'));
        }

        if ($_POST['type'] == 'remote' && !strlen($_POST['key'])) {
                Error::add('general',_('Error: Remote Catalog specified, but no key provided'));
        }

        if (!Core::form_verify('add_catalog','post')) {
                access_denied();
                exit;
        }

        // Make sure that there isn't a catalog with a directory above this one
        if (Catalog::get_from_path($_POST['path'])) {
                Error::add('general',_('Error: Defined Path is inside an existing catalog'));
        }

        // If an error hasn't occured
        if (!Error::occurred()) {

            $catalog_id = Catalog::Create($_POST);

            if (!$catalog_id) {
                Error::display('general');
                break;
            }

            $catalog = new Catalog($catalog_id);

            // Run our initial add
            $catalog->run_add($_POST);

            echo "<h2>" .  _('Catalog Created') . "</h2>";
            Error::display('general');
            Error::display('catalog_add');

        }
    break;
    case 'add_album':
        print_r($_POST); exit();
        if (!Core::form_verify('add_album','post')) {
            access_denied();
            exit;
        }

    break;
    default:
        $catalog_ids = Catalog::get_catalogs();
        $catalogs = Catalog::get_all_catalogs();
        include('tpl/admin.tpl.php');
    break;
}
