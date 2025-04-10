<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    
    // First, delete related marks to satisfy the foreign key constraint
    $delete_marks_sql = "DELETE FROM Marks WHERE student_id = '$student_id'";
    if ($conn->query($delete_marks_sql) === TRUE) {
        // Now delete the student
        $delete_student_sql = "DELETE FROM Students WHERE student_id = '$student_id'";
        if ($conn->query($delete_student_sql) === TRUE) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error deleting student: " . $conn->error;
        }
    } else {
        echo "Error deleting marks: " . $conn->error;
    }
}

$students = $conn->query("SELECT student_id, name FROM Students");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Delete Student</h1>
        <form method="POST">
            <label>Student: 
                <select name="student_id" required>
                    <?php while ($row = $students->fetch_assoc()) { echo "<option value='{$row['student_id']}'>{$row['name']} ({$row['student_id']})</option>"; } ?>
                </select>
            </label><br>
            <input type="submit" value="Delete Student">
        </form>
    </div>
</body>
</html>