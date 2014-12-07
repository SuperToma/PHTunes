<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="HandheldFriendly" content="True" />

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    
    <title>PHTunes</title>
    
    <link rel="stylesheet" type="text/css" href="css/jquery.mobile-1.3.2.min.css" />
    <link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css" />
    <link rel="stylesheet" type="text/css" href="css/zebra_dialog.css">
    <link rel="stylesheet" type="text/css" href="css/player.css" />
    
    <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="js/jquery.mobile-1.3.2.min.js"></script>
    <script type="text/javascript" src="js/jQuery.jPlayer.2.4.0/jquery.jplayer.min.js"></script>
    <script type="text/javascript" src="js/jQuery.jPlayer.2.4.0/add-on/jplayer.playlist.min.js"></script>
    <script type="text/javascript" src="js/jquery.contextMenu.js"></script>
    <script type="text/javascript" src="js/zebra_dialog.js"></script>
    
    <script src="js/jquery.ui.widget.js" type="text/javascript"></script>
    <script src="js/player.js" type="text/javascript"></script>
    
     <title>PHTunes</title>

</head>
<body>

<div data-role="page" id="player" class="ui-responsive-panel">
    
    <div id="jplayer" class="jp-jplayer"></div>

    
    <div data-role="header" data-position="fixed" data-fullscreen="true" data-dismissible="false" data-tap-toggle="false">
        <div data-type="horizontal" class="ui-btn-left"> 
            <a href="#leftPanel" class="headerLeft" data-icon="home" data-role="button" data-inline="true" data-mini="true">Menu</a>
            <input type="search" class="headerLeft" name="search" id="search" value="" data-mini="true" data-theme="b" data-inline="true" style="display: inline-block;" />
        </div>
        <h1 id="ajax-status">PHTunes</h1>
        <div data-type="horizontal" class="ui-btn-right">  
            <a href="#rightPanel" data-role="button" data-icon="bars" data-iconpos="notext" data-position-fixed="true">Current playlist</a>
        </div>
    </div>
    
    <ul id="search-results" data-role="listview" data-inset="true" data-divider-theme="d" data-theme="d"></ul>
    
    
    <div data-role="panel" id="leftPanel" data-position="left" data-display="overlay" data-position-fixed="true">
        <ul data-role="listview" data-divider-theme="d" data-icon="false">
            <li data-role="list-divider">Library</li>
            <li><a href="javascript:loadPage('artists.php?action=getAll');">Artists</a></li>
            <li><a href="javascript:loadPage('albums.php?action=getAll');">Albums</a></li>
            <li data-role="list-divider">Playlists</li>
            <!-- li><a href="#">Last 50 Played</a></li -->
            <li><a href="javascript:loadPage('albums.php?action=getLast');">Recently Added</a></li>
            <!-- li><a href="#">Recently Played</a></li -->
            <!-- li><a href="#">Favorites</a></li -->
            <li data-role="list-divider">Player</li>
            <li><a href="javascript:loadPage('admin.php');">Admin</a></li>
            <!-- li><a href="#">Conf</a></li>
            <li><a href="javascript:loadPage('index.php?tpl=thanks');">Thanks</a></li -->
            
        </ul>
        <br />
        <a href="#" data-rel="close" data-role="button" data-theme="a" data-icon="delete" data-inline="true" data-mini="true">Close panel</a>
    </div>
    
    
    <div data-role="panel" id="rightPanel" data-position="right" data-display="overlay" class="jp-playlist" data-position-fixed="true">
        <ul data-role="listview" data-divider-theme="d" data-icon="false" data-inset="true">
            <li></li>
        </ul>
        <br />
        <a href="#" data-rel="close" data-role="button" data-theme="a" data-icon="delete" data-inline="true" data-mini="true">Close</a>
    </div>
    
    <div data-role="content" id="playerContent">
        
    </div>

    
    <div id="footer" data-role="footer" data-theme="a" data-position="fixed" data-fullscreen="true" data-dismissible="false" data-tap-toggle="false">

        <!-- div class="ui-btn-left" data-role="controlgroup" data-type="horizontal">
            <a href="#" data-role="button" class="jp-previous"> &#9664;&#9664; </a>
            <a href="#" data-role="button" id="btn-play"> &#9654; </a> <a href="#" data-role="button" id="btn-pause" style="display:none"> &#10073;&#10073; </a>
            <a href="#" data-role="button" class="jp-next"> &#9654;&#9654; </a>
        </div>
    	<h1 style="max-width: 33%; margin: auto;">
            <img src="img/2-128x128/album.png" width="50" height="50" style="float: left;" />
            <div class="jp-progress">
                <div class="jp-seek-bar">
                    <div class="jp-play-bar"><img src="./img/handle.png" alt="handle" /></div>
                </div>
            </div>
            <div class="jp-current-time"></div>
            <div class="jp-duration"></div>
        </h1 -->
        
        <table style="margin-left: 5px; margin-right: 5px;">
            <tr>
                <td class="jp-current-time" valign="bottom">0:00</td>
                <td width="100%">
                    <div class="playBar">
                        <input type="range" name="slider" id="myPlayBar" value="0" min="0" max="100" width="100%" data-theme="b" data-track-theme="a" data-highlight="true" />
                    </div>
                </td>
                <td class="jp-duration" valign="bottom">0:00</td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="100%">
                    <fieldset>
                        <div data-role="controlgroup" data-type="horizontal">
                                <a href="#" data-role="button" class="jp-previous"> &#9664;&#9664; </a>
                                <a href="#" data-role="button" class="jp-play"> &#9654; </a> 
                                <a href="#" data-role="button" class="jp-pause hidden"> &#10073;&#10073; </a>
                                <a href="#" data-role="button" class="jp-next"> &#9654;&#9654; </a>
                        </div>
                    </fieldset>
                </td>
                <td>
                    <div data-role="controlgroup" data-type="horizontal">
                        <a href="#songOptionsPage" id="songOptionsButton" data-role="button" data-iconpos="right" data-icon="gear" data-iconpos="notext" data-rel="dialog"> Options </a>
                    </div>
                </td>
            </tr>
        </table>
        
    </div>
    
    <div data-role="popup" id="popupSongs">
        <ul data-role="listview" data-inset="true" style="min-width:210px;" data-theme="d">
        </ul>
    </div>
    
    <div data-role="popup" id="popupDialog" data-overlay-theme="a" data-theme="c" style="max-width:400px;" class="ui-corner-all">
        <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
        <div data-role="header" data-theme="a" class="ui-corner-top">
            <h1 class="title"></h1>
        </div>
        <div class="content" data-role="content" data-theme="d" class="ui-corner-bottom ui-content"></div>
    </div>
    
    <div data-role="popup" id="popupLogin" data-theme="a" class="ui-corner-all" data-overlay-theme="a">
        <div style="padding:10px 20px;">
            <h3>Please log in</h3>
            <label for="un" class="ui-hidden-accessible">Username:</label>
            <input name="username" value="" placeholder="username" data-theme="a" type="text">
            <label for="pw" class="ui-hidden-accessible">Password:</label>
            <input name="password" value="" placeholder="password" data-theme="a" type="password">
            <button type="button" data-theme="b" data-icon="check" onclick="login()">Sign in</button>
        </div>
    </div>
    
</div>

</body>
</html>