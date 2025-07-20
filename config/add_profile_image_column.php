<?php
require_once 'db.php';

// Add profile_photo column to students table
$sql = "ALTER TABLE students ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL";
if ($conn->query($sql) === TRUE) {
    echo "Profile photo column added successfully\n";
} else {
    echo "Error adding profile photo column: " . $conn->error . "\n";
}

// Add profile_photo column to admin table (if you have a separate admin table)
$sql = "ALTER TABLE admin ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL";
if ($conn->query($sql) === TRUE) {
    echo "Profile photo column added to admin table successfully\n";
} else {
    echo "Error adding profile photo column to admin table: " . $conn->error . "\n";
}
?>
