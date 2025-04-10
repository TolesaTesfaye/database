<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $subject_id = $_POST['subject_id'];
    $sql = "INSERT INTO Teacher_Subject (teacher_id, subject_id) VALUES ('$teacher_id', '$subject_id')";
    if ($conn->query($sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$teachers = $conn->query("SELECT teacher_id, teacher_name FROM Teachers");
$subjects = $conn->query("SELECT subject_id, subject_name FROM Subjects");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Teacher</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Assign Teacher to Subject</h1>
        <form method="POST">
            <label>Teacher: 
                <select name="teacher_id" required>
                    <?php while ($row = $teachers->fetch_assoc()) { echo "<option value='{$row['teacher_id']}'>{$row['teacher_name']}</option>"; } ?>
                </select>
            </label><br>
            <label>Subject: 
                <select name="subject_id" required>
                    <?php while ($row = $subjects->fetch_assoc()) { echo "<option value='{$row['subject_id']}'>{$row['subject_name']}</option>"; } ?>
                </select>
            </label><br>
            <input type="submit" value="Assign Teacher">
        </form>
    </div>
</body>
</html>