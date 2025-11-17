<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Winners</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <h1>Election Winners</h1>
    <nav>
        <a href="positions.php">Positions</a>
        <a href="candidates.php">Candidates</a>
        <a href="voters.php">Voters</a>
        <a href="voting.php">Vote</a>
        <a href="results.php">Results</a>
        <a href="winners.php">Winners</a>
    </nav>
    <div class="winners-container">
        <h2>Elective Position Winners</h2>
        <table class="winners-table">
            <thead>
                <tr>
                    <th>Elective Position</th>
                    <th>Winner</th>
                    <th>Total Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $posSql = "SELECT * FROM positions WHERE posStat = 'Active' ORDER BY posID";
                $posResult = $conn->query($posSql);
                $positions = $posResult->fetch_all(MYSQLI_ASSOC);
                
                foreach ($positions as $position):
                    $candSql = "SELECT  
                                    c.candID, 
                                    COUNT(v.candID) as voteCount
                                FROM candidates c
                                LEFT JOIN votes v ON c.candID = v.candID AND v.posID = '{$position['posID']}'
                                WHERE c.posID = '{$position['posID']}' AND c.candStat = 'Active'
                                GROUP BY c.candID
                                ORDER BY voteCount DESC, c.candID ASC";
                    $candResult = $conn->query($candSql);
                    $candidates = $candResult->fetch_all(MYSQLI_ASSOC);
                    
                    $winners = array_slice($candidates, 0, $position['numOfPositions']);
                    
                    foreach($winners as $winner):
                        $hasWinners = true;
                        $winnerID = $winner['candID'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($position['posName']); ?></td>
                        <td>Candidate <?php echo htmlspecialchars($winnerID); ?></td>
                        <td><?php echo $winner['voteCount']; ?></td>
                    </tr>
                <?php 
                endforeach;
                endforeach; 
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>