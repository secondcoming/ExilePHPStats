<?php
date_default_timezone_set('Europe/London');
$PageTitle = "Exile Dupe Search:";
$path = dirname($_SERVER['PHP_SELF']);
include 'includes/header.php';
include 'includes/config.php';
include 'includes/functions.php';




// Display trader history

$sql3 = "   SELECT steamid64, items_sold, COUNT(*) as amount, servername
            FROM trader_log
            WHERE time_sold > NOW() - INTERVAL 1 DAY
            AND steamid64 <> ''
            GROUP BY steamid64,items_sold
            ORDER BY COUNT(*) DESC";
$result3 = mysqli_query($db_traders, $sql3);

if (mysqli_num_rows($result3) > 0)
{
    echo "<hr><h2>POTENTIAL DUPERS - All Items sold (last 24 hours)</h2><hr>";
    echo '<table class="tftable" border="1"">
            <tr>
                <td style="width:250px;">Player</td>
                <td style="width:150px;">Server</td>
                <td>Items</td>               
                <td  align=right style="width:150px;">Amount</td>             
            </tr>';
}
else
{
    echo "<hr><h2>There is no trader history</h2><hr>";
}

while ($row3 = mysqli_fetch_object($result3))
{
    $steamid64 = $row3->steamid64;
    $items_sold = str_replace('","', '", "', $row3->items_sold);
    $amount = $row3->amount;
    $servername = $row3->servername;
    $isBanned = isBanned($steamid64, $servername, $ServerList);
    if($isBanned == 'false' && $amount >= 8)
    {
        // get the players name
        $playerName = getPlayersName($steamid64, $servername, $ServerList);
        echo '<tr>'
        . '<td valign=top style="width:250px;"><a href="playersearch.php?server='.$servername.'&searchtype=uid&searchfield=' . $steamid64 . '">'.$playerName.'</a></td>'
        . '<td valign=top>' . ucwords($servername) . '</td>'        
        . '<td valign=top>' . $items_sold . '</td>'
        . '<td valign=top align=right style="width:50px;">' . $amount . '</td>'
        . '</tr>';        
    }

}
echo "</table>";
?>