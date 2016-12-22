<?php
session_start();
include('../inc/functions.inc.php');
include('../inc/db.inc.php');
if ($db = fConnectDb()) {
  $sSQL="SELECT * FROM egaming_envs WHERE enabled=1 ORDER BY name ASC";
  $res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );
  
  $aOutput = Array();
  if ( $res->num_rows > 0 )
  {
    $indx=0;
    while ( $row = $res->fetch_assoc() )
    {
      $aOutput[$indx++] = $row;
    }
  }

  echo json_encode($aOutput);
  
} else {
  die("Couldn't connect to DB");
}
?>
