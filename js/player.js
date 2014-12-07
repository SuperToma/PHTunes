/*******************************************************************************
 *                        When Application is Loaded                           *
 *******************************************************************************/
var songs = [];
var playlist;
var timeUpdate = true;
var currentSong;
var isConnected = false;

$(document).bind("pageinit", function() {

    loadPage('artists.php?action=getAll');
    
    playlist = new jPlayerPlaylist({
        jPlayer: "#jplayer",
        cssSelectorAncestor: "#player"
    }, [], {
        swfPath: "js/jPlayer/Jplayer.swf",
        supplied: "mp3",
        wmode: "window",
    cssSelector: {
        play: '#btn-play',
        pause: '#btn-pause',
        //currentTime: '.track-position-time',
        //duration: '.track-position-remaining'
        playBar: '.jp-play-bar'
    },
    playlistOptions: {
        autoPlay: true,
        displayTime: 0,
        addTime: 0,
        removeTime: 0,
        shuffleTime: 0
    },
    progress: function(event) {
        $('.playBar .ui-slider-track .seekBar').width(event.jPlayer.status.seekPercent+"%");
    },
    loadstart: function (){
        $('.track-info h2').html(playlist.playlist[playlist.current].title);
        $('#song-album').html(playlist.playlist[playlist.current].artist);
    },
    play: function(event) {
        //If duration not found in the media file then take in the data
        if(event.jPlayer.status.duration == 0) {
            var duration = event.jPlayer.status.media.duration;
            event.jPlayer.status.duration = duration;
        }
        
        //Make the seekbar if not already created
        if( $('.playBar .ui-slider-track .seekBar').length == 0 ) {
            var seekBar = $('.playBar .ui-slider-track').clone().html('').addClass('seekBar').addClass('ui-btn-down-c');
            $('.playBar .ui-slider-track').prepend(seekBar);
            $('.playBar .ui-slider-track .seekBar').css('width', '0%');
        }

        $('.jp-play').hide();
        $('.jp-pause').show();
        $(".jp-pause").on('click', function() { $("#jplayer").jPlayer('pause'); });
        
        //Set song title
        $('#ajax-status').fadeOut().addClass('green').html('Login successfull').fadeIn();
    },
    pause: function() {
        $('.jp-pause').hide();
        $('.jp-play').show();
        $(".jp-play").on('click', function() { $("#jplayer").jPlayer('play'); });
    },
    timeupdate: function(event) {
        //Move playbar's cursor
        if(timeUpdate) {
            //event.jPlayer.status.currentPercentAbsolute often not work...
            currentPercent = event.jPlayer.status.currentTime * 100 / event.jPlayer.status.media.duration;
            $('.playBar .ui-slider-bg').css('width', currentPercent + '%');
            $('.playBar a').css('left', currentPercent + '%');
        }
        
        var duration = event.jPlayer.status.media.duration;
        var minutes = Math.floor(duration / 60).toString();

        if(minutes.length == 1) {
            minutes = '0' + minutes;
        }
        var seconds = duration - minutes * 60;
        seconds = seconds.toString();
        if(seconds.length == 1) {
            seconds = '0' + seconds;
        }

        $('.jp-duration').text(minutes + ':' + seconds);
    },
    error: function (event) {
        if(event.jPlayer.error.type == 'e_url') {

            var rep = $.ajax({
                url: event.jPlayer.error.context,
                statusCode: {
                    401: function() {
                        $('#popupLogin').popup('open', {positionTo: 'window'});
                    }
                }
            });
        }
    },
    errorAlerts: false,
    warningAlerts: false
    });

    //Onscroll close panel
    $(window).scroll(function () {
        $("#rightPanel").panel("close");
    });

    //Make the playBar draggable correctly
    $( "#myPlayBar" ).on('slidestart', function() { timeUpdate = false; });
    $( "#myPlayBar" ).on('slidestop',  function(event) { //event.target.value
        //alert($("#jplayer").data().jPlayer.status.srcSet);
        //alert(event.target.value);
        $("#jplayer").jPlayer('playHead', event.target.value);
        timeUpdate = true;
    });

});

/*******************************************************************************
 *                         When a New Page is Loaded                           *
 *******************************************************************************/
$(document).on('pageinit',function(){
    if($(window).width() > 800) {
        //$("#leftPanel").panel("open");
    }
    
    //Search input events
    $("#search").on('keyup focusin', function() {
        
        if($(this).val().length < 2) {
            $('#search-results').fadeOut("slow", function() {
                $('#search-results').html('');
            });
            return;
        }
        
        $.mobile.showPageLoadingMsg();
        
        $.ajax({
            type: "POST",
            url: "search.php",
            data: { action: "search", search: $(this).val() }
        }).done(function(html) {
            if(html == '') {
                $('#search-results').fadeOut();
            } else {
                $('#search-results').html(html).listview('refresh').fadeIn();
            }
        }).fail(function() {
            alert( "Search error" );
        }).always(function() {
            $.mobile.hidePageLoadingMsg();
        });
        
    });
    $("#search").focusout(function() {
        $('#search-results').fadeOut("slow", function() {
            $('#search-results').html('');
        });
    });
});

/*******************************************************************************
 *                             Player functions                                *
 *******************************************************************************/
function playSongs (songs) {
    playlist.setPlaylist(songs);
    
    $('.jp-playlist ul').prepend('<li data-role="list-divider">Current playlist :</li>')

    //$.each($('.jp-playlist ul li'), function(i) {
    //    $(this).wrapInner('<a href="#">');
    //});
    
    $(".jp-playlist ul").listview("refresh");
    
    //$("#rightPanel").panel("open");
    //setTimeout(function(){$("#rightPanel").panel("close");},3000);
}

function loadAjax(url) {
    $.mobile.showPageLoadingMsg();

    var oldresponse = '';
    
    if(window.XMLHttpRequest)     xhr = new XMLHttpRequest(); 
    else if(window.ActiveXObject) xhr = new ActiveXObject("Microsoft.XMLHTTP");
    else {
        alert('Sorry, your web browser doesn\'t support Ajax');
        return(false); 
    }

    xhr.onreadystatechange = function () {
        
        var newResponse = this.responseText.replace(oldresponse, '');
        oldresponse = this.responseText;
        
        if(newResponse != '') {
            eval(newResponse);
        }
        
        if (xhr.readyState==4) {
            $.mobile.hidePageLoadingMsg();
        }
    }
    xhr.open('GET', url, true);
    xhr.send(null);
}

function loadPage(url, disableLoading) {
    
    if(!disableLoading) {
        $.mobile.showPageLoadingMsg();
    }
    
    $("#leftPanel").panel("close");

    $('#playerContent').load(url, function(){
        
        $('#playerContent').trigger('create');
        
        //If we load albums then refresh right click
        if (url.match("^albums.php")) {
            //Left-click on album
            $.contextMenu({
                selector: ".album", 
                className: 'data-title',
                callback: function(key, options) {
                    switch (key) {
                        case 'download':
                            if(!isConnected) {
                                $('#popupLogin').popup('open', {positionTo: 'window'});
                            } else {
                                window.location.href = 'stream.php?downloadAlbum&id=' + $(this).attr('data-id');
                            }
                        break;
                    }
                },
                events: {
                    show: function(opt){
                        $('.data-title').attr('data-menutitle', $(this).find("h2").text());
                    }
                },
                items: {
                    "download":   {name: "Download", icon: "download"},
                }
            });
        }
        
        if(!disableLoading) {
            $.mobile.hidePageLoadingMsg();
        }
    });
}

function loadArtists() {
    $.mobile.showPageLoadingMsg();
    
    $("#playerContent").load('artists.php?action=getAll', function(){
        $('#playerContent').trigger('create');
        $.mobile.hidePageLoadingMsg();
    });
}

function loadSongs( id ) {
    
    window.currentAlbumClicked = id;
    
    $.mobile.showPageLoadingMsg();

    $('#popupSongs').load('albums.php?action=getSongs&id=' + id, function(){

        //Left-click on song
        $.contextMenu({
            selector: ".songName", 
            className: 'data-title',
            callback: function(key, options) {
                switch (key) {
                    case 'edit': 
                        new $.Zebra_Dialog('<strong>Edit file :</strong><br>', {
                             source:  {
                             ajax: 'edit_song.php?id=' + $(this).attr('data-id')},
                             width: 600,
                             type: false,
                             buttons:  [
                                {caption: 'Save',
                                    //Send data if save button is clicked
                                    callback: function() { 
                                        
                                        var uri = 'edit_song.php?action=save&id3='+$('[name="id3"]').is(':checked');
                                        var xhr = new XMLHttpRequest();
                                        var fd = new FormData();
                                        
                                        $('#modifySong :input').each(function(){
                                            fd.append($(this).attr('name'), $(this).val());
                                        });
                                        
                                        if(cover != '') {
                                            fd.append('cover', cover);
                                        }
                                        
                                        xhr.open('POST', uri, true);
                                        xhr.onreadystatechange = function() {
                                          if (xhr.readyState == 4 && xhr.status == 200) {
                                              alert(xhr.responseText);
                                              loadSongs(window.currentAlbumClicked);
                                          }
                                        };

                                        xhr.send(fd);
                                    }
                                },
                                {caption: 'Cancel'}
                             ],
                             title: $(this).find(".artist").text() + ' : ' + $(this).find(".name").text(),
                        });
                        $('#popupSongs').popup('close');
                    break;
                    case 'delete': alert('Nothing'); break;
                    case 'infos': alert ('Infos'); break;
                }
            },
            events: {
                show: function(opt){
                    $('.data-title').attr('data-menutitle', $(this).find(".name").text());
                }
            },
            items: {
                "edit":   {name: "Edit",   icon: "edit"},
                "delete": {name: "Delete", icon: "delete"},
                "sep1":   "---------",
                "infos":  {name: "Infos",  icon: "infos"}
            }
        });

        //click on a song
        $('.songName').click(function() {
			
            songs = []; //Delete songs 

            $(this).nextAll(".songName").andSelf().each(function () {

                var song = {
                    id: $(this).attr('data-id'),
                    mp3: 'stream.php?id=' + $(this).attr('data-id'),
                    title: $(this).find(".name").html(),
                    artist: $(this).parent().children(":nth-child(1)").html(),
                    duration: $(this).find(".ui-li-count").attr('data-duration')
                };
                songs.push(song);
            });

            playSongs( songs );
            
            //Close opup when song is clicked
            $('#popupSongs').popup('close');
        });
      
        $('#popupSongs').trigger('create');
        
        $.mobile.hidePageLoadingMsg();
        
        $('#popupSongs').popup('open', {positionTo: '#album'+id});
    });
}

function addCatalog() {
    $.mobile.showPageLoadingMsg();

    var oldresponse = '';

    if(window.XMLHttpRequest)     xhr = new XMLHttpRequest(); 
    else if(window.ActiveXObject) xhr = new ActiveXObject("Microsoft.XMLHTTP");
    else {
        alert('Sorry, your web browser doesn\'t support Ajax');
        return(false); 
    }

    xhr.onreadystatechange = function () {
        
        var newResponse = this.responseText.replace(oldresponse, '');
        oldresponse = this.responseText;
        
        if(newResponse != '') {
            eval(newResponse);
        }
        
        if (xhr.readyState==4) {
            loadPage('admin.php', null, 1);
            $.mobile.hidePageLoadingMsg();
        }
    }
    xhr.open('POST', 'admin.php?rnd=' + (new Date()).getTime(), true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    gather_art = 0;
    if( $("#addCatalog input[name=gather_art]").is(':checked') ) {
        gather_art = 1;
    }
    parse_m3u = 0;
    if( $("#addCatalog input[name=parse_m3u]").is(':checked')) {
        parse_m3u = 1;
    }
    xhr.send("action=add_catalog" +  
             "&name=" + $("#addCatalog input[name=name]").val() + 
             "&path=" + $("#addCatalog input[name=path]").val() + 
             "&type=" + $("#addCatalog input[name=type]").val() + 
             "&rename_pattern=" + $("#addCatalog input[name=rename_pattern]").val() + 
             "&sort_pattern=" + $("#addCatalog input[name=sort_pattern]").val() +
             "&gather_art=" + gather_art +
             "&parse_m3u=" + parse_m3u +
             "&form_validation=" + $("#addCatalog input[name=form_validation]").val() );
    return false;
}

function login() {
    
   $.mobile.showPageLoadingMsg();
   
    $.ajax({
        type: "POST",
        url: "login.php",
        data: { username: $("#popupLogin input[name='username']").val(), password: $("#popupLogin input[name='password']").val() }
    }).done(function(html) {
        if(html == '1') {
            $('#popupLogin').popup('close');
            $('#ajax-status').fadeOut().addClass('green').html('Login successfull').fadeIn();
            $("#jplayer").jPlayer('play');
            isConnected = true;
        } else {
            alert( html );
        }
    }).fail(function() {
        alert( "Login error" );
    }).always(function() {
        $.mobile.hidePageLoadingMsg();
    });
   
}