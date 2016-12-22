<?php
session_start();
include('inc/functions.inc.php');
include('inc/db.inc.php');
?>

<html>
    
<head>
    <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
    <link rel="icon" type="image/png"  href="Images/logo.png">
    <title>Contractor Machines List</title>
    <style>
    	#rightTop {
            float: right;
            margin-top: -40px;
            margin-right: 30px;
		}
		
		#search {
			margin-right: -180px;
			margin-top: 60px;
		}
		
		#editTable td:first-child {
			text-align: center;
		}

        ul#menu {
            margin-top: 40px;
            margin-right: 30px;
        }
		
        ul li#add {
            width:90px;
        }
		
        ul ul#options {
            width:210px;
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
            border: 1px solid rgba(65,75,86,0.3);
            border-radius:2px;
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

    </style>
    
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
    
    <div id="editTopLeft">
        <input type="button" id="button" value="Go Back" onclick="location.href='reservations.php'"/><br><br>
    </div>
    
    <div id="rightTop">
		
		<ul id="menu" class="drop">
			<li id="add"><img src="Images/greyaddicon.png" width="13px" height="16px">&nbsp;Add..
			<ul id="options">
				<li><a href="addContractorMachine.php">A contractor machine reservation</a></li>
				<li><a href="addMachine.php">A machine</a></li>
				<li><a href="addProject.php">A project</a></li>
			</ul>
		</li>
	</ul>
		
    </div>
    
    <?php
    if ($db = fConnectDb()) {
       
    ?>
    
    <div id="rightLink">
        <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
    </div>
    
    <h1 id="editTitle">Contractor Machine Reservations</h1>

<?php
            $sql_orderBy='id ASC';
            
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
            
            
            $sSQL= "SELECT cml.id,cml.name AS cml_name,cml.company,cml.project_id,cml.machine_id,m.name AS machine_name,p.name AS project_name FROM `contractor_machine_listing` cml
                    LEFT JOIN projects p ON cml.project_id = p.id
                    LEFT JOIN machines m ON cml.machine_id = m.id ORDER BY $sql_orderBy";
                    
            $res=mysqli_query($db,"$sSQL") or die("Could not query databases: " . mysqli_error($db) );
            if ($res->num_rows != 0) {
                fshowListings($res);
            }
            
    }
    
?>
<?php
exit();
function fPrintListings($res) {
    while ($row = $res->fetch_assoc()) {
    ?>
    <tr>
        <td><?=$row['cml_name']?></td>
        <td><?=$row['company']?></td>
        <td><?=$row['project_name']?></td>
        <td><?=$row['machine_name']?></td>
        <td><a href="updateContractorMachineReservation.php?id=<?=$row['id']?>" target="_blank" style="color: #76B900;">Edit</a></td>
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
                    <td colspan="3">&nbsp;</td>
                    <td style="color:#414b56;font-style:normal;"><?=$row['name']?></td>
					<td>&nbsp;</td>
                </tr>
                <?php
            }
        }
    }
}
    
    
function fshowListings($res) {

    ?>
    <form method="post">
    <table id="editTable" class="order-table table">
        <thead>
        <tr>

            <td>
                <?php
                if ($_GET['orderBy'] == 'name_desc') {
                    print '<a href="/contractorMachines.php?orderBy=name_asc">Name<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if($_GET['orderBy'] == 'name_asc') {
                    print '<a href="/contractorMachines.php?orderBy=name_desc">Name<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/contractorMachines.php?orderBy=name_asc">Name</a>';
                }
                ?>
            
            </td>

            <td>
                <?php
                if ($_GET['orderBy'] == 'company_desc') {
                    print '<a href="/contractorMachines.php?orderBy=company_asc">Company<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'company_asc') {
                    print '<a href="/contractorMachines.php?orderBy=company_desc">Company<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/contractorMachines.php?orderBy=company_asc">Company</a>';
                }
                ?>
            </td>
            <td>
                <?php
                if ($_GET['orderBy'] == 'project_desc') {
                    print '<a href="/contractorMachines.php?orderBy=project_asc">Project<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'project_asc') {
                    print '<a href="/contractorMachines.php?orderBy=project_desc">Project<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/contractorMachines.php?orderBy=project_asc">Project</a>';
                }
                ?>
            </td>
            <td>
                <?php
                if ($_GET['orderBy'] == 'machine_desc') {
                    print '<a href="/contractorMachines.php?orderBy=machine_asc">Machine Name<img src="Images/downArrow.png" width="10px" height="10px"></a>';
                }
                else if ($_GET['orderBy'] == 'machine_asc') {
                    print '<a href="/contractorMachines.php?orderBy=machine_desc">Machine Name<img src="Images/upArrow.png" width="10px" height="10px"></a>';
                }
                else {
                    print '<a href="/contractorMachines.php?orderBy=machine_asc">Machine Name</a>';
                } 
                ?>
            </td>
            <td>&nbsp;</td>
        </tr>
        </thead>
        <tbody>
        <?php
            fPrintListings($res);
          ?>
         </tbody>
    </table>
    </form>
</body>
    <?php
    }
    ?>
</html>