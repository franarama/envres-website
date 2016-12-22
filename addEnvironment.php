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
        <title>Add an Environment</title>
        
        <style>


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
    <center>
        <h1 id="title">Add an Environment</h1>
        </div>
    <?php
        
        if ($db=fConnectDb()) {
            
            if (isset($_POST['name'])) {
        
                    $sName = $_POST['name'];
                    $iEnvId; $iVendorId;$success=false;
    
                    $insertEnvNameQuery = "INSERT INTO egamingEnvs(name) VALUES(?) "; //insert the env name into egamingEnvs db
                    
                    if($s=$db->prepare($insertEnvNameQuery)) {
                        
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
                    
                    $iEnvIdQuery = "SELECT id FROM egamingEnvs WHERE name='$sName'"; //get the attached id
                
                
                    if ($eResult = $db->query($iEnvIdQuery)) {
                        while ($envRow = $eResult -> fetch_row()) {
                            $iEnvId = $envRow[0];
                        }
                    }
                    
    
                    
                    /*Section to add vendor envs to vendorEnvs_egEnvs joining table*/
                    foreach($_POST['vendorEnvs'] as $vendor) {
                        
                        $iVendorIdQuery = "SELECT id FROM vendorEnvs WHERE name='$vendor'";
                         if($vResult = $db->query($iVendorIdQuery)) {
                            while($vRow = $vResult -> fetch_row()) {
                                $iVendorId = $vRow[0];
                            }
                         }
    
                    $sSQL = "INSERT INTO vendorEnvs_egEnvs(eg_id,vendor_id) VALUES(?,?) ";
       
                    if ($sth = $db->prepare($sSQL) ) {
         
                    }
                    else {
                         echo "Query preparation failed! " . mysqli_error($db);
                    }
                    
                    if ($sth->bind_param("ii", $iEnvId, $iVendorId)) {
                        
                        if ($sth->execute()) {
                            echo '<script type="text/javascript">alert("Environment added.");</script>';
                            fShowDialog();
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
        ?>
    
        
        </body>
    </html>
    <?php
    exit();
    
    function fShowDialog() {
        ?>
    
        <form id="newEnv" method="post">
            <table id="addTable">
                
                <tr>
                    <td>
                        Environment name*:
                    </td>
                    <td>
                        <input type="text" name="name" required="required">
                    </td>
                </tr>
                
                <tr>
                <td>
                    Vendor environment(s):
                </td>
                <td>
                    <form name="form" id="form" method="post" action="CheckBox.jsp">
                        <div id="checkboxDiv" style="overflow: auto; width: 300px; height: 130px; border: 2px solid rgba(65,75,86,0.3); padding-left: 5px; text-align:left;">
                        <?php
                        
                        if ($db=fConnectDb()) {
                            $vendorName="SELECT name FROM vendor_envs ORDER BY name ASC";
                            $vendorNameResult=mysqli_query($db,$vendorName) or die("Query to get data failed " . mysql_error());
                            
                            while ($vendorNameRow = mysqli_fetch_array($vendorNameResult)) {
                                $name = $vendorNameRow["name"];
                                echo "<input type='checkbox' name='vendorEnvs[]' value='$name'>&nbsp;$name<br>";
                            }
                        }
                    
                        ?>
                        </div>
                    </form>
                </td>
            </tr>
                
                <tr>
                    <td>
                        <input type="submit" value="Add Environment" name="submit" id="submit"></input>
                    </td>
                    <td>
                        <button type="reset" id="reset">Clear Form</button>
                    </td>
                </tr>
                </center>
            </table>
        </form>
        <h3 id="note"><i>* indicates a required field</i></h3>
        
    <?php
    }
    ?>
