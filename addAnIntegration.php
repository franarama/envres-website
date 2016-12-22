<?php
    include('inc/functions.php');
    include('inc/db.inc.php');
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
        <link rel="icon" type="image/png"  href="Images/logo.png">
        <title>Add an Integration</title>
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
            <input type="button" id="button" value="Go Back" onclick="history.back(-1)" style="outline:0;"/><br><br>
        </div>
        
        <center>
            
            <h1 id="title">Add an Integration</h1>
            </div>
            
            <?php
        
            if ($db=fConnectDb()) {
                if (isset($_POST['name'])) {
            
                        $sName = $_POST['name'];
                        $insertIntegrationNameQuery = "INSERT IGNORE INTO integrations(name) VALUES(?) ";
                        
                        if($s=$db->prepare($insertIntegrationNameQuery)) {
                            
                        }
                        else {
                            echo "Query preparation failed! " . mysqli_error($db);
                        }
                        if($s->bind_param("s",$sName)) {
                            if($s->execute()) {
                                echo '<script type="text/javascript">alert("Integration added.");</script>';
                                fShowDialog();
                            }
                            else {
                                echo "Creation failed! " . mysqli_error($db);
                            }
                        }
                        else {
                            echo "Error binding parameters: " . mysqli_error($db);
                        }
                        $sth->close();
                    }
                    else {
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
                            Integration name*:
                        </td>
                        <td>
                            <input type="text" name="name" required="required" style="outline: 0;">
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <input type="submit" value="Add Integration" name="submit" id="submit"></input>
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
