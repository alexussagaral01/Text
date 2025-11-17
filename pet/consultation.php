<?php
session_start();
include 'db_conn.php';

if(isset($_POST['add'])) {
    $petID = $_POST['petID'];
    $vetID = $_POST['vetID'];
    $consultDate = $_POST['consultDate'];
    $diagnoses = $_POST['diagnoses'];
    $prescription = $_POST['prescription'];

    $sql = "INSERT INTO consultation (petID, vetID, consultDate, diagnoses, prescription) 
            VALUES ('$petID', '$vetID', '$consultDate', '$diagnoses', '$prescription')";
    
    if($conn->query($sql) === TRUE) {
        header("Location: consultation.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

if(isset($_POST['edit'])) {
    $id = $_POST['consultID'];
    $petID = $_POST['petID'];
    $vetID = $_POST['vetID'];
    $consultDate = $_POST['consultDate'];
    $diagnoses = $_POST['diagnoses'];
    $prescription = $_POST['prescription'];
    
    $sql = "UPDATE consultation SET 
            petID = '$petID',
            vetID = '$vetID',
            consultDate = '$consultDate',
            diagnoses = '$diagnoses',
            prescription = '$prescription'
            WHERE consultID = '$id'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: consultation.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

if(isset($_POST['delete'])) {
    $id = $_POST['consultID'];
    
    $sql = "DELETE FROM consultation WHERE consultID = '$id'";
    
    if($conn->query($sql) === TRUE) {
        header("Location: consultation.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

if(isset($_POST['search'])) {
    $searchDate = $_POST['consultDate'];
    $query = "SELECT consultation.*, pet.petName, veterinarians.vetFName 
              FROM consultation 
              JOIN pet ON consultation.petID = pet.petID 
              JOIN veterinarians ON consultation.vetID = veterinarians.vetID
              WHERE consultation.consultDate LIKE '$searchDate%'";
    $searchResult = $conn->query($query);
    
    if($searchResult->num_rows > 0) {
        $searchData = $searchResult->fetch_assoc();
        $_SESSION['search_data'] = $searchData;
    } else {
        echo "<script>alert('No record found!');</script>";
    }
}

// Fetch consultation records with pet and veterinarian details
$query = "SELECT consultation.*, pet.petName, veterinarians.vetFName 
          FROM consultation 
          JOIN pet ON consultation.petID = pet.petID 
          JOIN veterinarians ON consultation.vetID = veterinarians.vetID";
$result = $conn->query($query);

// Fetch pets for dropdown
$petQuery = "SELECT petID, petName FROM pet";
$petResult = $conn->query($petQuery);

// Fetch veterinarians for dropdown
$vetQuery = "SELECT vetID, vetFName FROM veterinarians";
$vetResult = $conn->query($vetQuery);
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
        <h1>Consultation Information</h1>
        
        <form method="POST" action="#">
            <input type="hidden" id="consultID" name="consultID">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="petID">Pet:</label>
                        <select id="petID" name="petID">
                            <option value="">Select Pet</option>
                            <?php
                            while($pet = $petResult->fetch_assoc()) {
                                echo "<option value='{$pet['petID']}'>{$pet['petName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="consultDate">Consultation Date:</label>
                        <input type="date" id="consultDate" name="consultDate">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="vetID">Veterinarian:</label>
                        <select id="vetID" name="vetID">
                            <option value="">Select Veterinarian</option>
                            <?php
                            while($vet = $vetResult->fetch_assoc()) {
                                echo "<option value='{$vet['vetID']}'>{$vet['vetFName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="diagnoses">Diagnoses:</label>
                        <input type="text" id="diagnoses" name="diagnoses">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="prescription">Prescription:</label>
                <input type="text" id="prescription" name="prescription">
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
                    <th>Consult ID</th>
                    <th>Pet Name</th>
                    <th>Veterinarian</th>
                    <th>Consultation Date</th>
                    <th>Diagnoses</th>
                    <th>Prescription</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['consultID'] . "</td>";
                        echo "<td>" . $row['petName'] . "</td>";
                        echo "<td>" . $row['vetFName'] . "</td>";
                        echo "<td>" . $row['consultDate'] . "</td>";
                        echo "<td>" . $row['diagnoses'] . "</td>";
                        echo "<td>" . $row['prescription'] . "</td>";
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
                document.getElementById('consultID').value = data.consultID;
                document.getElementById('petID').value = data.petID;
                document.getElementById('vetID').value = data.vetID;
                document.getElementById('consultDate').value = data.consultDate;
                document.getElementById('diagnoses').value = data.diagnoses;
                document.getElementById('prescription').value = data.prescription;
            }
        </script>
        <?php 
            unset($_SESSION['search_data']);
        endif; 
        ?>
    </div>
</body>
</html>
