<?php

	include 'includes/config.php';
	include 'includes/functions.php';

	$uid = $_GET['uid'];
	$Server = $_GET['server'];

	
	// Get the connection details for the selected server    
	foreach ($ServerList as $ServerToCheck)
    {
        $ServerDetails = explode("|", $ServerToCheck);
        $dbhost = $ServerDetails[0];
        $dbname = $ServerDetails[1];
        $dbuser = $ServerDetails[2];
        $dbpass = $ServerDetails[3];
        $servername = ucwords($ServerDetails[4]);

        if (strtolower($servername) == strtolower($Server))
        {
            $db_local = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        }
    }
	
	
	
	$sql = "SELECT * FROM account WHERE uid = '$uid'";
	$result = mysqli_query($db_local, $sql);
	$row = mysqli_fetch_object($result);
	
	if(mysqli_num_rows($result) <> 0) // Found a record
	{
		// Export the data	
		
		$PlayerName = $row->name;		

		// Export the territories
		$sql2 = "SELECT * FROM territory WHERE owner_uid = '$uid'";
		$result2 = mysqli_query($db_local, $sql2);
		if(mysqli_num_rows($result) <> 0) // Found a record
		{		
			$Records = "INSERT INTO territory (owner_uid, name, position_x, position_y, position_z, radius, level, flag_texture, flag_stolen, flag_stolen_by_uid, flag_stolen_at, flag_steal_message, created_at, last_payed_at, build_rights, moderators) VALUES ";
		
			while($row2 = mysqli_fetch_object($result2))
			{
				$owner_uid = $row2->owner_uid;
				$name = $row2->name;
				$position_x = $row2->position_x;
				$position_y = $row2->position_y;
				$position_z = $row2->position_z;
				$radius = $row2->radius;
				$level = $row2->level;
				$flag_texture = $row2->flag_texture;
				$flag_stolen = $row2->flag_stolen;
				$flag_stolen_by_uid = $row2->flag_stolen_by_uid;
				if($row2->flag_stolen_at == '')
				{
					$flag_stolen_at = 'NULL';
				}
				else
				{
					$flag_stolen_at = $row2->flag_stolen_at;
				}
				
				$flag_steal_message = $row2->flag_steal_message;
				$created_at = $row2->created_at;
				$last_payed_at = $row2->last_payed_at;
				$build_rights = $row2->build_rights;
				$moderators = $row2->moderators;				
				
				$territoryX = $position_x;
				$territoryY = $position_y;
				$territoryRadius = $radius;
				
				$Records .= " ('$owner_uid', '$name', $position_x, $position_y, $position_z, $radius, $level, '$flag_texture', $flag_stolen, '$flag_stolen_by_uid', $flag_stolen_at, '$flag_steal_message', '$created_at', '$last_payed_at', '$build_rights', '$moderators'),";
			}
			$Records = rtrim($Records, ",");
			$Records .= ";<br><br>";
			echo $Records;
		}
		else
		{
			echo "<br>Selected player has no construction records to export.";
		}		
		
		// Export the constructions
		$sql2 = "SELECT * FROM construction";
		$result2 = mysqli_query($db_local, $sql2);
		if(mysqli_num_rows($result) <> 0) // Found a record
		{		
			$Records = "INSERT INTO construction (class, account_uid, spawned_at, maintained_at, position_x, position_y, position_z, direction_x, direction_y, direction_z, up_x, up_y, up_z, is_locked, pin_code) VALUES ";
		
			while($row2 = mysqli_fetch_object($result2))
			{
				$class = $row2->class;
				$account_uid = $row2->account_uid;
				$spawned_at = $row2->spawned_at;
				$maintained_at = $row2->maintained_at;
				$position_x = $row2->position_x;
				$position_y = $row2->position_y;
				$position_z = $row2->position_z;
				$direction_x = $row2->direction_x;
				$direction_y = $row2->direction_y;
				$direction_z = $row2->direction_z;
				$up_x = $row2->up_x;
				$up_y = $row2->up_y;
				$up_z = $row2->up_z;
				$is_locked = $row2->is_locked;
				$pin_code = $row2->pin_code;
				
				if ((($position_x-$territoryX)**2 + ($position_y-$territoryY)**2 <= $territoryRadius**2))
				{
					$Records .= " ('$class', '$account_uid', '$spawned_at', '$maintained_at', $position_x, $position_y, $position_z, $direction_x, $direction_y, $direction_z, $up_x, $up_y, $up_z, $is_locked, '$pin_code'),";
				}				
				
				
			}
			$Records = rtrim($Records, ",");
			$Records .= ";";
			echo $Records;
			
			




		}
		else
		{
			echo "<br>Selected player has no construction records to export.";
		}
	}
	else  // Didn't find a record
	{
		if($uid <> '')
		{
			echo "<hr>Invalid uid ($uid) or player does not exist in the database<hr>";	
		}
		else
		{
			echo "<hr>No uid supplied. Try again!<hr>";
		}			
	}
	
	
?>
