<?php
date_default_timezone_set('Europe/London');
$PageTitle = "Display Logs:";
$path = dirname($_SERVER['PHP_SELF']);
include 'includes/header.php';
include 'includes/config.php';
include 'includes/functions.php';

$sql = "SELECT * FROM infistar_logs ORDER BY time DESC LIMIT 1000";
$result = mysqli_query($db_local, $sql);

if (mysqli_num_rows($result) > 0)
{
    echo "<hr><h2>infiSTAR Logs</h2><hr>";
    echo '<table class="tftable" border="1"">
            <tr>
                <td style="width:150px;">logname</td>
                <td style="width:450px;">logentry</td>
                <td style="width:80px;">time</td>            
            </tr>';
}
else
{
    echo "<hr><h2>There is no log history</h2><hr>";
}

while ($row = mysqli_fetch_object($result))
{
    $logname    = $row->logname;
    $logentry   = $row->logentry;
    $time       = $row->time;

    echo '<tr>'
    . '<td valign=top>' . $logname . '</td>'
    . '<td valign=top>' . $logentry . '</td>'
    . '<td valign=top>' . $time . '</td>'
    . '</tr>';
}
echo "</table>";
?>
