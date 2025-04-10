<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $grade = $_POST['grade'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $sql = "INSERT INTO Students VALUES ('$student_id', '$name', '$gender', '$grade', '$year', '$semester')";
    if ($conn->query($sql)) {
        header("Location: index.php?grade_section=$grade");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Add New Student</h1>
        <form method="POST">
            <label>Student ID: <input type="text" name="student_id" required></label><br>
            <label>Name: <input type="text" name="name" required></label><br>
            <label>Gender: <select name="gender"><option value="M">M</option><option value="F">F</option></select></label><br>
            <label>Grade/Section: <input type="text" name="grade" placeholder="e.g., 9A, 10B" required></label><br>
            <label>Academic Year: <input type="text" name="year" value="2016" required></label><br>
            <label>Semester: <input type="text" name="semester" value="I" required></label><br>
            <input type="submit" value="Add Student">
        </form>
    </div>
</body>
</html>