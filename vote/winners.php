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

    <?php
    // Get all active positions
    $posSql = "SELECT * FROM positions WHERE posStat = 'Active' ORDER BY posID";
    $posResult = $conn->query($posSql);
    $positions = $posResult->fetch_all(MYSQLI_ASSOC);
    
    foreach ($positions as $position):
        // Get candidates with vote counts, ordered by votes DESC
        $candSql = "SELECT 
                        c.candID, 
                        c.candFName, 
                        c.candLName, 
                        COUNT(v.candID) as voteCount
                    FROM candidates c
                    LEFT JOIN votes v ON c.candID = v.candID AND v.posID = '{$position['posID']}'
                    WHERE c.posID = '{$position['posID']}' AND c.candStat = 'Active'
                    GROUP BY c.candID
                    ORDER BY voteCount DESC, c.candFName, c.candLName";
        $candResult = $conn->query($candSql);
        $candidates = $candResult->fetch_all(MYSQLI_ASSOC);
        
        // Get top winners (limited by numOfPositions)
        $winners = array_slice($candidates, 0, $position['numOfPositions']);
    ?>
    
    <div class="winner-box">
        <h3><?php echo $position['posName']; ?></h3>
        <p><strong>Number of Positions Available:</strong> <?php echo $position['numOfPositions']; ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Winner Name</th>
                    <th>Total Votes</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($winners)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #666;">No candidates available for this position.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($winners as $index => $winner): ?>
                        <tr class="winner-row">
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $winner['candFName'] . " " . $winner['candLName']; ?></td>
                            <td><?php echo $winner['voteCount']; ?></td>
                            <td>
                                <?php if($index < $position['numOfPositions']): ?>
                                    <span style="color: #28a745; font-weight: bold;">✓ WINNER</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if(!empty($winners)): ?>
            <p class="summary-box">
                <strong>Summary:</strong> 
                <?php 
                $winnerNames = array_map(function($w) { return $w['candFName'] . " " . $w['candLName']; }, $winners);
                echo "The winner" . (count($winners) > 1 ? "s are: " : " is: ") . implode(", ", $winnerNames) . ".";
                ?>
            </p>
        <?php else: ?>
            <p class="summary-box" style="color: #dc3545;">
                <strong>No votes cast yet for this position.</strong>
            </p>
        <?php endif; ?>
    </div>
    
    <?php endforeach; ?>

</body>
</html>
