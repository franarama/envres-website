<?php
  include('inc/db.inc.php');
  include('inc/functions.inc.php');
  //checkLogin();

?>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
    <link rel="icon" type="image/png"  href="Images/logo.png">
    
    <link rel="stylesheet" href="libs/jquery-ui-themes-1.11.4/themes/smoothness/jquery-ui.css">
    <script src="libs/jquery-1.11.3.min.js"></script>
    <script src="libs/jquery-ui-1.11.4/jquery-ui.min.js"></script>
    
    <title>Edit Reservations</title>
    
    <style>
      #addTable {
        margin-top: 0;
      }
      
      #dates input[type=text] {
        margin-left: 20px;
        text-align: center;
      }
    </style>
        
        <script type="text/javascript">
                
                /* for the calendar popups for textboxes with
                   IDs datepicker and datepicker2*/
                
                $(document).ready(function() {
                  $("#datepicker").datepicker({
                        dateFormat: "yymmdd",
                        numberOfMonths: 2,
                        onSelect: function(selected) {
                            $('#datepicker2').datepicker("option","minDate",selected)
                            }
                  });
                  $( "#datepicker2" ).datepicker({
                        dateFormat: "yymmdd",
                        numberOfMonths: 2,
                        onSelect: function(selected) {
                           $("#datepicker").datepicker("option","maxDate", selected)
                        }
                  });
                });
                
        </script>
        
</head>

<body>
    
    
    <header>
        <h1><img src="Images/logoreverse.png"></h1>
    </header>

    <ul id="nav" class="nav-drop">
        <li><a href="versions.php">Overview</a></li>
        <li><a href="reservations.php">View and Request Reservations</a></li>
        <li>Maintain Reservations for...
            <ul>
                <li><a href="maintain-EG.php">EGaming</a></li>
                <li><a href="maintain-CASINO.php">Casino</a></li>
                <li><a href="maintain-LOTTERY.php">Lottery</a></li>
            </ul>
        </li>
    </ul>
    
    <center>
    <h1 id="title">Edit Reservation</h1>
    <form id="reserve" method="POST" action="editReservationAction-IMPROVED.php?id=<?=$_GET['id']?>">
           <table id="addTable">
            <tr>

    
</body>
</html>
    
    <?php
    function fShowDialog() {
      
            if ($db = fConnectDb()) {
              
            $id=$_GET['id'];
            $sSQL = "SELECT e.id,e.owner,e.startdate,e.enddate,eg.name AS env_name,p.name AS project_name,e.description FROM env_reservations e " .
                    "LEFT JOIN egaming_envs eg ON e.env_id = eg.id LEFT JOIN projects p ON e.project_id = p.id WHERE e.id=$id";
                    
            $response=mysqli_query($db,$sSQL);
            
            $rowEnviro;$rowName;$rowOwner;$rowStart;$rowEnd;$rowDescription;
            
            if($response) {
              
                while($row=mysqli_fetch_array($response)) {
                  
                    $rowEnviro=$row['env_name'];
                    $rowName=$row['project_name'];
                    $rowOwner=$row['owner'];
                    $rowDescription=$row['description'];
                    
                    $rowStart=$row['startdate'];
                    $rowStart = str_replace('-','',$rowStart); //display the date in same format as in database
                    
                    $rowEnd=$row['enddate'];
                    $rowEnd = str_replace('-','',$rowEnd); //display the date in same format as in database
                }
            }
        ?>
        <tr>
            <td>
              EGaming environment*: 
            </td>
            
            <td>
              <select required="required" name="env" id="env">
          
            <?php
            
            if ($db = fConnectDb()) {
                
                $envName="SELECT name FROM egaming_envs ORDER BY name ASC";
                $envNameResult=mysqli_query($db,$envName);
                
                while($envNamerow = mysqli_fetch_array($envNameResult)) {
                    
                    $name=$envNamerow["name"];
                    
                    if ($name == $rowEnviro) {
                        echo "<option selected=\"selected\">$rowEnviro</option>";
                    }
                    
                    else {
                        echo "<option>$name</option>";
                    }
                }
            }
            
            ?>

        </select>
            </td>
        
        </tr>
        
        <tr>
            <td>Project*:</td>
            
            <td>
              <select required="required" name='project'>
                <?php
                if ($db = fConnectDb()) {
                    $projName="SELECT name FROM projects ORDER BY name ASC";
                    $projNameResult=mysqli_query($db,$projName) or die("Query to get data failed " . mysql_error());
                
                        while($projNamerow = mysqli_fetch_array($projNameResult)) {
                            $name=$projNamerow["name"];
                            if ($name == $rowName) {
                                echo "<option selected=\"selected\">$rowName</option>";
                            }
                            else {
                                echo "<option>$name</option>";
                            }
                }
                }
            
            ?>
        </select>
            </td>
            
        </tr>
        <br><br>
        
        <tr>
          <td>Description:</td>
          <td><textarea name='description'><?php echo $rowDescription?></textarea></td>
        </tr>
        <br><br>
        
        <tr>
            <td>Integration(s):</td>
            <td>
                <form name="form" id="form" method="post" action="CheckBox.jsp">
                    <div id="checkboxDiv" style="overflow: auto; width: 290px; height: 100px; border: 2px solid rgba(65,75,86,0.3); padding-left: 5px; padding-top: 3px; text-align:left;">
                    <?php
                        $iId=$_GET['id'];
                        $integrationName="SELECT name, i.id, IF(eri.env_res_id IS NOT NULL, true,false) AS is_active FROM vendor_envs i
                                          LEFT JOIN envRes_integrations eri ON eri.integration_id=i.id AND eri.env_res_id=$iId";
                                          
                        $integrationNameResult=mysqli_query($db,$integrationName) or die("Query to get data failed " . mysql_error());
                        $attachedIntegrations=getIntegrationsUPDATED($db,$_GET['id']);
                         while ($integrationNameRow = mysqli_fetch_array($integrationNameResult)) {
                            $name = $integrationNameRow["name"];
                            $iIntegrationId = $integrationNameRow['id'];
                            $bIsInUseByOtherRes = isInUseByOtherRes($db,$iId,$iIntegrationId);
                            if($integrationNameRow["is_active"] == 1) {
                                echo "<input type='checkbox' name='integrations[]' value='$name' checked='checked'>&nbsp;$name<br>";
                            }
                            /*
                            else if ($bIsInUseByOtherRes) {
                              echo "<p style='color:#959ca1;font-style:italic;' title='in use by a reservation'>
                                    <input type='checkbox' name='integrations[]' value='$name' disabled='disabled' id='inUseCheck' style='opacity:0.3;'
                                      title='in use by a reservation'>&nbsp;$name<br></p>";
                            }
                            */
                            
                            else {
                                echo "<input type='checkbox' name='integrations[]' value='$name'>&nbsp;$name<br>";
                            }
                        }                       
                    ?>
                    </div>
                </form>
            </td>
        </tr>
        <br><br>
        
        <tr>
            <td>Person to contact*:</td>
            <td>
              <input type="text" required="required" name='owner' value="<?php echo $rowOwner ?>">
            </td>
        </tr>
        
        <tr id="dates">
            <td>Start date*:<input type="text" required="required" name='start' placeholder="YYYYMMDD" style="width:150px;" id="datepicker" value="<?php echo $rowStart?>">&nbsp;
            <td>End date*:<input type="text" required="required" id="datepicker2" name='end' placeholder = "YYYYMMDD" style="width:150px;" id="end" value="<?php echo $rowEnd ?>">&nbsp;
        </tr>
        <tr style="border:none;" id="buttons">
            <td>
              <input type="submit" name="action" value="Update" id="submit"></input>
            </td>
            <td>
                <button type="reset" id="reset">Revert form</button>
            </td>
        </tr>
        
    </table>
           </form>
    <h3 id="note"><i>* indicates a required field</i></h3>
    </center>
    
    
    <?php
        }
    }
    fShowDialog();
    ?>
    
  
