<?php

date_default_timezone_set('Europe/London');
setlocale(LC_ALL, array('en_GB.UTF8', 'en_GB@euro', 'en_GB', 'english'));

ini_set('memory_limit', '1024M');
include 'includes/config.php';


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

    $servername = strtolower($servername);
    $sql = "SELECT MAX(time_sold) as last_update FROM trader_log WHERE servername = '$servername'";

    // Get the date of the last record in the database
    $result = mysqli_query($db_traders, $sql);
    $row = mysqli_fetch_object($result);

    if(isset($row->last_update) && $row->last_update <> '')
    {
        $LastUpdated = $row->last_update;
    }
    else
    {
        $LastUpdated = "2015-01-01 00:00:01";
    }
    

    $sql2 = "SELECT * FROM infistar_logs WHERE logname LIKE '%trader%' AND time > '$LastUpdated'";
    $result2 = mysqli_query($db_local, $sql2);

    // Import the Trader Logs
    
    while ($row2 = mysqli_fetch_object($result2))
    {
        $data = $row2->logentry;
        $data = str_replace("'", "", $data);
        $data = str_replace("=", "|", $data);
        //echo "<hr>$data<hr>";
        
        //get row data
        $row_data = explode('|', $data);

        // when
        $timestamp = $row2->time;

        // who
        $steamid64 = $row_data[1];        
        $steamid64 = str_replace(" ", "", $steamid64);
        $steamid64 = str_replace("(", "", $steamid64);
        $steamid64 = substr($steamid64,0,17);
        

        // total amount
        $PoptabTotal = substr($row_data[2], 20, 8);

        // get item
        $string = explode(" FOR ", $row_data[3]);
        

        // what
        $ClassName = str_replace(" ", "", $string[0]);


        $Reward = explode(" POPTABS AND ", $string[1]);
        // Poptabs Gained
        $Poptabs = $Reward[0];

        // Repect Gained
        $Reward = explode(" RESPECT", $Reward[1]);
        $Respect = $Reward[0];

        $sql = "SELECT * FROM trader_log WHERE time_sold = '$timestamp' AND steamid64 = '$steamid64'";
        //echo "$sql<br>";
        $result = mysqli_query($db_traders, $sql);
        $num_rows = mysqli_num_rows($result);

        if ($num_rows == 0)
        {
            $sql2 = "INSERT INTO trader_log 
                    (time_sold, steamid64, items_sold, poptabs, respect, servername)
                    VALUES('$timestamp','$steamid64','$ClassName','$Poptabs','$Respect','$servername')";
            //echo "$sql2<br>";
            $result2 = mysqli_query($db_traders, $sql2);
        }

    }
    
    
    
    // Import the Recycle Log
    $sql2 = "SELECT * FROM infistar_logs WHERE logname LIKE '%recycle%' AND time > '$LastUpdated'";
    $result2 = mysqli_query($db_local, $sql2);

    while ($row2 = mysqli_fetch_object($result2))
    {
        $data = $row2->logentry;
        
        $data = str_replace("'", "", $data); 
        $data = str_replace("=", "|", $data);
        //echo "<hr>$data<hr>";

        //echo "<hr><b>IMPORTING: $data</b><hr>";
        //get row data
        $row_data = explode('|', $data);


        // when
        $timestamp = $row2->time;

        if ($timestamp > $LastUpdated)
        {

            // who
            $steamid64 = $row_data[3];        
            $steamid64 = str_replace(" ", "", $steamid64);
            $steamid64 = str_replace("(", "", $steamid64);
            $steamid64 = substr($steamid64,0,17);
            
            $Reward = explode(" POPTABS AND ", substr($row_data[3],18));
            // Poptabs Gained
            $Poptabs = $Reward[0];

            // Repect Gained
            $Reward = explode(" RESPECT", $Reward[1]);
            $Respect = $Reward[0];            
            
            
            // Get a list of items
            $string = explode(" REMOTE WITH [", $data);
            $ItemList = $string[1];
            $Items = explode("] CARGO FOR", $ItemList);
            //echo "Items: $Items[0]<br>";
            $ClassName = '';
            $ClassName = $Items[0];
            $SoldItems = explode('"', $Items[0]);

            foreach ($SoldItems as $SoldItem)
            {
                if ($SoldItem <> ',' && $SoldItem <> '')
                {
                    if (strpos($SoldItem, '] CARGO') !== false)
                    {
                            $SoldItem = 'Vehicle';
                    }    

                    $sql2 = "INSERT INTO trader_log 
                            (time_sold, steamid64, items_sold, poptabs, respect, servername)
                            VALUES('$timestamp','$steamid64','$SoldItem','$Poptabs','$Respect','$servername')";
                    //echo "$sql2<br>";
                    $result2 = mysqli_query($db_traders, $sql2);
                }
            }
        }

    }
}
echo "<h1>All Done!</h1>";
