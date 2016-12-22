<?php
include('inc/functions.inc.php');
include('inc/db.inc.php');
checkLogin();
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
        <link rel="icon" type="image/png"  href="Images/logo.png">
        <title>Add a Vendor Environment</title>
        
        <style>
        #nav {
            padding-left:190px;
        }
        #nav a {
            margin-left:10%;
            margin-right:10%;
        }
            #middleStuff {
                margin-top: 3%;
            }
            #vendorEnvsTable {
                font-family: Arial;
                border-collapse: collapse;
                width: 40%;
                color: #414b56;
                font-size: 15px;
                margin-top: 3%;
                border-top: 2px dotted #959ca1;
                border-bottom: 2px dotted #959ca1;
                height: 30%;
                text-align: center;
                margin-right:0;
            }
            #vendorEnvsTable td {
                padding: 10px;
                padding-top: 40px;
                padding-bottom: 30px;
            }

            #vendorEnvsTable input[type=text] {
                height: 50px;
                letter-spacing: 0.1em;
                color: #414b56;
                font-size: 15px;
                width: 90%;
                text-align: left;
                border:2px solid rgba(65,75,86,0.3);
                border-radius:3px;
                padding-left: 7px;
            }
            #vendorEnvsTable textarea {
                letter-spacing: 0.1em;
                color: #414b56;
                font-size: 15px;
                border:2px solid rgba(65,75,86,0.3);
                border-radius:3px;
                padding-left: 7px;
                padding-top: 7px;
            }
            #vendorEnvsTable textarea:focus, #vendorEnvsTable input[type=text]:focus {
                outline:0;
                border: 2px solid rgba(65,75,86,0.6);
            }

            h1 {
                text-align: center;
                color: #414b56;
                font-weight: normal;
                letter-spacing: 0.1em;
                font-size: 25px;
                margin-bottom: -1%;
                color: #414b56;
                margin-right:6%;
                word-spacing: 3px;
            }
            #reset, #submit {
                width: 300px;
                height: 40px;
                margin-top: 30px;
                margin-bottom: 30px;
                background-color: #e3e3e0;
                color: #414b56;
                letter-spacing: 0.1em;
                border:2px solid rgba(65,75,86,0.3);
                border-radius: 4px;
            }
            #vendorEnvsTable td:first-child {
                color: #76b900;
                letter-spacing: 0.1em;
                font-size: 16px;
                text-align: left;
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
            }
            #vendorEnvsTable select {
                background-color: white;
                text-align: center;
                height: 50px;
                color: #414b56;
                width: 240px;
                letter-spacing: 0.1em;
            }
            #vendorEnvsTable textarea {
                width:90%;
                height:100px;
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
            text-decoration: none;
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
        
    </head>
    
    <body>
        
        <header>
            <h1><img src="Images/logoreverse.png"></h1>
        </header>
        
    <div id="nav">
        <center>
            <a href="versions.php">Overview</a>
            <a href="reservations.php">View and Request Reservations</a>
            <a href="maintain.php">Manage Reservations</a>
        </center>
    </div>
    
    <div id="topLeft">
        <input type="button" id="button" value="Go Back" onclick="history.back(-1)" style='outline:0;'/><br><br>
    </div>
    
    <center>
        <div id="middleStuff">
            <h1>Add a Vendor Environment</h1>
        </div>
<?php
    if ($db=fConnectDb()) {
        
        if (isset($_POST['name'])) {

            $sName = $_POST['name'];
            $sDescription = $_POST['description'];
            $sURL = $_POST['url'];
            $sSQL = "INSERT INTO vendorEnvs(name,description,url) VALUES(?,?,?)";
            
            if ($sth = $db->prepare($sSQL) ) {
                
            }
            else {
                 die("Query preparation failed!" . mysqli_error($db));
            }
            
            if ($sth->bind_param("sss", $sName, $sDescription, $sURL))
            {
                if ($sth->execute()) {
                    echo '<script type="text/javascript">alert("Vendor environment added.");</script>';
                    fShowDialog();
                }
                else {
                    echo '<script type="text/javascript">alert("Vendor environment failed to add!" . mysqli_error($db));</script>';
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
    ?>

    <form id="newEnv" method="post">
        <table id="vendorEnvsTable">
            
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
                    Description:
                </td>
                <td>
                    <textarea name="description"></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    URL:
                </td>
                <td>
                    <input type="text" name="url">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value="Add Environment" name="submit" id="submit" style='outline:0;'></input>
                </td>
                <td>
                    <button type="reset" id="reset" style='outline:0;'>Clear Form</button>
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
