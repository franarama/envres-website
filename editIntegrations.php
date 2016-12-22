<?php
    session_start();
    include('inc/db.inc.php');
    include('inc/functions.inc.php');
?>
        
        <html>
        
        <head>
            <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
            <link rel="icon" type="image/png"  href="Images/logo.png">
            <title>Edit Integrations</title>

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
            
            <div id="rightLink">
                <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
            </div>
            
            <?php
            
            if ($db = fConnectDb()) {
                fCheckAction($db);
            ?>
            
            <div id="editTopLeft">
                <input type="button" id="button" value="Go Back" onclick="history.go(-1);" style="outline:0;"/><br><br>
                 <a href="addAnIntegration.php"><img src="Images/greyaddicon.png" width="15px" height="15px">&nbsp;Add an integration</a><br><br><br>
            </div>
            
            <h1 id="editTitle">Integrations</h2>
            
        <?php
        
        /* gets a GET variable orderBy and puts in the SQL query to allow
           sorting by headers */
        
        $sql_orderBy='id ASC'; //default orderBy
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
        $sSQL="SELECT * FROM integrations ORDER BY $sql_orderBy";
        $res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );
        
        if ($res->num_rows != 0) {
            fshowIntegrations($res);
            }
        }
        exit();
        
        /* this function prints table data for each project */
        function fPrintIntegrations($res) {
            while ($row = $res->fetch_assoc()) {
            ?>
            <tr>
                <?php
                if ($db=fConnectDb()) {
                    if (integrationInUse($db,$row['id']))  {
                        echo '<td><input type="checkbox" disabled="disabled" style="opacity:0.8;" title="integration is in use">&nbsp;</td>';
                    }
                    else {
                        $iId = $row['id'];
                        echo '<td><input type="checkbox" name="chkSelected[]" value='.$iId.' id="checkable"></td>';
                    }
                }
                ?>
                <td><?=$row['name']?></td>
                <td><?php
                        $iIdToCheck=$row['id'];
                        if ($db = fConnectDb()) {
                            if (integrationInUse($db,$iIdToCheck)) {
                                $iResId = getResId($db, $iIdToCheck);
                                $sInfo = getResInfo($db,$iIdToCheck);
                                echo 'Yes';
                                echo '<p id="info"><a href="editReservation.php?id='.$iResId.'" target="_blank"><img src="Images/info.png" height="15px" width="15px" title="'.$sInfo.'"></a></p>';
                            }
                            else {
                                echo 'No';
                            }
                        }
                    ?>
                </td>
                <td><a href="updateIntegration.php?id=<?=$row['id']?>">Edit</a></td>
            </tr>
            <?php
            }
        }
        function fshowIntegrations($res) {
            
            /*************************Headers section***********************************/
            
            ?>
            
            <form method="post">
                
                <table id="editTable" class="order-table table">
                <thead>
                <tr>
                    <td><input type="checkbox" onclick="toggle(this)">&nbsp;Select all</td>
                    
                    <td>
                        <?php
                        
                            /* get the GET orderBy variable and show appropriate arrow on the header */
                            /* and set the GET variable */
                            
                            if ($_GET['orderBy'] == 'name_desc') {
                                print '<a href="editIntegrations.php?orderBy=name_asc">Name<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                            }
                            else if($_GET['orderBy'] == 'name_asc') {
                                print '<a href="editIntegrations.php?orderBy=name_desc">Name<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                            }
                            else {
                                print '<a href="editIntegrations.php?orderBy=name_asc">Name</a>';
                            }
                        
                        ?>
                    </td>
                    <td>In use?</td>
                    <td>&nbsp;</td>
                    
        
                </tr>
            
                </thead>
                
                <tbody>
                <?php
                    fPrintIntegrations($res);
                  ?>
                </tbody>
                
                <tfoot>
                <tr>
                    <td colspan="4"><input type="submit" value="Delete" name="action" id="deleteBtn"></td>
                </tr>
                </tfoot>
                
            </table>
            </form>
        </body>
        
            <?php
            
            }
            /* function to delete integration based on selected checkboxes */
            
            function fCheckAction($db) {
                $gotRows = false;
                if (isset($_POST['action'])) {
                    $sAction=$_POST['action'];
                    if ($sAction == "Delete") {
                        $aSelected=$_POST['chkSelected'];
                        foreach ($aSelected as $iSelected) {
                            echo $iSelected;
                            $sSQL="DELETE FROM integrations WHERE id='$iSelected'";
                            if ( mysqli_query($db, $sSQL) ) {
                                $iAffectedRows=$db->affected_rows;
                                if (  $iAffectedRows > 0 ) {
                                    $gotRows = true;
                                }
                                else {
                                    $gotRows = false;
                                }
                            }
                        }
                        
                    }
                    if ($gotRows) {
                        echo '<script type="text/javascript">alert("Deletion successful.");</script>';
                    }
                    else {
                        echo '<script type="text/javascript">alert("Select an integration to delete.");</script>';
                    }
                }
            }
            
            ?>
            
        
        </html>