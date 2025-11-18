<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Election Results</h1>
    <nav>
        <a href="positions.php">Positions</a>
        <a href="candidates.php">Candidates</a>
        <a href="voters.php">Voters</a>
        <a href="voting.php">Vote</a>
        <a href="results.php">Results</a>
        <a href="winners.php">Winners</a>
    </nav>

    <?php
    $posSql = "SELECT * FROM positions WHERE posStat = 'Active' ORDER BY posID";
    $posResult = $conn->query($posSql);
    $positions = $posResult->fetch_all(MYSQLI_ASSOC);
    
    foreach ($positions as $position):
        $candSql = "SELECT c.candID, c.candFName, c.candLName 
                    FROM candidates c 
                    WHERE c.posID = '{$position['posID']}' AND c.candStat = 'Active'
                    ORDER BY c.candFName, c.candLName";
        $candResult = $conn->query($candSql);
        $candidates = $candResult->fetch_all(MYSQLI_ASSOC);
        
        $totalVotesSql = "SELECT COUNT(*) as totalVotes FROM votes WHERE posID = '{$position['posID']}'";
        $totalVotesResult = $conn->query($totalVotesSql);
        $totalVotesRow = $totalVotesResult->fetch_assoc();
        $totalVotes = $totalVotesRow['totalVotes'];
        
        $candidateVotes = [];
        foreach ($candidates as $candidate) {
            $voteSql = "SELECT COUNT(*) as voteCount FROM votes WHERE candID = '{$candidate['candID']}' AND posID = '{$position['posID']}'";
            $voteResult = $conn->query($voteSql);
            $voteRow = $voteResult->fetch_assoc();
            $candidateVotes[$candidate['candID']] = $voteRow['voteCount'];
        }
    ?>
    
    <div class="position-box">
        <h3><?php echo $position['posName']; ?></h3>
        <p><strong>Number of Positions Available:</strong> <?php echo $position['numOfPositions']; ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Candidate Name</th>
                    <th>Total Votes</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $candidate): ?>
                    <?php 
                    $voteCount = $candidateVotes[$candidate['candID']];
                    $percentage = ($totalVotes > 0) ? ($voteCount / $totalVotes) * 100 : 0;
                    ?>
                    <tr>
                        <td><?php echo $candidate['candFName'] . " " . $candidate['candLName']; ?></td>
                        <td><?php echo $voteCount; ?></td>
                        <td><?php echo number_format($percentage, 2); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="tfoot-row">
                    <td>Total Votes Cast</td>
                    <td><?php echo $totalVotes; ?></td>
                    <td>100.00%</td>
                </tr>
            </tfoot>
        </table>
    </div>  
    <?php endforeach; ?>
</body>
</html>
