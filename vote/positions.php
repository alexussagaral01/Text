<?php
session_start();
include 'db.php';

// ============================================
// ADD POSITION
// ============================================
if(isset($_POST['add'])) {
    $posName = $_POST['posName'];
    $numOfPositions = $_POST['numOfPositions'];
    $posStat = 'Active';

    $sql = "INSERT INTO positions (posName, numOfPositions, posStat) 
            VALUES ('$posName', '$numOfPositions', '$posStat')";
    
    if($conn->query($sql) === TRUE) {
        header("Location: positions.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

// ============================================
// UPDATE POSITION
// ============================================
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

// ============================================
// TOGGLE STATUS (Active/Inactive)
// ============================================
if(isset($_GET['toggle'])) {
    $posID = $_GET['toggle'];
    $currentStatus = $_GET['status'];
    
    $newStatus = ($currentStatus == 'Active') ? 'Inactive' : 'Active';
    
    $sql = "UPDATE positions SET posStat = '$newStatus' WHERE posID = '$posID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: positions.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

// ============================================
// DELETE POSITION
// ============================================
if(isset($_GET['delete'])) {
    $posID = $_GET['delete'];
    
    $sql = "DELETE FROM positions WHERE posID = '$posID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: positions.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// ============================================
// GET ALL POSITIONS
// ============================================
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
        
        <button type="submit" name="add" id="submitBtn">Add Position</button>
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
            <?php foreach ($positions as $p): 
                // Simple variables para sa buttons
                $id = $p['posID'];
                $name = $p['posName'];
                $num = $p['numOfPositions'];
                $stat = $p['posStat'];
                $isActive = ($stat == 'Active');
            ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= $name ?></td>
                <td><?= $num ?></td>
                <td><?= $stat ?></td>
                <td>
                    <!-- UPDATE BUTTON -->
                    <button class="btn-update" onclick="editPosition(<?= $id ?>, '<?= $name ?>', <?= $num ?>, '<?= $stat ?>')">Update</button>
                    
                    <!-- TOGGLE BUTTON (SIMPLIFIED!) -->
                    <button class="btn-toggle-<?= $isActive ? 'inactive' : 'active' ?>" onclick="location='?toggle=<?= $id ?>&status=<?= $stat ?>'"><?= $isActive ? 'Deactivate' : 'Activate' ?></button>
                    
                    <!-- DELETE BUTTON (SIMPLIFIED!) -->
                    <button class="btn-delete" onclick="if(confirm('Delete this position?')) location='?delete=<?= $id ?>'">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function editPosition(posID, posName, numOfPositions, posStat) {
            document.getElementById('posID').value = posID;
            document.getElementById('posName').value = posName;
            document.getElementById('numOfPositions').value = numOfPositions;
            document.getElementById('formTitle').textContent = 'Update Position';
            document.getElementById('submitBtn').name = 'update';
            document.getElementById('submitBtn').textContent = 'Update Position';
        }
    </script>
</body>
</html>