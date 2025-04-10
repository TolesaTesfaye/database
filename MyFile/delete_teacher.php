<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    // Delete related teacher-subject assignments and homeroom assignments first
    $conn->query("DELETE FROM Teacher_Subject WHERE teacher_id = '$teacher_id'");
    $conn->query("DELETE FROM Homeroom WHERE teacher_id = '$teacher_id'");
    // Now delete the teacher
    $sql = "DELETE FROM Teachers WHERE teacher_id = '$teacher_id'";
    if ($conn->query($sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$teachers = $conn->query("SELECT teacher_id, teacher_name FROM Teachers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Teacher</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Delete Teacher</h1>
        <form method="POST">
            <label>Teacher: 
                <select name="teacher_id" required>
                    <?php while ($row = $teachers->fetch_assoc()) { echo "<option value='{$row['teacher_id']}'>{$row['teacher_name']}</option>"; } ?>
                </select>
            </label><br>
            <input type="submit" value="Delete Teacher">
        </form>
    </div>
</body>
</html>