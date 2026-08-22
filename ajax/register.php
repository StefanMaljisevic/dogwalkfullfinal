<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';

if (!isPostRequest()) {
    jsonResponse(['success' => false, 'message' => 'Invalid request.'], 405);
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (!$email || strlen($password) < 8 || $password !== $confirmPassword) {
    jsonResponse(['success' => false, 'message' => 'Proveri email i lozinke. Lozinka mora imati bar 8 karaktera.'], 400);
}

$userService = new User();
$result = $userService->register([
    'email' => $email,
    'password' => $password,
    'confirm_password' => $confirmPassword,
    'first_name' => $_POST['first_name'] ?? '',
    'last_name' => $_POST['last_name'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'address' => $_POST['address'] ?? '',
    'is_walker' => isset($_POST['is_walker']),
]);

jsonResponse($result, $result['success'] ? 200 : 400);
