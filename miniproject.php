<!DOCTYPE html>
<html>
<head>
    <title>Student Marks Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 10px gray;
        }
        input[type=text], input[type=number] {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type=submit] {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background: #0056b3;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background: #e9f7ef;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🎓 Student Marks Calculator</h2>
    <form method="POST">
        <label>Student Name:</label>
        <input type="text" name="name" required>

        <label>Math Marks:</label>
        <input type="number" name="math" min="0" max="100" required>

        <label>Science Marks:</label>
        <input type="number" name="science" min="0" max="100" required>

        <label>English Marks:</label>
        <input type="number" name="english" min="0" max="100" required>

        <input type="submit" name="calculate" value="Calculate Result">
    </form>

<?php
// --- PHP Logic starts here ---
if (isset($_POST['calculate'])) {

    // 🧾 Get data from form
    $name = $_POST['name'];
    $marks = [
        "Math" => $_POST['math'],
        "Science" => $_POST['science'],
        "English" => $_POST['english']
    ];

    // 🧮 User-defined functions
    function calculateTotal($marks) {
        return array_sum($marks); // built-in
    }

    function calculatePercentage($total, $maxMarks) {
        return round(($total / $maxMarks) * 100, 2); // built-in
    }

    function getGrade($percentage) {
        if ($percentage >= 90) return "A+";
        elseif ($percentage >= 75) return "A";
        elseif ($percentage >= 60) return "B";
        elseif ($percentage >= 40) return "C";
        else return "Fail";
    }

    // 🔢 Calculation
    $total = calculateTotal($marks);
    $maxMarks = count($marks) * 100; // built-in
    $percentage = calculatePercentage($total, $maxMarks);
    $grade = getGrade($percentage);

    // 🖨️ Display Result
    echo "<div class='result'>";
    echo "<h3>Student Report</h3>";
    echo "<b>Name:</b> $name <br>";
    foreach ($marks as $subject => $score) {
        echo "$subject: $score <br>";
    }
    echo "<hr>";
    echo "Total Marks: $total / $maxMarks <br>";
    echo "Percentage: $percentage% <br>";
    echo "Grade: <b>$grade</b>";
    echo "</div>";
}
?>
</div>

</body>
</html>
