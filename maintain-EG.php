<?php
    
    include('inc/functions.inc.php');
    include('inc/db.inc.php');
    //checkLogin();

?>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
    <link rel="icon" type="image/png"  href="Images/logo.png">
    <title>Maintain Reservations</title>
    <style>
        ul#menu {
            margin-top: 30px;
            margin-left: 46px;
        }
        ul.drop a {
            display: block;
            color: #414b56;
            font-size: 15px;
            text-decoration: none;
        }
        ul.drop a:hover {
            color: #76B900;
        }
        ul.drop, ul.drop li, ul.drop ul {
            list-style:none;
            margin: 0;
            padding: 1px;
            border: 1px solid #e3e3e0;
            background-color: #faf8f6;
            color: #414b56;
            letter-spacing: 0.1em;
            font-size: 15px;
        }
        ul.drop {
            position: relative;
            z-index: 597;
            float: left;
        }
        ul.drop li {
            float: left;
            line-height: 1.3em;
            vertical-align: middle;
            padding: 5px 10px;
        }
        ul.drop li:hover, ul.drop li.hover {
            position: relative;
            z-index: 599;
            cursor: default;
        }
        ul.drop ul {
            visibility: hidden;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 598;
            width: 237px;
            border-bottom: 1px solid #414b56;
        }
        ul.drop ul li {
            float: none;
        }
        ul.drop ul ul {
            top: -2px;
            left: 100%;
        }
        ul.drop li:hover > ul {
            visibility: visible;
        }
        #reservationList td:nth-child(5) {
            width:40%;
        }
        #reservationList td:nth-child(4) {
            width:10%;
        }
        #reservationList td:nth-child(7), #reservationList td:nth-child(8) {
            width:7%;
        }
    
    </style>

    <script language="JavaScript">
        
        /***********************************************************************/
        
        //function to select all checkboxes
        
        function toggle(source) {
            checkboxes = document.querySelectorAll('input[type="checkbox"]');
            for (var i=0; i < checkboxes.length; i++) {
                if (checkboxes[i] != source) {
                    checkboxes[i].checked=source.checked;
                }
            }
        }
        /***********************************************************************/
        
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
        
        /***********************************************************************/
        
    </script>

    
</head>

<body>
    <header>
        <h1><img src="Images/logoreverse.png"></h1>
        <h2><input type=button onclick="location.href='logout.php'" id="logoutBtn" value='Logout'></input></h2>
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
        fCheckAction($db);
    ?>

    <!-- these are the drop down add and edit menus -->
    <ul id="menu" class="drop">
        
        <li><img src="Images/greyediticon.png" width="13px" height="13px">&nbsp;Edit..
            <ul>
                <li><a href="editEnvironment.php">environments</a></li>
                <li><a href="editVendorEnv.php">vendor environments</a></li>
                <li><a href="editProjects.php">projects</a></li>
                <li><a href="editContractorMachine.php">contractor machine reservations</a></li>
                <li><a href="editMachines.php">machines</a></li>
            </ul>
        </li>
        
        <li><img src="Images/greyaddicon.png" wdith="15px" height="15px">&nbsp;Add..
            <ul>
                <li><a href="addEnvironment.php">an environment</a></li>
                <li><a href="addVendorEnv.php">a vendor environment</a></li>
                <li><a href="addProject.php">a project</a></li>
                <li><a href="reserveEnviro.php">a reservation</a></li>
                <li><a href="addContractorMachine.php">a contractor machine reservation</a></li>
                <li><a href="addMachine.php">a machine</a></li>
            </ul>
        </li>
        
    </ul>
    
    <!-- this is for the search bar -->
    <div id="rightLink">
        <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
    </div>

    <h1 id="editTitle">Reservations for EGaming</h1>

<?php
            /* this allows for sorting by headers */
            /* by calling a GET variable orderBy and placing it in the SQL query */
            
            $sql_orderBy='id ASC';
            
            if (!isset($_GET['orderBy'])){
                $_GET['orderBy']='startdate';
            }
            else {
                switch($_GET['orderBy']) {
                    case 'env_name_desc':
                        $sql_orderBy = 'env_name DESC';
                        break;
                    case 'env_name_asc':
                        $sql_orderBy = 'env_name ASC';
                        break;
                    case 'project_name_desc':
                        $sql_orderBy = 'project_name DESC';
                        break;
                    case 'project_name_asc':
                        $sql_orderBy = 'project_name ASC';
                        break;
                    case 'description_desc':
                        $sql_orderBy = 'description DESC';
                        break;
                    case 'description_asc':
                        $sql_orderBy = 'description ASC';
                        break;
                    case 'owner_desc':
                        $sql_orderBy = 'owner DESC';
                        break;
                    case 'owner_asc':
                        $sql_orderBy = 'owner ASC';
                        break;
                    case 'enddate_desc':
                        $sql_orderBy = 'enddate DESC';
                        break;
                    case 'enddate_asc':
                        $sql_orderBy = 'enddate ASC';
                        break;
                    case 'startdate_desc':
                        $sql_orderBy = 'startdate DESC';
                        break;
                   case 'startdate_asc':
                        $sql_orderBy = 'startdate ASC';
                        break;
                    
                }
            }
            
            $sSQL= "SELECT e.id,e.owner,e.startdate,e.enddate,eg.name AS env_name,res_type,p.name AS project_name,e.description FROM `env_reservations` e
                    LEFT JOIN egaming_envs eg ON e.env_id = eg.id
                    LEFT JOIN projects p ON e.project_id = p.id WHERE eg.enabled=1 AND res_type=0 ORDER BY $sql_orderBy, startdate ASC";
            $res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );
            if ($res->num_rows != 0) {
                fshowSchedule($res);
            }
            
    }
    
?>
<?php
exit();

/* this function will print the table rows and data for each listing */

function fPrintProject($res) {
    while ($row = $res->fetch_assoc()) {
    ?>
    <tr>
        <td><input type="checkbox" name="chkSelected[]" value="<?=$row['id']?>"></td>
        <td><?=$row['env_name']?></td>
        <?php
        $bIsPatching = false;
        $sProjName = $row['project_name'];
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
        <td>
            <?php
            if ($db=fConnectDb()) {
                $aIntegrations = getIntegrationsUPDATED($db,$row['id']);
                foreach($aIntegrations as $sVal) {
                    echo $sVal;
                    echo '<br>';
                }
            }
            ?>
        </td>
        <!--<td>$row['project_name']</td>-->
        <td style="text-align:left;"><?=$row['description']?></td>
        <td><?=$row['owner']?></td>
        <td><?=$row['startdate']?></td>
        <td><?=$row['enddate']?></td>
        <td><a href="editReservation.php?id=<?=$row['id']?>" target="_blank" style="color: #76B900;">Edit</a></td>
    </tr>
    <?php
    }
}
    
    
function fshowSchedule($res) {

    ?>
    <form method="post">
    <table id="editTable" class="order-table table">
        <thead>
        <tr>
            <td><input type="checkbox" onclick="toggle(this)"><br><p style="padding-top:5px;">Select all</p></td>

            <td>
                <?php
                
                /************************************************Headers section***************************************************************/
                
                /* get the GET variable and show the correct arrow image */
                /* for each table data */
                /* and setting the GET variable */
                
                if ($_GET['orderBy'] == 'env_name_desc') {
                    print '<a href="maintain-EG.php?orderBy=env_name_asc">Env<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if($_GET['orderBy'] == 'env_name_asc') {
                    print '<a href="maintain-EG.php?orderBy=env_name_desc">Env<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="maintain-EG.php?orderBy=env_name_asc">Env</a>';
                }
                ?>
            
            </td>

            <td>
                <?php
                if ($_GET['orderBy'] == 'project_name_desc') {
                    print '<a href="maintain-EG.php?orderBy=project_name_asc">Project<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'project_name_asc') {
                    print '<a href="maintain-EG.php?orderBy=project_name_desc">Project<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="maintain-EG.php?orderBy=project_name_asc">Project</a>';
                }
                ?>
            </td>
            
            <td>Integration(s)</td>
            
            <td>
                <?php
                if ($_GET['orderBy'] == 'description_desc') {
                    print '<a href="maintain-EG.php?orderBy=description_asc">Description<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'description_asc') {
                    print '<a href="maintain-EG.php?orderBy=description_desc">Description<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="maintain-EG.php?orderBy=description_asc">Description</a>';
                }
                ?>
            </td>
            
            <td>
                <?php
                if ($_GET['orderBy'] == 'owner_desc') {
                    print '<a href="maintain-EG.php?orderBy=owner_asc">Owner<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'owner_asc') {
                    print '<a href="maintain-EG.php?orderBy=owner_desc">Owner<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="maintain-EG.php?orderBy=owner_asc">Owner</a>';
                } 
                ?>
            </td>
            
            <td>
                <?php
                if ($_GET['orderBy'] == 'startdate_desc') {
                    print '<a href="maintain-EG.php?orderBy=startdate_asc">Start<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'startdate_asc') {
                    print '<a href="maintain-EG.php?orderBy=startdate_desc">Start<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="maintain-EG.php?orderBy=startdate_asc">Start</a>';
                }
                ?>
            </td>
            
            <td>
                <?php
                if ($_GET['orderBy'] == 'enddate_desc') {
                    print '<a href="maintain-EG.php?orderBy=enddate_asc">End<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'enddate_asc') {
                    print '<a href="maintain-EG.php?orderBy=enddate_desc">End<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="maintain-EG.php?orderBy=enddate_asc">End</a>';
                }
                ?>
            </td>
            
            <td>&nbsp;</td>
            
        </tr>
        </thead>
        <tbody>
            
        <?php
            fPrintProject($res);
          ?>
          
         </tbody>
        <tfoot>
        <tr>
            <td><input type="submit" value="Delete" name="action" id="deleteBtn" style="outline:0;"></td>
            <td colspan="8">&nbsp;</td>
        </tr>
       </tfoot>
    </table>
    </form>
</body>

    <?php
    
    }
    /* function to delete a reservation based on selected checkboxes */
    function fCheckAction($db) {
        $gotRows = false;
        if ( isset( $_POST['action'] ) && $_POST['action'] ) {
            $sAction=$_POST['action'];
            if ($sAction == "Delete") {
                $aSelected=$_POST['chkSelected'];
                foreach ($aSelected as $iSelected) {
                    if (is_numeric($iSelected))
                    {
                        $sSQL="DELETE FROM env_reservations WHERE id=$iSelected";
                        
                        $aIntegrations = getIntegrations($db,$iSelected);
                        
                        if (count($aIntegrations)!=0) {
                            $sSQLEnvResInt = "DELETE FROM envRes_integrations WHERE env_res_id=$iSelected";
                            if (mysqli_query($db,$sSQLEnvResInt)) {
                                $iAffectedRows=$db->affected_rows;
                                if ($iAffectedRows>0) {
                                    $gotRows=true;
                                }
                                else {
                                    $gotRows=false;
                                }
                            }
                        }
                        
                         if (mysqli_query($db, $sSQL)) {
                            $iAffectedRows=$db->affected_rows;
                            if ($iAffectedRows > 0) {
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
                    echo '<script type="text/javascript">alert("Select a reservation to delete.");</script>';
                }
            }
        }
    }
    ?>
    
</html>