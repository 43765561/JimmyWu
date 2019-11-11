<!DOCTYPE html>
<?php
	include_once("User.php");
	include_once("MarketData.php");
	date_default_timezone_set("America/Vancouver");

	$sql = new MySQL(DB_HOST, DB_USER, DB_PASS, DB_DB);
	$user = new User();
	$user->setId('ben');

	$value = file_get_contents("php://input");

	$title = $sql->where('id', $value)->get('competitions');

	if ($sql->num_rows() > 0) {
		for ($i=0; $i < $sql->num_rows(); $i++) {
			echo "<table><tr><th>Competition ".$title[$i]["id"]."</th><th>".$title[$i]["startTimestamp"]."</th><th>".$title[$i]["endTimestamp"]."</th><th>initCapital ".$title[$i]["startAmount"]."</th></tr></table>";
		}
	}

	$query = "SELECT (@count := @count + 1) AS Rank, b.userid, (b.usd-c.startAmount) / c.startAmount*100 AS percent 
	FROM balances b JOIN competitions c ON b.competitionID = c.id 
	CROSS JOIN (SELECT @count := 0) params
	WHERE competitionID = ".$value." ORDER BY USD DESC";
	$result = $sql->query($query,true);
	
	if ($sql->num_rows() > 0) {
    	echo "<table><tr><th>Rank</th><th>UserId</th><th>P/L</th></tr>";
    	if ($sql->num_rows() < 10) {
    		for ($i=0; $i < $sql->num_rows(); $i++) {
        		echo "<tr><td>".$result[$i]["Rank"]."</td><td>".$result[$i]["userid"]."</td>
        		<td>". sprintf("%+.3f",$result[$i]["percent"])."%)</td></tr>";
    		}
    	} else {
    		for ($i=0; $i < 10; $i++) {
        		echo "<tr><td>".$result[$i]["Rank"]."</td><td>".$result[$i]["userid"]."</td>
        		<td>". sprintf("%+.3f",$result[$i]["percent"])."%)</td></tr>";
    	}
    	if ($user->getId() == 'ben'){
    		$id = $sql->where('userid',$user->getId())->query($query,true);
    		echo "<tr><td>".$id[$i]["Rank"]."</td><td>".$id[$i]["userid"]."</td>
        		<td>". sprintf("%+.3f",$result[$i]["percent"])."%)</td></tr>";
    	}
    	echo "</table>";
    	}
	} 
	if ($sql->num_rows() > 0) {
    	echo "<table><tr><th>Your Rank</th><th>UserId</th><th>P/L</th></tr>";
    	if ($sql->num_rows() < 10) {
    		for ($i=0; $i < $sql->num_rows(); $i++) {
    			if ($result[$i]["userid"] == $user->getId()){
        		echo "<tr><td>".$result[$i]["Rank"]."</td><td>".$result[$i]["userid"]."</td>
        		<td>". sprintf("%+.3f",$result[$i]["percent"])."%</td></tr>";
        		}
    		}
    	} else {
    		for ($i=0; $i < 10; $i++) {
    			if ($result[$i]["userid"] == $user->getId()){
        			echo "<tr><td>".$result[$i]["Rank"]."</td><td>".$result[$i]["userid"]."</td>
        			<td>". sprintf("%+.3f",$result[$i]["percent"])."%</td></tr>";
        		}
    	}
    	echo "</table>";
    	}
	} 
?>

<html>
	<body>


		</div>
	</body>
</html>