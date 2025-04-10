<?php
include 'db_connect.php';

// Get grade and section from GET parameters or default to 9A
$grade_section = isset($_GET['grade_section']) ? $_GET['grade_section'] : '9A';
$year = '2016';
$semester = 'I';

// Fetch all subjects dynamically
$subjects_sql = "SELECT subject_id, subject_name FROM Subjects";
$subjects_result = $conn->query($subjects_sql);
$subjects = [];
while ($row = $subjects_result->fetch_assoc()) {
    $subjects[$row['subject_id']] = $row['subject_name'];
}

// Build dynamic SQL for marks
$case_statements = '';
if (!empty($subjects)) {
    foreach ($subjects as $id => $name) {
        $case_statements .= "MAX(CASE WHEN m.subject_id = $id THEN m.mark ELSE NULL END) as " . strtolower($name) . ",";
    }
    $case_statements = rtrim($case_statements, ',');
} else {
    $case_statements = "'No subjects available' as no_subjects";
}

$sql = "SELECT s.student_id, s.name, s.gender, 
        $case_statements,
        SUM(m.mark) as total, " . (empty($subjects) ? "0" : "SUM(m.mark)/" . count($subjects)) . " as avg
        FROM Students s
        LEFT JOIN Marks m ON s.student_id = m.student_id
        WHERE s.grade = '$grade_section' AND s.academic_year = '$year' AND s.semester = '$semester'
        GROUP BY s.student_id, s.name, s.gender
        ORDER BY total DESC";
$result = $conn->query($sql);

$homeroom_sql = "SELECT t.teacher_name FROM Homeroom h JOIN Teachers t ON h.teacher_id = t.teacher_id WHERE h.grade = '$grade_section'";
$homeroom_result = $conn->query($homeroom_sql);
$homeroom_teacher = $homeroom_result->fetch_assoc()['teacher_name'] ?? 'Not Assigned';

// Fetch all grade/section options
$grades_sql = "SELECT DISTINCT grade FROM Students ORDER BY grade";
$grades_result = $conn->query($grades_sql);

// Calculate dynamic values for Notes
$subject_level_total = 100;
$overall_total = count($subjects) * $subject_level_total;
$pass_mark = 50;
?>

<!DOCTYPE html>
<html>
<head>
    <title>ABC High School Student Roster</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>ABC High School Student Roster</h1>
        <div class="header-info">
            <p>HOMEROOM TEACHER: <?php echo $homeroom_teacher; ?> | ACADEMIC YEAR: <?php echo $year; ?> | SEMESTER: <?php echo $semester; ?></p>
        </div>
        <div class="navbar">
            <a href="index.php">Home</a>
            <a href="add_student.php">Add Student</a>
            <a href="add_mark.php">Add Mark</a>
            <a href="add_course.php">Add Course</a>
            <a href="assign_teacher.php">Assign Teacher</a>
            <a href="delete_course.php">Delete Course</a>
            <a href="delete_teacher.php">Delete Teacher</a>
            <a href="delete_student.php">Delete Student</a>
            <form method="GET" class="grade-filter">
                <select name="grade_section" onchange="this.form.submit()">
                    <?php while ($grade = $grades_result->fetch_assoc()) { 
                        $selected = ($grade['grade'] == $grade_section) ? 'selected' : '';
                        echo "<option value='{$grade['grade']}' $selected>{$grade['grade']}</option>";
                    } ?>
                </select>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>STUDENT NAME</th>
                    <th>GENDER</th>
                    <th>ID</th>
                    <?php 
                    if (!empty($subjects)) {
                        foreach ($subjects as $subject) { 
                            echo "<th>" . strtoupper($subject) . "</th>"; 
                        }
                    } else {
                        echo "<th>NO SUBJECTS</th>";
                    }
                    ?>
                    <th>TOTAL</th>
                    <th>AVG</th>
                    <th>RANK</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                while ($row = $result->fetch_assoc()) {
                    $status = ($row['avg'] >= $pass_mark) ? 'PASS' : 'FAIL';
                    $status_class = ($row['avg'] >= $pass_mark) ? 'pass' : 'fail';
                    echo "<tr>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['gender']}</td>";
                    echo "<td>{$row['student_id']}</td>";
                    if (!empty($subjects)) {
                        foreach ($subjects as $id => $subject) {
                            echo "<td>" . ($row[strtolower($subject)] ?? '-') . "</td>";
                        }
                    } else {
                        echo "<td>-</td>";
                    }
                    echo "<td>" . ($row['total'] ?? 0) . "</td>";
                    echo "<td>" . (empty($subjects) ? '-' : number_format($row['avg'], 1)) . "</td>";
                    echo "<td>{$rank}</td>";
                    echo "<td class='$status_class'>" . (empty($subjects) ? '-' : $status) . "</td>";
                    echo "</tr>";
                    $rank++;
                }
                ?>
            </tbody>
        </table>
        <div class="notes">
            <h3>Performance Summary</h3>
            <div class="notes-table">
                <div class="notes-row">
                    <span class="notes-label">Subject Level Total</span>
                    <span class="notes-value"><?php echo $subject_level_total; ?></span>
                </div>
                <div class="notes-row">
                    <span class="notes-label">Overall Total</span>
                    <span class="notes-value"><?php echo $overall_total; ?></span>
                </div>
                <div class="notes-row">
                    <span class="notes-label">Pass Mark</span>
                    <span class="notes-value"><?php echo $pass_mark; ?>%</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>