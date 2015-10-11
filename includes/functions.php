<?php

function displayServerDetails($serverIP, $serverDB, $serverUser, $serverPass, $ServerName, $ServerPort)
{

    // Database Connection Setup
    // -------------------------------------------------------
    $db_local = mysqli_connect($serverIP, $serverUser, $serverPass, $serverDB, $ServerPort);
    echo '<div style="width:100%;text-align:left;margin-left:10px;"><h2>'.$ServerName.' Server:</h2></div>';

    // Server Stats
    $sql = "SELECT * FROM account";
    $result = mysqli_query($db_local, $sql);
    $AccountCount = mysqli_num_rows($result);

    $sql = "SELECT * FROM player WHERE is_alive = 1";
    $result = mysqli_query($db_local, $sql);
    $LivePlayerCount = mysqli_num_rows($result);

    $sql = "SELECT * FROM territory";
    $result = mysqli_query($db_local, $sql);
    $TerritoryCount = mysqli_num_rows($result);

    $sql = "SELECT * FROM construction";
    $result = mysqli_query($db_local, $sql);
    $ConstructionCount = mysqli_num_rows($result);

    $sql = "SELECT * FROM container";
    $result = mysqli_query($db_local, $sql);
    $ContainerCount = mysqli_num_rows($result);

    $sql = "SELECT * FROM vehicle";
    $result = mysqli_query($db_local, $sql);
    $VehicleCount = mysqli_num_rows($result);


    echo '<table class="tftable" border="1">';
    echo '<tr><td>Database:</td><td align=right>' . $serverDB . '</td></tr>';
    echo '<tr><td>Accounts:</td><td align=right>' . $AccountCount . '</td></tr>';
    echo '<tr><td>Players:</td><td align=right>' . $LivePlayerCount . '</td></tr>';
    echo '<tr><td>Territories:</td><td align=right>' . $TerritoryCount . '</td></tr>';
    echo '<tr><td>Constructions:</td><td align=right>' . $ConstructionCount . '</td></tr>';
    echo '<tr><td>Containers:</td><td align=right>' . $ContainerCount . '</td></tr>';
    echo '<tr><td>Vehicles:</td><td align=right>' . $VehicleCount . '</td></tr>';
    echo '</table>';

    // Territory Info				
    echo '<div  class="expand-container" style="width:100%;text-align:left;padding-left:10px;"><h2>Territories on '.$ServerName.':</h2>';
    echo '<a class="expander" href="#" style="color:#fff;">Display '.$ServerName.' Territories</a></div>';
    
    echo '<div id="territory_'.$ServerName.'">
	<div class="content">
	<table class="tftable" border="1">
	<tr>
	<td style="width:300px;">TerritoryName</td>'
    . '<td>Coords</td>'
    . '<td>Radius</td>'
    . '<td>Level</td>'
    . '<td>Owner</td>'
    . '<td>Moderators</td>'
    . '<td style="width:200px;">Created_at</td>'
    . '<td style="width:200px;">Last_paid_at</td>'
    . '<td>BuildRights</td>'        
    . '</tr>';    
    
    $sql = "SELECT territory.name, territory.position_x, territory.position_y, territory.radius, territory.level,
            account.name as owner_name, account.uid, territory.build_rights, territory.moderators, territory.created_at, territory.last_payed_at 

            FROM territory, account 
            WHERE territory.owner_uid = account.uid 
            ORDER BY territory.name";
    $result = mysqli_query($db_local, $sql);
    
    while ($row = mysqli_fetch_object($result))
    {
        $steam64id = $row->uid;
        $territoryName = utf8_encode($row->name);
        $position_x = $row->position_x;
        $position_x = sprintf( '%05d', $position_x );
        $position_y = $row->position_y;
        $position_y = sprintf( '%05d', $position_y );
        $inGameCoords = substr($position_x, 0, 3).substr($position_y, 0, 3);
        $radius = $row->radius;
        $level = $row->level;
        $owner_name = $row->owner_name; 
       
        $moderators = $row->moderators;
        $moderators = str_replace('[', "", $moderators);
        $moderators = str_replace(']', "", $moderators);
        $moderators = str_replace('"', "", $moderators);
        $moderators = explode(",", $moderators);
        $territoryModerators = "";        
        
        foreach ($moderators as $moderator)
        {
            if($moderator <> "")
            {
                $sql2 = "SELECT name FROM account WHERE uid = '$moderator'";
                //echo "<hr>$sql2<hr>";
                $result2 = mysqli_query($db_local, $sql2);
                $row2 = mysqli_fetch_object($result2);

                $ModeratorName = $row2->name;
                $ModeratorName =  utf8_decode($ModeratorName);
				$territoryModerators .= '<a href="playersearch.php?server='.$ServerName.'&searchtype=uid&searchfield=' . $moderator . '" target=_blank>' . $ModeratorName . '</a> ';             
            }
        }        
        
        $created_at = $row->created_at;
        $last_payed_at = $row->last_payed_at;   
        
        $build_rights = $row->build_rights;
        $build_rights = str_replace('[', "", $build_rights);
        $build_rights = str_replace(']', "", $build_rights);
        $build_rights = str_replace('"', "", $build_rights);
        $buildRights = explode(",", $build_rights);
        $territoryBuilders = "";
        foreach ($buildRights as $builder)
        {
            if($builder <> "")
            {
                $sql2 = "SELECT name FROM account WHERE uid = '$builder'";
                //echo "<hr>$sql2<hr>";
                $result2 = mysqli_query($db_local, $sql2);
                $row2 = mysqli_fetch_object($result2);

                $BuilderName = $row2->name;
                $BuilderName =  utf8_decode($BuilderName);
				$territoryBuilders .= '<a href="playersearch.php?server='.$ServerName.'&searchtype=uid&searchfield=' . $builder . '" target=_blank>' . $BuilderName . '</a> ';				
            }
        }
        $territoryBuilders = rtrim($territoryBuilders);
        echo '<tr>'
        . '<td>' . $territoryName . '</td>'
        . '<td>' . $inGameCoords . '</td>'  
        . '<td>' . $radius . '</td>'
        . '<td>' . $level . '</td>'
        . '<td><a href="playersearch.php?server='.$ServerName.'&searchtype=uid&searchfield=' . $steam64id . '" target=_blank>' . $owner_name . '</a></td>'
        . '<td>' . $territoryModerators . '</td>'
        . '<td>' . $created_at . '</td>'
        . '<td>' . $last_payed_at . '</td>'
        . '<td>' . $territoryBuilders . '</td>'
        . '</tr>';
        
    }
    echo "</table></div></div>";
    
    
    // Top 100 poptabs
    echo '<div id="top100_'.$ServerName.'"><div  class="expand-container" style="width:100%;text-align:left;padding-left:10px;"><h2>Top 100 on '.$ServerName.':</h2>';
    echo '<a class="expander" href="#" style="color:#fff;">Top 100 Pop Tab owners on the '.$ServerName.' server</a></div>';

    $sql = "SELECT * FROM account ORDER BY money desc limit 100";
    $result = mysqli_query($db_local, $sql);
    echo '<div class="content"><table class="tftable" border="1">';

    echo '<tr><td>steam64id</td>'
    . '<td>name</td>'
    . '<td>score</td>'
    . '<td>money</td>'
    . '<td>kills</td>'
    . '<td>deaths</td>'
    . '<td>k/d ratio</td>'
    . '<td>First Connected</td>'
    . '<td>Last Connected</td>'
    . '<td>total_connections</td></tr>';
    while ($row = mysqli_fetch_object($result))
    {
        $steam64id = $row->uid;
        $name = $row->name;
        $score = $row->score;
        $money = $row->money;
        $kills = $row->kills;
        $deaths = $row->deaths;
        $first_connected = $row->first_connect_at;
        $last_connected = $row->last_connect_at;
        if ($kills != 0 && $deaths != 0)
        {
            $kdratio = number_format($kills / $deaths, 2);
        }
        else
        {
            $kdratio = 'n/a';
        }

        $total_connections = $row->total_connections;
        echo '<tr>
		<td><a href="playersearch.php?server='.$ServerName.'&searchtype=uid&searchfield=' . $steam64id . '" target=_blank>' . $steam64id . '</a></td>'
        . '<td>' . $name . '</td>'
        . '<td>' . $score . '</td>'
        . '<td>' . $money . '</td>'
        . '<td>' . $kills . '</td>'
        . '<td>' . $deaths . '</td>'
        . '<td>' . $kdratio . '</td>'
        . '<td>' . $first_connected . '</td>'
        . '<td>' . $last_connected . '</td>'
        . '<td>' . $total_connections . '</td></tr>';
    }
    echo "</table></div>";

}

function clean_input($input)
{
    $html_entities_match = array('#&(?!(\#[0-9]+;))#', '#<#', '#>#', '#"#');
    $html_entities_replace = array('&amp;', '&lt;', '&gt;', '&quot;');


    $badchr = array(
        "\\xe2\\x80\\xa6", // ellipsis
        "\\xe2\\x80\\x93", // long dash
        "\\xe2\\x80\\x94", // long dash
        "\\xe2\\x80\\x98", // single quote opening
        "\\xe2\\x80\\x99", // single quote closing
        "\\xe2\\x80\\x9c", // double quote opening
        "\\xe2\\x80\\x9d", // double quote closing
        "\\xe2\\x80\\xa2"        // dot used for bullet points
    );

    $goodchr = array(
        '...',
        '-',
        '-',
        '\'',
        '\'',
        '"',
        '"',
        '*'
    );
    $input = str_replace($badchr, $goodchr, $input);
    $input = preg_replace($html_entities_match, $html_entities_replace, $input);
    $input = addslashes($input);
    return $input;
}
?>
