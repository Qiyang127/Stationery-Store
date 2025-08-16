<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
  http_response_code(400);
  exit('Bad request');
}

$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['confirm_password'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || $password !== $confirm) {
  redirect('/register.php');
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
  redirect('/register.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$ins = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
$ins->execute([$name, $email, $hash]);
$_SESSION['user_id'] = (int)$pdo->lastInsertId();

redirect('/account.php');