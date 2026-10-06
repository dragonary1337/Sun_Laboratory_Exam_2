<?php
require_once 'student_common.php';

$students = fetchStudents();
$errors = [];
$notice = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
$formMode = 'list';
$editingStudent = null;
$formValues = ['name' => '', 'email' => '', 'age' => '', 'course' => ''];
$formState = $_SESSION['form_state'] ?? null;
unset($_SESSION['form_state']);

if (($_GET['action'] ?? '') === 'create') {
    $formMode = 'create';
    if (($formState['mode'] ?? '') === 'create') {
        $formValues = $formState['values'];
        $errors = $formState['errors'];
    }
} elseif (($_GET['action'] ?? '') === 'edit') {
    $studentId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    $editingStudent = $studentId !== false ? findStudent($studentId) : null;
    if ($editingStudent === null) {
        $notice = 'The student record could not be found.';
    } else {
        $formMode = 'update';
        $formValues = $editingStudent;
        if (($formState['mode'] ?? '') === 'update' && ($formState['id'] ?? null) === $studentId) {
            $formValues = $formState['values'];
            $errors = $formState['errors'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f4">
    <title>Student Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="index.php" aria-label="Student Management home">
            <span class="brand-mark" aria-hidden="true">S</span>
            <span class="brand-name">Student Management</span>
        </a>
        <span class="topbar-note">Academic records</span>
    </header>

    <main>
        <?php if ($formMode === 'list'): ?>
            <section aria-labelledby="pageTitle">
                <p class="eyebrow">Records</p>
                <h1 id="pageTitle">Student directory</h1>
                <p class="subtitle">Manage student information and course enrollment.</p>
                <?php if ($notice !== ''): ?>
                    <p class="notice" role="status"><?= escapeHtml($notice) ?></p>
                <?php endif; ?>
                <?php if (isset($errors['form'])): ?>
                    <p class="error-banner" role="alert"><?= escapeHtml($errors['form']) ?></p>
                <?php endif; ?>
                <div class="toolbar">
                    <div class="count"><strong><?= count($students) ?></strong> students</div>
                    <a class="button button-primary" href="index.php?action=create"><span aria-hidden="true">＋</span> Add student</a>
                </div>
                <div class="table-wrap">
                    <?php if ($students === []): ?>
                        <p class="empty-state">No students yet. Add a student to get started.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Age</th><th scope="col">Course</th><th scope="col">Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= escapeHtml($student['id']) ?></td>
                                        <td><span class="student-name"><?= escapeHtml($student['name']) ?></span></td>
                                        <td><span class="student-email"><?= escapeHtml($student['email']) ?></span></td>
                                        <td><?= escapeHtml($student['age']) ?></td>
                                        <td><span class="course-tag"><?= escapeHtml($student['course']) ?></span></td>
                                        <td>
                                            <div class="actions">
                                                <a class="action-button" href="index.php?action=edit&amp;id=<?= escapeHtml($student['id']) ?>">Edit</a>
                                                <form method="post" action="delete.php">
                                                    <input type="hidden" name="csrf_token" value="<?= escapeHtml($_SESSION['csrf_token']) ?>">
                                                    <input type="hidden" name="id" value="<?= escapeHtml($student['id']) ?>">
                                                    <button class="action-button delete" type="submit" onclick="return confirm('Delete this student?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>
        <?php else: ?>
            <section class="form-view" aria-labelledby="formTitle">
                <div class="form-heading">
                    <div>
                        <p class="eyebrow"><?= $formMode === 'update' ? 'Update record' : 'New record' ?></p>
                        <h1 id="formTitle"><?= $formMode === 'update' ? 'Edit student #' . escapeHtml($editingStudent['id']) : 'Add student' ?></h1>
                        <p class="subtitle">Enter the student's details below.</p>
                    </div>
                    <a class="button button-secondary" href="index.php">← Back</a>
                </div>
                <?php if (isset($errors['form'])): ?>
                    <p class="error-banner" role="alert"><?= escapeHtml($errors['form']) ?></p>
                <?php endif; ?>
                <form class="form-panel" method="post" action="<?= $formMode === 'update' ? 'update.php' : 'create.php' ?>">
                    <input type="hidden" name="csrf_token" value="<?= escapeHtml($_SESSION['csrf_token']) ?>">
                    <?php if ($formMode === 'update'): ?>
                        <input type="hidden" name="id" value="<?= escapeHtml($editingStudent['id']) ?>">
                    <?php endif; ?>
                    <div class="field-grid">
                        <div class="field field-full">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" value="<?= escapeHtml($formValues['name']) ?>" placeholder="e.g. Juan Dela Cruz" required maxlength="100" autocomplete="name">
                            <?php if (isset($errors['name'])): ?><span class="field-error"><?= escapeHtml($errors['name']) ?></span><?php endif; ?>
                        </div>
                        <div class="field field-full">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" value="<?= escapeHtml($formValues['email']) ?>" placeholder="name@university.edu.ph" required maxlength="150" autocomplete="email">
                            <?php if (isset($errors['email'])): ?><span class="field-error"><?= escapeHtml($errors['email']) ?></span><?php endif; ?>
                        </div>
                        <div class="field">
                            <label for="age">Age</label>
                            <input id="age" name="age" type="number" value="<?= escapeHtml($formValues['age']) ?>" min="1" max="120" placeholder="20" required>
                            <?php if (isset($errors['age'])): ?><span class="field-error"><?= escapeHtml($errors['age']) ?></span><?php endif; ?>
                        </div>
                        <div class="field">
                            <label for="course">Course</label>
                            <select id="course" name="course" required>
                                <option value="" disabled <?= $formValues['course'] === '' ? 'selected' : '' ?>>Select course</option>
                                <option value="BSIT" <?= $formValues['course'] === 'BSIT' ? 'selected' : '' ?>>BSIT</option>
                                <option value="BSCS" <?= $formValues['course'] === 'BSCS' ? 'selected' : '' ?>>BSCS</option>
                            </select>
                            <?php if (isset($errors['course'])): ?><span class="field-error"><?= escapeHtml($errors['course']) ?></span><?php endif; ?>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="button button-primary" type="submit"><?= $formMode === 'update' ? 'Update student' : 'Save student' ?></button>
                        <a class="button button-secondary" href="index.php">Cancel</a>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
