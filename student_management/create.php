<?php
require_once 'student_common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('index.php?action=create');
}
if (!hasValidCsrfToken()) {
    $_SESSION['flash'] = 'Your session could not be verified. Please try again.';
    redirectTo('index.php?action=create');
}

[$values, $errors, $age] = validateStudentInput($_POST);
if ($errors !== []) {
    rememberFormErrors('create', null, $values, $errors);
    redirectTo('index.php?action=create');
}

$statement = getDatabaseConnection()->prepare(
    'INSERT INTO students (name, email, age, course) VALUES (:name, :email, :age, :course)'
);
$statement->execute([
    'name' => $values['name'],
    'email' => $values['email'],
    'age' => $age,
    'course' => $values['course'],
]);
$_SESSION['flash'] = 'Student added.';

redirectTo('index.php');