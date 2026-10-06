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
$editingStudent = $studentId !== false ? findStudent($studentId) : null;
if ($editingStudent === null) {
    $_SESSION['flash'] = 'The student record could not be found.';
    redirectTo('index.php');
}

[$values, $errors, $age] = validateStudentInput($_POST);
if ($errors !== []) {
    rememberFormErrors('update', $studentId, $values, $errors);
    redirectTo('index.php?action=edit&id=' . $studentId);
}

$statement = getDatabaseConnection()->prepare(
    'UPDATE students SET name = :name, email = :email, age = :age, course = :course WHERE id = :id'
);
$statement->execute([
    'name' => $values['name'],
    'email' => $values['email'],
    'age' => $age,
    'course' => $values['course'],
    'id' => $studentId,
]);

$_SESSION['flash'] = 'Student updated.';
redirectTo('index.php');