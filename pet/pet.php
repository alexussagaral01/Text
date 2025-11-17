<?php
session_start();
include 'db_conn.php';

if(isset($_POST['add'])) {
    $petName = $_POST['petName'] ?? '';
    $petType = $_POST['petType'] ?? '';
    $petBreed = $_POST['petBreed'] ?? '';
    $petBdate = $_POST['petBdate'] ?? '';
    $petOwnerID = $_POST['petOwnerID'] ?? '';
    
    if(empty($petOwnerID)) {
    } else {
        $sql = "INSERT INTO pet (petName, petType, petBreed, petBdate, petOwnerID) 
                VALUES ('$petName', '$petType', '$petBreed', '$petBdate', '$petOwnerID')";

        if($conn->query($sql) === TRUE) {
            header("Location: pet.php");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if(isset($_POST['edit'])) {
    $id = $_POST['petID'];
    $petName = $_POST['petName'];
    $petType = $_POST['petType'];
    $petBreed = $_POST['petBreed'];
    $petBdate = $_POST['petBdate'];
    $petOwnerID = $_POST['petOwnerID'];
    
    $sql = "UPDATE pet SET 
            petName = '$petName',
            petType = '$petType',
            petBreed = '$petBreed',
            petBdate = '$petBdate',
            petOwnerID = '$petOwnerID'
            WHERE petID = '$id'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: pet.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

if(isset($_POST['delete'])) {
    $id = $_POST['petID'];
    
    $sql = "DELETE FROM pet WHERE petID = '$id'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: pet.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

if(isset($_POST['search'])) {
    $searchName = $_POST['petName'];
    $query = "SELECT pet.*, petowner.petOwnerFName as ownerName 
              FROM pet 
              JOIN petowner ON pet.petOwnerID = petowner.petOwnerID 
              WHERE pet.petName LIKE '$searchName%'";
    $searchResult = $conn->query($query);
    
    if($searchResult->num_rows > 0) {
        $searchData = $searchResult->fetch_assoc();
        $_SESSION['search_data'] = $searchData;
    }
}

// Modify the main select query to JOIN with petowner
$query = "SELECT pet.*, petowner.petOwnerFName as ownerName 
          FROM pet 
          JOIN petowner ON pet.petOwnerID = petowner.petOwnerID";
$result = $conn->query($query);

// Fetch pet owners for dropdown
$ownerQuery = "SELECT petOwnerID, petOwnerFName as ownerName FROM petowner";
$ownerResult = $conn->query($ownerQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinary Clinic Consultation System</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div>
        <h1>Veterinarian Information</h1>
        
        <form method="POST">
            <input type="hidden" id="petID" name="petID">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="petName">Pet Name:</label>
                        <input type="text" id="petName" name="petName">
                    </div>
                    <div class="form-group">
                        <label for="petType">Pet Type:</label>
                        <input type="text" id="petType" name="petType">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="petBreed">Pet Breed:</label>
                        <input type="text" id="petBreed" name="petBreed">
                    </div>
                    <div class="form-group">
                        <label for="petOwnerID">Pet Owner:</label>
                        <select id="petOwnerID" name="petOwnerID">
                            <option value="">Select Owner</option>
                            <?php
                            while($owner = $ownerResult->fetch_assoc()) {
                                echo "<option value='{$owner['petOwnerID']}'>{$owner['ownerName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="petBdate">Pet Birth Date:</label>
                <input type="date" id="petBdate" name="petBdate">
            </div>
            <div class="button-group">
                <button type="submit" name="add" class="btn-add">ADD</button>
                <button type="submit" name="edit" class="btn-edit">EDIT</button>
                <button type="submit" name="search" class="btn-search">SEARCH</button>
                <button type="submit" name="delete" class="btn-delete">DELETE</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Pet ID</th>
                    <th>Pet Name</th>
                    <th>Pet Owner</th>
                    <th>Pet Type</th>
                    <th>Pet Breed</th>
                    <th>Pet BDate</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['petID'] . "</td>";
                        echo "<td>" . $row['petName'] . "</td>";
                        echo "<td>" . $row['ownerName'] . "</td>";
                        echo "<td>" . $row['petType'] . "</td>";
                        echo "<td>" . $row['petBreed'] . "</td>";
                        echo "<td>" . $row['petBdate'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
        
        <?php if(isset($_SESSION['search_data'])): ?>
        <script>
            window.onload = function() {
                const data = <?php echo json_encode($_SESSION['search_data']); ?>;
                document.getElementById('petID').value = data.petID;
                document.getElementById('petName').value = data.petName;
                document.getElementById('petType').value = data.petType;
                document.getElementById('petBreed').value = data.petBreed;
                document.getElementById('petOwnerID').value = data.petOwnerID;
                document.getElementById('petBdate').value = data.petBdate;
            }
        </script>
        <?php 
            unset($_SESSION['search_data']);
        endif; 
        ?>
    </div>
</body>
</html>
