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
        
        //Test PHP5
        if ((int)phpversion() < 5) {
            $errors++;
            $msgs[] = 'PHP 5 : <span class="error">ERROR: PHTunes requires PHP 5</span>';
        } else {
            $msgs[] = 'PHP 5 : <span class="ok">OK</span>';
        }
        
        //Test MySQL connection
        $dsn = 'mysql:dbname='.$database.';host='.$hostname;
        try {
            $dbh = new PDO($dsn, $username, $password);
            $msgs[] = 'Connection MySQL : <span class="ok">OK</span>';
        } catch (PDOException $e) {
            $errors++;
            $msgs[] = 'Connection MySQL : <span class="error">ERROR: '.$e->getMessage().'<br />Please check file config/ampache.cfg.php</span>';
        }
        
        //test cache folder is writable
        $result = 'Cache folder writable : <span class="ok">OK</span>';
        if( !is_dir('cache') ) {
            $ret = mkdir("cache", 0755);
            if( !$ret ) {
                $result = 'Cache folder : <span class="error">ERROR: Please create a "cache" folder with writing autorization</span>';
            } else {
                if( !is_writable('cache') ) {
                    $ret = chmod('/home/path/directory/', 0775);
                    if( !$ret ) {
                        $result = 'Cache folder writable : <span class="error">ERROR: Please add the right to write inside the folder "cache"</span>';
                    }
                }
            }
        }
        $msgs[] = $result;
        
        //Test date.timezone
        $timezone = ini_get('date.timezone');
        if( empty($timezone) ) {
            $msgs[] = 'Timezone : <span class="warning">WARNING: Please configure your date.timezone directive in your php.ini file.</span>';
        } else {
            $msgs[] = 'Timezone : <span class="ok">OK</span>';
        }
        
        //Test GD is installed
        if (extension_loaded('gd') && function_exists('gd_info')) {
            $msgs[] = 'GD Library : <span class="ok">OK</span>';
        } else {
            $msgs[] = 'GD Library : <span class="error">ERROR: Please install the GD library (PHP extension for images resizing)</span>';
        }
        
        //Test PHP extension ZipArchive
        if ( class_exists('ZipArchive') ) {
            $msgs[] = 'PHP Extension ZipArchive : <span class="ok">OK</span>';
        } else {
            $msgs[] = 'PHP Extension ZipArchive: <span class="error">ERROR: Please install the ZipArchive extension (PHP extension for uploading songs and albums)</span>';
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
        
        if(!class_exists('PharData') && !class_exists('ZipArchive')) {
            die('Can\'t extract archives : PHP Extensions ZipArchive or Phar not installed');
        }
        
        if(!isset($_REQUEST['catalog']) || !is_numeric($_REQUEST['catalog'])) {
            die('Request error');
        }
        
        if(!isset($_FILES['zip'])) {
            die('No zip found');
        }
        
        //Initialize catalog
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
        
        //Get temporary file
        $tmpFile = 'tmp.zip';

        if(file_exists($tmpFile)) {
            unlink($tmpFile);
        }
        move_uploaded_file($_FILES['zip']['tmp_name'], $tmpFile);
        
        if(class_exists('PharData')) {
            
            $archive = new PharData($tmpFile);

            $filePath = 'phar://'.__DIR__.'/'.$tmpFile;

            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($filePath), RecursiveIteratorIterator::SELF_FIRST);

            $i = 0;
            $nbMp3Files = 0;
            foreach($files as $name => $file) {
                if($i == 0) { 
                    if(!$file->isDir()) {
                        die('First entry in the zip file is not a directory');
                    }
                    $rootFolder = $name;
                } else {
                    if(strpos($name, $rootFolder) !== 0) {
                        die('Zip file does not content only one root directory');
                    }
                    if(substr($name, -strlen('.mp3')) === '.mp3') {
                        $nbMp3Files++;
                    }
                }
                $i++;
            }
            
            if($nbMp3Files == 0) {
                die('No mp3 files found in the zip');
            }

            $archive->extractTo($catalog->path.'/upload');
            if(file_exists($tmpFile)) {
                unlink($tmpFile);
            }
            die('ok');
        } //End if(class_exists('PharData'))
        
        if(class_exists('ZipArchive') && !class_exists('PharData')) {
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

            $zip->extractTo($catalog->path.'/upload');
            if(file_exists($tmpFile)) {
                unlink($tmpFile);
            }
            die('ok');
        } //End if(class_exists('ZipArchive'))
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
    case 'gather_empty_album_art':
        $catalogs = $_REQUEST['catalogs'] ? $_REQUEST['catalogs'] : Catalog::get_catalogs();

        // Itterate throught the catalogs and gather as needed
        foreach ($catalogs as $catalog_id) {
            $catalog = new Catalog($catalog_id);
            $catalog->get_art('','empty');
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

            #TODO : display errors don't know how...
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
