<?php
session_start();
include('inc/db.inc.php');

function fShowDialog() {
	if($db=fConnectDb()) {

    ?>

    
    <form id="reserve" method="post">
           <table id="addTable">
        <tr>
            <td>
                Name*:
            </td>
        
            <td>
                <input type="text" required="required" name="name" id="name"></input>
            </td>            
        
        </tr>
        
        <tr>
            <td>
                Company*:
            </td>
            <td>
                <input type="text" required="required" name="company" id="company"></input>
            </td>
            
        </tr>
		
		<tr>
			<td>
				Project*:
			</td>
            
			<td>
                <select required="required" name='project'>
                    <?php
                        if ($db=fConnectDb()) {
                            $projName="SELECT name FROM projects ORDER BY name ASC";
                            $projNameResult=mysqli_query($db,$projName) or die("Query to get data failed " . mysqli_error() );
                            
                            while ($projNamerow = mysqli_fetch_array($projNameResult)) {
                                $name=$projNamerow['name'];
                                echo "<option>$name</option>";
                            }
                        }
                    ?>
                </select>
            </td>
		</tr>
		
        <tr>
            <td>
                Machine name*:
            </td>
            <td>
                <select required="required" name='machine'>
                <?php
                    if ($db = fConnectDb()) {
                        $machineName="SELECT name FROM machines ORDER BY name ASC";
                        $machineNameResult=mysqli_query($db,$machineName) or die("Query to get data failed " . mysqli_error());
                        
                        while ($machineNameRow=mysqli_fetch_array($machineNameResult)) {
                            $name=$machineNameRow['name'];
                            echo "<option>$name</option>";
                        }
                    }
                ?>
                </select>
            </td>
        </tr>
        
            <tr style="border:none;" id="buttons">
            <td>
            <input type="submit" name="submit" value="Submit" id="submit"></input>
            </td>
            <td>
                <button type="reset" id="reset">Clear form</button>
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
		
        <title>Reserve a Contractor Machine</title>
        
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
		
		<div id="topLeft">
			<input type="button" id="button" value="Go Back" onclick="history.back(-1)" style='outline:0;'/><br><br>
		</div>
		<center>
	
			
			<h1 id="title">Add a Contractor Machine Reservation</h1>
			
		<?php
			
			if ($db=fConnectDb()) {
				
				if (isset($_POST['name']) && (isset($_POST['company'])) && (isset($_POST['project'])) && (isset($_POST['machine']))) {
		
					$sName = $_POST['name'];
					$sProjName = $_POST['project'];
					$sCompany = $_POST['company'];
					$sMachineName = $_POST['machine'];
					$iProjId;$iMachineId;            
					
					$iMachineIdQuery = "SELECT id FROM machines WHERE name='$sMachineName'";
					$iProjIdQuery = "SELECT id FROM projects WHERE name='$sProjName'";
		
					
					if ($mResult = $db->query($iMachineIdQuery)) {
						while ($machineRow = $mResult -> fetch_row()) {
							$iMachineId = $machineRow[0];
						}
					}
					
					if ($pResult = $db->query($iProjIdQuery)) {
						while ($projRow = $pResult -> fetch_row()) {
							$iProjId = $projRow[0];
						}
					}
					
					$enableMachineQuery = "UPDATE machines SET enabled=1 WHERE id=$iMachineId"; //enable the machine
					
					if ($db->query($enableMachineQuery)) {
						
					}
					
					$sSQL = "INSERT INTO contractor_machines_listing(name,company,project_id,machine_id) VALUES(?,?,?,?) ";
		   
					if ($sth = $db->prepare($sSQL) ) {
		 
					}
					else {
						 echo "Query preparation failed! " . mysqli_error($db);
					}
					
					if ($sth->bind_param("ssii", $sName, $sCompany, $iProjId, $iMachineId)) {
						if ($sth->execute()) {
							echo '<script type="text/javascript">alert("Contractor machine added.");</script>';
							fShowDialog();
						}
						else {
							echo "Creation failed!" . mysqli_error($db);
						}
					}
					else {
						die("Error binding paramaters" . mysqli_error($db));
					}
					$sth->close();
				}
				else
				{
					fShowDialog();
				}
		
			?>
		</center>
	</body>
		
	</html>
		
	<?php
	}
	?>