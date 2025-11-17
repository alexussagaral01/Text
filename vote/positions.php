<?php
session_start();
include 'db.php';

if(isset($_POST['add'])) {
    $posName = $_POST['posName'];
    $numOfPositions = $_POST['numOfPositions'];
    $posStat = $_POST['posStat'];

    $sql = "INSERT INTO positions (posName, numOfPositions, posStat) 
            VALUES ('$posName', '$numOfPositions', '$posStat')";
    
    if($conn->query($sql) === TRUE) {
        header("Location: positions.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

if(isset($_POST['update'])) {
    $posID = $_POST['posID'];
    $posName = $_POST['posName'];
    $numOfPositions = $_POST['numOfPositions'];
    $posStat = $_POST['posStat'];
    
    $sql = "UPDATE positions SET 
            posName = '$posName',
            numOfPositions = '$numOfPositions',
            posStat = '$posStat'
            WHERE posID = '$posID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: positions.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

if(isset($_POST['toggle'])) {
    $posID = $_POST['posID'];
    $currentStatus = $_POST['currentStatus'];
    
    $newStatus = ($currentStatus == 'Active') ? 'Inactive' : 'Active';
    
    $sql = "UPDATE positions SET posStat = '$newStatus' WHERE posID = '$posID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: positions.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

if(isset($_POST['delete'])) {
    $posID = $_POST['posID'];
    
    $sql = "DELETE FROM positions WHERE posID = '$posID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: positions.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$sql = "SELECT * FROM positions";
$result = $conn->query($sql);
$positions = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Positions Management</title>
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
    <h1>Positions Management</h1>
    <nav>
        <a href="positions.php">Positions</a>
        <a href="candidates.php">Candidates</a>
        <a href="voters.php">Voters</a>
        <a href="voting.php">Vote</a>
        <a href="results.php">Results</a>
        <a href="winners.php">Winners</a>
    </nav>

    <form method="POST" class="form-section">
        <h3 id="formTitle">Add New Position</h3>
        
        <input type="hidden" name="posID" id="posID">
        
        <label>Position Name:</label>
        <input type="text" name="posName" id="posName" required>
        
        <label>Number of Positions:</label>
        <input type="number" name="numOfPositions" id="numOfPositions" min="1" value="1" required>
        
        <label>Status:</label>
        <select name="posStat" id="posStat" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
        
        <button type="submit" name="add" id="submitBtn">Add Position</button>
        <button type="button" id="cancelBtn" class="hidden" onclick="cancelEdit()">Cancel</button>
    </form>

    <h3>List of Positions</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Position Name</th>
                <th>Number of Positions</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($positions as $position): ?>
            <tr>
                <td><?php echo $position['posID']; ?></td>
                <td><?php echo $position['posName']; ?></td>
                <td><?php echo $position['numOfPositions']; ?></td>
                <td><?php echo $position['posStat']; ?></td>
                <td>
                    <button type="button" onclick="editPosition(<?php echo $position['posID']; ?>, '<?php echo $position['posName']; ?>', <?php echo $position['numOfPositions']; ?>, '<?php echo $position['posStat']; ?>')">Update</button>
                    
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="posID" value="<?php echo $position['posID']; ?>">
                        <input type="hidden" name="currentStatus" value="<?php echo $position['posStat']; ?>">
                        <button type="submit" name="toggle"><?php echo ($position['posStat'] == 'Active') ? 'Deactivate' : 'Activate'; ?></button>
                    </form>
                    
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this position?');">
                        <input type="hidden" name="posID" value="<?php echo $position['posID']; ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function editPosition(posID, posName, numOfPositions, posStat) {
            // Fill the form with position data
            document.getElementById('posID').value = posID;
            document.getElementById('posName').value = posName;
            document.getElementById('numOfPositions').value = numOfPositions;
            document.getElementById('posStat').value = posStat;
            
            // Change form title and buttons
            document.getElementById('formTitle').textContent = 'Update Position';
            document.getElementById('submitBtn').name = 'update';
            document.getElementById('submitBtn').textContent = 'Update Position';
            document.getElementById('cancelBtn').classList.remove('hidden');
            
            // Scroll to form
            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
        }

        function cancelEdit() {
            // Reset form
            document.getElementById('posID').value = '';
            document.getElementById('posName').value = '';
            document.getElementById('numOfPositions').value = '1';
            document.getElementById('posStat').value = 'Active';
            
            // Reset form title and buttons
            document.getElementById('formTitle').textContent = 'Add New Position';
            document.getElementById('submitBtn').name = 'add';
            document.getElementById('submitBtn').textContent = 'Add Position';
            document.getElementById('cancelBtn').classList.add('hidden');
        }
    </script>
</body>
</html>
