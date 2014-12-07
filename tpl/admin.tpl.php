<div>
    
    <h3>Install</h3>
    <a href="#" onclick="testConfig()"  data-role="button" data-inline="true" data-mini="true" data-icon="arrow-r">Test configuration</a>
    <a href="#" onclick="testConfig(1)" data-role="button" data-inline="true" data-mini="true" data-icon="edit">Install</a>

    <br /><br />
    <hr />
    
   
    
    
    <h3>Catalogs :</h3>
    
    <!-- LIST CATALOGS -->
    <table data-role="table" class="ui-body-d ui-shadow table-stripe ui-responsive">
        <thead>
            <tr class="ui-bar-d">
                <th align="left"><?php echo _('Name'); ?></th>
                <th align="left"><?php echo _('Path'); ?></th>
                <th align="left"><?php echo _('Last Verify'); ?></th>
                <th align="left"><?php echo _('Last Add'); ?></th>
                <th align="left"><?php echo _('Last Clean'); ?></th>
                <th align="left"><?php echo _('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach ($catalog_ids as $catalog_id):
            $catalog = new Catalog($catalog_id);
            $catalog->format();
            ?>
            <tr>
                <td class="cel_catalog"><?php echo $catalog->f_name_link; ?></td>
                <td class="cel_path"><?php echo scrub_out($catalog->f_path); ?></td>
                <td class="cel_lastverify"><?php echo scrub_out($catalog->f_update); ?></td>
                <td class="cel_lastadd"><?php echo scrub_out($catalog->f_add); ?></td>
                <td class="cel_lastclean"><?php echo scrub_out($catalog->f_clean); ?></td>
                <td class="cel_action">
                  <!-- a href="#" onclick="loadAjax('admin.php?action=verify&amp;catalogs[]=<?php echo $catalog->id; ?>');"><?php echo _('Verify'); ?></a -->
                  <a href="#" onclick="loadAjax('admin.php?action=add&amp;catalogs[]=<?php echo $catalog->id; ?>');"><?php echo 'Search new files'; ?></a>
                | <a href="#" onclick="loadAjax('admin.php?action=clean_catalog&amp;catalogs[]=<?php echo $catalog->id; ?>');"><?php echo 'Clean deleted files'; ?></a>
                | <a href="#" onclick="loadAjax('admin.php?action=full_service&amp;catalogs[]=<?php echo $catalog->id; ?>');"><?php echo 'FULL Update'; ?></a>
                
                | <strong><?php echo _('Gather Art'); ?></strong>
                  ( 
                    <a href="#" onclick="loadAjax('admin.php?action=gather_album_art&amp;catalogs[]=<?php echo $catalog->id; ?>');"><?php echo _('All'); ?></a>
                    |
                    <a href="#" onclick="loadAjax('admin.php?action=gather_empty_album_art&amp;catalogs[]=<?php echo $catalog->id; ?>');"><?php echo _('Empty'); ?></a>
                   )
                   
                | <a href="#" onclick="if(confirm('Are you sure to delete this catalog ? (<?php echo scrub_out($catalog->name); ?>)')) { loadAjax('admin.php?action=delete_catalog&amp;form_validation=<?php echo Core::form_register('delete_catalog', 'get')?>&amp;catalog_id=<?php echo $catalog->id; ?>'); loadPage('admin.php'); }"><?php echo _('Delete'); ?></a>
                </td>
            </tr>
        <?php
        endforeach;
    ?>
        </tbody>
    </table>
    
    <br />
    
    <a href="#" onclick="$('#addCatalog').toggle();" data-role="button" data-mini="true" data-inline="true" data-icon="plus" data-theme="b">Add new catalog</a>
    
    <!-- FORM ADD CATALOG -->
    <form action="" class="hidden" id="addCatalog">
        <table class="tabledata" cellpadding="0" cellspacing="0">
            <tr>
                <td><?php echo _('Catalog Name'); ?>: </td>
                <td><input size="60" type="text" name="name" value="<?php echo scrub_out($_POST['name']); ?>" /></td>
                <!-- td style="vertical-align:top; font-family: monospace;" rowspan="6">
                    <strong><?php echo _('Auto-inserted Fields'); ?>:</strong><br />
                    %A = <?php echo _('album name'); ?><br />
                    %a = <?php echo _('artist name'); ?><br />
                    %c = <?php echo _('id3 comment'); ?><br />
                    %T = <?php echo _('track number (padded with leading 0)'); ?><br />
                    %t = <?php echo _('song title'); ?><br />
                    %y = <?php echo _('year'); ?><br />
                    %o = <?php echo _('other'); ?><br />
                </td -->
            </tr>

            <tr>
                <td><?php echo _('Path'); ?>: </td>
                <td><input size="60" type="text" name="path" value="<?php echo scrub_out($_POST['path']); ?>" /></td>
            </tr>
            <input type="hidden" name="type" value="local" />
            <input type="hidden" name="rename_pattern" value="%a - %T - %t" />
            <input type="hidden" name="sort_pattern" value="%a/%A" />
            <!-- tr>
                <td><?php echo _('Catalog Type'); ?>: </td>
                <td>
                    <select name="type">
                        <option value="local"><?php echo _('Local'); ?></option>
                        <option value="remote"><?php echo _('Remote'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo _('Remote Catalog Username'); ?>: </td>
                <td><input size="30" type="text" name="remote_username" value="" /><span class="error">*<?php echo _('Required for Remote Catalogs'); ?></span></td>
            </tr>
            <tr>
                <td><?php echo _('Remote Catalog Password'); ?>: </td>
                <td><input size="30" type="password" name="remote_password" value="" /><span class="error">*<?php echo _('Required for Remote Catalogs'); ?></span></td>
            </tr>
            <tr>
                <td><?php echo _('Filename Pattern'); ?>: </td>
                <td><input size="60" type="text" name="rename_pattern" value="<?php echo $default_rename; ?>" /></td>
            </tr>

            <tr>
                <td><?php echo _('Folder Pattern'); ?>:<br /><?php echo _("(no leading or ending '/')"); ?></td>
                <td valign="top"><input size="60" type="text" name="sort_pattern" value="<?php echo $default_sort; ?>" /></td>
            </tr -->

            <tr>
                <td valign="top"><?php echo _('Gather Album Art'); ?>:</td>
                <td><input type="checkbox" name="gather_art" value="1" /></td>
            </tr>
            <tr>
                <td valign="top"><?php echo _('Build Playlists from m3u Files'); ?>:</td>
                <td><input type="checkbox" name="parse_m3u" value="1" /></td>
            </tr>
        </table>
        <div class="formValidation">
            <input type="hidden" name="action" value="add_catalog" />
            <?php echo Core::form_register('add_catalog'); ?>
            <input type="button" data-mini="true" data-inline="true" value="<?php echo _('Add Catalog'); ?>" onclick="addCatalog();" />
        </div>
    </form>
    
    <br /><br />
    <hr />
    
    
    
    
    
    
    
    <h3>Upload album :</h3>
    Only a .zip file with a folder and .mp3 files inside
    <br /><br />
    <div style="width:300px;">
        <select id="uploadCatalog">
            <option value="">Choose a catalog</option>
            <?php
                foreach($catalogs as $catalog) {
                    if($catalog['type'] == 'local') {
                        echo '<option value="'.$catalog['id'].'">'.$catalog['name'].'</option>';
                    }
                }
            ?>
        </select>
        <input name="file" id="file" value="" type="file">
        <input type="text" id="uploadStatus" disabled="disabled" value="0%">
    </div>
    <hr />
</div>





<script type="text/javascript">
    
    var zip = '';
    
    handleImageUpload = function handleImageUpload(event) {
      if($('#uploadCatalog').val() == '') {
          alert('Please choose a catalog before uploading');
          return;
      }
      var files = event.target.files;
      zip = files[0];
      uploadZip();
    }

    $("#file").change(function(event) {
      handleImageUpload(event);
    });
    
    function uploadZip() { 
        
        var uri = 'admin.php?action=upload_zip';
        var xhr = new XMLHttpRequest();
        var fd = new FormData();
        
        fd.append('zip', zip);
        fd.append('catalog', $('#uploadCatalog').val());

        xhr.open('POST', uri, true);
        
        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                var complete = (event.loaded / event.total * 100 | 0);
                $('#uploadStatus').val(complete + '%');
            }
        }
        
        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && xhr.status == 200) {
              $('#uploadStatus').val('100%');
              if(xhr.responseText == 'ok') {
                  alert('Upload Successful\n\nPlease refresh catalog !');
              } else {
                  alert(xhr.responseText);
              }
          }
        };

        xhr.send(fd);
    }
    
    
    //Test configuration
    function testConfig( install ) {
        
        if( !install ) {
            install = 0;
            popupTitle = 'Test Environement';
        } else {
            install = 1;
            popupTitle = 'Installation';
        }
        
        $.mobile.showPageLoadingMsg();
        
        $.ajax({
            type: "POST",
            url: "admin.php",
            data: { action: "testConfig", install: install }
        }).done(function(html) {
            $('#popupDialog .title').html(popupTitle);
            $('#popupDialog .content').html( html );
            
            $('#popupDialog').popup('open');
        }).fail(function() {
            alert( "Search error" );
        }).always(function() {
            $.mobile.hidePageLoadingMsg();
        });
    }
    
</script>