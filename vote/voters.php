<?php
session_start();
include 'db.php';

if(isset($_POST['add'])) {
    $voterID = $_POST['voterID'];
    $voterFName = $_POST['voterFName'];
    $voterLName = $_POST['voterLName'];
    $voterPass = password_hash($_POST['voterPass'], PASSWORD_DEFAULT);
    $voterStat = 'Active';

    $sql = "INSERT INTO voters (voterID, voterFName, voterLName, voterPass, voterStat) 
            VALUES ('$voterID', '$voterFName', '$voterLName', '$voterPass', '$voterStat')";
    
    if($conn->query($sql) === TRUE) {
        header("Location: voters.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

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

if(isset($_GET['delete'])) {
    $voterID = $_GET['delete'];
    
    $sql = "DELETE FROM voters WHERE voterID = '$voterID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: voters.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

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
            <?php foreach ($voters as $v): 
                $id = $v['voterID'];
                $fname = $v['voterFName'];
                $lname = $v['voterLName'];
                $stat = $v['voterStat'];
            ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= $fname ?></td>
                <td><?= $lname ?></td>
                <td><?= $stat ?></td>
                <td><?= $v['voted'] ?></td>
                <td>
                    <button class="btn-update" onclick="editVoter('<?= $id ?>', '<?= $fname ?>', '<?= $lname ?>', '<?= $stat ?>')">Update</button>
                    
                    <button class="btn-delete" onclick="if(confirm('Delete this voter?')) location='?delete=<?= $id ?>'">Delete</button>
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
