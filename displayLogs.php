<?php
date_default_timezone_set('Europe/London');
$PageTitle = "Display Logs:";
$path = dirname($_SERVER['PHP_SELF']);
include 'includes/header.php';
include 'includes/config.php';
include 'includes/functions.php';

$serverToSearch     = "";
$logsToSearch       = "";
$searchterm         = "";
$limit              = 500;



echo '<form method="post" action="displayLogs.php" name="myform">';
echo '    <p style="width:100%;padding-top:5px;padding-bottom:5px">';
echo '        <b>Define Search:</b><br>';
echo '        <select name="server">';

foreach ($ServerList as $ServerToCheck)
{
	$ServerDetails = explode("|", $ServerToCheck);
	$servername = ucwords($ServerDetails[4]);
        echo '<option value="'.$servername.'">'.$servername.'</option>';    
}
echo '<option value="" selected="selected">All Servers</option>';

echo '  </select>
        <select name="searchtype">
            <option value="all">All Logs</option>  
            <option value="dodgy" selected="selected">Dodgy</option>         
            <option value="performance">Performance</option>
            <option value="traders">Traders</option>
        </select>
        <select name="limit">
            <option value="500" selected="selected">Last 500 Records</option>  
            <option value="1500">Last 1500 Records</option>         
            <option value="5000">Last 5000 Records</option>
            <option value="10000">Last 10000 Records</option>
        </select>        
        <input type="text" name="searchterm" size=45 id="textfield"></input>
        <input type="submit">
        <a href="displayLogs.php">Reset Form</a>
    </p>
</form>';


if(isset($_POST['limit']))
{
  $limit   = $_POST['limit'];
}

if(isset($_POST['server']))
{
  $serverToSearch   = $_POST['server'];
}

if(isset($_POST['searchterm']))
{
  $searchterm  = strtolower($_POST['searchterm']);
}

if(isset($_POST['searchtype']))
{
  $searchtype  = $_POST['searchtype'];
  
  if($searchtype == 'dodgy')
  {
      $logsToSearch = " WHERE logname LIKE '%DUPE%' OR logname LIKE '%KICK%' OR logname LIKE '%BAN%' OR logname LIKE '%SURVEILLANCE%' OR logname LIKE '%GOD%' AND logentry like '%$searchterm%' ";
  }
  elseif($searchtype == 'traders')
  {
      $logsToSearch = " WHERE logname LIKE '%RECYCLE%' OR logname LIKE '%TRADER%' AND logentry like '%$searchterm%' ";
  }
  elseif($searchtype == 'performance')
  {
      $logsToSearch = " WHERE logname LIKE '%PROCESSREPORTER%' OR logname LIKE '%TRADER%' AND logentry like '%$searchterm%' ";
  } 
  elseif($searchtype == 'all')
  {
      $logsToSearch = " WHERE logname NOT LIKE '%PROCESSREPORTER%' AND logentry like '%$searchterm%' ";
  }   
  else
  {
      $logsToSearch = " WHERE logname NOT LIKE '%PROCESSREPORTER%' AND logentry like '%$searchterm%' ";
  }
}
else
{
      $logsToSearch = " WHERE logname LIKE '%DUPE%' OR logname LIKE '%KICK%' OR logname LIKE '%BAN%' OR logname LIKE '%SURVEILLANCE%' OR logname LIKE '%GOD%' AND logentry like '%$searchterm%' ";
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
	$db_local = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);

        if(isset($serverToSearch) && $serverToSearch != '')
        {
            if(strtolower($servername) == strtolower($serverToSearch))
            {
                $sql = "SELECT * 
                        FROM infistar_logs 
                        $logsToSearch 
                        ORDER BY time 
                        ASC 
                        LIMIT $limit";
                //echo "<hr>$sql<hr>";
                $result = mysqli_query($db_local, $sql);

                if (mysqli_num_rows($result) > 0)
                {
                    echo "<hr><h2>infiSTAR Logs on the $servername server</h2><hr>";
                    if(isset($searchterm) && $searchterm != '')
                    {
                        echo "Searching for: $searchterm<hr>";
                    }
                    echo '<table class="tftable" border="1"">
                                    <tr>
                                            <td style="width:150px;">logname</td>
                                            <td style="width:800px;">logentry</td>
                                            <td style="width:150px;">time</td>            
                                    </tr>';
                    while ($row = mysqli_fetch_object($result))
                    {
                        $logname    = $row->logname;
                        $logentry   = $row->logentry;
                        $logentry   = wordwrap($logentry,200,"<br>\n",true);
                        $time       = $row->time;

                        echo '<tr>'
                        . '<td valign=top style="width:150px;">' . $logname . '</td>'
                        . '<td valign=top>' . $logentry . '</td>'
                        . '<td valign=top  style="width:150px;">' . $time . '</td>'
                        . '</tr>';
                    }
                    echo "</table>";
                }
                else
                {
                        echo "<hr><h2>There is no log history</h2><hr>";
                }                     
            }
                   
        }
        else
        {
            $sql = "SELECT * 
                    FROM infistar_logs 
                    $logsToSearch
                    ORDER BY time ASC 
                    LIMIT $limit";
            //echo "<hr>$sql<hr>";
            $result = mysqli_query($db_local, $sql);

            if (mysqli_num_rows($result) > 0)
            {
                echo "<hr><h2>infiSTAR Logs on the $servername server</h2><hr>";
                if(isset($searchterm) && $searchterm != '')
                {
                    echo "Searching for: $searchterm<hr>";
                }
                echo '<table class="tftable" border="1"">
                                <tr>
                                        <td style="width:150px;">logname</td>
                                        <td style="width:800px;">logentry</td>
                                        <td style="width:150px;">time</td>            
                                </tr>';
                while ($row = mysqli_fetch_object($result))
                {
                    $logname    = $row->logname;
                    $logentry   = $row->logentry;
                    $logentry   = wordwrap($logentry,200,"<br>\n",true);
                    $time       = $row->time;

                    echo '<tr>'
                    . '<td valign=top style="width:150px;">' . $logname . '</td>'
                    . '<td valign=top>' . $logentry . '</td>'
                    . '<td valign=top style="width:150px;">' . $time . '</td>'
                    . '</tr>';
                }
                echo "</table>";
            }
            else
            {
                    echo "<hr><h2>There is no log history for $servername yet</h2><hr>";
            }             
        }

	
	
}



?>