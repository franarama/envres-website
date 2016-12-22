<?php
session_start();
include('inc/functions.inc.php');
include('inc/db.inc.php');

function is_valid_chars($var){
  //Check that $var contains valid characters (letters, underscore, dash)
  return preg_match('/^[\w_\-]+$/', $var ) > 0;
}

  
  //print is_valid_chars($sEnv) ? "ok $sEnv" : "not ok $sEnv";
  //exit;
    if ($db = fConnectDb()) {
      $sSQL=  sprintf("SELECT eg.name AS env_name, eg.id AS env_id, v.name AS vendor_name " .
                      "FROM egaming_envs eg " .
                      "LEFT JOIN vendorEnvs_egEnvs ve ON ve.eg_id = eg.id " .
                      "LEFT JOIN vendor_envs v ON v.id=ve.vendor_id "
                     );
      
      if ( $res=mysqli_query($db,"$sSQL") ){
        $aOutput = Array();
        if ( $res->num_rows > 0 ){
          $indx=0;
          while ( $row = $res->fetch_assoc() ){
            //$sEnvironment = $row['environment'];
            //$sProjectName = $row['project'];
            //$sProjectDesc = $row['description'];
            //$sOwner =       $row['owner'];
            //$sStart =       $row['startdate'];
            //$sEnd =         $row['enddate'];
            //$sDuration=     $row['duration'];
            $aOutput[$indx++] = $row;
            
          }
        }
        echo json_encode($aOutput);
        
      } else {
        die("Could not query database: " . mysqli_error($db) );
      }
      
    } else {
      die("Cannot connect to database");
    }

?>