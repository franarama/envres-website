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
    <title>Edit Vendor Environments</title>

    <script language="JavaScript">
        
        /********************************************************************/
                
        //this function selects all the checkboxes
        
        function toggle(source) {
            checkboxes = document.querySelectorAll('#checkable');
            for (var i=0; i < checkboxes.length; i++) {
                if (checkboxes[i] != source) {
                    checkboxes[i].checked=source.checked;
                }
            }
        }
        
        /********************************************************************/
                
        //this is for the search bar with data-table: order-table, with the class name
        //light-table-filter for the table with class order-table table
        
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
        
        /********************************************************************/
        
    </script>
</head>

<body>
    <header>
        <h1><img src="Images/logoreverse.png"></h1>
        <h2><a href="logout.php" id="logoutBtn">Logout</a>
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
    
    <?php
    
    if ($db = fConnectDb()) {
        fCheckAction();
    ?>
    <div id="editTopLeft">
        <input type="button" id="button" value="Go Back" onclick="location.href='maintain.php'" style="outline:0;"/><br><br>
         <a href="addVendorEnv.php"><img src="Images/greyaddicon.png" width="15px" height="15px">&nbsp;Add a vendor environment</a><br><br><br>
    </div>
    
    <h1 id="editTitle">Vendor Environments</h1>

    <div id="rightLink">
        <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
    </div>
    
    
<?php

/* gets a GET variable orderBy and puts in the SQL query to allow
   sorting by headers */
        
$sql_orderBy='id ASC';

if (isset($_GET['orderBy'])) {
    
    switch($_GET['orderBy']) {
        case 'name_desc':
            $sql_orderBy = 'name DESC';
            break;
        case 'name_asc':
            $sql_orderBy = 'name ASC';
            break;
    }
}

$sSQL="SELECT * FROM vendor_envs ORDER BY $sql_orderBy";
$res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );

if ($res->num_rows != 0) {
    fshowVendorEnvs($res);
    }
}
    
exit();

/* this function prints all the vendor env info for each env */

function fPrintVendorEnvs($res) {
    while ($row = $res->fetch_assoc()) {
    ?>
    
    <tr>
        <?php
            if ($db=fConnectDb()) {
                $iId = $row['id'];
                if (vendorEnvInUse($iId,$db)) {
                    echo '<td><input type="checkbox" disabled="disabled" style="opacity:0.8;" title="Environment in use"></td>';
                }
                else {
                    echo '<td><input type="checkbox" name="chkSelected[]" id="checkable" value='.$iId.'></td>';
                }
            }
        ?>
        <td><?=$row['name']?></td>
        <td><?=$row['description']?></td>
        <td><a href="updateVendorEnv.php?id=<?=$row['id']?>">Edit</td>
    </tr>
    
    <?php
    }
}
function fshowVendorEnvs($res) {
    
    /************************************Headers section*************************************/
    
    ?>
    
    <form method="post">
    <table id="editTable" class="order-table table">
        <thead>
            <tr>
                <th><input type="checkbox" onclick="toggle(this)">&nbsp;Select all</th>
            
                <th>
                <?php
                    
                    /* get the GET orderBy variable and show appropriate arrow on the header */
                    /* and set the GET variable */
                    
                    if ($_GET['orderBy'] == 'name_desc') {
                        print '<a href="/editVendorEnv.php?orderBy=name_asc">Environment Name<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                    }
                    else if($_GET['orderBy'] == 'name_asc') {
                        print '<a href="/editVendorEnv.php?orderBy=name_desc">Environment Name<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                    }
                    else {
                        print '<a href="/editVendorEnv.php?orderBy=name_asc">Environment Name</a>';
                    }
                    ?>
            </th>
            <th>Description</th>
            <th>&nbsp;</th>

        </tr>
        </thead>
        
        <tbody>
        <?php
            fPrintVendorEnvs($res);
          ?>
        </tbody>
        
        <tfoot>
            <tr>
            <td colspan="4"><input type="submit" value="Delete" name="action" id="deleteBtn" style="outline:0;"></td>
            </tr>
        </tfoot>
    </table>
    </form>
</body>

    <?php
    
    }
    /* function to delete vendor env based on selected checkboxes */
    function fCheckAction() {
        $gotRows = false;
        if ( isset( $_POST['action'] ) && $_POST['action'] ) {
            if ($db = fConnectDb()) {
                $sAction=$_POST['action'];
                    if ($sAction == "Delete") {
                        $aSelected=$_POST['chkSelected'];
                        foreach ($aSelected as $iSelected) {
                                    $sDeleteVendorQuery="DELETE FROM vendor_envs WHERE id=$iSelected";
                                    $deleteIdQuery="DELETE FROM vendorEnvs_egEnvs WHERE eg_id=$iSelected";
                                        if (mysqli_query($db, $sDeleteVendorQuery)) {
                                            $iAffectedRows=$db->affected_rows;
                                                if ($iAffectedRows > 0)
                                                {
                                                    $gotRows = true;
                                                } else {
                                                    $gotRows = false;
                                                }           
                                            }
                                        if (mysqli_query($db,$deleteIdQuery)) {
                                            
                                        }
                                        else {
                                            die('Could not query database! ' . mysqli_error());
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
    

</html>