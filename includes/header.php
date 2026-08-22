<?php
require_once __DIR__ . '/auth.php';
?>
<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dog Walk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php">Dog Walk</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php">Početna</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/walkers.php">Šetači</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/profile.php">Profil</a></li>
                    <?php if (isWalker()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/walker_profile.php">Moj oglas</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/walker_requests.php">Zahtevi</a></li>
                    <?php endif; ?>
                    <?php if (isAdmin()): ?><li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/users.php">Admin</a></li><?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/logout.php">Odjava</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Prijava</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/register.php">Registracija</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
