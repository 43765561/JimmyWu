<?php
include_once("User.php");
require_once("MySQL.php");
include_once("MarketData.php");
date_default_timezone_set("America/Vancouver");

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Leaderboard</title>
</head>

<body>
<?php
$sql = new MySQL(DB_HOST, DB_USER, DB_PASS, DB_DB);
$user = new User();
$market = new MarketData();
$stotal_test = 1;

$user->setId("jon"); 
echo "<h1>Upcoming Competition</h1>";
$qry = "SELECT id, startTimestamp, endTimestamp, startAmount
	FROM competitions
	WHERE UNIX_TIMESTAMP(startTimestamp) > UNIX_TIMESTAMP(now())
	ORDER BY UNIX_TIMESTAMP(startTimestamp)";
$upcoming = $sql->query($qry,true);
if ($sql->num_rows() > 0) {
	echo "<table><tr><th>CompetitionID</th><th>Start</th><th>End</th><th>initCapital</th><th>   </th></tr>";

	echo "<tr><td>".$upcoming[0]["id"]."</td><td>".$upcoming[0]["startTimestamp"]."</td>
	<td>".$upcoming[0]["endTimestamp"]."</td><td>".$upcoming[0]["startAmount"]."</td>
	<td><button id='button' onclick='joincompetition(this)'>Join</button></td></tr>";

	echo "</table>";
} else {
	echo "There is no upcoming event";
}

	echo "<h1>Current Competition</h1>";

    $cquery="SELECT t.userID AS userID,t.symbol AS symbol,t.amount AS amount,t.competitionID AS competitionID,b.USD AS balance FROM trades t JOIN balances b WHERE t.competitionID = getCurrentComp() AND t.userID = b.userID AND isClosed = 1 GROUP BY t.userID,t.symbol,t.competitionID";

	$result = $sql->query($cquery,true);
	$price = array();

	$prices = json_decode($market->getAllPrices(),true);
	foreach( $prices as $key => $item )
	{
		$price[$item['symbol']] = $item['price'];
	}

	for ($i=0; $i < $sql->num_rows(); $i++){
		echo $stotal_test;//$price[$result[$i]['symbol']."USDT"]*$result[$i]['amount'];
	}

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
		$result = $sql->query($query,true);


		if ($sql->num_rows() > 0){
			for ($i=0; $i < $sql->num_rows(); $i++) {
				echo "<option value = ". $result[$i]['id']. "> Competition ". $result[$i]['id']. "</option>;";
				
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

	function joincompetition(target) {
		var userIdString="<?php echo $user->getId(); ?>";
		var upcoming = "<?php echo $upcoming[0]['id']; ?>";
		var initCapital = "<?php echo $upcoming[0]['startAmount']; ?>";
		alert(upcoming);
		if (userIdString == null){
			document.location.href = 'registration/login.php';
		} else {
			var data = JSON.stringify({
    			userID: userIdString,
    			competitionID : upcoming,
    			USD: initCapital,
			})
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'joincompe.php',true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(data);
			xhr.onreadystatechange = function() {
    			if (xhr.readyState === 4){
        			document.getElementById('Test').innerHTML = xhr.responseText;
    			}
			};
		}
	}

</script>

<br>
<div id = "Leaderboard"> Leaderboard...</div>
<div id = "Test"> Test</div>

</body>
</html>