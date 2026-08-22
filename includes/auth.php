<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/functions.php';

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirectTo('login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        redirectTo('index.php');
    }
}

function requireWalker(): void
{
    requireLogin();
    if (!isWalker()) {
        redirectTo('index.php');
    }
}
