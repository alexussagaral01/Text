<?php
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Philippine National Election System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Philippine National Election System</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="positions.php">Positions</a>
        <a href="candidates.php">Candidates</a>
        <a href="voters.php">Voters</a>
        <a href="voting.php">Vote</a>
        <a href="results.php">Results</a>
        <a href="winners.php">Winners</a>
    </nav>
    <table>
        <thead>
            <tr>
                <th>Module</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Positions Management</td>
                <td>Add, update, and delete election positions</td>
                <td><a href="positions.php">Manage Positions</a></td>
            </tr>
            <tr>
                <td>Candidates Management</td>
                <td>Add, update, and delete candidates</td>
                <td><a href="candidates.php">Manage Candidates</a></td>
            </tr>
            <tr>
                <td>Voters Management</td>
                <td>Add, update, and manage voters</td>
                <td><a href="voters.php">Manage Voters</a></td>
            </tr>
            <tr>
                <td>Voting</td>
                <td>Cast your vote</td>
                <td><a href="voting.php">Vote Now</a></td>
            </tr>
            <tr>
                <td>Election Results</td>
                <td>View detailed results</td>
                <td><a href="results.php">View Results</a></td>
            </tr>
            <tr>
                <td>Election Winners</td>
                <td>See the winners</td>
                <td><a href="winners.php">View Winners</a></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
