<?php
session_start();
session_destroy();
header( "refresh:1;url=reservations.php" );
echo "You are logged out. Returning to the main page\n";
exit;
?>