<?php
session_start();
include 'db_conn.php';

if(isset($_POST['add'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $birthDate = $_POST['birthDate'];
    $telNo = $_POST['telNo'];

    $sql = "INSERT INTO petowner (petOwnerFName, petOwnerLName, petOwnerBDate, petOwnerTelNo) 
            VALUES ('$firstName', '$lastName', '$birthDate', '$telNo')";
    
    if($conn->query($sql) === TRUE) {
        header("Location: PetOwner.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

if(isset($_POST['edit'])) {
    $id = $_POST['petOwnerID'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $birthDate = $_POST['birthDate'];
    $telNo = $_POST['telNo'];
    
    $sql = "UPDATE petowner SET 
            petOwnerFName = '$firstName',
            petOwnerLName = '$lastName',
            petOwnerBDate = '$birthDate',
            petOwnerTelNo = '$telNo'
            WHERE petOwnerID = '$id'";
    
    if($conn->query($sql) === TRUE) {
        echo "<script>alert('Record updated successfully!');</script>";
        header("Location: PetOwner.php");
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }
}

if(isset($_POST['delete'])) {
    $id = $_POST['petOwnerID'];
    $firstName = $_POST['firstName'];
    
    // Instead of actually deleting, we'll move the record to end of table and mark it by adding '(DELETED)' to the name
    $sql = "UPDATE petowner SET 
            petOwnerFName = CONCAT(petOwnerFName, ' (DELETED)'),
            petOwnerLName = CONCAT(petOwnerLName, ' (DELETED)')
            WHERE petOwnerID = '$id'";
    
    if($conn->query($sql) === TRUE) {
        echo "<script>alert('Record deleted successfully!');</script>";
        header("Location: PetOwner.php");
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
    }
}

// Modify the search query to exclude deleted records
if(isset($_POST['search'])) {
    $searchName = $_POST['firstName'];
    $query = "SELECT * FROM petowner WHERE petOwnerFName LIKE '$searchName%' AND petOwnerFName NOT LIKE '%(DELETED)%'";
    $searchResult = $conn->query($query);
    
    if($searchResult->num_rows > 0) {
        $searchData = $searchResult->fetch_assoc();
        $_SESSION['search_data'] = $searchData;
    } else {
        echo "<script>alert('No record found!');</script>";
    }
}

// Modify the main select query to exclude deleted records
$query = "SELECT * FROM petowner WHERE petOwnerFName NOT LIKE '%(DELETED)%'";
$result = $conn->query($query);
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
        <h1>Pet Owner Information</h1>
        
        <form method="POST" action="#">
            <input type="hidden" id="petOwnerID" name="petOwnerID">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="firstName" class="form-label">Owner First Name:</label>
                        <input type="text" id="firstName" name="firstName">
                    </div>
                    <div class="form-group">
                        <label for="birthDate" class="form-label">Owner Birth Date:</label>
                        <input type="date" id="birthDate" name="birthDate">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="lastName" class="form-label">Owner Last Name:</label>
                        <input type="text" id="lastName" name="lastName">
                    </div>
                    <div class="form-group">
                        <label for="telNo" class="form-label">Owner Tel No.:</label>
                        <input type="tel" id="telNo" name="telNo">
                    </div>
                </div>
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
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Birth Date</th>
                    <th>Tel No.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['petOwnerID'] . "</td>";
                        echo "<td>" . $row['petOwnerFName'] . "</td>";
                        echo "<td>" . $row['petOwnerLName'] . "</td>";
                        echo "<td>" . $row['petOwnerBDate'] . "</td>";
                        echo "<td>" . $row['petOwnerTelNo'] . "</td>";
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
                document.getElementById('petOwnerID').value = data.petOwnerID;
                document.getElementById('firstName').value = data.petOwnerFName;
                document.getElementById('lastName').value = data.petOwnerLName;
                document.getElementById('birthDate').value = data.petOwnerBDate;
                document.getElementById('telNo').value = data.petOwnerTelNo;
            }
        </script>
        <?php 
            unset($_SESSION['search_data']);
        endif; 
        ?>
    </div>
</body>
</html>