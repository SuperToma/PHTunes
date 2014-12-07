<?php
class image {
    
    private static function getInfos($id) {
        $sql = 'SELECT image, mime, size, object_type, object_id 
                FROM image 
                WHERE id = '.(int)$id.'
                LIMIT 1';

        $result = Dba::read($sql);

        return mysql_fetch_assoc($result);
    }
    
    public static function resize($id, $dest, $new_width = 0, $new_height = 0) {

        $image = self::getInfos($id);

        if(empty($image)) {
            header("HTTP/1.0 404 Image not Found");
            header("Status: 404 Image not Found");
            exit();
        }

        $file = $image['image'];
        $Mime_type = $image['mime'];

        $retour = true;

        //Get image size
        $newImg = imagecreatefromstring($file);
        $width = imagesx($newImg);
        $height = imagesy($newImg);
        imagedestroy($newImg);

        #TODO something better to do here... destroy/create/destroy/create...
        
        if(($new_width > $width) && ($new_height > $height)){
            $retour = file_put_contents($dest, $file);
        } else {

            $image = imagecreatefromstring($file);
            
            if( is_resource($image) ) {
                // Gestion de la transparence
                if($Mime_type == "image/gif" || $Mime_type == "image/png") {
                    $image_p = imagecreatetruecolor($new_width, $new_height);
                    imagealphablending($image_p, false);
                    imagesavealpha($image_p,true);
                    $transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
                    imagefilledrectangle($image_p, 0, 0, $new_width, $new_height, $transparent);
                } else {
                    $image_p = imagecreatetruecolor($new_width, $new_height);
                }
                $retour = imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            } else {
                $retour = false;
            }

            if($retour){
                switch($Mime_type){
                    case "image/jpeg": 
                    case "image/jpg":
                        $retour = imagejpeg($image_p, $dest, 85); //100 = max
                        break;
                    case "image/gif":
                        $retour = imagegif($image_p, $dest);
                        break;
                    case "image/png":
                        $retour = imagepng($image_p, $dest, 7); //9 = max
                        break;
                }

                imagedestroy($image);
                imagedestroy($image_p);
            }
        }
        return $retour;
    }

}

