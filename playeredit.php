<?php

$PageTitle = "Exile Player Edit:";
$path = dirname($_SERVER['PHP_SELF']);
include 'includes/header.php';
include 'includes/config.php';
include 'includes/functions.php';

$ServerOptions = "";



if ((isset($_GET['action']) && isset($_GET['uid']) && isset($_GET['server'])) || (isset($_POST['action']) && isset($_POST['uid']) && isset($_POST['server'])))
{
    if(isset($_GET['action']) && isset($_GET['uid']) && isset($_GET['server']))
	{
		$action = $_GET['action'];
		$uid = $_GET['uid'];
		$server = $_GET['server'];		
	}
	else
	{
		$action = $_POST['action'];
		$uid = $_POST['uid'];
		$server = $_POST['server'];			
	}
	
	//echo "<hr>action: $action   uid: $uid    server: $server<hr>";


	foreach ($ServerList as $ServerToCheck)
	{
		$ServerDetails = explode("|", $ServerToCheck);
		$dbhost = $ServerDetails[0];
		$dbname = $ServerDetails[1];
		$dbuser = $ServerDetails[2];
		$dbpass = $ServerDetails[3];
		$servername = ucwords($ServerDetails[4]);
		$dbport = $ServerDetails[5];

		if (strtolower($servername) == strtolower($server))
		{
			$db_local = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
		}
	}



	
	if($action == 'delete')
	{
		$sql = "SELECT * FROM account WHERE uid = '$uid'";
		$result = mysqli_query($db_local, $sql);
		$row = mysqli_fetch_object($result);
		
		$steam64id = '<a href="http://steamcommunity.com/profiles/' . $uid . '" target=_blank>' . $uid . '</a>';
		$name = $row->name;
	
		
		echo '<div>
				<div>

					<h2>Confirm Character Delete:</h2>
				 
					<form method="post" action="playeredit.php" name="myform">
						
						<p style="width:100%;padding-top:5px;padding-bottom:5px;">
							<h2>'.$name.' ('.$steam64id.')</h2>
							<input type="hidden" name="uid" value="'.$uid.'">
							<input type="hidden" name="action" value="deleteconfirmed">
							<input type="hidden" name="server" value="'.$server.'">
							<input type="hidden" name="submitok" value="true"><br><br>
							<input type="submit" value="Confirm Delete"><br><br><a href="playersearch.php?server='.$server.'&searchtype=uid&searchfield='.$uid.'">Cancel Delete</a>
						</p>
						</form>

					</div>
				</div>';	
		
	}
	elseif($action == 'deleteconfirmed')
	{
		$sql = "DELETE FROM player WHERE account_uid = '$uid '";
		//echo "<hr>$sql<hr>";
		$result = mysqli_query($db_local, $sql);
		$msg = "Player deleted";
		$url = "playersearch.php?server=$server&searchtype=uid&searchfield=$uid";
		redirect($msg, $url);
		
	}	
	elseif($action == 'edit')
	{
		$sql = "SELECT * FROM account WHERE uid = '$uid'";
		$result = mysqli_query($db_local, $sql);
		$row = mysqli_fetch_object($result);
		
		$steam64id = '<a href="http://steamcommunity.com/profiles/' . $uid . '" target=_blank>' . $uid . '</a>';
		$name = $row->name;
		$poptabs = $row->money;
		$respect = $row->score;		
		
		echo '<div>
		<div>

			<h2>Player Edit:</h2>
		 
			<form method="post" action="playeredit.php" name="myform">
				
				<p style="width:100%;padding-top:5px;padding-bottom:5px;">
					<h2>'.$name.' ('.$steam64id.')</h2>
					<span style="width: 250px;text-align:left;">Pop Tabs:</span>
					<span style="width: 200px;text-align:left;">
						<input style="width: 100px;text-align:center;" type="text" name="poptabs" size=5 id="poptabs" value='.$poptabs.'></input>
					</span>
					
					<span style="width: 250px;text-align:left;">Respect:</span>
					<span style="width: 200px;text-align:left;">
						<input style="width: 100px;text-align:center;" type="text" name="respect" size=5 id="respect" value='.$respect.'></input>
					</span>
					<input type="hidden" name="uid" value="'.$uid.'">
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="server" value="'.$server.'">
					<input type="hidden" name="submitok" value="true"><br><br>
					<input type="submit"><input type="reset">
				</p>
				</form>

				</div>
				</div>

				<script language="Javascript"  type="text/javascript"><!--
					document.myform.searchfield.focus();
					//-->
				</script>';		
	}
	elseif($action == 'update' && $_POST['submitok'] == 'true')
	{
		$uidToUpdate = $_POST['uid'];
		$newPoptabs = $_POST['poptabs'];
		$newRespect = $_POST['respect'];
		
		$sql = "UPDATE account SET money = '$newPoptabs', score = '$newRespect' WHERE uid = '$uidToUpdate'";
		//echo "<hr>$sql<hr>";
		$result = mysqli_query($db_local, $sql);
		$msg = "Updated pop tab and respect levels";
		$url = "playersearch.php?server=$server&searchtype=uid&searchfield=$uid";
		redirect($msg, $url);
		
	}
	else
	{
		echo "<h1>ERROR</h1>";
	}

}
else
{
	echo "<h1>ERROR1</h1>";
	
}
include 'includes/footer.php';
?>
