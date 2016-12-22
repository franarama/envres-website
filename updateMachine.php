<?php
session_start();
include('inc/functions.inc.php');
include('inc/db.inc.php');
// checkLogin();
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
        <link rel="icon" type="image/png"  href="Images/logo.png">
        <title>Update Machine</title>
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
        
            <h1 id="title">Update Machine</h1>
            
        <?php
        
            $id=$_GET['id'];
            
            if ($db=fConnectDb()) {
                
                if (isset($_POST['name'])) {
        
                    $sName = $_POST['name'];
                        
                    $sSQL = "UPDATE machines SET name=? WHERE id=$id";
                    if ($sth = $db->prepare($sSQL) ) {
                        
                    }
                    else {
                         die("Query preparation failed! " . mysqli_error($db));
                    }
                    
                    if ($sth->bind_param("s", $sName))
                    {
                        if ($sth->execute()) {
                            echo '<script type="text/javascript">alert("Machine updated");</script>';
                            fShowDialog();
                        }
                        else {
                            die("Machine failed to update! " . mysqli_error($db));
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
                    $sSQL = "SELECT * FROM machines WHERE id=$id";
                    $response=mysqli_query($db,$sSQL);
                    if($response) {
                        while($row=mysqli_fetch_array($response)) {
                            $rowName=$row['name'];               }
                    }
                    }
            ?>
        
            <form id="newProj" method="post">
                <table id="addTable">
                    <tr>
                        <td>
                            Machine name*:
                        </td>
                        <td>
                            <input type="text" required="required" name="name" value="<?php echo $rowName?>">
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
            
    <?php
    }
    ?>
