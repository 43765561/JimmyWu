<?php
include_once("User.php");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Leaderboard</title>
</head>

<body>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cryptotrade";
 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
		echo "<h1>Upcoming Competition</h1>";
		$query = "SELECT id, startTimestamp, endTimestamp, startAmount
		FROM competitions
		WHERE UNIX_TIMESTAMP(startTimestamp) > UNIX_TIMESTAMP(now())
		ORDER BY UNIX_TIMESTAMP(startTimestamp)";
		$result = $conn->query($query);

	if ($result->num_rows > 0) {
		echo "<table><tr><th>CompetitionID</th><th>Start</th><th>End</th><th>initCapital</th><th>   </th></tr>";
		while($upcoming = $result->fetch_assoc()) {
		echo "<tr><td>".$upcoming["id"]."</td><td>".$upcoming["startTimestamp"]."</td><td>".$upcoming["endTimestamp"]."</td><td>".$upcoming["startAmount"]."</td><td><button id='button' onclick='changeFontSize(this)'>Join</button></td></tr>";
		}
		echo "</table>";
		echo "<div><p id='demo'>test</p></div>";
	} else {
    	echo "There is no upcoming event";
	}

		echo "<h1>Current Competition</h1>";

	$query = "SELECT (@count := @count + 1) AS Rank, userID, totalpl, percent,compeID FROM (SELECT userID,(SUM(stotal) + balance - startA) AS totalpl,(SUM(stotal) + balance - startA)/ startA*100 AS percent, compeID, SUM(stotal) AS total, balance 
		FROM (SELECT t.userID AS userID,t.symbol AS symbol, SUM( t.amount * t.price) AS stotal,t.competitionID AS compeID,b.USD AS balance, c.startAmount AS startA
		FROM trades t JOIN balances b JOIN competitions c 
		WHERE t.userID = b.userID AND t.competitionID = b.competitionID AND t.isClosed = 0 AND UNIX_TIMESTAMP(c.endTimestamp) > UNIX_TIMESTAMP(now()) AND UNIX_TIMESTAMP(c.startTimestamp) < UNIX_TIMESTAMP(now()) 
		GROUP BY t.symbol,t.userID,t.competitionID) AS table1
        GROUP BY userID
        ORDER BY totalpl DESC) AS table2
        CROSS JOIN (SELECT @count := 0) params";
		$result = $conn->query($query);

		if ($result->num_rows > 0) {
    		echo "<table><tr><th>Rank</th><th>UserId</th><th>P/L</th></tr>";
    		$i = 0;

    		while(($row = $result->fetch_assoc()) && ($i < 10)) {
        		echo "<tr><td>".$row["Rank"]."</td><td>".$row["userID"]."</td><td>". sprintf("%+.3f",$row["totalpl"]). "USD/(". sprintf("%+.3f",$row["percent"])."%)</td></tr>";
        		$i++;
    		}
    		echo "</table>";
		} else {
    		echo "No Ongoing Competition";
		}

		echo "<div id = 'links'> </div>";
		echo "<h2>Your Rank</h2>";
		echo "<table><tr><th>Rank</th><td>UserRank</td></tr></table>";

		echo "<h1>Passed Competition Result</h1>";
?>

<form id = "leaderboardForm" action = "showleaderboard.php" method="post">
	<select onchange = "showTop10(this.value)" name = "select">
		<option selected="selected" disabled="disabled"> Select a competition </option>

		<?php
		$query = "SELECT id
		FROM competitions
		WHERE UNIX_TIMESTAMP(endTimestamp) < UNIX_TIMESTAMP(now())
		ORDER BY UNIX_TIMESTAMP(endTimestamp) DESC";
		$result = $conn->query($query);


		if ($result->num_rows > 0){
			while($row = $result->fetch_array()) {
				echo "<option value = ". $row["id"]. "> Competition ". $row["id"]. "</option>;";
			}
		}
		?>
	</select>
</form>
<script type="text/javascript">

	function showTop10(value){
		var data = JSON.stringify({
    		value: value,
		})
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
    		if (xhr.readyState === 4){
        		document.getElementById('Leaderboard').innerHTML = xhr.responseText;
    		}
		};
		xhr.open('POST', 'showleaderboard.php',true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.send(value);
	}

	function changeFontSize(target) {
  		var demo = document.getElementById("demo");
  		var computedStyle = window.getComputedStyle
   		    ? getComputedStyle(demo) 
      		: demo.currentStyle;     
  		var fontSize;

  		if (computedStyle) {
    		fontSize = parseFloat(computedStyle && computedStyle.fontSize);

    		if (target == document.getElementById("button")) {
    		    fontSize += 5;
    		} else if (target == document.getElementById("button2")) {
     		   fontSize -= 5;
     		}
      		demo.style.fontSize = fontSize + "px";
    	}
	}

</script>

<br>
<div id = "Leaderboard"> Leaderboard...</div>

</body>
</html>