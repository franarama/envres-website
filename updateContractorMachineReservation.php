<?php
    include('inc/functions.inc.php');
    include('inc/db.inc.php');
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
        <link rel="icon" type="image/png"  href="Images/logo.png">
        <title>Edit Contractor Machine Reservation</title>
        
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
        <input type="button" id="button" value="Go Back" onclick="history.back(-1)"/><br><br>
    </div>
    <center>
    <div id="middleStuff">
    <h1 id="title">Edit Contractor Machine Reservation</h1>
    </div>
<?php
   
    $id=$_GET['id'];
    
    if ($db=fConnectDb()) {
        
        if (isset($_POST['name']) && isset($_POST['company']) && isset($_POST['project']) && isset($_POST['machine'])) {

            $sName = $_POST['name'];
            $sProject = $_POST['project'];
            $sCompany = $_POST['company'];
            $sMachine = $_POST['machine'];
            
            $sSQL = "UPDATE contractor_machines_listing SET name=?, company=?, project_id=?, machine_id=? WHERE id=$id";
            
            $projectIdQuery = "SELECT id FROM projects WHERE name='$sProject'";
            $machineIdQuery = "SELECT id FROM machines WHERE name='$sMachine'";
            
            if ($mResult = $db->query($machineIdQuery)) {
                while ($machineRow = $mResult -> fetch_row()) {
                    $iMachineId = $machineRow[0];
                }
            }
            
            if ($pResult = $db->query($projectIdQuery)) {
                while ($projRow = $pResult -> fetch_row()) {
                    $iProjId = $projRow[0];
                }
            }
            
            if ($sth = $db->prepare($sSQL) ) {
                
            }
            else {
                 die("Query preparation failed!" . mysqli_error($db));
            }
            
            if ($sth->bind_param("ssii", $sName, $sCompany, $iProjId, $iMachineId))
            {
                if ($sth->execute()) {
                    echo '<script type="text/javascript">alert("Contractor machine reservation updated.");</script>';
                    echo '<script type="text/javascript">close();</script>';
                }
                else {
                    echo '<script type="text/javascript">alert("Contractor machine reservation failed to update!" . mysqli_error($db));</script>';
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
    }
    else {
        die("Error connecting to MySQL" . mysqli_error($db));
            
    }
    ?>
    
    </body>
</html>
<?php
exit();

function fShowDialog() {

    if($db=fConnectDb()) {
        
        $id=$_GET['id'];
        $sSQL = "SELECT cml.name AS cml_name,company,m.name AS machine_name,p.name AS project_name FROM `contractor_machines_listing` cml
                    LEFT JOIN projects p ON cml.project_id = p.id
                    LEFT JOIN machines m ON cml.machine_id = m.id WHERE cml.id=$id";
        $response=mysqli_query($db,$sSQL);
            if($response) {
                while($row=mysqli_fetch_array($response)) {
                    $rowName=$row['cml_name'];
                    $rowCompany=$row['company'];
                    $rowProject=$row['project_name'];
                    $rowMachine=$row['machine_name'];
                }
            }
   }
   
    ?>

    <form id="newEnv" method="post">
        <table id="addTable">
            
            <tr>
                <td>
                    Name*:
                </td>
                
                <td>
                    <input type="text" name="name" required="required" value="<?php echo $rowName?>">
                </td>
                
            </tr>
            
            <tr>
                <td>
                    Company*:
                </td>
                
                <td>
                    <input type="text" name="company" required="required" value="<?php echo $rowCompany?>">
                </td>
            </tr>
        
            
                <td>
                    Project*:
                </td>
                
                <td> 
                    <select required="required" style="text-align:center;" name='project'>
                        <?php
                        if ($db = fConnectDb()) {
                            $projName="SELECT name FROM projects ORDER BY name ASC";
                            $projNameResult=mysqli_query($db,$projName) or die("Query to get data failed " . mysql_error());
                        
                                while($projNamerow = mysqli_fetch_array($projNameResult)) {
                                    $name=$projNamerow["name"];
                                    if ($name == $rowProject) {
                                        echo "<option selected=\"selected\">$rowProject</option>";
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
            <td>Machine name*:</td>
            
            <td>
              <select required="required" style="text-align:center;" name='machine'>
                <?php
                if ($db = fConnectDb()) {
                    $machName="SELECT name FROM machines ORDER BY name ASC";
                    $machNameResult=mysqli_query($db,$machName) or die("Query to get data failed " . mysql_error());
                
                        while($machNamerow = mysqli_fetch_array($machNameResult)) {
                            $name=$machNamerow["name"];
                            if ($name == $rowMachine) {
                                echo "<option selected=\"selected\">$rowMachine</option>";
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
                <td>
                    <input type="submit" value="Update" name="submit" id="submit"></input>
                </td>
                
                <td>
                    <button type="reset" id="reset">Revert form</button>
                </td>
            </tr>
            
            </center>
        </table>
    </form>
    
    <h3 id="note"><i>* indicates a required field</i></h3>
    </div>
    
<?php
}
?>
