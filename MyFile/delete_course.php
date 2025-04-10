<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_id = $_POST['subject_id'];
    
    // Step 1: Delete related marks
    $delete_marks_sql = "DELETE FROM Marks WHERE subject_id = '$subject_id'";
    if ($conn->query($delete_marks_sql) === TRUE) {
        // Step 2: Delete related teacher assignments
        $delete_teacher_subject_sql = "DELETE FROM Teacher_Subject WHERE subject_id = '$subject_id'";
        if ($conn->query($delete_teacher_subject_sql) === TRUE) {
            // Step 3: Delete the subject
            $delete_subject_sql = "DELETE FROM Subjects WHERE subject_id = '$subject_id'";
            if ($conn->query($delete_subject_sql) === TRUE) {
                header("Location: index.php");
                exit;
            } else {
                echo "Error deleting subject: " . $conn->error;
            }
        } else {
            echo "Error deleting teacher assignments: " . $conn->error;
        }
    } else {
        echo "Error deleting marks: " . $conn->error;
    }
}

$subjects = $conn->query("SELECT subject_id, subject_name FROM Subjects");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Delete Course</h1>
        <form method="POST">
            <label>Subject: 
                <select name="subject_id" required>
                    <?php while ($row = $subjects->fetch_assoc()) { echo "<option value='{$row['subject_id']}'>{$row['subject_name']}</option>"; } ?>
                </select>
            </label><br>
            <input type="submit" value="Delete Course">
        </form>
    </div>
</body>
</html>