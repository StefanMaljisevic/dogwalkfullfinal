<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';

if (!isPostRequest()) {
    jsonResponse(['success' => false, 'message' => 'Invalid request.'], 405);
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || $password === '') {
    jsonResponse(['success' => false, 'message' => 'Unesi email i lozinku.'], 400);
}

$userService = new User();
$result = $userService->login($email, $password);
jsonResponse($result, $result['success'] ? 200 : 400);
