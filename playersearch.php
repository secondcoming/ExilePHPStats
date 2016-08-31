<?php
date_default_timezone_set('Europe/London');
$PageTitle = "Exile Player Search:";
$path = dirname($_SERVER['PHP_SELF']);
include 'includes/header.php';
include 'includes/config.php';
include 'includes/functions.php';

$ServerOptions = "";

foreach ($ServerList as $ServerToCheck)
{
    $ServerDetails = explode("|", $ServerToCheck);
    $servername = $ServerDetails[4];

    $ServerOptions .= "<option value=\"$servername\" selected>$servername</option>\n";
}

if ((!isset($_POST['searchfield']) || $_POST['searchfield'] == '') && !isset($_GET['searchfield']))
{
    echo '<div>
    <div>

        <h2>Player Search:</h2>
     
        <form method="post" action="playersearch.php" name="myform">
            
            <p style="width:100%;padding-top:5px;padding-bottom:5px">
                <b>Search By:</b><br>
				<select name="server">';
    echo $ServerOptions;
    echo '
                </select>
                <select name="searchtype">                 
                    <option value="name">Name</option>
                    <option value="uid" selected>SteamID64</option>
                </select>
                <input type="text" name="searchfield" size=45 id="textfield"></input>
                <input type="hidden" name="submitok" value="true">
                <input type="submit">
                <a href="' . $path . '/playersearch.php" style="color:#fff;">Reset</a>
            </p>
        </form>

    </div>
</div>

<script language="Javascript"  type="text/javascript"><!--
    document.myform.searchfield.focus();
    //-->
</script>';
}
else
{
    echo '<div class="content">
    <div id="login" class="remove" style="padding:30px;padding-left:110px;padding-right:120px">

        <h2>Player Search:</h2>
     
        <form method="post" action="playersearch.php" name="myform">
            
            <p style="width:100%;padding-top:5px;padding-bottom:5px">
                <b>Search By:</b><br>
				<select name="server">';
    echo $ServerOptions;
    echo '
                </select>
                <select name="searchtype">                 
                  <option value="name" selected>Name</option>
				  <option value="uid">Steam64id</option>
                </select>
                <input type="text" name="searchfield" size=45 id="textfield"></input>
                <input type="hidden" name="submitok" value="true">
                <input type="submit">
                <a href="/exilestats/playersearch.php" style="color:#fff;">Reset</a>
            </p>
        </form>

    </div>
</div>

<script language="Javascript"  type="text/javascript"><!--
    document.myform.searchfield.focus();
    //-->
</script>';

    if (isset($_GET['searchfield']))
    {
        $Server = strtolower($_GET['server']);
        $Searchfield = $_GET['searchfield'];
        $SearchType = $_GET['searchtype'];
    }
    else
    {
        $Server = $_POST['server'];
        $Searchfield = $_POST['searchfield'];
        $SearchType = $_POST['searchtype'];
    }


    if ($SearchType == 'name')
    {
        $Searchfield = strtolower($Searchfield);
        $sql = "SELECT * FROM account WHERE LOWER(account.name) LIKE '%$Searchfield%' ORDER BY name";
    }
    elseif ($SearchType == 'uid')
    {
        $Searchfield = strtolower($Searchfield);
        $Searchfield = str_replace(' ', '', $Searchfield);
        $sql = "SELECT * FROM account WHERE LOWER(uid) LIKE '%$Searchfield%'";
    }
    else
    {
        $sql = "SELECT * FROM account WHERE $SearchType = '$Searchfield'";
    }

    foreach ($ServerList as $ServerToCheck)
    {
        $ServerDetails = explode("|", $ServerToCheck);
        $dbhost = $ServerDetails[0];
        $dbname = $ServerDetails[1];
        $dbuser = $ServerDetails[2];
        $dbpass = $ServerDetails[3];
        $servername = ucwords($ServerDetails[4]);
        $dbport = $ServerDetails[5];

        if (strtolower($servername) == strtolower($Server))
        {
            $db_local = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
        }
    }

    $result = mysqli_query($db_local, $sql);
    $align1 = ' align=left style="padding:8px;font-size:11px;" ';

    echo '	<hr><h2>Results for: ' . $SearchType . ' = "' . $Searchfield . '" on Server: ' . ucwords($Server) . '</h2><hr>';


    while ($row = mysqli_fetch_object($result))
    {
        if (isset($row->uid) && $row->uid <> '')
        {
            echo "<table cellspacing=1 width=100%><tr>
                            <td width=150 $align1>steamID64</td>
                            <td width=150 $align1>guid</td>
                            <td width=250 $align1>name</td>
                            <td width=75 $align1>pop&nbsp;tabs</td>
                            <td width=75 $align1>Respect</td>
                            <td width=75 $align1>Kills</td>
                            <td width=75 $align1>Deaths</td>
                            <td width=120 $align1>First&nbsp;Connected</td>
                            <td width=120 $align1>Last&nbsp;Connected</td>
                            <td width=120 $align1>Last&nbsp;Disconnect</td>
                            <td width=120 $align1>Connections</td>
                            <td width=85 $align1></td>
                    </tr>";
            // Display Account
            $uid = $row->uid;
            $steamID64 = '<a href="http://steamcommunity.com/profiles/' . $uid . '" target=_blank>' . $uid . '</a> ';
            $guid = getGUID($uid);
            $name = $row->name;
            $poptabs = $row->locker;
            $respect = $row->score;
            $kills = $row->kills;
            $deaths = $row->deaths;
            $clan = $row->clan_id;
            $first_connect_at = $row->first_connect_at;
            $last_connect_at = $row->last_connect_at;

            if (isset($row->last_disconnect_at))
            {
                $last_disconnect_at = $row->last_disconnect_at;
            }
            else
            {
                $last_disconnect_at = '0000-00-00 00:00:00';
            }

            $total_connections = $row->total_connections;
			
			// does the account have a currently alive character
			$sql4 = "SELECT * FROM player WHERE account_uid = '$uid'";
			$result4 = mysqli_query($db_local, $sql4);

			if (mysqli_num_rows($result4) > 0)
                        {
				$options = "<a href=\"playeredit.php?action=edit&uid=$uid&server=$Server\"><img src=\"images/edit.png\" title=\"edit pop tabs and respect\"></a> <a href=\"playeredit.php?action=delete&uid=$uid&server=$Server\"><img src=\"images/delete.png\" title=\"delete the character\"></a>";
			}
			else
			{
				$options = "<a href=\"playeredit.php?action=edit&uid=$uid&server=$Server\"><img src=\"images/edit.png\" title=\"edit pop tabs and respect\"></a>";
			}

            echo "<tr style=\"background-color:#000;\">
			<td $align1>$steamID64</td>
                        <td $align1>$guid</td>
			<td $align1>$name</td>
			<td $align1>$poptabs</td>
			<td $align1>$respect</td>
			<td $align1>$kills</td>
			<td $align1>$deaths</td>
			<td $align1>$first_connect_at</td>
			<td $align1>$last_connect_at</td>
			<td $align1>$last_disconnect_at</td>
			<td $align1>$total_connections</td>
			<td style=\"width:200px;text-align:left;padding:8px;\">$options</td>
			</tr></table>";

            


            if($clan <> '')
            {
                $sql3 = "SELECT clan.name as clan_name, clan.leader_uid, clan.created_at, account.name as player_name, account.uid,
                        account.locker, account.score, account.last_connect_at
                        FROM account,clan WHERE account.clan_id = '$clan' AND account.clan_id = clan.id ORDER BY account.name";
                $result3 = mysqli_query($db_local, $sql3);
                
                echo "<hr><h2>Clan Details for $name</h2><hr>";
                echo "<table cellspacing=1 width=100%>
                        <tr>
                            <td width=200 $align1>Clan</td>
                            <td width=250 $align1>Player</td>
                            <td width=250 $align1>Steam Profile</td>
                            <td width=250 $align1>Poptabs</td>
                            <td width=75 $align1>Respect</td>
                            <td width=120 $align1>Last Logged On</td>
                        </tr>";
                while ($row3 = mysqli_fetch_object($result3))
                {                  
                    $clanName       = $row3->clan_name;
                    $playerName     = $row3->player_name;
                    $playerUID      = $row3->uid;
                    $playerLastOn   = $row3->last_connect_at;
                    $steamID64      = '<a href="http://steamcommunity.com/profiles/' . $playerUID . '" target=_blank>'.$playerUID.'</a> ';
                    $playerLink     = '<a href="playersearch.php?server='.$Server.'&searchtype=uid&searchfield=' . $playerUID . '">'.$playerName.'</a>';
                    $playerPoptabs  = $row3->locker;
                    $playerRespect  = $row3->score;

                    echo "<tr style=\"background-color:#000;\">
                            <td $align1>$clanName</td>
                            <td $align1>$playerLink</td>
                            <td $align1>$steamID64</td>
                            <td $align1>$playerPoptabs</td>
                            <td $align1>$playerRespect</td>
                            <td $align1>$playerLastOn</td>
                        </tr>";

                    
                }
                echo "</table>";
            }


            // Display associated territories	
            $sql3 = "SELECT territory.name, territory.position_x, territory.position_y, territory.radius, territory.level,
					account.name as owner_name, account.uid, territory.build_rights, territory.moderators, territory.created_at, territory.last_paid_at 

					FROM territory, account 
					WHERE (territory.owner_uid = account.uid OR territory.build_rights LIKE '%$uid%' OR territory.moderators LIKE '%$uid%')
					AND account.uid = '$uid'
					ORDER BY territory.name";
            $result3 = mysqli_query($db_local, $sql3);


            if (mysqli_num_rows($result3) > 0)
            {
                echo "<hr><h2>Territories for $name</h2><hr><a href=\"export.php?uid=$uid&server=$Server\" target=_blank>Export Player Territories and Constructions</a><hr>";
                echo '
				<table class="tftable">
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
            }
            else
            {
                //echo "<hr><h2>This player has no territories</h2><hr>";
            }


            while ($row3 = mysqli_fetch_object($result3))
            {
                $steam64id = $row3->uid;
                $territoryName = $row3->name;
                $position_x = $row3->position_x;
                $position_x = sprintf('%05d', $position_x);
                $position_y = $row3->position_y;
                $position_y = sprintf('%05d', $position_y);
                $inGameCoords = substr($position_x, 0, 3) . substr($position_y, 0, 3);
                $radius = $row3->radius;
                $level = $row3->level;
                $owner_name = $row3->owner_name;

                $moderators = $row3->moderators;
                $moderators = str_replace('[', "", $moderators);
                $moderators = str_replace(']', "", $moderators);
                $moderators = str_replace('"', "", $moderators);
                $moderators = explode(",", $moderators);
                $territoryModerators = "";

                foreach ($moderators as $moderator)
                {
                    if ($moderator <> "")
                    {
                        $sql4 = "SELECT name FROM account WHERE uid = '$moderator'";
                        //echo "<hr>$sql2<hr>";
                        $result4 = mysqli_query($db_local, $sql4);
                        $row4 = mysqli_fetch_object($result4);

                        $ModeratorName = $row4->name;
                        $ModeratorName = html_entity_decode(utf8_decode($ModeratorName));
                        $territoryModerators .= '<a href="http://steamcommunity.com/profiles/' . $moderator . '" target=_blank>' . $ModeratorName . '</a> ';
                    }
                }

                $created_at = $row3->created_at;
                $last_paid_at = $row3->last_paid_at;

                $build_rights = $row3->build_rights;
                $build_rights = str_replace('[', "", $build_rights);
                $build_rights = str_replace(']', "", $build_rights);
                $build_rights = str_replace('"', "", $build_rights);
                $buildRights = explode(",", $build_rights);
                $territoryBuilders = "";
                foreach ($buildRights as $builder)
                {
                    if ($builder <> "")
                    {
                        $sql4 = "SELECT name FROM account WHERE uid = '$builder'";
                        //echo "<hr>$sql4<hr>";
                        $result4 = mysqli_query($db_local, $sql4);
                        $row4 = mysqli_fetch_object($result4);

                        $BuilderName = $row4->name;
                        $BuilderName = html_entity_decode(utf8_decode($BuilderName));
                        $territoryBuilders .= '<a href="playersearch.php?server=' . $Server . '&searchtype=uid&searchfield=' . $builder . '" target=_blank>' . $BuilderName . '</a> ';
                    }
                }
                $territoryBuilders = rtrim($territoryBuilders);
                echo '<tr>'
                . '<td>' . $territoryName . '</td>'
                . '<td>' . $inGameCoords . '</td>'
                . '<td>' . $radius . '</td>'
                . '<td>' . $level . '</td>'
                . '<td><a href="http://steamcommunity.com/profiles/' . $steam64id . '" target=_blank>' . $owner_name . '</a></td>'
                . '<td>' . $territoryModerators . '</td>'
                . '<td>' . $created_at . '</td>'
                . '<td>' . $last_paid_at . '</td>'
                . '<td>' . $territoryBuilders . '</td>'
                . '</tr>';
            }
            echo "</table>";



            // Display Containers
            $sql2 = "SELECT * FROM container WHERE account_uid = '$uid'";
            $result2 = mysqli_query($db_local, $sql2);

            if (mysqli_num_rows($result2) > 0)
            {
                echo "<hr><h2>Containers owned by $name</h2><hr>";
                echo '
				<table class="tftable" border="1"">
				<tr>
				<td style="width:300px;">Container</td>'
                . '<td>Coords</td>'
                . '<td>PIN</td>'
                . '<td style="width:600px;">Contents</td>'
                . '<td style="width:250px;">Spawned at</td>'
                . '<td style="width:250px;">Last Used</td>'
                . '</tr>';
            }
            else
            {
                //echo "<hr><h2>This player has no containers</h2><hr>";
            }

            while ($row2 = mysqli_fetch_object($result2))
            {
                $vehicle = $row2->class;
                $position_x = $row2->position_x;
                $position_x = sprintf('%05d', $position_x);
                $position_y = $row2->position_y;
                $position_y = sprintf('%05d', $position_y);
                $inGameCoords = substr($position_x, 0, 3) . substr($position_y, 0, 3);
                $pin_code = $row2->pin_code;
                $spawned_at = $row2->spawned_at;
                $last_updated_at = $row2->last_updated_at;
                $contents = "Items: " . $row2->cargo_items . "<hr>";
                $contents .= "Magazines: " . $row2->cargo_magazines . "<hr>";
                $contents .= "Weapons: " . $row2->cargo_weapons . "<hr>";
                echo '<tr>'
                . '<td valign=top>' . $vehicle . '</td>'
                . '<td valign=top>' . $inGameCoords . '</td>'
                . '<td valign=top>' . $pin_code . '</td>'
                . '<td valign=top style="width:600px;">' . $contents . '</td>'
                . '<td valign=top>' . $spawned_at . '</td>'
                . '<td valign=top>' . $last_updated_at . '</td>'
                . '</tr>';
            }
            echo "</table>";



            // Display Vehicles
            $sql2 = "SELECT * FROM vehicle WHERE account_uid = '$uid'";
            $result2 = mysqli_query($db_local, $sql2);

            if (mysqli_num_rows($result2) > 0)
            {
                echo "<hr><h2>Vehicles owned by $name</h2><hr>";
                echo '
				<table class="tftable" border="1"">
				<tr>
				<td style="width:300px;">Vehicle</td>'
                . '<td>Coords</td>'
                . '<td>PIN</td>'
                . '<td style="width:600px;">Contents</td>'
                . '<td style="width:250px;">Spawned at</td>'
                . '<td style="width:250px;">Last Used</td>'
                . '</tr>';
            }
            else
            {
                //echo "<hr><h2>This player has no vehicles</h2><hr>";
            }

            while ($row2 = mysqli_fetch_object($result2))
            {
                $vehicle = $row2->class;
                $position_x = $row2->position_x;
                $position_x = sprintf('%05d', $position_x);
                $position_y = $row2->position_y;
                $position_y = sprintf('%05d', $position_y);
                $inGameCoords = substr($position_x, 0, 3) . substr($position_y, 0, 3);
                $pin_code = $row2->pin_code;
                $spawned_at = $row2->spawned_at;
                if (!isset($row2->last_updated_at))
                {
                    $last_updated = "n/a";
                }
                else
                {
                    $last_updated = $row2->last_updated_at;
                }
                $contents = "Items: " . $row2->cargo_items . "<hr>";
                $contents .= "Magazines: " . $row2->cargo_magazines . "<hr>";
                $contents .= "Weapons: " . $row2->cargo_weapons . "<hr>";
                echo '<tr>'
                . '<td valign=top>' . $vehicle . '</td>'
                . '<td valign=top>' . $inGameCoords . '</td>'
                . '<td valign=top>' . $pin_code . '</td>'
                . '<td valign=top style="width:600px;">' . $contents . '</td>'
                . '<td valign=top>' . $spawned_at . '</td>'
                . '<td valign=top>' . $last_updated . '</td>'
                . '</tr>';
            }
            echo "</table>";
            
            
            // Display trader history
            $sql3 = "SELECT * FROM trader_log WHERE steamid64 = '$uid' AND time_sold > NOW() - INTERVAL 30 DAY
                    ORDER BY time_sold DESC";
            $result3 = mysqli_query($db_traders, $sql3);

            if (mysqli_num_rows($result3) > 0)
            {
                echo "<hr><h2>Items sold by $name (last 30 days)</h2><hr>";
                echo '<table class="tftable" border="1"">
		<tr>
		<td style="width:150px;">When</td>
                <td style="width:150px;">Server</td>
                <td>Items</td>
                <td style="width:50px;">Poptabs</td>
                <td style="width:50px;">Respect</td>
                </tr>';
            }
            else
            {
                echo "<hr><h2>This player has no trader history</h2><hr>";
            }

            while ($row3 = mysqli_fetch_object($result3))
            {
                $time_sold = $row3->time_sold;
                $server = $row3->servername;
                $items_sold = str_replace('","','", "',$row3->items_sold);
                $poptabs = $row3->poptabs;
                $respect = $row3->respect;
                echo '<tr>'
                . '<td valign=top style="width:150px;">' . $time_sold . '</td>'
                . '<td valign=top style="width:150px;">' . $server . '</td>'        
                . '<td valign=top>' . $items_sold . '</td>'
                . '<td valign=top align=right style="width:50px;">' . $poptabs . '</td>'
                . '<td valign=top align=right style="width:50px;">' . $respect . '</td>'
                . '</tr>';
            }
            echo "</table>";            
            
            
            
            
            
        }
        else
        {
            echo "<hr><h1>Invalid. reset the form to continue</h1>$sql<hr>";
        }
    }
}

include 'includes/footer.php';
?>
