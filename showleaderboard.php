<!DOCTYPE html>
<?php
	$value = file_get_contents("php://input");

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "cryptotrade";

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
	}

	$query = "SELECT id, startTimestamp, endTimestamp, startAmount
	FROM competitions 
	WHERE id = ".$value;
	$res = $conn->query($query);

	if ($res->num_rows > 0) {
		while($title = $res->fetch_assoc()){
			echo "<table><tr><th>Competition ".$title["id"]."</th><th>".$title["startTimestamp"]."</th><th>".$title["endTimestamp"]."</th><th>initCapital ".$title["startAmount"]."</th></tr></table>";
		}
	}

	$query = "SELECT (@count := @count + 1) AS Rank, b.userid, b.usd-c.startAmount AS pl, (b.usd-c.startAmount) / c.startAmount*100 AS percent 
	FROM balances b JOIN competitions c ON b.competitionID = c.id 
	CROSS JOIN (SELECT @count := 0) params
	WHERE competitionID = ".$value." ORDER BY USD DESC";
	$result = $conn->query($query);

	
	if ($result->num_rows > 0) {
    	echo "<table><tr><th>Rank</th><th>UserId</th><th>P/L</th></tr>";
    	$i = 0;
    	while(($row = $result->fetch_assoc()) && ($i < 10)) {
        	echo "<tr><td>".$row["Rank"]."</td><td>".$row["userid"]."</td><td>". sprintf("%+.3f",$row["pl"]). "USD/(". sprintf("%+.3f",$row["percent"])."%)</td></tr>";
        	$i++;
    	}
    	echo "</table>";
	} 
?>
			
<html>
	<body>


		</div>
	</body>
</html>

