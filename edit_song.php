<?php

if(!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id']) ) {
    die('Bad arguments');
}

require_once 'lib/init.php';

$song = new Song( $_REQUEST['id'] );

switch ($_REQUEST['action']) {
    case 'save':
        if (!Core::form_verify('edit_song', 'post')) {
            access_denied();
            exit;
        }
        
        //Delete magic quotes
        if(version_compare(PHP_VERSION, '5.3.0', '<=') AND ini_get('magic_quotes_gpc') != 'Off') {
            foreach($_REQUEST as $key => $val) {
                if(is_string($val)) {
                    $_REQUEST[$key] = stripslashes($val);
                }
            }
	}
        
        //Song Update
        if($_REQUEST['artist'] != $song->get_artist_name()) {
            $song->artist = Catalog::check_artist($_REQUEST['artist']);
        }
        
        //Album Id can change if year or album name change
        $albumId = Catalog::check_album($_REQUEST['album'], (int)$_REQUEST['year']);

        $song->album = $albumId;
        $song->title  = $_REQUEST['title'];
        $song->track  = $_REQUEST['track'];
        $song->year   = (int)$_REQUEST['year'];
        
        $song->update_song($song->id, $song);
        
        //Cover update
        if (!empty($_FILES['cover']['tmp_name'])) {
            if (is_uploaded_file($_FILES['cover']['tmp_name'])) {
                if ($fd = fopen($_FILES['cover']['tmp_name'], 'rb')) {
                    $APICdata = fread($fd, filesize($_FILES['cover']['tmp_name']));
                    fclose ($fd);
                    
                    list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($_FILES['cover']['tmp_name']);

                    $imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');
                    
                    $art = new Art($albumId, 'album');
                    $tmp = $art->insert($APICdata, 'image/'.$imagetypes[$APIC_imageTypeID]);

                    if (Config::get('resize_images')) { 
                        $thumb = $art->generate_thumb($APICdata, array('width'=>275,'height'=>275),'image/'.$imagetypes[$APIC_imageTypeID]); 
                        if (is_array($thumb)) { $art->save_thumb($thumb['thumb'], $thumb['thumb_mime'], '275x275'); } 
                    } 
        
                } else {
                    echo 'cannot open '.$_FILES['userfile']['tmp_name'];
                }
            } else {
                echo '!is_uploaded_file('.$_FILES['userfile']['tmp_name'].')';
            }
        }
        
        //ID3 mp3 update
        if($_REQUEST['id3'] == 'on') {
            
            $song->artist = $song->get_artist_name();
            $song->album  = $song->get_album_name();

            $TaggingFormat = 'UTF-8';
            
            require_once('./modules/getid3/getid3.php');
            
            $getID3 = new getID3;
            $getID3->setOption(array('encoding'=>$TaggingFormat));
            
            getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, true);
            
            $tagwriter = new getid3_writetags;
            $tagwriter->filename          = $song->file;
            $tagwriter->tagformats        = array('id3v2.3'); // !!! ID3v2 min. for covers
            $tagwriter->overwrite_tags    = true;
            $tagwriter->tag_encoding      = $TaggingFormat;
            $tagwriter->remove_other_tags = false;
            
            //Making text tags
            $commonkeysarray = array('title', 'track', 'artist', 'album', 'year', 'comment');
            foreach ($commonkeysarray as $key) {
                if (!empty($song->$key)) {
                    $TagData[strtolower($key)][] = $song->$key;
                }
            } 

            if(isset($APICdata)) {
                #TODO : Resize 300x300 max

                if($APIC_width != 300) {
                    echo 'Sorry cover width is not equal 300px ('.$APIC_width.')';
                } elseif($APIC_height != 300) {
                    echo 'Sorry cover height is not equal 300px ('.$APIC_height.')';
                } elseif(!isset($imagetypes[$APIC_imageTypeID])) {
                    echo 'invalid image format (only GIF, JPEG, PNG)';
                } else {
                    $TagData['attached_picture'][0]['data']          = '';//$APICdata;
                    $TagData['attached_picture'][0]['picturetypeid'] = 0x03; //Cover (front) cf. getid3_id3v2::APICPictureTypeLookup
                    $TagData['attached_picture'][0]['description']   = 'cover : '.$_FILES['cover']['name'];
                    $TagData['attached_picture'][0]['mime']          = 'image/'.$imagetypes[$APIC_imageTypeID];
                }
            }

            $tagwriter->tag_data = $TagData;
            
            if ($tagwriter->WriteTags()) {
                echo 'Successfully wrote tags';
                Catalog::check_album($_REQUEST['album']);
                if (!empty($tagwriter->warnings)) {
                    echo 'There were some warnings : '.implode("\n", $tagwriter->warnings).'';
                }
                Catalog::clean_albums();
            } else {
                    echo 'Failed to write tags ! '.implode("\n", $tagwriter->errors).'';
            }
        } // END if (id3 == on)
        
    break;
    default:
        include('tpl/edit_song.tpl.php');
    break;
}
