<?php
$conn = new mysqli("localhost", "root", "", "spare_parts_db");
if ($conn->connect_error) {
    $conn = null;
}
?>
