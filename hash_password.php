<?php
$staff_password = 'staff123'; 
$admin_password = 'admin123'; 

echo "Staff Password Hash: " . password_hash($staff_password, PASSWORD_DEFAULT) . "\n";
echo "Admin Password Hash: " . password_hash($admin_password, PASSWORD_DEFAULT);
?>