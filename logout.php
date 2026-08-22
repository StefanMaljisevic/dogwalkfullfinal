<?php
require_once __DIR__ . '/includes/auth.php';
session_destroy();
redirectTo('index.php');
