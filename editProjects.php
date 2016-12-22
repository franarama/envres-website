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
            <title>Edit Projects</title>

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
            
            <div id="rightLink">
                <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
            </div>
            
            <?php
            
            if ($db = fConnectDb()) {
                fCheckAction($db);
            ?>
            
            <div id="editTopLeft">
                <input type="button" id="button" value="Go Back" onclick="location.href='maintain.php'" style=
                       'outline:0;'/><br><br>
                 <a href="addProject.php"><img src="Images/greyaddicon.png" width="15px" height="15px">&nbsp;Add a project</a><br><br><br>
            </div>
            
            <h1 id="editTitle">Edit Projects</h1>
            
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
        $sSQL="SELECT * FROM projects ORDER BY $sql_orderBy";
        $res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );
        
        if ($res->num_rows != 0) {
            fshowProjects($res);
            }
        }
        exit();
        
        /* this function prints table data for each project */
        function fPrintProjects($res) {
            while ($row = $res->fetch_assoc()) {
            ?>
            <tr>
                <?php
                if ($db=fConnectDb()) {
                    $iId = $row['id'];
                    if (projInUse($iId,$db)) {
                        echo '<td><input type="checkbox" style="opacity:0.8;" disabled="disabled" title="project is in use"></td>';
                    }
                    else {
                        echo '<td><input type="checkbox" name="chkSelected[]" value='.$iId.' id="checkable"></td>';
                    }
                }
                ?>
                
                <?php
                $bIsPatching = false;
                $sProjName = $row['name'];
                $aProjName = explode(' ', $sProjName);
                foreach($aProjName as $sWord) {
                    if (strtolower($sWord) == 'patching') {
                        $bIsPatching = true;
                    }
                }
                if ($bIsPatching == false) {
                    echo "<td>$sProjName</td>";
                }
                else {
                    echo "<td style='background-color:#e2edc3;'>$sProjName</td>";
                }
                ?>
               
                <td><?=$row['description']?></td>
                <td><a href="updateProject.php?id=<?=$row['id']?>">Edit</a></td>
            </tr>
            <?php
            }
        }
        function fshowProjects($res) {
            
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
                                print '<a href="/editProjects.php?orderBy=name_asc">Project Name<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                            }
                            else if($_GET['orderBy'] == 'name_asc') {
                                print '<a href="/editProjects.php?orderBy=name_desc">Project Name<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                            }
                            else {
                                print '<a href="/editProjects.php?orderBy=name_asc">Project Name</a>';
                            }
                        
                        ?>
                    </td>
                    <td>Description</td>
                    <td>&nbsp;</td>
        
                </tr>
                </thead>
                
                <tbody>
                <?php
                    fPrintProjects($res);
                  ?>
                </tbody>
                
                <tfoot>
                <tr>
                    <td colspan="4"><input type="submit" value="Delete" name="action" id="deleteBtn" style='outline:0;'></td>
                </tr>
                </tfoot>
                
            </table>
            </form>
        </body>
        
            <?php
            
            }
            
            /* function to delete project based on selected checkboxes */
            
            function fCheckAction($db) {
                $gotRows = false;
                if ( isset( $_POST['action'] ) && $_POST['action']) {
                    $sAction=$_POST['action'];
                    if ($sAction == "Delete") {
                        $aSelected=$_POST['chkSelected'];
                        foreach ($aSelected as $iSelected) {
                            $sSQL="DELETE FROM projects WHERE id='$iSelected'";
                                if ( mysqli_query($db, $sSQL) ) {
                                    $iAffectedRows=$db->affected_rows;
                                    if (  $iAffectedRows > 0 )
                                    {
                                        $gotRows = true;
                                    } else {
                                        $gotRows = false;
                                    }
                                }
                    
                        }
                        if ($gotRows) {
                            echo '<script type="text/javascript">alert("Deletion successful.");</script>';
                        }
                        else {
                            echo '<script type="text/javascript">alert("Select a project to delete.");</script>';
                        }
                    }
                }
            }
            ?>
            
        
        </html>