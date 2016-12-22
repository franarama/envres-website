<?php
include('inc/db.inc.php');
include('inc/functions.inc.php');
?>


<html>

<head>
    <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
    <link rel="icon" type="image/png"  href="Images/logo.png">
    <title>ShowEnvReservations</title>
    
    <style>
        #nav {
            padding-left:190px;
        }
        #nav a {
            margin-left:10%;
            margin-right:10%;
        }
      #envReservationTable {
            font-family: Arial;
            border-collapse: collapse;
            width: 95%;
            margin-left: 2.5%;
            border-bottom: 2px solid #949ca1;
            color: #414b56;
            font-size: 15px;
            letter-spacing: 0.1em;
            margin-top:2%;
        }
        #envReservationTable tr {
            text-align: center;
            border: 1px dotted #949ca1;
            background-color: #faf8f6;
        }
        #envReservationTable thead tr{
            color: #76b900;
            border-bottom: 2px solid #949ca1;
            background-color: #e3e3e0;
        }
        #envReservationTable tr:first-child a {
            color: #76B900;
        }
        #envReservationTable td {
            padding: 10px;
            border: 1px dotted #949ca1;
        }
        #envReservationTable td:last-child, #envReservationTable td a:visited {
            letter-spacing: 0.1em;
            color: #76b900;
            font-weight: normal;
            font-style: italic;
        }
        #envReservationTable tr:hover {
            background-color: #e3e3e0;
        }
        #h2 h2 {
            font-size: 18px;
            letter-spacing: 0.1em;
            color: #414b56;
            font-weight: normal;
            text-decoration: none;
            position:relative;
            margin-top:2%;
            margin-left:14%;
            word-spacing: 4px;
        }
        #rightLink {
            float:right;
            margin-top: -2.2%;
            letter-spacing: 0.1em;
            font-style: italic;
            margin-right: 3%;
        }
        #search {
            height: 30px;
            letter-spacing: 0.1em;
            color: #414b56;
            font-size: 15px;
            margin-top:35%;
            text-align: center;
            border-radius: 12px;
            border: 1px solid #949ca1;
            background: url('Images/searchicon.png') no-repeat;
            background-size: 30px 25px;
            margin-bottom: 30px;
        }
        #topLeft {
            float:left;
            margin-left: 4%;
            margin-top: 1%;
        }
        #topLeft #button {
            width: 80px;
            height: 30px;
            margin-top: 15%;
            background-color: #e3e3e0;
            color: #414b56;
            letter-spacing: 0.1em;
            font-style: italic;
            border-radius: 6px;
            border: 2px solid rgba(65,75,86, 0.3);             
        }
        #none {
            color: #414b56;
            font-style: italic;
            letter-spacing: 0.1em;
            margin-top:5%;
        }
        #envReservationTable td:nth-child(3) {
            width:50%;
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
              #search {
                margin-bottom: 20px;
              }
          }
    </style>
    
<script type='text/javascript'>
    
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

    <div id="nav">
        <center>
            <a href="versions.php">Overview</a>
            <a href="reservations.php">View and Request Reservations</a>
            <a href="maintain.php" style="color:#76b900;">Manage Reservations</a>
        </center>
    </div>
    
    <?php
    if ($db = fConnectDb()) {
        

    ?>
    <div id="topLeft">
        <input type="button" id="button" value="Go Back" onclick="history.go(-1);" style="outline:0;"/><br><br>
    </div>
    <div id="rightLink">
        <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
    </div>
    
    <center>
        
    <div id="h2"><h2>Reservations for <?php getEnvName($db,$_GET['id']);?></h2></div>
    
</body>
</html>

    <?php
    /* gets a GET variable orderBy and puts in the SQL query to allow
       sorting by headers */
    
    
        $sql_orderBy = "id ASC";
        
        if (!isset($_GET['orderBy'])){
            $_GET['orderBy']='startdate';
        }
            
        else {
            switch($_GET['orderBy']) {
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

        $id=$_GET['id'];
        
        $sSQL = "SELECT e.id,e.owner,e.startdate,e.enddate,eg.name AS env_name,p.name AS project_name,e.description FROM env_reservations e " .
                "LEFT JOIN egamingEnvs eg ON e.env_id = eg.id LEFT JOIN projects p ON e.project_id = p.id WHERE e.env_id=$id ORDER BY $sql_orderBy";
                
        $response=mysqli_query($db,$sSQL);
        
        if ($response->num_rows != 0) { //if there are reservations
            fshowSchedule($response);
        }
        
        else { //if no reservations
            echo '<div id="none">';
            echo '--No reservations for this environment--';
            echo '</div>';
        }
}
    ?>
    <?php
    
    exit();
    
    /* this function prints all the reservation info for each reservation */
    
    function fPrintProject($res) {
        
        while ($row = $res->fetch_assoc()) {
            
        ?>
        
        <tr>
            <?php
            
            $bIsPatching = false;
            $sProjName = $row['project_name'];
            $aProjName = explode(' ', $sProjName);
            foreach($aProjName as $sWord) {
                if (strtolower($sWord)=='patching') {
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
            <td><?php
            if ($db=fConnectDb()) {
                $aIntegrations = getIntegrations($db,$row['id']);
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
    
    /**************************headers section******************************/

    ?>
    <form method="post">
    <table id="envReservationTable" class="order-table table">
        <thead>
        <tr>

            <td>
                <?php
                
                /* get the GET orderBy variable and show appropriate arrow on the header */
                /* and set the GET variable  */
                
                $id = $_GET['id'];
                
                if ($_GET['orderBy'] == 'project_name_desc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=project_name_asc">Project<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'project_name_asc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=project_name_desc">Project<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=project_name_asc">Project</a>';
                }
                ?>
            </td>
            <td>Integration(s)</td>            
            <td>
                <?php
                if ($_GET['orderBy'] == 'description_desc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=description_asc">Description<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'description_asc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=description_desc">Description<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=description_asc">Description</a>';
                }
                ?>
            </td>
            <td>
                <?php
                if ($_GET['orderBy'] == 'owner_desc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=owner_asc">Owner<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'owner_asc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=owner_desc">Owner<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=owner_asc">Owner</a>';
                } 
                ?>
            </td>
            <td>
                <?php
                if ($_GET['orderBy'] == 'startdate_desc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=startdate_asc">Start<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'startdate_asc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=startdate_desc">Start<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=startdate_asc">Start</a>';
                }
                ?>
            </td>
            <td>
                <?php
                if ($_GET['orderBy'] == 'enddate_desc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=enddate_asc">End<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'enddate_asc') {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=enddate_desc">End<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/showEnvReservations.php?id='.$id.'&orderBy=enddate_asc">End</a>';
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
    /*******************************************************/
    // this function echoes the env name based on a given ID
    // to be used in the title of the page
    
    function getEnvName($db,$id) {
        $sSQL = "SELECT name FROM egamingEnvs WHERE id=$id";
        $response = mysqli_query($db,$sSQL);
        if($response) {
            while($row=mysqli_fetch_array($response)) {
                $env_name = $row['name'];
            }
        }
        echo $env_name;
    }
    
    /*******************************************************/
?>
