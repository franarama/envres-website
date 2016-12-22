<?php
session_start();
include('../inc/functions.inc.php');
include('../inc/db.inc.php');

function is_date($var){
  //Return true if $var matches pattern ####-##-##
  return preg_match('/^\d{4}-\d{2}-\d{2}$/', $var ) > 0;
}

function is_valid_chars($var){
  //Check that $var contains valid characters (letters, underscore, dash)
  return preg_match('/^[\w_\-]+$/', $var ) > 0;
}

//Check that we got an "env" variable as well as a "start" and "end" variable (that are dates)
if ( isset($_REQUEST['env']) &&  isset($_REQUEST['start_dt']) && isset($_REQUEST['end_dt']) ){
  $sEnv =     $_REQUEST['env'];
  $sStart =   $_REQUEST['start_dt'];
  $sEnd =     $_REQUEST['end_dt'];
  
  //print is_valid_chars($sEnv) ? "ok $sEnv" : "not ok $sEnv";
  //exit;
  if ( is_valid_chars($sEnv) && is_date($sStart) && is_date($sEnd) ) {
    if ($db = fConnectDb()) {
      $sSQL=  sprintf("SELECT e.id,e.owner,e.startdate,e.enddate,eg.name AS env_name,p.name AS project_name,p.description FROM env_reservations e " .
                       "LEFT JOIN egaming_envs eg ON e.env_id = eg.id LEFT JOIN projects p ON e.project_id = p.id "
                      . "UNIX_TIMESTAMP(e.startdate) AS start_epoch,UNIX_TIMESTAMP(e.enddate) AS end_epoch,(DATEDIFF(enddate,startdate)+1) AS duration "
                      . "WHERE env_name LIKE '%s'AND e.startdate >= '%s' AND e.startdate <= '%s'"  
                      . "ORDER by startdate ASC, enddate ASC",
                      $sEnv,
                      $sStart,
                      $sEnd
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

  } else {
    die("Invalid parameters");
  }
} else {
  die("Missing parameters");
}
?>
