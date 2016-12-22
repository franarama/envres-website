<?php
session_start();
include('inc/functions.inc.php');
include('inc/db.inc.php');
?>

<html>
    
<head>
    <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
    <link rel="icon" type="image/png"  href="Images/logo.png">
    <title>Reservations List</title>

    <script>
        (function(document) { //for the search bar
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
        <input type="button" id="button" value="Go Back" onclick="location.href='reservations.php'" style="outline:0;"/><br><br>
    </div>
    
    <?php
    if ($db = fConnectDb()) {
       
    ?>
    
    <div id="rightLink">
        <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
    </div>

    <h1 id="editTitle">Reservations</h1>

<?php
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
            
            $sSQL= "SELECT e.id,e.owner,e.startdate,e.enddate,eg.name AS env_name,p.name AS project_name,e.description FROM `env_reservations` e
                    LEFT JOIN egaming_envs eg ON e.env_id = eg.id
                    LEFT JOIN projects p ON e.project_id = p.id WHERE eg.enabled=1 ORDER BY $sql_orderBy, startdate ASC";
            $res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );
            if ($res->num_rows != 0) {
                fshowSchedule($res);
            }
            
    }
    
?>
<?php
exit();
function fPrintProject($res) {
    while ($row = $res->fetch_assoc()) {
    ?>
    <tr>
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
        
        <!--<td>$row['project_name']</td>-->
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
        <td style="text-align: left;"><?=$row['description']?></td>
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

            <td>
                <?php
                if ($_GET['orderBy'] == 'env_name_desc') {
                    print '<a href="/reservationsList.php?orderBy=env_name_asc">Environment<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if($_GET['orderBy'] == 'env_name_asc') {
                    print '<a href="/reservationsList.php?orderBy=env_name_desc">Environment<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/reservationsList.php?orderBy=env_name_asc">Environment</a>';
                }
                ?>
            
            </td>

            <td>
                <?php
                if ($_GET['orderBy'] == 'project_name_desc') {
                    print '<a href="/reservationsList.php?orderBy=project_name_asc">Project<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'project_name_asc') {
                    print '<a href="/reservationsList.php?orderBy=project_name_desc">Project<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/reservationsList.php?orderBy=project_name_asc">Project</a>';
                }
                ?>
            </td>
            
            <td>Integration(s)</td>
            
            <td>
                <?php
                if ($_GET['orderBy'] == 'description_desc') {
                    print '<a href="/reservationsList.php?orderBy=description_asc">Description<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'description_asc') {
                    print '<a href="/reservationsList.php?orderBy=description_desc">Description<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/reservationsList.php?orderBy=description_asc">Description</a>';
                }
                ?>
            </td>
            <td>
                <?php
                if ($_GET['orderBy'] == 'owner_desc') {
                    print '<a href="/reservationsList.php?orderBy=owner_asc">Owner<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'owner_asc') {
                    print '<a href="/reservationsList.php?orderBy=owner_desc">Owner<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/reservationsList.php?orderBy=owner_asc">Owner</a>';
                } 
                ?>
            </td>
            <td>
                <?php
                if ($_GET['orderBy'] == 'startdate_desc') {
                    print '<a href="/reservationsList.php?orderBy=startdate_asc">Start<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'startdate_asc') {
                    print '<a href="/reservationsList.php?orderBy=startdate_desc">Start<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/reservationsList.php?orderBy=startdate_asc">Start</a>';
                }
                ?>
            </td>
            <td>
                <?php
                if ($_GET['orderBy'] == 'enddate_desc') {
                    print '<a href="/reservationsList.php?orderBy=enddate_asc">End<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'enddate_asc') {
                    print '<a href="/reservationsList.php?orderBy=enddate_desc">End<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/reservationsList.php?orderBy=enddate_asc">End</a>';
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
    </table>
    </form>
</body>
    <?php
    }
    ?>
</html>