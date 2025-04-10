<?php
include 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $marks = $_POST['marks']; // Array of marks keyed by subject_id
    
    // Insert or update each mark
    foreach ($marks as $subject_id => $mark) {
        // Check if mark is provided and valid
        if (isset($mark) && $mark !== '' && is_numeric($mark) && $mark >= 0 && $mark <= 100) {
            $sql = "INSERT INTO Marks (student_id, subject_id, mark) VALUES ('$student_id', '$subject_id', '$mark') 
                    ON DUPLICATE KEY UPDATE mark = '$mark'";
            if (!$conn->query($sql)) {
                echo "Error inserting mark for subject $subject_id: " . $conn->error . "<br>";
            }
        }
    }
    
    // Get student's grade for redirect
    $grade_result = $conn->query("SELECT grade FROM Students WHERE student_id = '$student_id'");
    if ($grade_result && $grade_result->num_rows > 0) {
        $grade = $grade_result->fetch_assoc()['grade'];
        header("Location: index.php?grade_section=$grade");
    } else {
        echo "Error fetching grade: " . $conn->error . "<br>";
        header("Location: index.php"); // Fallback redirect
    }
    exit;
}

// Fetch students and subjects
$students = $conn->query("SELECT student_id, name FROM Students");
$subjects = $conn->query("SELECT subject_id, subject_name FROM Subjects");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Marks</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Add Marks for Student</h1>
        <form method="POST">
            <label>Student: 
                <select name="student_id" required>
                    <option value="">Select a student</option>
                    <?php while ($row = $students->fetch_assoc()) { echo "<option value='{$row['student_id']}'>{$row['name']} ({$row['student_id']})</option>"; } ?>
                </select>
            </label><br>
            <h3>Enter Marks</h3>
            <div class="marks-inputs">
                <?php 
                $subjects->data_seek(0); // Reset pointer for re-use
                while ($row = $subjects->fetch_assoc()) { ?>
                    <label><?php echo $row['subject_name']; ?>: 
                        <input type="number" name="marks[<?php echo $row['subject_id']; ?>]" min="0" max="100" placeholder="0-100">
                    </label><br>
                <?php } ?>
            </div>
            <input type="submit" value="Add Marks">
        </form>
    </div>
</body>
</html>