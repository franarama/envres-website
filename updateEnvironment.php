<?php
session_start();
include('inc/functions.inc.php');
include('inc/db.inc.php');
//checkLogin();
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
        <link rel="icon" type="image/png"  href="Images/logo.png">
        <title>Update Environment</title>
        
        <style>
            h1#title {
                margin-left: 43%;
            }
            
        </style>
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
            <input type="button" id="button" value="Go Back" onclick="history.back(-1)" style='outline:none;'/><br><br>
        </div>
            
        <h1 id="title">Update Environment</h1>
        
        <?php
        
        $envId = $_GET['id'];
        $success = false;
        
        if ($db=fConnectDb()) {
            
            if (isset($_POST['name'])) {
        
                    $sName = $_POST['name'];
                    $iVendorId;
                    
                    //update the egamingEnvs db table
                    $updateEnvNameQuery = "UPDATE egamingEnvs SET name=? WHERE id=$envId ";
                    
                    if($s=$db->prepare($updateEnvNameQuery)) {
                        
                    }
                    else {
                        echo "Query preparation failed! " . mysqli_error($db);
                    }
                    if($s->bind_param("s",$sName)) {
                        if($s->execute()) {
                            $success=true;
                        }
                        else {
                            echo "Creation failed! " . mysqli_error($db);
                        }
                    }
                    else {
                        echo "Error binding parameters: " . mysqli_error($db);
                    }
                    $s->close();
                    
                    //delete from the vendorEnvs_egEnvs db table
                    $deleteEnvIdQuery = "DELETE FROM vendorEnvs_egEnvs WHERE eg_id=$envId";
                    if (mysqli_query($db,$deleteEnvIdQuery)) {
                        
                    }
                    else {
                        die('Could not query database! ' . mysqli_error());
                    }
                    
                    foreach($_POST['vendorEnvs'] as $vendor) {
                        
                        $iVendorIdQuery = "SELECT id FROM vendorEnvs WHERE name='$vendor'";
                         if($vResult = $db->query($iVendorIdQuery)) {
                            while($vRow = $vResult -> fetch_row()) {
                                $iVendorId = $vRow[0];
                            }
                         }
                    
                    //insert into the vendorEnvs_egEnvs db table
                    $sSQL = "INSERT INTO vendorEnvs_egEnvs(eg_id,vendor_id) VALUES(?,?) ";
       
                    if ($sth = $db->prepare($sSQL) ) {
         
                    }
                    else {
                         echo "Query preparation failed! " . mysqli_error($db);
                    }
                    
                    if ($sth->bind_param("ii", $envId, $iVendorId)) {
                        
                        if ($sth->execute()) {
                            $success=true;
                        }
                        else {
                            echo "Creation failed!" . mysqli_error($db);
                        }
                    }
                    else {
                        die("Error binding paramaters" . mysqli_error($db));
                    }
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
            if($success) {
                echo '<script type="text/javascript">alert("Environment updated.");</script>';
                echo '<script type="text/javascript">window.close();</script>';
            }
        ?>
    
    
    <?php
    exit();
    function fShowDialog() {
        
        if($db=fConnectDb()) {
            
            $id=$_GET['id'];
            $sSQL = "SELECT eg.name AS env_name, v.name AS vendor_name FROM egaming_envs eg, vendor_envs v WHERE eg.id=$id";
            $response=mysqli_query($db,$sSQL);
            $rowName;
            
            while($row=$response->fetch_array()) {
                    $rowName=$row['env_name'];
            }
        }
    ?>
    <center>
        <form id="newEnv" method="post">
            <table id="addTable">
                
                <tr>
                    <td>
                        Environment name*:
                    </td>
                    <td>
                        <input type="text" name="name" value="<?php echo $rowName?>">
                    </td>
                </tr>
                
                <tr>
                <td>
                    Vendor environment(s):
                </td>
                <td>
                    <form name="form" id="form" method="post" action="CheckBox.jsp">
                        <div id="checkboxDiv" style="overflow: auto; width: 280px; height: 170px; border: 2px solid rgba(65,75,86,0.3); padding-left: 5px; text-align:left;">
                        
                        <?php
                        $envId = $_GET['id'];
                        if($db=fConnectDb()) {
                            $vendorQuery="SELECT ve.name AS vendor_env_name, ve.id AS vendor_env_id,IF(ve_ee.eg_id IS NOT NULL, true,false) AS is_active FROM `vendor_envs` ve
                                            LEFT JOIN vendorEnvs_egEnvs ve_ee ON ve_ee.vendor_id=ve.id AND ve_ee.eg_id=$envId
                                            LEFT JOIN egaming_envs ee ON ee.id=ve_ee.eg_id ORDER BY vendor_env_name ASC";
                                                
                            $vendorResult=mysqli_query($db,$vendorQuery) or die("Query to get data failed " . mysql_error());
           
                            while ($vendorNameRow = mysqli_fetch_array($vendorResult)) {
                                $name = $vendorNameRow["vendor_env_name"];
                                
                                if($vendorNameRow["is_active"] == 1) {
                                    echo "<input type='checkbox' name='vendorEnvs[]' value='$name' checked='checked'>&nbsp;$name<br>";
                                }
                                
                                else {
                                    echo "<input type='checkbox' name='vendorEnvs[]' value='$name'>&nbsp;$name<br>";
                                }
                            }
                        }
    
                    
                        ?>
                        
                        
                        </div>
                    </form>
                </td>
            </tr>
                
                <tr>
                    <td>
                        <input type="submit" value="Update" name="submit" id="submit"></input>
                    </td>
                    <td>
                        <button type="reset" id="reset">Revert Form</button>
                    </td>
    
                </tr>
            </table>
        </form>
        
        <h3 id="note"><i>* indicates a required field</i></h3>
        </center>
        </div>
        
        </body>
    
    </html>
    
    
        <?php
    }
    fShowDialog();
    
    ?>

