<?php

include ("inc/db.inc.php");
session_start();
include('inc/functions.inc.php');
checkLogin();

print_r($_POST);
$sAction=$_POST['action'];
$aSelected=$_POST['chkSelected'];

$db = fConnectDb();

foreach ($aSelected as $iSelected) {
    if (is_numeric($iSelected))
    {
        $sSQL="DELETE FROM env_reservations WHERE id=$iSelected";
        if ( mysqli_query($db, $sSQL) )
        {
            $iAffectedRows=$db->affected_rows;
            if (  $iAffectedRows > 0 )
            {
                printf("Project %d was removed<br>\n", $iSelected);
            } else {
                printf("Could not remove project #%d<br>\n", $iSelected);
            }
            echo "<hr>";
        }
    }
}
?>