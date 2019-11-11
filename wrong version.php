<?php
//wrong version of current competition
		$query = "SELECT (@count := @count + 1) AS Rank, userID, totalpl, percent,compeID FROM (SELECT userID,(SUM(stotal) + balance - startA) AS totalpl,(SUM(stotal) + balance - startA)/ startA*100 AS percent, compeID, SUM(stotal) AS total, balance 
		FROM (SELECT t.userID AS userID,t.symbol AS symbol, SUM( t.amount * t.price) AS stotal,t.competitionID AS compeID,b.USD AS balance, c.startAmount AS startA
		FROM trades t JOIN balances b JOIN competitions c 
		WHERE t.userID = b.userID AND t.competitionID = b.competitionID AND t.isClosed = 1 AND UNIX_TIMESTAMP(c.endTimestamp) > UNIX_TIMESTAMP(now()) AND UNIX_TIMESTAMP(c.startTimestamp) < UNIX_TIMESTAMP(now()) 
		GROUP BY t.symbol,t.userID,t.competitionID) AS table1
    	GROUP BY userID
    	ORDER BY totalpl DESC) AS table2
    	CROSS JOIN (SELECT @count := 0) params";
		$result = $conn->query($query);

	if ($sql->num_rows() > 0) {
    	echo "<table><tr><th>Rank</th><th>UserId</th><th>P/L</th></tr>";
    	if ($sql->num_rows() < 10) {
    		for ($i=0; $i < $sql->num_rows(); $i++) {
        		echo "<tr><td>".$result[$i]["Rank"]."</td><td>".$result[$i]["userID"]."</td><td>". sprintf("%+.3f",$result[$i]["percent"])."%</td></tr>";
        		if ($result[$i]["userID"] == $user->getId()){
    				$GLOBALS['$rowString'] = "<tr><td>".$result[$i]["Rank"]."</td><td>".$result[$i]["userID"]."</td><td>". sprintf("%+.3f",$result[$i]["percent"])."%</td></tr>";
    			}
        	}
    	} else {
    		for ($i=0; $i < 10; $i++) {
    			echo "<tr><td>".$result[$i]["Rank"]."</td><td>".$result[$i]["userID"]."</td><td>". sprintf("%+.3f",$result[$i]["percent"])."%</td></tr>";
    			if ($result[$i]["userID"] == $user->getId()){
    				$GLOBALS['$rowString'] = "<tr><td>".$result[$i]["Rank"]."</td><td>".$result[$i]["userID"]."</td><td>". sprintf("%+.3f",$result[$i]["percent"])."%</td></tr>";
    			}

    		}
    	}
    	echo "</table>";
	} else {
    	echo "No Ongoing Competition";
	}

	if ($sql->num_rows() > 0) {
    	if ($sql->num_rows() < 10) {
    		for ($i=0; $i < $sql->num_rows(); $i++) {
        		if ($result[$i]["userID"] == $user->getId()){
        			echo "<table><tr><th>".$user->getId()."'s Rank</th><td>".$result[$i]["Rank"]."</td></tr>";
        			echo "<tr><th>P/L</th><td>". sprintf("%+.3f",$result[$i]["percent"])."%</td></tr></table>";
    			}
        	}
    	} else {
    		for ($i=0; $i < 10; $i++) {
    			if ($result[$i]["userID"] == $user->getId()){
    				echo "<table><tr><th>".$user->getId()."'s Rank</th><td>".$result[$i]["Rank"]."</td></tr>";
					echo "<tr><th>P/L</th><td>". sprintf("%+.3f",$result[$i]["percent"])."%</td></tr></table>";
    			}

    		}
    	}
    	echo "</table>";
	} else {
    	echo "";
	}
