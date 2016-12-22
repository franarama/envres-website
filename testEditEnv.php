<?php
session_start();
include('inc/functions.inc.php');
include('inc/db.inc.php');
checkLogin();
$_SESSION['orderBy'] = ' eg.id ASC';
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
    <style>
        #nav {
            padding-left:190px;
        }
        #nav a {
            margin-left:10%;
            margin-right:10%;
        }
        #tbl #deleteBtn {
            width: 80px;
            height: 30px;
            margin-top: 5px;
            margin-bottom: 5px;
            background-color: #e3e3e0;
            color: #414b56;
            letter-spacing: 0.1em;
            border:2px solid rgba(65,75,86,0.3);
            border-radius: 4px;
        }           
       #tbl {
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
        #tbl tr {
            text-align: center;
            border: 1px dotted #949ca1;
            background-color: #faf8f6;
        }
        #tbl thead tr{
            color: #76b900;
            border-bottom: 2px solid #949ca1;
            background-color: #e3e3e0;
        }
        #tbl tbody tr:hover {
            background-color: #e3e3e0;
        }
        #tbl thead th, #tbl thead a:link, #tbl thead a:visited {
            color: #76b900;
            padding: 10px;
            font-weight: normal;
        }
        #tbl thead th:nth-child(2) {
            font-style: italic;
        }
        #tbl tr:first-child a {
            color: #76B900;
        }
        #tbl td {
            padding: 10px;
            border: 1px dotted #949ca1;
        }
        #tbl th {
            border: 1px dotted #949ca1;
        }
        #tbl td:last-child, #tbl td a:visited {
            letter-spacing: 0.1em;
            color: #76b900;
            font-weight: normal;
            font-style: italic;
        }
        #tbl th:nth-child(3) {
            font-style:italic;
        }
        #tbl tfoot td {
            border: none;
        }
        #h2 h2 {
            font-size: 20px;
            letter-spacing: 0.1em;
            color: #414b56;
            font-weight: normal;
            text-decoration: none;
            position:relative;
            margin-top:2%;
            text-align: center;
            margin-right: 16%;
        }
        #topLeft {
            float:left;
            margin-left: 5%;
            margin-top: 1%;
        }
        #topLeft #button {
            width: 80px;
            height: 30px;
            margin-top: 5px;
            margin-bottom: 5px;
            margin-left:-25%;
            background-color: #e3e3e0;
            color: #414b56;
            letter-spacing: 0.1em;
            font-style: italic;
            border-radius: 6px;
            border: 2px solid rgba(65,75,86, 0.3);
        }
        #topLeft a, #topLeft a:visited {
            color: #414b56;
            letter-spacing: 0.1em;
            font-style: italic;
            text-decoration:none;
            margin-left: -25%;
        }
        #tbl td a, #envsList td a:visited {
            letter-spacing: 0.1em;
            color: #76b900;
            font-weight: normal;
            font-style: italic;
        }
        #logoutBtn {
            float:right;
            margin-top: -50px;
            margin-right: 40px;
            color: rgba(227,227,224,0.7);
            letter-spacing: 0.2em;
            width: 70px;
            height: 30px;
            font-weight: normal;
            font-size:20px;
            font-style:italic;
            text-decoration: none;
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
            margin-top:55%;
            text-align: center;
            border-radius: 12px;
            border: 1px solid #949ca1;
            background: url('Images/searchicon.png') no-repeat;
            background-size: 30px 25px;
        }
        #search:focus {
            outline: none;
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

    <script language="JavaScript">
        //funtion to select all checkboxes
        function toggle(source) {
            checkboxes = document.querySelectorAll('input[type="checkbox"]');
            for (var i=0; i < checkboxes.length; i++) {
                if (checkboxes[i] != source) {
                    checkboxes[i].checked=source.checked;
                }
            }
        }
/********************************************* */
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
/*********************************************  */ 
        function getEnvs(env,td,onSuccess){
          
          console.log('getEnvs() started');
          $.ajax({
            type: "POST",
            data: {
              'env': env
            },
            url: "test_get_eg_v.php",
            success: function(data){
              if (onSuccess !== undefined && typeof(onSuccess) == 'function' ){ //ensure onSuccess is a function
                onSuccess(env,td,data); //Call the function passed as "onSuccess" with the returned data
              }
            },
            dataType: 'json'
          });
        }
 
/********************************************* */
        function getEgEnvs(onSuccess) {
            console.log('getEgEnvs() started');
            $.ajax({
                type:"GET",
                data: {
                    
                },
                url: "api/get_envs.php",
                success: function(data) {
                    if (onSuccess !== undefined && typeof(onSuccess) == 'function' ) {
                        onSuccess(data);
                    }
                },
                dataType: 'json'
            });
        }
/********************************************* */
$(document).ready(function() {
    generateTable($("#tbl")[0]);
});

/********************************************* */
    function generateTable(tbl) {
         
         tbl.setAttribute('class','order-table table'); //for the search bar
         
        /*************Headers section**************/
        var thead = document.createElement('thead');
        var trHead = document.createElement('tr');
        
        var thEnv = document.createElement('th');
        var envLink = document.createElement('a');
        envLink.setAttribute('href','test.php');
        envLink.appendChild(document.createTextNode('Environment'));
        thEnv.appendChild(envLink);
        thEnv.style.fontStyle='normal';
        
        var thVendor = document.createElement('th');
        thVendor.appendChild(document.createTextNode('Vendor Environment(s)'));
        thVendor.style.fontStyle='normal';
        
        var thBlank = document.createElement('th');
        thBlank.appendChild(document.createTextNode(''));
        
        trHead.appendChild(thEnv);
        trHead.appendChild(thVendor);
        trHead.appendChild(thBlank);
        
        thead.appendChild(trHead);
        tbl.appendChild(thead);
        
        var tBody=document.createElement('tbody');
        
        
        getEgEnvs(function (EgEnvData) {
            
            for(sEgEnv in EgEnvData) {
            
                var trNew = document.createElement('tr');
                
                var oEgEnv = EgEnvData[sEgEnv];
                var envName = oEgEnv.name;
                var id = oEgEnv.id;
    
                
                var tdEgEnv = document.createElement('td');
                var aEnv = document.createElement('a');
                aEnv.setAttribute("href","showEnvReservations.php?id="+oEgEnv.id);
                aEnv.setAttribute('title','Click to view reservations for this env');
                aEnv.appendChild(document.createTextNode(envName));
                aEnv.style.color="#414b56";
                aEnv.style.fontStyle="normal";
                tdEgEnv.appendChild(aEnv);
                trNew.appendChild(tdEgEnv);
                var tdVendors = document.createElement('td');
                getEnvs(envName,tdVendors,function(envName,tdCurrent,EnvData) {
                    
                    for (sEnv in EnvData) {
                        var oEnv = EnvData[sEnv];
                        var sVendorName = oEnv.vendor_name;
                        tdCurrent.appendChild(document.createTextNode(sVendorName));
                        tdCurrent.appendChild(document.createElement('br'));
                    }
                    });
                
                trNew.appendChild(tdVendors);
                var editTd = document.createElement('td');
                var a = document.createElement('a');
                a.setAttribute('href','updateEnvironment.php?id='+oEgEnv.id);
                a.setAttribute('target','_blank');
                a.appendChild(document.createTextNode('edit'));
                editTd.appendChild(a);
                trNew.appendChild(editTd);
                
                tBody.appendChild(trNew);
                    
            }
            
            tbl.appendChild(tBody);
           
      });
    }


    </script>

</head>

<body>
    <header>
        <h1><img src="Images/logoreverse.png"></h1>
        <h2><a href="logout.php" id="logoutBtn">Logout</a>
</h2>
    </header>

    <div id="nav">
        <center>
            <a href="versions.php">Overview</a>
            <a href="reservations.php">View and Request Reservations</a>
            <a href="maintain.php" style="color:#76b900;">Manage Reservations</a>
        </center>
    </div>
    
    <div id="topLeft">
         <input type="button" id="button" value="Go Back" onclick="history.go(-1)"><br><br> 
         <a href="addEnvironment.php"><img src="Images/greyaddicon.png" width="15px" height="15px">&nbsp;Add an environment</a><br><br><br>
    </div>
    
    <div id="rightLink">
        <input type="text" id="search" placeholder="Search" class="light-table-filter" data-table="order-table" >
    </div>
    
    <div id="h2"><h2>Environments</h2></div>
        
    <form method="POST" action="api/debug.php">
        
    <table id="tbl">
        
    </table>

    </form>

    
    
</body>
</html>
