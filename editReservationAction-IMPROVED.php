<?php
include('inc/db.inc.php');
include('inc/functions.inc.php');

if (isset($_POST['action'])) {
    
    if ($db = fConnectDb()) {
        
            $sEnviro = $_POST['env'];
            $sProjName = $_POST['project'];
            $sOwner = $_POST['owner'];
            $sStartDate = $_POST['start'];
            $sEndDate = $_POST['end'];
            $sDescription = $_POST['description'];
            $id=$_GET['id'];
            $success=false;
            
            $iProjId;$iEnvId;

 
            
            /********STEP 1) CHECK IF THE RESERVATION HAS ATTACHED INTEGRATIONS************/
            
            $aIntegrations = getIntegrations($db, $id);
            if (count($aIntegrations) == 0) {
                $bHasIntegrations = false;
            }
            else {
                $bHasIntegrations = true;
            }
            
            /********STEP 2) IF THERE IS ATTACHED INTEGRATIONS, DELETE THEM*************/
            
            if ($bHasIntegrations) {
                $deleteQuery = "DELETE FROM envRes_integrations WHERE env_res_id=$id";
                if ($dResult = $db -> query($deleteQuery)) {
                    
                }
                else {
                    echo 'Error! Could not query database ' . mysqli_error($db);
                }
            }
            
            /**********STEP 3) CHECK IF THE SUBMITTED FORM HAS INTEGRATIONS CHECKED OFF********/
            /********************AND IF SO, CHECK IF ANY ARE IN USE***************************/
            
            if (isset($_POST['integrations'])) { //checked off integrations?
                foreach ($_POST['integrations'] as $sIntegration) {
                    $integrationIdQuery = "SELECT id FROM vendor_envs WHERE name='$sIntegration'";
                    if($iResult = $db->query($integrationIdQuery)) {
                        while($iRow = $iResult -> fetch_row()) {
                            $iIntegrationId = $iRow[0];
                        }                       
                    }
                    
                    //if (!integrationInUse($db,$iIntegrationId)) { //if the integration is not in use then add to the db
                        $isInUse = false;
                        $addSQL = "INSERT INTO envRes_integrations(env_res_id,integration_id) VALUES(?,?)";
                        if ($sth = $db->prepare($addSQL)) {
                            
                        }
                        else {
                            echo 'Query preparation failed!' . mysqli_error($db);
                        }
                        if ($sth->bind_param("ii",$id,$iIntegrationId)) {
                            if ($sth->execute()) {
                                $success=true;
                            }
                            else {
                                echo 'Failed! ' . mysqli_error();
                            }
                        }
                        else {
                            echo 'Error binding parameters! ' . mysqli_error($db);
                        }
                    //}
                    /*
                    else { //the integration is in use
                        $success=false;
                        echo '<script type="text/javascript">alert("Integration is in use!");</script>';
                        echo '<script type="text/javascript">close();</script>';
                        break;
                    }
                    */
                }
            }
            else {
                $success=true;
            }
            
            /**********STEP 4) UPDATE ALL EXCEPT THE INTEGRATIONS IF IT WAS A SUCCESS**********/
            
            if ($success) {
                // get project and env ids
                 $iProjIdQuery = "SELECT id FROM projects WHERE name= '$sProjName'";
                 $iEnvIdQuery = "SELECT id FROM egaming_envs WHERE name='$sEnviro'";
                 
                 if ($eResult = $db->query($iEnvIdQuery)) {
                     while ($envRow = $eResult -> fetch_row()) {
                         $iEnvId = $envRow[0];
                     }
                 }
                 
                 if ($pResult = $db->query($iProjIdQuery)) {
                     while ($projRow = $pResult -> fetch_row()) {
                         $iProjId = $projRow[0];
                     }
                 }
                 
                 $sSQL = "UPDATE env_reservations SET env_id=?, project_id=?, owner=?, description=?, startdate=?, enddate=? WHERE id=$id";
                 
                 if ($sth = $db->prepare($sSQL) ) {
                     
                 }
                 else {
                     die("Query preparation failed!" . mysqli_error($db));
                 }
                 
                 if ($sth->bind_param("iissss", $iEnvId, $iProjId, $sOwner, $sDescription, $sStartDate, $sEndDate)) {
                     
                     if ($sth->execute()) {
                            echo '<script type="text/javascript">alert("Reservation updated");</script>';
                            echo '<script type="text/javascript">close();</script>';
                    }
                     
                     else {
                         die("Entry failed to update!" . mysqli_error($db));
                     }
                 }
                 
                 else {
                     die("Error binding paramaters" . mysqli_error($db));
                 }

            }
            
    
  
    }
        
    else {
        die("Error connecting to MySQL" . mysqli_error($db));
    }
        
}
        
?>