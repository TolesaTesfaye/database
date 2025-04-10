<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_name = $_POST['subject_name'];
    $sql = "INSERT INTO Subjects (subject_name) VALUES ('$subject_name')";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error adding course: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Add New Course</h1>
        <form method="POST">
            <label>Subject Name: 
                <input type="text" name="subject_name" required>
            </label><br>
            <input type="submit" value="Add Course">
        </form>
    </div>
</body>
</html>