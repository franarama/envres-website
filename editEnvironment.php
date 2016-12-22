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
    <!--
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    -->
    <link rel="stylesheet" href="libs/jquery-ui-themes-1.11.4/themes/smoothness/jquery-ui.css">
    <script src="libs/jquery-1.11.3.min.js"></script>
    <script src="libs/jquery-ui-1.11.4/jquery-ui.min.js"></script>
        
    <title>Edit Environments</title>


    <script language="JavaScript">
        
        /*******************************************************************************/
        
        //funtion to select all checkboxes
        
        function toggle(source) {
            checkboxes = document.querySelectorAll('#checkable');
            for (var i=0; i < checkboxes.length; i++) {
                if (checkboxes[i] != source) {
                    checkboxes[i].checked=source.checked;
                }
            }
        }
        
        /*******************************************************************************/

        //this is for the textbox search bar with data-table: order-table, with the class name
        //light-table-filter, associated with the table with class order-table table
        
        (function(document) { 
            'use strict';
        
            var LightTableFilter = (function(Arr) {
        
                var _input;
        
                function _onInputEvent(e) {
                    _input = e.target;
                    var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
                    Arr.forEach.call(tables, function(table) {
                        Arr.forEach.call(table.tBodies, function(tbody) {
                            Arr.forEach.call(tbody.rows, _filter);
                        });
                    });
                }
        
                function _filter(row) {
                    var text = row.textContent.toLowerCase(), val = _input.value.toLowerCase();
                    row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
                }
        
                return {
                    init: function() {
                        var inputs = document.getElementsByClassName('light-table-filter');
                        Arr.forEach.call(inputs, function(input) {
                            input.oninput = _onInputEvent;
                        });
                    }
                };
            })(Array.prototype);
        
            document.addEventListener('readystatechange', function() {
                if (document.readyState === 'complete') {
                    LightTableFilter.init();
                }
            });
        
        })(document);
        
        
    </script>

</head>

<body>
    <header>
        <h1><img src="Images/logoreverse.png"></h1>
</h2>
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
    
    <div id="editTopLeft">
        <input type="button" id="button" value="Go Back" onclick="location.href='maintain.php'" style="outline:0;"/><br><br>
         <a onclick="location.href='addEnvironment.php'" style="font-style:italic;"><img src="Images/greyaddicon.png" width="15px" height="15px">&nbsp;Add an environment</a><br><br>
    </div>
    
    <div id="rightLink">
        <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
    </div>
    
    <h1 id="editTitle">Environments<h1>
    
    <?php
    if ($db = fConnectDb()) {
        fCheckAction($db);
    ?>

<?php
            /* this allows for sorting by headers */
            /* by calling a GET variable orderBy and placing it in the SQL query */
            
            $sql_orderBy='id ASC';
            
            if (isset($_GET['orderBy'])){
                switch($_GET['orderBy']) {
                    case 'env_name_desc':
                        $sql_orderBy = 'name DESC';
                        break;
                    case 'env_name_asc':
                        $sql_orderBy = 'name ASC';
                        break;
                }
            }
            
            $sSQL= "SELECT * FROM egaming_envs eg 
                    WHERE eg.enabled=1 ORDER BY $sql_orderBy";
                    
            $res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );
            if ($res->num_rows != 0) {
                fshowEnvs($res);
            }
            
    }
    
?>
<?php
exit();

/* this function will print the table rows and data for each listing */

function fPrintEnvs($res) {
    while ($row = $res->fetch_assoc()) {
    ?>
    <tr>
        <?php
        if ($db=fConnectDb()) {
            if (egEnvInUse($row['id'],$db)) {
                echo '<td><input type="checkbox" disabled="disabled" style="opacity:0.3;" title="Environment is in use" id="notCheckable">&nbsp;</td>';
            }
            else {
                $iEgEnv = $row['id'];
                echo '<td><input type="checkbox" name="chkSelected[]" value='.$iEgEnv.' id="checkable"></td>';
            }
        }
        ?>
            
        <td><?=$row['name']?></td>
        <td>
        <?php
            if ($db=fConnectDb()) {
                $iEgEnv = $row['id'];
                $aVendorEnvs = getVendorEnvs($db,$iEgEnv);
                if (count($aVendorEnvs) == 0) {
                    echo '';
                }
                else {
                    foreach($aVendorEnvs as $sVendor) {
                        echo $sVendor;
                        echo '<br>';
                
                    }
                }
            }
        ?>
        </td>
        <td><a href="updateEnvironment.php?id=<?=$row['id']?>" target="_blank" style="color: #76B900;">Edit</a></td>
    </tr>
    <?php
    }
}
    
    
function fshowEnvs($res) {

    ?>
    <form method="post">
    <table id="editTable" class="order-table table">
        <thead>
        <tr>
            <td><input type="checkbox" onclick="toggle(this)"><br><p style="padding-top:7px;">Select all</p></td>

            <td>
                <?php
                
                /************************************************Headers section***************************************************************/
                
                /* get the GET variable and show the correct arrow image */
                /* for each table data */
                /* and setting the GET variable */
                
                if ($_GET['orderBy'] == 'env_name_desc') {
                    print '<a href="editEnvironment.php?orderBy=env_name_asc">Environment<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if($_GET['orderBy'] == 'env_name_asc') {
                    print '<a href="editEnvironment.php?orderBy=env_name_desc">Environment<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="editEnvironment.php?orderBy=env_name_asc">Environment</a>';
                }
                ?>
            </td>
            <td>Vendor environment(s)</td>
            <td>&nbsp;</td>
            
        </tr>
        </thead>
        <tbody>
            
        <?php
            fPrintEnvs($res);
          ?>
          
         </tbody>
        <tfoot>
        <tr>
            <td><input type="submit" value="Delete" name="action" id="deleteBtn" style="outline:0;"></td>
            <td colspan="7">&nbsp;</td>
        </tr>
       </tfoot>
    </table>
    </form>
</body>

    <?php
    
    }
    /* function to delete an environment based on selected checkboxes */
    function fCheckAction($db) {
        $gotRows = false;
        if (isset( $_POST['action'])) {
            $sAction=$_POST['action'];
            if ($sAction == "Delete") {
                $aSelected=$_POST['chkSelected'];
                foreach ($aSelected as $iSelected) {
                    $sqlUpdate = "UPDATE egamingEnvs SET enabled='0' WHERE id='$iSelected'";
                    $result = mysqli_query($db,$sqlUpdate);
                    if ($result) {
                        $iAffectedRows=$db->affected_rows;
                        if (  $iAffectedRows > 0 ) {
                            $gotRows = true;
                        }
                        else {
                            $gotRows = false;
                        }
                    }
                }

                if ($gotRows) {
                    echo '<script type="text/javascript">alert("Deletion successful.");</script>';
                }
                else {
                    echo '<script type="text/javascript">alert("Select an environment to delete.");</script>';
                }
            }
        }
    }
    ?>    

    
    
</body>
</html>
