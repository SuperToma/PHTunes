<?php echo $song->file; ?>
<br /><br />

<form action="" id="modifySong" enctype="multipart/form-data" method="post">
    
    <input type="hidden" name="id" value="<?php echo $song->id; ?>" />
    <?php echo Core::form_register('edit_song'); ?>
    
    <table>
        <tr>
            <td>Title : </td>
            <td><input type="text" name="title" value="<?php echo $song->title; ?>" /></td>
        </tr>
        <tr>
            <td>Track number : </td>
            <td><input type="text" name="track" value="<?php echo $song->track; ?>" /></td>
        </tr>
        
        <tr>
            <td>Artist : </td>
            <td><input type="text" name="artist" value="<?php echo $song->get_artist_name(); ?>" /></td>
        </tr>
        <tr>
            <td>Album : </td>
            <td><input type="text" name="album" value="<?php echo $song->get_album_name(); ?>" /></td>
        </tr>
        <tr>
            <td>Year : </td>
            <td><input type="text" name="year" value="<?php echo $song->year; ?>" /></td>
        </tr>
        <tr>
            <td>Album cover : </td>
            <td><input type="file" name="cover" id="file" /></td>
        </tr>
        <tr>
            <td>Modify mp3 file : </td>
            <td><input type="checkbox" name="id3" id="id3" checked="checked" /></td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    
    var cover = '';
    
    handleImageUpload = function handleImageUpload(event) {
      var files = event.target.files;
      cover = files[0];
    }

    $("#file").change(function(event) {
      handleImageUpload(event);
    });
</script>