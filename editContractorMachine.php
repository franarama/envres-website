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
    <title>Edit Contractor Machines</title>

    <script language="JavaScript">
        
        /********************************************************************/
                
        //this function selects all the checkboxes
        
        function toggle(source) {
            checkboxes = document.querySelectorAll('input[type="checkbox"]');
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
         <a href="addContractorMachine.php"><img src="Images/greyaddicon.png" width="15px" height="15px">&nbsp;Add a contractor machine</a><br><br><br>
    </div>
    
    <h1 id="editTitle">Contractor Machine Reservations</h1>

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
            $sql_orderBy = 'cml.name DESC';
            break;
        case 'name_asc':
            $sql_orderBy = 'cml.name ASC';
            break;
        case 'project_desc':
            $sql_orderBy = 'project_name DESC';
            break;
        case 'project_asc':
            $sql_orderBy = 'project_name ASC';
            break;
        case 'machine_desc':
            $sql_orderBy = 'machine_name DESC';
            break;
        case 'machine_asc':
            $sql_orderBy = 'machine_name ASC';
            break;
        case 'company_asc':
            $sql_orderBy = 'company ASC';
            break;
        case 'company_desc':
            $sql_orderBy = 'company DESC';
            break;
    }
}

$sSQL="SELECT cml.id,cml.name AS cml_name,cml.company,cml.project_id,cml.machine_id,m.name AS machine_name,p.name AS project_name FROM `contractor_machine_listing` cml
       LEFT JOIN projects p ON cml.project_id = p.id
       LEFT JOIN machines m ON cml.machine_id = m.id WHERE m.enabled=1 ORDER BY $sql_orderBy";
       
$res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );

if ($res->num_rows != 0) {
    fshowListings($res);
    }
}
    
exit();

/* this function prints all the vendor env info for each env */

function fPrintListings($res) {
    while ($row = $res->fetch_assoc()) {
    ?>
    
    <tr>
        <td><input type="checkbox" name="chkSelected[]" value="<?=$row['id']?>"></td>
        <td><?=$row['cml_name']?></td>
        <td><?=$row['company']?></td>
        <td><?=$row['project_name']?></td>
        <td><?=$row['machine_name']?></td>
        <td><a href="updateContractorMachineReservation.php?id=<?=$row['id']?>" target="_blank">Edit</td>
    </tr>
    
    <?php
    }
    if($db=fConnectDb()) {
        $machineQuery="SELECT * FROM machines WHERE enabled=0";
        $response = mysqli_query($db, $machineQuery);
        
        if ($response) {
            while($row=mysqli_fetch_array($response)) {
                ?>
                <tr>
                    <td colspan="4">&nbsp;</td>
                    <td><?=$row['name']?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php
            }
        }
    }
    
}
function fshowListings($res) {
    
    /************************************Headers section*************************************/
    
    ?>
    
    <form method="post">
    <table id="editTable" class="order-table table">
        <thead>
            <tr>
                <th><input type="checkbox" onclick="toggle(this)">&nbsp;Select all</th>
                <th>
                <?php
                if ($_GET['orderBy'] == 'name_desc') {
                    print '<a href="/editContractorMachine.php?orderBy=name_asc">Name<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if($_GET['orderBy'] == 'name_asc') {
                    print '<a href="/editContractorMachine.php?orderBy=name_desc">Name<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/editContractorMachine.php?orderBy=name_asc">Name</a>';
                }
                ?>
            
            </th>

            <th>
                <?php
                if ($_GET['orderBy'] == 'company_desc') {
                    print '<a href="/editContractorMachine.php?orderBy=company_asc">Company<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'company_asc') {
                    print '<a href="/editContractorMachine.php?orderBy=company_desc">Company<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/editContractorMachine.php?orderBy=company_asc">Company</a>';
                }
                ?>
            </th>
            <th>
                <?php
                if ($_GET['orderBy'] == 'project_desc') {
                    print '<a href="/editContractorMachine.php?orderBy=project_asc">Project<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'project_asc') {
                    print '<a href="/editContractorMachine.php?orderBy=project_desc">Project<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/editContractorMachine.php?orderBy=project_asc">Project</a>';
                }
                ?>
            </th>
            <th>
                <?php
                if ($_GET['orderBy'] == 'machine_desc') {
                    print '<a href="/editContractorMachine.php?orderBy=machine_asc">Machine Name<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'machine_asc') {
                    print '<a href="/editContractorMachine.php?orderBy=machine_desc">Machine Name<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/editContractorMachine.php?orderBy=machine_asc">Machine Name</a>';
                } 
                ?>
            </th>
            <th>&nbsp;</th>
           

        </tr>
        </thead>
        
        <tbody>
        <?php
            fPrintListings($res);
          ?>
        </tbody>
        
        <tfoot>
            <tr>
            <td colspan="6"><input type="submit" value="Delete" name="action" id="deleteBtn" onclick="return confirm('Are you sure?')" style="outline:0;"></td>
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
                            if (is_numeric($iSelected)) {
   
                                $sDeleteQuery="DELETE FROM contractor_machines_listing WHERE id=$iSelected";
                                $getMachineId = "SELECT machine_id FROM contractor_machines_listing WHERE id=$iSelected";
                                
                                $machineIdResult = $db->query($getMachineId);
                                
                                if ($machineIdResult->num_rows != 0) {
                                    while ($row=$machineIdResult->fetch_assoc()) {
                                        $iMachineId=$row['machine_id'];
                                    }
                                }
                                $disableMachine = "UPDATE machines SET enabled='0' WHERE id=$iMachineId";
                                
                                if ($db->query($disableMachine)) {
                                    
                                }
                                
                                if (mysqli_query($db, $sDeleteQuery)) {
                                        $iAffectedRows=$db->affected_rows;
                                            if ($iAffectedRows > 0)
                                            {
                                                $gotRows = true;
                                            } else {
                                                $gotRows = false;
                                            }           
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
                            echo '<script type="text/javascript">alert("Select a contractor machine reservation to delete.");</script>';
                        }
                    }
                }       
            }
        }
            ?>
    

</html>