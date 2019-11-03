<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Leaderboard</title>
</head>

<body>
<h1>Leaderboard</h1>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cryptotrade";

$conn = new mysqli($servername, $username, $password, $dbname);

if(!$conn){
	die("Connection failed: " . $conn->connect_error);
}

?>
<form action = "" method = "get">
	<select name = "comp">
		<option value= "balances">
			balance
		</option>
		<option value = "competitions">
			table2
		</option>
		<option value = "trades">
			table1
		</option>
	</select>
	<input type = "submit" value = "sub">
</form>
<?php
$sql = "SELECT userID, USD FROM ". $_GET["comp"]. " ORDER BY USD DESC LIMIT 10";
$res = $conn->query($sql);
$i=1;

if ($res->num_rows > 0){
	while($row = $res->fetch_assoc()) {
		echo $i." ". $row["userID"]." ".$row["USD"]. "<br>";
		$i++;
	}
}
?>
</body>
</html>