<?php
header('Content-Type: application/json');
include 'config.php';

// Helper function to send JSON response
function send_response($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Handle sign up
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if ($password !== $confirmPassword) {
        send_response(false, 'Passwords do not match.');
    } elseif (strlen($password) < 6) {
        send_response(false, 'Password must be at least 6 characters.');
    } else {
        $check = $conn->prepare('SELECT * FROM users WHERE email = ?');
        $check->bind_param('s', $email);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            send_response(false, 'Email already exists.');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $name, $email, $role, $hash);
            if ($stmt->execute()) {
                send_response(true, 'Account created successfully!');
            } else {
                send_response(false, 'Error creating account.');
            }
        }
    }
}

// Handle sign in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            unset($user['password']); // Don't send password back
            send_response(true, 'Login successful.', $user);
        } else {
            send_response(false, 'Invalid password.');
        }
    } else {
        send_response(false, 'No account found with that email.');
    }
}

send_response(false, 'Invalid request.');
?>
