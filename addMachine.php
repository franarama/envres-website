<?php
session_start();
include('inc/functions.inc.php');
include('inc/db.inc.php');

?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
        <link rel="icon" type="image/png"  href="Images/logo.png">
        <title>Add a Machine</title>
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
            <h1 id="title">Add a Machine</h1>
            
            <?php
                
                if ($db=fConnectDb()) {
                    
                    if (isset($_POST['name'])) {
            
                        $sName = $_POST['name'];
                        
                        $sSQL = "INSERT INTO machines(name) VALUES(?)";
                        
                        if ($sth = $db->prepare($sSQL) ) {
                            
                        }
                        else {
                             die("Query preparation failed!" . mysqli_error($db));
                        }
                        
                        if ($sth->bind_param("s", $sName))
                        {
                            if ($sth->execute()) {
                                echo '<script type="text/javascript">alert("Machine added.");</script>';
                                fShowDialog();
                            }
                            else {
                                echo '<script type="text/javascript">alert("Machine failed to add!" . mysqli_error($db));</script>';
                            }
                        }
                        else {
                            die( "Error binding paramaters" . mysqli_error($db) );
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
            
                <form id="newProj" method="post">
                    
                    <table id="addTable">
                        <tr>
                            <td>
                                Machine name*:
                            </td>
                            <td>
                                <input type="text" required="required" name="name">
                            </td>
                        </tr>
                        
                        
                        <tr>
                            <td>
                                <input type="submit" value="Add Machine" name="submit" id="submit" style='outline:0;'></input>
                            </td>
                            <td>
                                <button type="reset" id="reset">Clear Form</button>
                            </td>
                        </tr>
                    </table>
                </form>
                <h3 id="note"><i>* indicates a required field</i></h3>
                </div>
                
            <?php
            }
            ?>
