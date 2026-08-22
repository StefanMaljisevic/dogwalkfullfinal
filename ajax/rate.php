<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../classes/Rating.php';
requireLogin();

if (!isPostRequest()) {
    jsonResponse(['success' => false, 'message' => 'Invalid request.'], 405);
}

$rating = (int) ($_POST['rating'] ?? 0);
$walkerId = (int) ($_POST['walker_id'] ?? 0);
$comment = cleanInput($_POST['comment'] ?? '');

if ($rating < 1 || $rating > 5 || $walkerId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Neispravna ocena.'], 400);
}

$ratingService = new Rating();
$result = $ratingService->addRating(currentUserId(), $walkerId, $rating, $comment);
jsonResponse($result, $result['success'] ? 200 : 400);
