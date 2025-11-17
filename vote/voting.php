<?php
session_start();
include 'db.php';

$error = '';
$loggedIn = false;

if(isset($_POST['login'])) {
    $voterID = $_POST['voterID'];
    $voterPass = $_POST['voterPass'];
    
    $sql = "SELECT * FROM voters WHERE voterID = '$voterID' AND voterStat = 'Active'";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        $voter = $result->fetch_assoc();
        
        // Verify password
        if(password_verify($voterPass, $voter['voterPass'])) {
            
            if($voter['voted'] == 'Yes') {
                $error = "You have already voted! You cannot vote again.";
            } else {
                $_SESSION['voterID'] = $voter['voterID'];
                $_SESSION['voterFName'] = $voter['voterFName'];
                $_SESSION['voterLName'] = $voter['voterLName'];
                $_SESSION['voted'] = $voter['voted'];
                $loggedIn = true;
            }
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Voter ID not found or account is inactive!";
    }
}

if(isset($_POST['logout'])) {
    session_destroy();
    header("Location: voting.php");
    exit();
}

if(isset($_POST['submit_vote'])) {
    $voterID = $_SESSION['voterID'];
    
    $posSql = "SELECT * FROM positions WHERE posStat = 'Active'";
    $posResult = $conn->query($posSql);
    $positions = $posResult->fetch_all(MYSQLI_ASSOC);
    
    $errors = [];
    
    foreach ($positions as $position) {
        $selectedCandidates = isset($_POST['position_' . $position['posID']]) ? $_POST['position_' . $position['posID']] : [];
        $maxVotes = $position['numOfPositions'];
        
        if (count($selectedCandidates) > $maxVotes) {
            $errors[] = "Position '{$position['posName']}': You selected " . count($selectedCandidates) . " candidates, but maximum allowed is {$maxVotes}.";
        }
    }

    if (empty($errors)) {
        $votesInserted = true;
        
        foreach ($positions as $position) {
            $selectedCandidates = isset($_POST['position_' . $position['posID']]) ? $_POST['position_' . $position['posID']] : [];
            
            foreach ($selectedCandidates as $candID) {
                $voteSql = "INSERT INTO votes (voterID, candID, posID) VALUES ('$voterID', '$candID', '{$position['posID']}')";
                if(!$conn->query($voteSql)) {
                    $votesInserted = false;
                    break;
                }
            }
        }
        
        if($votesInserted) {
            $updateSql = "UPDATE voters SET voted = 'Yes' WHERE voterID = '$voterID'";
            $conn->query($updateSql);
            
            $_SESSION['voteSuccess'] = true;
            $loggedIn = false;
        }
    }
}

if(isset($_SESSION['voterID'])) {
    $loggedIn = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Module</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Voting Module</h1>
    <nav>
        <a href="positions.php">Positions</a>
        <a href="candidates.php">Candidates</a>
        <a href="voters.php">Voters</a>
        <a href="voting.php">Vote</a>
        <a href="results.php">Results</a>
        <a href="winners.php">Winners</a>
    </nav>

    <?php if(isset($_SESSION['voteSuccess'])): ?>
        <div class="success-msg">
            <strong>✓ Success!</strong> Your vote has been recorded successfully. Thank you for voting!
        </div>
        <?php unset($_SESSION['voteSuccess']); ?>
    <?php endif; ?>

    <?php if(!$loggedIn): ?>
        <form method="POST" class="form-section">
            <h3>Voter Login</h3>
            
            <?php if($error != ''): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <label>Voter ID:</label>
            <input type="text" name="voterID" required>
            
            <label>Password:</label>
            <input type="password" name="voterPass" required>
            
            <button type="submit" name="login">Login</button>
        </form>
    <?php else: ?>
        <div class="welcome-box">
            <h3>Welcome, <?php echo $_SESSION['voterFName'] . " " . $_SESSION['voterLName']; ?>!</h3>
            <p><strong>Voter ID:</strong> <?php echo $_SESSION['voterID']; ?></p>
            <form method="POST" class="inline-form">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>

        <?php if(!empty($errors)): ?>
            <?php foreach($errors as $err): ?>
                <div class="error-msg"><?php echo $err; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="position-box">
            <h3>Cast Your Vote</h3>
            <p><strong>Instructions:</strong> Select up to the maximum number of candidates allowed for each position.</p>
            
            <form method="POST">
                <?php
                $posSql = "SELECT * FROM positions WHERE posStat = 'Active' ORDER BY posID";
                $posResult = $conn->query($posSql);
                $positions = $posResult->fetch_all(MYSQLI_ASSOC);
                
                foreach ($positions as $position):
                    $candSql = "SELECT * FROM candidates WHERE posID = '{$position['posID']}' AND candStat = 'Active' ORDER BY candLName, candFName";
                    $candResult = $conn->query($candSql);
                    $candidates = $candResult->fetch_all(MYSQLI_ASSOC);
                ?>
                
                <div class="position-box">
                    <h4><?php echo $position['posName']; ?></h4>
                    <p><strong>Select up to <?php echo $position['numOfPositions']; ?> candidate(s)</strong></p>
                    
                    <?php if(empty($candidates)): ?>
                        <p class="no-candidates">No active candidates available for this position.</p>
                    <?php else: ?>
                        <?php foreach($candidates as $candidate): ?>
                            <label class="candidate-label">
                                <input type="checkbox" 
                                       name="position_<?php echo $position['posID']; ?>[]" 
                                       value="<?php echo $candidate['candID']; ?>"
                                       onchange="checkMaxVotes(<?php echo $position['posID']; ?>, <?php echo $position['numOfPositions']; ?>)">
                                <?php echo $candidate['candFName'] . " " . $candidate['candLName']; ?>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <?php endforeach; ?>
                
                <button type="submit" name="submit_vote" class="btn-submit-vote">
                    Submit Vote
                </button>
            </form>
        </div>

        <script>
            function checkMaxVotes(posID, maxVotes) {
                const checkboxes = document.querySelectorAll('input[name="position_' + posID + '[]"]:checked');
                if (checkboxes.length > maxVotes) {
                    alert('You can only select up to ' + maxVotes + ' candidate(s) for this position.');
                    event.target.checked = false;
                }
            }
        </script>
    <?php endif; ?>
</body>
</html>