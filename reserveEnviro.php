<?php
session_start();
include('inc/db.inc.php');
include('inc/functions.inc.php');
function fShowDialog() {
	if($db=fConnectDb()) {

    ?>

    
    <form id="reserve" method="post">
           <table id="reserveTable">
        <tr>
            <td>
        Environment*:
            </td>
            <td>
        <select required="required" name="env" id="env">
            <?php
                $envName="SELECT name FROM egamingEnvs WHERE enabled=1 ORDER BY name ASC";
                $envNameResult=mysqli_query($db,$envName) or die("Query to get data failed " . mysql_error());
                
                while($envNamerow = mysqli_fetch_array($envNameResult)) {
                    $name=$envNamerow["name"];
                    echo "<option>$name</option>";
                }
            
            ?>

    
        </select>
            </td>            
        </tr>

    
            <tr>
            <td>
        Project*:
            </td>
            <td>
        <select required="required" name='project'>
                <?php
                if (! $db->connect_error) {
                    $projName="SELECT name FROM projects ORDER BY name ASC";
                    $projNameResult=mysqli_query($db,$projName) or die("Query to get data failed " . mysql_error());
                
                        while($projNamerow = mysqli_fetch_array($projNameResult)) {
                            $name=$projNamerow["name"];
                            echo "<option>$name</option>";
                }
            }
            
            ?>
        </select>
            </td>
            
        </tr>
        <br><br>

        <tr>
            <td>Integration(s):</td>
            <td>
                <form name="form" id="form" method="post" action="CheckBox.jsp">
                    <div id="checkboxDiv" style="overflow: auto; width: 340px; height: 100px; border: 2px solid rgba(65,75,86,0.3); padding-left: 5px; padding-top:5px;text-align:left;">
                    <?php
                    
                    if ($db=fConnectDb()) {

                        $integrationName="SELECT name FROM vendorEnvs";
                        $integrationNameResult=mysqli_query($db,$integrationName) or die("Query to get data failed " . mysql_error());
                         while ($integrationNameRow = mysqli_fetch_array($integrationNameResult)) {
                            $name = $integrationNameRow["name"];
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
			<td>
				Description:
			</td>
			<td><textarea name="description" style='padding-left:7px;padding-top:7px;'></textarea></td>
		</tr>
		<br><br>
		
        <tr>
            <td>
				Person to contact*:
            </td>
            <td>
				<input type="text" required="required" name='owner' style='padding-left:7px;'>
            </td>
        </tr>
        
        <tr id="dates">
            
            <td>Start date*:<input type="text" required="required" name='start' placeholder="YYYYMMDD" style="width:150px;" id="datepicker">&nbsp;
            <td>End date*:<input type="text" required="required" id="datepicker2" name='end' placeholder = "YYYYMMDD" style="width:150px;" id="end">&nbsp
        
        </tr>
        <tr style="border:none;" id="buttons">
            <td>
            <input type="submit" name="submit" value="Submit" id="submit" style='outline:0;'></input>
            </td>
            <td>
                <button type="reset" style='outline:0;'>Clear form</button>
            </td>
        </tr>
        
    </table>
           </form>
    <h3 id="note"><i>* indicates a required field</i></h3>
    </center>

           
           <?php
           }
}
?>

<html>
    <head>
        
        <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
        <link rel="icon" type="image/png"  href="Images/logo.png">
		<link rel="stylesheet" href="libs/jquery-ui-themes-1.11.4/themes/smoothness/jquery-ui.css">
		<script src="libs/jquery-1.11.3.min.js"></script>
		<script src="libs/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		
        <title>Reserve an Environment</title>
        
        <style>
        #nav {
            padding-left:190px;
        }
        #nav a {
            margin-left:10%;
            margin-right:10%;
        }
        #reserveTable {
			font-family: Arial;
			border-collapse: collapse;
			width: 30%;
			color: #414b56;
			font-size: 15px;
			margin-top: -4%;
			border-top: 2px dotted #959ca1;
			border-bottom: 2px dotted #959ca1;
			height: 80%;
			margin-right: 0;
        }
        h2 {
			margin-top: 40px;
			text-align: center;
			color: #414b56;
			font-weight: normal;
			letter-spacing: 0.1em;
			margin-right: 7%;
			margin-bottom: 30px;
			word-spacing: 3px;
        }
        #reserveTable td {
            padding:10px;
        }
        #reserveTable td{
            padding-top:40px;
        }
        #reserve #reserveTable #buttons {
            text-align: center;
        }
        #reserve #reserveTable td:first-child, #reserve #reserveTable #dates td:last-child {
            color: #76b900;
            letter-spacing: 0.1em;
        }
        #reserve #reserveTable select, #reserve #reserveTable textarea, #reserve #reserveTable input[type=text] {
            width: 90%;
            margin-left: 40px;
            border:2px solid rgba(65,75,86,0.3);
            border-radius: 3px;
        }
        #reserve #reserveTable select,  #reserve #reserveTable select option{
            background-color: white;
            height: 50px;
            color: #414b56;
            letter-spacing: 0.1em;
            width: 90%;
			padding-left: 7px;
        }
        #reserveTable textarea {
            color: #414b56;
            letter-spacing: 0.1em;
            height: 85px;
            font-size: 15px;
        }
        #reserve #reserveTable input[type=text] {
            height: 50px;
            letter-spacing: 0.1em;
            color: #414b56;
            font-size: 15px;
        }
		#reserve #reserveTable input[type=text]:focus, #reserve #reserveTable textarea:focus, #reserve #reserveTable select:focus {
			outline: 0;
			border: 2px solid rgba(65,75,86,0.6);
		}
        #reserve #reserveTable button, #reserve #reserveTable #submit {
            width: 380px;
            height: 40px;
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #e3e3e0;
            color: #414b56;
            letter-spacing: 0.1em;
            border:2px solid rgba(65,75,86,0.3);
            border-radius: 4px;
        }
        #dates td:last-child {
            padding-left:50px;
        }
        #dates input[type=text] {
            text-align: center;
        }
        #reserve #reserveTable #dates #start {
            width: 245px;
        }
        #reserve #reserveTable #dates #end{
            width: 210px;
        }
        h3 {
            font-size: 10px;
            padding-top:10px;
            font-weight: normal;
            letter-spacing: 0.1em;
            color: #414b56;
        }
        #note {
            padding-bottom: 40px;
            font-size:12px;
            margin-right: -50px !important;
        }
        #topLeft {
            float:left;
            margin-left: 3%;
            margin-top: 1%;
        }
        #topLeft #button {
            width: 80px;
            height: 30px;
            margin-top: 5px;
            margin-bottom: 5px;
            background-color: #e3e3e0;
            color: #414b56;
            letter-spacing: 0.1em;
            font-style: italic;
            border-radius: 6px;
            border: 2px solid rgba(65,75,86, 0.3);
        }
        #link a, #link a:visited{
            color: #414b56;
            text-decoration: none;
        }
        #link {
            letter-spacing: 0.1em;
            font-style: italic;
            color: #414b56;
            text-decoration:none;
        }
        input[type=textarea] {
		    color: #414b56;
            letter-spacing: 0.1em;
            height: 85px;
            font-size: 15px;
		}
        #checkboxDiv {
            color: #414b56;
            letter-spacing: 0.1em;
            border-radius: 3px;
            margin-left:50px;
        }
        @media screen and (max-width: 1715px) {
              #nav a {
                  margin-left: 80px;
              }
          }
          @media screen and (max-width: 1435px) {
              #nav a {
                  margin-left: 0;
              }
          }
          @media screen and (max-width: 1090px) {
              #nav a {
                  display: block;
                  margin-left:-210px;
                  margin-right: -10px;
              }
              #nav {
                  height: 35px;
                  margin-bottom: 100px;
              }
          }

        </style>
        
        <script>

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
        
    <div id="nav">
		<center>
			<a href="versions.php">Overview</a>
			<a href="reservations.php" id="this" style="color:#76b900;">View and Request Reservations</a>
			<a href="maintain.php">Manage Reservations</a>
		</center>
    </div>
    <div id="topLeft">
        <input type="button" id="button" value="Go Back" onclick="history.back(-1)" style='outline:0;'/><br><br>
    </div>
    <center>

        
    <h2>Reserve an Environment</h2>
    
<?php
    
    if ($db=fConnectDb()) {
		
        if (isset($_POST['owner']) && (isset($_POST['env'])) && (isset($_POST['project'])) && (isset($_POST['start'])) && (isset($_POST['end']))) {

            $sEnviro = $_POST['env'];
            $sProjName = $_POST['project'];
            $sOwner = $_POST['owner'];
            $sStartDate = $_POST['start'];
            $sEndDate = $_POST['end'];
			$sDescription = $_POST['description'];
            $iEnvId;$iProjId;$iVendorId;            
            
            $iEnvIdQuery = "SELECT id FROM egamingEnvs WHERE name='$sEnviro'";
            $iProjIdQuery = "SELECT id FROM projects WHERE name='$sProjName'";
            
            
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

            $sSQL = "INSERT INTO env_reservations(env_id,project_id,owner,description,startdate,enddate) VALUES(?,?,?,?,?,?) ";
   
            if ($sth = $db->prepare($sSQL) ) {
 
            }
            
            else {
                 echo "Query preparation failed! " . mysqli_error($db);
            }
            
            if ($sth->bind_param("iissss", $iEnvId, $iProjId, $sOwner, $sDescription, $sStartDate, $sEndDate)) {
                if ($sth->execute()) {
                }
                
                else {
                    echo "Creation failed!" . mysqli_error($db);
                }
            }
            else {
                die("Error binding paramaters" . mysqli_error($db));
            }
            
            $sth->close();

        
            $iEnvResIdQuery = "SELECT id FROM env_reservations WHERE env_id='$iEnvId' AND project_id='$iProjId' AND owner='$sOwner' AND description='$sDescription' AND
                                startdate='$sStartDate' AND enddate='$sEndDate'"; //get the attached id
            
            if ($eResult = $db->query($iEnvResIdQuery)) {
                while ($resRow = $eResult -> fetch_row()) {
                    $iResId = $resRow[0];
                }
            }
            else {
                echo "Error " . mysqli_error();
            }
                
            $success=false;
                
            /*Section to add integrations to envRes_integrations joining table*/
            foreach($_POST['integrations'] as $integration) {
                $iIntegrationIdQuery = "SELECT id FROM vendorEnvs WHERE name='$integration'";
                if($intResult = $db->query($iIntegrationIdQuery)) {
                    while($intRow = $intResult -> fetch_row()) {
                        $iIntegrationId = $intRow[0];
                    }
                }
                //if (!integrationInUse($db,$iIntegrationId)) {

                    $sSQL = "INSERT INTO envRes_integrations(env_res_id,integration_id) VALUES(?,?) ";
       
                    if ($sth = $db->prepare($sSQL) ) {
         
                    }
                    
                    else {
                         echo "Query preparation failed! " . mysqli_error($db);
                    }
                    
                    if ($sth->bind_param("ii", $iResId, $iIntegrationId)) {
                        if ($sth->execute()) {
                            $success = true;
                        }
                        
                        else {
                            echo "Creation failed!" . mysqli_error($db);
                        }
                    }
                    
                    else {
                        die("Error binding paramaters" . mysqli_error($db));
                    }
                //}
                /*else {
                    $bInUse = true;
                    $success= false;
                }*/
            }
            if ($success) {
                echo '<script type="text/javascript">alert("Reservation added.");</script>';
                fShowDialog();
            }
			/*
            if ($bInUse) {
                echo '<script type="text/javascript">alert("Integration is in use.");</script>';
            }
            */
            
            $sth->close();
        }
        else {
            fShowDialog();
        }

    ?>
       </body>

</html>

<?php
}
           
           ?>