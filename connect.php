
<?php 
    $conn=mysqli_connect("localhost","root","dipper08062545","equipment-system") or die("เกิดข้อผิดพลาดเกิดขึ้น");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>