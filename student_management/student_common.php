<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function getDatabaseConnection()
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;dbname=student_management;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    return $pdo;
}

function findStudent($id)
{
    $statement = getDatabaseConnection()->prepare('SELECT id, name, email, age, course FROM students WHERE id = :id');
    $statement->execute(['id' => $id]);
    $student = $statement->fetch();

    return $student === false ? null : $student;
}

function fetchStudents()
{
    return getDatabaseConnection()->query('SELECT id, name, email, age, course FROM students ORDER BY id')->fetchAll();
}

function hasValidCsrfToken()
{
    $token = $_POST['csrf_token'] ?? '';
    return is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function validateStudentInput($input)
{
    $name = is_string($input['name'] ?? null) ? trim($input['name']) : '';
    $email = is_string($input['email'] ?? null) ? trim($input['email']) : '';
    $ageInput = is_string($input['age'] ?? null) ? trim($input['age']) : '';
    $course = is_string($input['course'] ?? null) ? $input['course'] : '';
    $values = ['name' => $name, 'email' => $email, 'age' => $ageInput, 'course' => $course];
    $errors = [];

    if ($name === '') {
        $errors['name'] = 'The name field is required.';
    }
    if ($email === '') {
        $errors['email'] = 'The email field is required.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Enter a valid email address.';
    }

    $age = filter_var($ageInput, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 120],
    ]);
    if ($age === false) {
        $errors['age'] = 'Enter an age between 1 and 120.';
    }
    if (!in_array($course, ['BSIT', 'BSCS'], true)) {
        $errors['course'] = 'Select a course.';
    }

    return [$values, $errors, $age];
}

function rememberFormErrors($mode, $id, $values, $errors)
{
    $_SESSION['form_state'] = [
        'mode' => $mode,
        'id' => $id,
        'values' => $values,
        'errors' => $errors,
    ];
}

function redirectTo($location)
{
    header('Location: ' . $location);
    exit;
}