<?php
session_start();
include 'db.php';

if(isset($_POST['add'])) {
    $candFName = $_POST['candFName'];
    $candLName = $_POST['candLName'];
    $posID = $_POST['posID'];
    $candStat = 'Active';

    $sql = "INSERT INTO candidates (candFName, candLName, posID, candStat) 
            VALUES ('$candFName', '$candLName', '$posID', '$candStat')";
    
    if($conn->query($sql) === TRUE) {
        header("Location: candidates.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

if(isset($_POST['update'])) {
    $candID = $_POST['candID'];
    $candFName = $_POST['candFName'];
    $candLName = $_POST['candLName'];
    $posID = $_POST['posID'];
    
    $sql = "UPDATE candidates SET 
            candFName = '$candFName',
            candLName = '$candLName',
            posID = '$posID'
            WHERE candID = '$candID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: candidates.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

if(isset($_GET['toggle'])) {
    $candID = $_GET['toggle'];
    $currentStatus = $_GET['status'];
    
    $newStatus = ($currentStatus == 'Active') ? 'Inactive' : 'Active';
    
    $sql = "UPDATE candidates SET candStat = '$newStatus' WHERE candID = '$candID'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: candidates.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$posSql = "SELECT posID, posName FROM positions WHERE posStat = 'Active'";
$posResult = $conn->query($posSql);
$positions = $posResult->fetch_all(MYSQLI_ASSOC);

$candSql = "SELECT candID, candFName, candLName, posID, candStat FROM candidates";
$candResult = $conn->query($candSql);
$candidates = $candResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Candidates Management</h1>
    <nav>
        <a href="positions.php">Positions</a>
        <a href="candidates.php">Candidates</a>
        <a href="voters.php">Voters</a>
        <a href="voting.php">Vote</a>
        <a href="results.php">Results</a>
        <a href="winners.php">Winners</a>
    </nav>

    <form method="POST">
        <h3 id="formTitle">Add New Candidate</h3>
        
        <input type="hidden" name="candID" id="candID">
        
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label>Candidate First Name:</label>
                    <input type="text" name="candFName" id="candFName" required>
                </div>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label>Candidate Last Name:</label>
                    <input type="text" name="candLName" id="candLName" required>
                </div>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label>Position:</label>
                    <select name="posID" id="posID" required>
                        <option value="">Select Position</option>
                        <?php foreach ($positions as $pos): ?>
                            <option value="<?php echo $pos['posID']; ?>"><?php echo $pos['posName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <button type="submit" name="add" id="submitBtn">Add Candidate</button>
    </form>

    <h3>List of Candidates</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Position</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $c): 
                $id = $c['candID'];
                $fname = $c['candFName'];
                $lname = $c['candLName'];
                $posid = $c['posID'];
                $stat = $c['candStat'];
            ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= $fname ?></td>
                <td><?= $lname ?></td>
                <td><?= $c['posName'] ?></td>
                <td><?= $stat ?></td>
                <td>
                    <button class="btn-update" onclick="editCandidate(<?= $id ?>, '<?= $fname ?>', '<?= $lname ?>', <?= $posid ?>, '<?= $stat ?>')">Update</button>
                    
                    <?php if($stat == 'Active'): ?>
                        <button class="btn-toggle-inactive" onclick="location='?toggle=<?= $id ?>&status=<?= $stat ?>'">Deactivate</button>
                    <?php else: ?>
                        <button class="btn-toggle-active" onclick="location='?toggle=<?= $id ?>&status=<?= $stat ?>'">Activate</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function editCandidate(candID, candFName, candLName, posID, candStat) {
            document.getElementById('candID').value = candID;
            document.getElementById('candFName').value = candFName;
            document.getElementById('candLName').value = candLName;
            document.getElementById('posID').value = posID;
            document.getElementById('formTitle').textContent = 'Update Candidate';
            document.getElementById('submitBtn').name = 'update';
            document.getElementById('submitBtn').textContent = 'Update Candidate';
        }
    </script>
</body>
</html>
