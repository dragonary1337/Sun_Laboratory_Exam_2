<?php
require_once 'student_common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('index.php');
}
if (!hasValidCsrfToken()) {
    $_SESSION['flash'] = 'Your session could not be verified. Please try again.';
    redirectTo('index.php');
}

$studentId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($studentId !== false) {
    $statement = getDatabaseConnection()->prepare('DELETE FROM students WHERE id = :id');
    $statement->execute(['id' => $studentId]);
    if ($statement->rowCount() > 0) {
    $_SESSION['flash'] = 'Student deleted.';
    }
}

redirectTo('index.php');