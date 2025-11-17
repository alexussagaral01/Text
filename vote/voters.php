<?php
session_start();
include 'db.php';

// ADD VOTER
if(isset($_POST['add'])) {
    $voterID = $_POST['voterID'];
    $voterFName = $_POST['voterFName'];
    $voterLName = $_POST['voterLName'];
    $voterPass = password_hash($_POST['voterPass'], PASSWORD_DEFAULT);
    $voterStat = $_POST['voterStat'];

    $sql = "INSERT INTO voters (voterID, voterFName, voterLName, voterPass, voterStat) 
            VALUES ('$voterID', '$voterFName', '$voterLName', '$voterPass', '$voterStat')";
    
    if($conn->query($sql) === TRUE) {
        header("Location: voters.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

// UPDATE VOTER
if(isset($_POST['update'])) {
    $voterID = $_POST['voterID'];
    $voterFName = $_POST['voterFName'];
    $voterLName = $_POST['voterLName'];
    $voterStat = $_POST['voterStat'];
    
    if(!empty($_POST['voterPass'])) {
        $voterPass = password_hash($_POST['voterPass'], PASSWORD_DEFAULT);
        $sql = "UPDATE voters SET 
                voterFName = '$voterFName',
                voterLName = '$voterLName',
                voterPass = '$voterPass',
                voterStat = '$voterStat'
                WHERE voterID = '$voterID'";
    } else {
        $sql = "UPDATE voters SET 
                voterFName = '$voterFName',
                voterLName = '$voterLName',
                voterStat = '$voterStat'
                WHERE voterID = '$voterID'";
    }
    
    if($conn->query($sql) === TRUE) {
        header("Location: voters.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// DEACTIVATE/ACTIVATE VOTER
if(isset($_POST['toggle'])) {
    $voterID = $_POST['voterID'];
    $currentStatus = $_POST['currentStatus'];
    
    $newStatus = ($currentStatus == 'Active') ? 'Inactive' : 'Active';
    
    $sql = "UPDATE voters SET voterStat = '$newStatus' WHERE voterID = '$voterID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: voters.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

// DELETE VOTER
if(isset($_POST['delete'])) {
    $voterID = $_POST['voterID'];
    
    $sql = "DELETE FROM voters WHERE voterID = '$voterID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: voters.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// GET ALL VOTERS FROM DATABASE
$sql = "SELECT voterID, voterFName, voterLName, voterStat, voted FROM voters";
$result = $conn->query($sql);
$voters = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voters Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-section {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Voters Management</h1>
    <nav>
        <a href="positions.php">Positions</a>
        <a href="candidates.php">Candidates</a>
        <a href="voters.php">Voters</a>
        <a href="voting.php">Vote</a>
        <a href="results.php">Results</a>
        <a href="winners.php">Winners</a>
    </nav>

    <form method="POST" class="form-section">
        <h3 id="formTitle">Add New Voter</h3>
        
        <input type="hidden" name="oldVoterID" id="oldVoterID">
        
        <label>Voter ID:</label>
        <input type="text" name="voterID" id="voterID" required>
        
        <label>Voter First Name:</label>
        <input type="text" name="voterFName" id="voterFName" required>
        
        <label>Voter Last Name:</label>
        <input type="text" name="voterLName" id="voterLName" required>
        
        <label>Password:</label>
        <input type="password" name="voterPass" id="voterPass" required>
        
        <label>Status:</label>
        <select name="voterStat" id="voterStat" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
        
        <button type="submit" name="add" id="submitBtn">Add Voter</button>
    </form>

    <h3>List of Voters</h3>
    <table>
        <thead>
            <tr>
                <th>Voter ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Status</th>
                <th>Voted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($voters as $voter): ?>
            <tr>
                <td><?php echo $voter['voterID']; ?></td>
                <td><?php echo $voter['voterFName']; ?></td>
                <td><?php echo $voter['voterLName']; ?></td>
                <td><?php echo $voter['voterStat']; ?></td>
                <td><?php echo $voter['voted']; ?></td>
                <td>
                    <button type="button" onclick="editVoter('<?php echo $voter['voterID']; ?>', '<?php echo $voter['voterFName']; ?>', '<?php echo $voter['voterLName']; ?>', '<?php echo $voter['voterStat']; ?>')">Update</button>
                    
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="voterID" value="<?php echo $voter['voterID']; ?>">
                        <input type="hidden" name="currentStatus" value="<?php echo $voter['voterStat']; ?>">
                        <button type="submit" name="toggle"><?php echo ($voter['voterStat'] == 'Active') ? 'Deactivate' : 'Activate'; ?></button>
                    </form>
                    
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this voter?');">
                        <input type="hidden" name="voterID" value="<?php echo $voter['voterID']; ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function editVoter(voterID, voterFName, voterLName, voterStat) {
            document.getElementById('oldVoterID').value = voterID;
            document.getElementById('voterID').value = voterID;
            document.getElementById('voterFName').value = voterFName;
            document.getElementById('voterLName').value = voterLName;
            document.getElementById('voterStat').value = voterStat;
            document.getElementById('voterPass').value = '';
            
            document.getElementById('formTitle').textContent = 'Update Voter';
            document.getElementById('submitBtn').name = 'update';
            document.getElementById('submitBtn').textContent = 'Update Voter';
            document.getElementById('voterPass').required = false;
            
            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>
