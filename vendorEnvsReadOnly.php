<?php
    session_start();
    include('inc/functions.inc.php');
    include('inc/db.inc.php');
?>

<html>

<head>
    <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
    <link rel="icon" type="image/png"  href="Images/logo.png">
    <title>Vendor Environments List</title>

    <style>
        #editTable td:last-child, #editTable th:last-child {
            color: #414b56;
            font-style: normal;
        }
        #editTable td:first-child, #editTable th {
            text-align: center !important;
        }
    </style>
    <script language="JavaScript">
        
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

    ?>
    <div id="topLeft">
        <input type="button" id="button" value="Go Back" onclick="location.href='reservations.php'"/><br><br>
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
        <td><?=$row['name']?></td>
        <td><?=$row['description']?></td>
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
            
                <th>
                <?php
                    
                    /* get the GET orderBy variable and show appropriate arrow on the header */
                    /* and set the GET variable */
                    
                    if ($_GET['orderBy'] == 'name_desc') {
                        print '<a href="/vendorEnvsReadOnly.php?orderBy=name_asc">Environment Name<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                    }
                    else if($_GET['orderBy'] == 'name_asc') {
                        print '<a href="/vendorEnvsReadOnly.php?orderBy=name_desc">Environment Name<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                    }
                    else {
                        print '<a href="/vendorEnvsReadOnly.php?orderBy=name_asc">Environment Name</a>';
                    }
                    ?>
            </th>
            <th>Description</th>

        </tr>
        </thead>
        
        <tbody>
        <?php
            fPrintVendorEnvs($res);
          ?>
        </tbody>
        
    </table>
    </form>
</body>

    <?php
    
    }
    
    ?>
    

</html>