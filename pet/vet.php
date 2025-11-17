<?php
session_start();
include 'db_conn.php';

if(isset($_POST['add'])) {
    $vetFName = $_POST['vetFName'];
    $vetLName = $_POST['vetLName'];
    $vetAddress = $_POST['vetAddress'];
    $vetSpecial = $_POST['vetSpecial'];

    $sql = "INSERT INTO veterinarians (vetFName, vetLName, vetAddress, vetSpecial) 
            VALUES ('$vetFName', '$vetLName', '$vetAddress', '$vetSpecial')";
    
    if($conn->query($sql) === TRUE) {
        header("Location: Vet.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

if(isset($_POST['edit'])) {
    $id = $_POST['vetID'];
    $vetFName = $_POST['vetFName'];
    $vetLName = $_POST['vetLName'];
    $vetAddress = $_POST['vetAddress'];
    $vetSpecial = $_POST['vetSpecial'];
    
    $sql = "UPDATE veterinarians SET 
            vetFName = '$vetFName',
            vetLName = '$vetLName',
            vetAddress = '$vetAddress',
            vetSpecial = '$vetSpecial'
            WHERE vetID = '$id'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: Vet.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

if(isset($_POST['delete'])) {
    $id = $_POST['vetID'];
    
    $sql = "DELETE FROM veterinarians WHERE vetID = '$id'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: Vet.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

if(isset($_POST['search'])) {
    $searchName = $_POST['vetFName'];
    $query = "SELECT * FROM veterinarians WHERE vetFName LIKE '$searchName%'";
    $searchResult = $conn->query($query);
    
    if($searchResult->num_rows > 0) {
        $searchData = $searchResult->fetch_assoc();
        $_SESSION['search_data'] = $searchData;
    }
}

$query = "SELECT * FROM veterinarians";
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
        <h1>Veterinarian Information</h1>
        
        <form method="POST" action="#">
            <input type="hidden" id="vetID" name="vetID">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="vetFName">Veterinarian First Name:</label>
                        <input type="text" id="vetFName" name="vetFName">
                    </div>
                    <div class="form-group">
                        <label for="vetAddress">Veterinarian Address:</label>
                        <input type="text" id="vetAddress" name="vetAddress">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="vetLName">Veterinarian Last Name:</label>
                        <input type="text" id="vetLName" name="vetLName">
                    </div>
                    <div class="form-group">
                        <label for="vetSpecial">Veterinarian Specialization:</label>
                        <input type="text" id="vetSpecial" name="vetSpecial">
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
                    <th>Vet ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Address</th>
                    <th>Specialization</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['vetID'] . "</td>";
                        echo "<td>" . $row['vetFName'] . "</td>";
                        echo "<td>" . $row['vetLName'] . "</td>";
                        echo "<td>" . $row['vetAddress'] . "</td>";
                        echo "<td>" . $row['vetSpecial'] . "</td>";
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
                document.getElementById('vetID').value = data.vetID;
                document.getElementById('vetFName').value = data.vetFName;
                document.getElementById('vetLName').value = data.vetLName;
                document.getElementById('vetAddress').value = data.vetAddress;
                document.getElementById('vetSpecial').value = data.vetSpecial;
            }
        </script>
        <?php 
            unset($_SESSION['search_data']);
        endif; 
        ?>
    </div>
</body>
</html>