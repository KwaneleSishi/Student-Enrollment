<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../login.php");
    exit();
}

$instructor_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Verify the instructor owns the course
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = :course_id AND instructor_id = :instructor_id");
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
$stmt->execute();
if (!$stmt->fetch()) {
    header("Location: dashboard.php");
    exit();
}

// Fetch existing questions (if any)
$stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE course_id = :course_id ORDER BY question_number");
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$questions = array_column($questions, null, 'question_number');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        // Delete existing questions
        $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();

        // Insert new questions
        for ($i = 1; $i <= 8; $i++) {
            $question_text = $_POST["question_$i"];
            $choices = [
                $_POST["choice_{$i}_1"],
                $_POST["choice_{$i}_2"],
                $_POST["choice_{$i}_3"],
                $_POST["choice_{$i}_4"]
            ];
            $correct_choice = (int)$_POST["correct_$i"];

            $stmt = $conn->prepare("INSERT INTO quiz_questions (course_id, question_number, question_text, choice_1, choice_2, choice_3, choice_4, correct_choice) VALUES (:course_id, :question_number, :question_text, :choice_1, :choice_2, :choice_3, :choice_4, :correct_choice)");
            $stmt->execute([
                ':course_id' => $course_id,
                ':question_number' => $i,
                ':question_text' => $question_text,
                ':choice_1' => $choices[0],
                ':choice_2' => $choices[1],
                ':choice_3' => $choices[2],
                ':choice_4' => $choices[3],
                ':correct_choice' => $correct_choice
            ]);
        }

        $conn->commit();
        $success_message = "Quiz questions updated successfully!";
    } catch (Exception $e) {
        $conn->rollBack();
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Quiz | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .form-section {
            margin-bottom: 20px;
        }

        .form-section label {
            display: block;
            margin: 5px 0;
        }

        .form-section input[type="text"],
        .form-section select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>
    <div class="main-layout">
        <div class="content">
            <h1>Set Quiz Questions for Course ID: <?php echo $course_id; ?></h1>
            <?php if (isset($success_message)): ?>
                <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form method="POST">
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <div class="form-section">
                        <h3>Question <?php echo $i; ?></h3>
                        <label>Question Text:</label>
                        <input type="text" name="question_<?php echo $i; ?>" value="<?php echo isset($questions[$i]) ? htmlspecialchars($questions[$i]['question_text']) : ''; ?>" required>
                        <label>Choice 1:</label>
                        <input type="text" name="choice_<?php echo $i; ?>_1" value="<?php echo isset($questions[$i]) ? htmlspecialchars($questions[$i]['choice_1']) : ''; ?>" required>
                        <label>Choice 2:</label>
                        <input type="text" name="choice_<?php echo $i; ?>_2" value="<?php echo isset($questions[$i]) ? htmlspecialchars($questions[$i]['choice_2']) : ''; ?>" required>
                        <label>Choice 3:</label>
                        <input type="text" name="choice_<?php echo $i; ?>_3" value="<?php echo isset($questions[$i]) ? htmlspecialchars($questions[$i]['choice_3']) : ''; ?>" required>
                        <label>Choice 4:</label>
                        <input type="text" name="choice_<?php echo $i; ?>_4" value="<?php echo isset($questions[$i]) ? htmlspecialchars($questions[$i]['choice_4']) : ''; ?>" required>
                        <label>Correct Choice:</label>
                        <select name="correct_<?php echo $i; ?>" required>
                            <option value="1" <?php echo isset($questions[$i]) && $questions[$i]['correct_choice'] == 1 ? 'selected' : ''; ?>>Choice 1</option>
                            <option value="2" <?php echo isset($questions[$i]) && $questions[$i]['correct_choice'] == 2 ? 'selected' : ''; ?>>Choice 2</option>
                            <option value="3" <?php echo isset($questions[$i]) && $questions[$i]['correct_choice'] == 3 ? 'selected' : ''; ?>>Choice 3</option>
                            <option value="4" <?php echo isset($questions[$i]) && $questions[$i]['correct_choice'] == 4 ? 'selected' : ''; ?>>Choice 4</option>
                        </select>
                    </div>
                <?php endfor; ?>
                <button type="submit" class="btn btn-primary">Save Quiz</button>
            </form>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>

</html>