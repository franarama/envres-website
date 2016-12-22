<?php

function checkLogin() {
  if ( session_id() == '' ) {
    session_start();
  }
  if (isset($_SESSION['username']) &&  ! empty($_SESSION['username'])) {
    $sSessionUser= $_SESSION['username'];
  }
  else {
    //Not logged in? Display the login page and cease any other output
    readfile("login.html");
    exit();
  }
}

// function to check if a reservation is using a particular project
function projInUse($id,$db) {
  
    $query = "SELECT name FROM projects WHERE id=$id";
    
    if ($result = $db->query($query)) {
      while ($row=$result->fetch_assoc()) {
        
      }
        
    $sSQL = "SELECT * FROM env_reservations WHERE project_id='$id'";
    $res = mysqli_query($db, $sSQL) or die("Could not query databases: " . mysqli_error($db));
    
    if ($res->num_rows==0) {
        return false;
    }
    
    else {
        return true;
    }
  }
}

// function to check if a reservation is using a particular BCLC environment
function egEnvInUse($id,$db) {
  
    $query = "SELECT name FROM egaming_envs WHERE id=$id";
    
    if($result = $db ->query($query)) {
        while ($row=$result->fetch_assoc()) {
            $sEnv = $row['name'];
        }
    }
    
    $sSQL= "SELECT * FROM env_reservations WHERE env_id='$id'";
    $res = mysqli_query($db,$sSQL) or die("Could not query databases: " . mysqli_error($db));
    
    if ($res->num_rows==0) {
        return false;
    }
    
    else {
        return true;
    }
}
// function to check if a BCLC environment is using a particular vendor environment
function vendorEnvInUse($id,$db) {
  
    $query = "SELECT eg_id FROM vendorEnvs_egEnvs WHERE vendor_id=$id";
    
    if($result = $db -> query($query)) {
      while ($row = $result -> fetch_assoc()) {
            $iEgId = $row['eg_id'];
        }
    }
    
    $sSQL="SELECT * FROM env_reservations WHERE env_id='$iEgId'";
    $res = mysqli_query($db,$sSQL) or die("Could not query databases: " . mysqli_error($db));
    
    if ($res->num_rows==0) {
        return false;
    }
    else {
        return true;
    }
}

/********************This function returns all attached vendor environments in an array************************************/
function getVendorEnvs($db,$iEgEnvId) {
  $sSQL = "SELECT * FROM vendor_envs ve
           LEFT JOIN vendorEnvs_egEnvs veg ON veg.vendor_id=ve.id
           WHERE veg.eg_id=$iEgEnvId";
  $result = mysqli_query($db,$sSQL) or die("Could not query databases: " . mysqli_error($db));
  if ($result->num_rows==0) {
    return array();
  }
  else {
    $iIndex=0;$aToReturn=array();
    while ($row=$result->fetch_assoc()) {
        $sName = $row['name'];
        $aToReturn[$iIndex] = $sName;
        $iIndex++;
    }
    return $aToReturn;    
  }
}
/***********This function returns the names of integrations attached to an env res id as an array***********/
function getIntegrations($db,$resId) {
  $sSQL = "SELECT * FROM envRes_integrations ei
           LEFT JOIN integrations i ON i.id=ei.integration_id
           WHERE env_res_id=$resId";
  $res = mysqli_query($db,$sSQL) or die("Could not query databases! " . mysqli_error($db));
  if ($res->num_rows==0){
    return array();
  }
  else {
    $iIndex=0;$aToReturn=array();
    while ($row=$res->fetch_assoc()) {
        $sName = $row['name'];
        $aToReturn[$iIndex] = $sName;
        $iIndex++;
    }
    return $aToReturn;
  }
}

/***********This function returns the names of integrations attached to an env res id as an array***********/
function getIntegrationsUPDATED($db,$resId) {
  $sSQL = "SELECT * FROM envRes_integrations ei
           LEFT JOIN vendor_envs i ON i.id=ei.integration_id
           WHERE env_res_id=$resId";
  $res = mysqli_query($db,$sSQL) or die("Could not query databases! " . mysqli_error($db));
  if ($res->num_rows==0){
    return array();
  }
  else {
    $iIndex=0;$aToReturn=array();
    while ($row=$res->fetch_assoc()) {
        $sName = $row['name'];
        $aToReturn[$iIndex] = $sName;
        $iIndex++;
    }
    return $aToReturn;
  }
}

/***********This function checks if the integrations are in use***********/
function integrationInUse($db,$id) {
  
  $sSQL="SELECT * FROM envRes_integrations WHERE integration_id=$id";
  $res = mysqli_query($db,$sSQL) or die("Could not query databases: " . mysqli_error($db));
  
  if ($res->num_rows==0) {
      return false;
  }
  
  else {
      return true;
  }
}

/*************This function returns a formatted string of info based on a reservation ID**********************/
function getResInfo($db, $integrationId) {
    
    $sEgEnv;$sProjName;$sOwner;$sStart;$sEnd;$sDescription;$sToReturn;$sIntegrations="";$i;
    
    
    $sSQL = "SELECT er.id, er.env_id, er.project_id, er.owner, er.description, er.startdate, er.enddate, p.name AS proj_name, eg.name AS eg_name FROM env_reservations er 
             LEFT JOIN egaming_envs eg ON eg.id=er.env_id
             LEFT JOIN projects p ON p.id=er.project_id
             LEFT JOIN envRes_integrations eri ON eri.env_res_id=er.id
             WHERE eri.integration_id='$integrationId'";
             
    $res=mysqli_query($db,$sSQL) or die("Could not query databases: " . mysqli_error($db));
    
    while ($row = $res->fetch_assoc()) {
        $aIntegrations=getIntegrations($db,$row['id']);
        for($i=0;$i<(count($aIntegrations));$i++) {
          if ($i != (count($aIntegrations) - 1)) {
            $sIntegrations .= $aIntegrations[$i] . ", ";
          }
          else {
            $sIntegrations .= $aIntegrations[$i];
          }
        }
        return "---Corresponding reservation information--- \nEgaming environment: " . $row['eg_name'] . "\nProject: " . $row['proj_name'] . "\nIntegration(s): " . $sIntegrations . "\nOwner: " . $row['owner'] . "\n\n"
                . $row['description'] . "\n\nDuration: " . $row['startdate'] . ' to ' . $row['enddate'] . "\nClick to edit";
    }
}

/******************This function returns the attached reservation ID for an integration, 0 if none exists*********************/
function getResId($db,$iIntegrationId) {
  $iResId;
  $sSQL = "SELECT er.id FROM env_reservations er
           LEFT JOIN envRes_integrations eri ON eri.env_res_id=er.id
           WHERE eri.integration_id=$iIntegrationId";
  $result = mysqli_query($db,$sSQL) or die("Could not query databases: " . mysqli_error($db));
  if ($result->num_rows==0) {
    return 0;
  }
  else {
    while($row=$result->fetch_assoc()) {
      $iResId=$row['id'];
    }
  }
  return $iResId;
}

/*****************This function checks if OTHER res' are using an integration (excludes the given id)**********************/
function isInUseByOtherRes($db,$resIdToExclude,$integrationIdToCheck) {
  $sSQL = "SELECT * FROM envRes_integrations eri
           WHERE integration_id=$integrationIdToCheck AND env_res_id!=$resIdToExclude";
  $result = mysqli_query($db,$sSQL) or die("Could not query databases: " . mysqli_error($db));
  if ($result -> num_rows == 0) {
    return false;
  }
  else {
    return true;
  }
}


?>
