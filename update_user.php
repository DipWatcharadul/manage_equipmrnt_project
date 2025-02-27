<?php
session_start();
require_once 'connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_login'])) {
    header('Location: index.php');
    exit();
}

// Retrieve form data
$id_user = $_POST['id_user'];
$username = $_POST['username'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];
$email = $_POST['email'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];

// Fetch user data to check the current password
$sql = "SELECT password FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('ไม่พบข้อมูลผู้ใช้');</script>";
    header('refresh:1;url=manage_users.php');
    exit();
}

// Check if the current password matches the one in the database
if (!password_verify($current_password, $user['password'])) {
    echo "<script>alert('รหัสผ่านปัจจุบันไม่ถูกต้อง');</script>";
    header('refresh:1;url=manage_users.php');
    exit();
}

// Update password if a new password is provided
if (!empty($new_password) && $new_password === $confirm_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET username = ?, password = ?, email = ?, fname = ?, lname = ? WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $username, $hashed_password, $email, $firstname, $lastname, $id_user);
} else {
    // If no new password is provided, update other fields
    $sql = "UPDATE users SET username = ?, email = ?, fname = ?, lname = ? WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $username, $email, $firstname, $lastname, $id_user);
}

// Execute the query
if ($stmt->execute()) {
    echo "<script>alert('แก้ไขข้อมูลสำเร็จ');</script>";
    header('refresh:1;url=manage_users.php');
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
