<!DOCTYPE html>
<?php

	include_once("User.php");
	date_default_timezone_set("America/Vancouver");

	$sql = new MySQL(DB_HOST, DB_USER, DB_PASS, DB_DB);

	$value = file_get_contents("php://input");
	$obj = json_decode($value); 
	$userID =  $obj->{'userID'};
	$competitionID = $obj->{'competitionID'};
	$query = $sql->where('userID',$userID)->and_where('competitionID',$competitionID)->get('balances');
	if($query == true){
		echo "You have already joined in this competition!";
	} else {
		$addtocompe = $sql->insert('balances', $obj);
	}


?>
<html>
	<body>


		</div>
	</body>
</html>