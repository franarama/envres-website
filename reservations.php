<?php
session_start();
include('inc/functions.inc.php');
?>

<html>
    <head>
        <title>Reservation Calendar</title>
        <link rel="icon" type="image/png"  href="Images/logo.png">   
        <link rel="stylesheet" type="text/css" href="Css/defaultCSS.css">
        <link rel="stylesheet" type="text/css" href="Css/calendar2.css">
        <!--
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		-->
		
		<link rel="stylesheet" href="libs/jquery-ui-themes-1.11.4/themes/smoothness/jquery-ui.css">
		<script src="libs/jquery-1.11.3.min.js"></script>
		<script src="libs/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		
        <style>
        header {
            height:90px;
        }
		#leftTop {
			float: left;
			margin-left: 20px;
			margin-top: -30px;
		}
		
		#topBottom {
			width: 100%;
			border-top: 2px dotted rgba(65,75,86,0.4);
		}
        .calendar th {
            background-color: #e3e3e0;
        }
		a:link, a:visited {
			color: #414b56;
		}
        #fromDate, #toDate {
            font-size: 16px;
			padding-top: 15px;
        }
        #topCenter {
            height:120px;
			width: 30%;
			min-width: 600px;
			text-align: center;
			float: right;
			margin-right: 35%;
        }
        #topCenter input[type=text] {
            height: 35px;
            width: 200px;
            font-size: 15px;
            color: #414b56;
            text-align: center;
            background-color: rgba(65,75,86,0.1);
            border:1px dotted rgba(65,75,86,0.4);
            border-radius:3px;
			outline:0;
        }
		
		#fromDate {
			float: left;
		}
		
		#toDate {
			float: right;
		}
		#topCenter input[type=text]:focus {
			border:1px solid rgba(65,75,86,0.6);
		}
        #topCenter h1 {
            font-size:20px;
			margin-top: -30px;
			margin-bottom: 20px;
        }
		
		#goButton {
           width: 60px;
           height: 35px;
           margin-left: 200px;
           background-color: #e3e3e0;
           color: #414b56;
           border:2px solid rgba(65,75,86,0.3);
           border-radius: 4px;
           font-size: 14px;
		}
		.calendarEntry {
			background-color: rgba(118,185,0,0.2);
		}
		/**
        #top {
            border-bottom: 2px dotted rgba(65,75,86,0.4);
        }
        **/
        #lnkPrevious {
            margin-right: 33%;
        }
        #lnkNext {
            margin-left: 33%;
        }
        ul#menu {
            margin-top: 40px;
            margin-right: 10px;
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
                var dStart = new Date();
                var MilliSecsPerDay = 86400000;
                var curDates = new Array();
                
                Date.prototype.getCurrent = function() { //Adds a function "getCurrent" to date object which returns a string yyyy-mm-dd
                  var sYear=this.getFullYear();
                  var sMonth=this.getMonth()+1;
                  var sDay=this.getDate();
                  if ( sMonth < 10) {
                    sMonth = '0'+sMonth;
                  }
                  if (sDay<10) {
                    sDay='0'+sDay;
                  }
                  
                  return sYear+'-'+sMonth+'-'+sDay;
                };

                    
                function getElementAbsPosition(element){
                  console.log("getElementAbsPosition() started");
                  var pos={};
                  pos.top=element.offset().top-$(document).scrollTop();
                  pos.left=element.offset().left;
                  return pos;
                }
                function setDateRangeVisibility() {
					//this hides the previous and next links
                    document.getElementById('lnkPrevious').style.visibility="hidden";
                    document.getElementById('lnkNext').style.visibility="hidden";
                }
                function setCurrentWeekVisibility() {
					//this shows the previous and next links
                    document.getElementById('lnkPrevious').style.visibility="visible";
                    document.getElementById('lnkNext').style.visibility="visible";
                }
                function validateInputBoxes() {
					//this checks that the datepicker inputs are both not null
                    var fromDate = $(datepicker).datepicker('getDate');
                    var toDate = $(datepicker2).datepicker('getDate');
                    if (fromDate !== null && toDate !== null) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
                function clearInputBoxes() {
				//this sets the datepicker input boxes to null
                $('#datepicker').datepicker('setDate',null);
                $('#datepicker2').datepicker('setDate',null);
                }
/********************************************* */
                function getReservations(start_dt,end_dt,onSuccess){
                  //This will call get_env_reserations.php to get all reservations within a date range
                  console.log('getReservations() started');
                  var dNow=new Date();
                  $.ajax({
                    type: "POST",
                    data: {
                      'start_dt' : start_dt,
                      'end_dt': end_dt
                    },
                    url: "api/get_reservations.php?"+dNow.getTime(), //Throwing a date/time on the end ensures no caching in the response
                    success: function(data){
                      if (onSuccess !== undefined && typeof(onSuccess) == 'function' ){ //ensure onSuccess is a function
                        onSuccess(data); //Call the function passed as "onSuccess" with the returned data
                      }
                    },
                    dataType: 'json'
                  });
                }
/********************************************* */
                function getEnvReservations(env,start_dt,end_dt,onSuccess){
                  
                  //This will call get_env_reserations.php to get reservations for a particular environment within a date range
                  console.log('getReservations() started');
                  $.ajax({
                    type: "POST",
                    data: {
                      'env' : env,
                      'start_dt' : start_dt,
                      'end_dt': end_dt
                    },
                    url: "api/get_env_reservations.php",
                    success: function(data){
                      if (onSuccess !== undefined && typeof(onSuccess) == 'function' ){ //ensure onSuccess is a function
                        onSuccess(env,data); //Call the function passed as "onSuccess" with the returned data
                      }
                    },
                    dataType: 'json'
                  });
                }

/********************************************* */               
                function getEnvironments(onSuccess) {
                  //This will call an external page to get data, and then call the passed function (onSuccess) with the data from the page
                  console.log('getEnvironments() started');
                  $.ajax({
                    type: "GET",
                    url: "api/get_envs.php",
                    success: function(data){
                      
                      if (onSuccess !== undefined && typeof(onSuccess) == 'function' ){ //ensure onSuccess is a function
                        onSuccess(data); //Call the function passed as "onSuccess" with the returned data
                      }
                    },
                    dataType: 'json'
                  });
                }

/********************************************* */
				function UpdateCalendar(tblCalendar,dStart, dEnd, EnvData,ResData)
				{
                //Generates a calendar with JavaScript
				console.log('generateCalendar() started');

                  var aWeekDays= ["Sun","Mon","Tues","Wed","Thu","Fri","Sat"];
                  var aMonths = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
                  
                  var iNumDays=Math.floor((dEnd.getTime() - dStart.getTime() ) / MilliSecsPerDay)+1 ; //Variable for number of days

				  var dCurrent = new Date(dStart);
                  
                  
                  //Header section :: Create the header of the calendar containing columns labelled with weekday and Month/Mday/Year
                  console.log("== Headers section ==")
				  var trHead = document.createElement('tr'); 	//Create Table Row
                  var thBlank = document.createElement('th');
                  thBlank.width=100;
                  trHead.appendChild( thBlank ); //Blank header column
                 
                  var aDateCols = [];
                  
                  var tCalHead = document.createElement('thead');
                  tCalHead.appendChild(trHead);
                  
                  var tracker=0;
                  
                  for (var iDay=0; iDay < iNumDays; iDay++) {
        
                    dCurrent.setTime(dStart.getTime()+(tracker * MilliSecsPerDay));
                    //console.log("Create header for day "+iDay);

                    var thNew = document.createElement('th'); //Create Table Header Column
                    trHead.appendChild(thNew); //Append TH to TR
                    
                    if (iDay < 7) {
                         thNew.appendChild( document.createTextNode(aWeekDays[dCurrent.getDay()]) );
                    }
                    else if (iDay >= 7) {
                        thNew.appendChild( document.createTextNode(aWeekDays[dCurrent.getDay() % 7]) );
                    }
                   
                    thNew.appendChild( document.createElement('br') );
                    thNew.appendChild( document.createTextNode( aMonths[dCurrent.getMonth()]+" "+ dCurrent.getDate()+" "+dCurrent.getFullYear() ) );
                    
                    var sCurrentHeaderDate=dCurrent.getCurrent();
                    aDateCols[sCurrentHeaderDate] = thNew;
                    
                    tracker++;
                  }
									
                  tblCalendar.appendChild(tCalHead);
                  
                  //End of Header section

                  
                  var iHeaderHeight=$(aDateCols[Object.keys(aDateCols)[0]]).height();
                  
                  var trEnv = [];
                  var tdEnv = [];
                  
                  var tCalBody =  document.createElement('tbody');

                  
                  //Environments section :: Create rows per each environment, plus a following column equal in length to the number of "days" columns
                  console.log("== Environments section ==")
                  for (var iIndex in EnvData){ //data is an object containing sub-objects
                    var oEnv=EnvData[iIndex]; //each item in data is an object containing various values
                    var iID=oEnv.id;
                    var sEnvName=oEnv.name;
                    
                    //console.log("Create row for environment: "+sEnvName);
                    var trNew = document.createElement('tr'); 	//Create Table Row
                    //var tdNew = document.createElement('td');
					var tdNew = document.createElement('td');
                    var a = document.createElement('a');
                    a.setAttribute('href','showEnvReservations.php?id='+oEnv.id);
					a.setAttribute('title','Click to view reservations for this env');
                    a.appendChild(document.createTextNode(sEnvName));
					tdNew.appendChild(a);
				
                   // tdNew.appendChild( document.createTextNode( sEnvName ) ); //Create a column with text containing the "name" of the environment
                    trNew.appendChild(tdNew);
      
                    tdEnv[sEnvName] = document.createElement('td');
                    tdEnv[sEnvName].colSpan=iNumDays;
                    tdEnv[sEnvName].style.height=(iHeaderHeight*(1.5)); //Column is 1.5 x the height of the headers

                    trNew.appendChild(tdEnv[sEnvName]);

                    trEnv[sEnvName] = trNew;
                    
                    tCalBody.appendChild(trNew); //Append row to table
                  }
                  tblCalendar.appendChild(tCalBody);
                  //End of environment section
                

                  //Reservations section :: Populate the data column of each environments section with current project reservation(s)
                  console.log("== Reservations section ==")

                  
                  for (sEnv in ResData) { //Data should be an object which contains various sub-objects, each named after an environment
                    var oReservations=ResData[sEnv];
                    var tdCurrent=tdEnv[sEnv]; //Grab table row for current environment
                    var iNumReservations=oReservations.length;
                    var iVerticalOffset=0;
                    var oColumnPos=$(tdCurrent).offset();
                    var iColumnTop=oColumnPos.top;
                    var iColumnLeft=oColumnPos.left;
 
                    for (var indx=0; indx<iNumReservations; indx++) {
                      var oReservation =  oReservations[indx];
                      console.log(oReservation);
                      
                      var sProject=oReservation.project_name;
                      var sStart=oReservation.startdate;
                      var sEnd=oReservation.enddate;
                      var dProjectStart=new Date(oReservation.start_epoch);
                      var dProjectEnd=new Date(oReservation.end_epoch);
                      var sOwner = oReservation.owner;
                      var sDescription = oReservation.description;
                      var iId = oReservation.id;
                      
                      console.log("Add project '"+sProject+ "' to Environment '"+sEnv+"'");


                      var dvCurrentRes=document.createElement('div');
                      

                      dvCurrentRes.className='CalendarEntry';
                      dvCurrentRes.appendChild(document.createTextNode(sProject));
                      
                      var iTop = iColumnTop+iVerticalOffset;
                      dvCurrentRes.style.top = iTop;
                      
                      
                      var iLeft=$(tdCurrent).offset().left;
                      
                      if ( aDateCols[sStart] != undefined) { //If we have a column matching the start date
                        
                        iLeft=$(aDateCols[sStart]).offset().left;
                      }

                      var iWidth=$(tdCurrent).innerWidth()+$(tdCurrent).offset().left-iLeft-1;
                        
                      if ( aDateCols[sEnd] != undefined) { //If we have a column matching the end date
                        var iLastColLeft=$(aDateCols[sEnd]).offset().left;
                        iWidth=iLastColLeft-iLeft;
                      }
                      dvCurrentRes.style.left=iLeft;
                      dvCurrentRes.style.width=iWidth;
                      
                      var fBindClick=function(){
                        var oResCurrent=oReservation;
						
						if (oResCurrent.vendor_name != null) {
                        $(dvCurrentRes).attr('title', 
											 "Owner: " + oResCurrent.owner + "\n" + oResCurrent.description +
                                             "\nDuration: " + oResCurrent.startdate + " to " + oResCurrent.enddate + "\nClick to edit");                            
                        }
						
						else {
                        $(dvCurrentRes).attr('title', 
											 "Owner: " + oResCurrent.owner + "\n" + oResCurrent.description +
                                             "\nDuration: " + oResCurrent.startdate + " to " + oResCurrent.enddate + "\nClick to edit");
						}
						
                        $(dvCurrentRes).click(function(){
                            window.open("editReservation.php?id='"+oResCurrent.id+"'",'_blank');
                        });
                      };
                      
                      fBindClick(); //The binding must be in a seperate function otherwise all variable references will be from the last loop


                      tdCurrent.appendChild(dvCurrentRes);
                      iVerticalOffset+=15;
                    }
                  }
                  //End of reservations section
				  
				  /*********************************************************************************************************/
				  var trHead = document.createElement('tr'); 	//Create Table Row
                  var thBlank = document.createElement('th');
                  thBlank.width=100;
                  trHead.appendChild( thBlank ); //Blank header column
                 
                  var aDateCols = [];
                  
                  var tCalHead = document.createElement('thead');
                  tCalHead.appendChild(trHead);
                  
                  var temp=0;
                  
                  for (var iDay=0; iDay< iNumDays; iDay++) {
                    dCurrent.setTime(dStart.getTime()+(temp* MilliSecsPerDay) );
                    //console.log("Create header for day "+iDay);

                    var thNew = document.createElement('th'); //Create Table Header Column
                    trHead.appendChild(thNew); //Append TH to TR
                    
                    if (iDay < 7) {
                         thNew.appendChild( document.createTextNode(aWeekDays[dCurrent.getDay()]) );
                    }
                    else if (iDay >= 7) {
                        thNew.appendChild( document.createTextNode(aWeekDays[dCurrent.getDay() % 7]) );
                    }
                   
                    thNew.appendChild( document.createElement('br') );
                    thNew.appendChild( document.createTextNode( aMonths[dCurrent.getMonth()]+" "+dCurrent.getDate()+" "+dCurrent.getFullYear() ) );
                    
                    var sCurrentHeaderDate=dCurrent.getCurrent();
                    aDateCols[sCurrentHeaderDate] = thNew;
					temp++;
                  }
									
                  tblCalendar.appendChild(tCalHead);


                }
                
/****************/
                function UpdateLinks(){
                  $("#lnkPrevious").click(function(){
                    dStart.setTime( dStart.getTime() -(MilliSecsPerDay*7) );
                    UpdatePage();
                  });

                  $("#lnkNext").click(function(){
                    dStart.setTime( dStart.getTime() + (MilliSecsPerDay*7));
                    UpdatePage();
                  });

                  $("#lnkCurrent").click(function(){
                    dStart = new Date();
                  //Setup initial dates
                if ( dStart.getDay() > 0 ) //Not the first day of the week
                {
                    dStart.setTime(dStart.getTime() - (dStart.getDay()* MilliSecsPerDay) ); //Reset date object to first day of the week
                }
                  

                  dStart.setHours(0);
                  dStart.setMinutes(0);
                  dStart.setSeconds(0);
                  dStart.setMilliseconds(0);
                  
                    UpdatePage();
                  });
                  
                }

/****************/

                function UpdatePage(){ //Used to update the page after calendar date changed
                  
                  //Remove and regenerate table
                  $("#dvCalendar > table").remove();  //Clear table contents
                  var tblCalendar=document.createElement('table');
                  tblCalendar.className='Calendar';
                  dvCalendar.appendChild(tblCalendar);

                  dStart.setHours(0);
                  dStart.setMinutes(0);
                  dStart.setSeconds(0);
                  dStart.setMilliseconds(0);
                  
                  var dEnd=new Date();
                  
                  dEnd.setTime(dStart.getTime()+ (6* MilliSecsPerDay));
                  //dEnd.setHours(23);
                  //dEnd.setMinutes(59);
                  //dEnd.setSeconds(59);
                  GenerateCalendar(tblCalendar,dStart,dEnd);
                }
/********************************************/
				function clearAndGenerateCalendar(dStart,dEnd) {
				  //this removes and rebuilds tblCalendar 
                  $("#dvCalendar > table").remove();  //Clear table contents
                  var tblCalendar=document.createElement('table');
                  tblCalendar.className='Calendar';
                  dvCalendar.appendChild(tblCalendar);
				  GenerateCalendar(tblCalendar,dStart,dEnd);
                }
/********************************************/
                function GenerateCalendar( tblCalendar, dStart, dEnd){
                  
                  //Call getEnvironments and getReservations to pull data from remote URL's, then pass data off to "UpdateCalendar"
                  getEnvironments( function(EnvData){
                    var sStart=dStart.getCurrent();
                    var sEnd=dEnd.getCurrent();
                    getReservations(sStart,sEnd, function (ResData){
                      UpdateCalendar( tblCalendar, dStart, dEnd,EnvData,ResData ); //Run generateCalendar, pass table object "tblCalendar" as an arg
                    });
                  });
                }
/**********************************************/
                $(document).ready(function() {
				  //set up the datepicker calendars
                  $("#datepicker").datepicker({
                        dateFormat: "yymmdd",
                        numberOfMonths: 2,
                        onSelect: function(selected) {
                            $('#datepicker2').datepicker("option","minDate",selected)
                        }
                  });
                  $( "#datepicker2" ).datepicker({
                        dateFormat: "yymmdd",
                        numberOfMonths: 2,
                        onSelect: function(selected) {
                           $("#datepicker").datepicker("option","maxDate", selected)
                        }
                  });
                  
                  document.getElementById("datepicker").style.position="absolute";
                  document.getElementById("datepicker").style.zIndex="10";
                  
                  document.getElementById("datepicker2").style.position="absolute";
                  document.getElementById("datepicker2").style.zIndex="10";
                  
                  //Upate the previous/current/next week links
                  UpdateLinks();
                  
                  
                  
                  //Setup initial dates
                if ( dStart.getDay() > 0 ) //Not the first day of the week
                {
                    dStart.setTime(dStart.getTime() - (dStart.getDay()* MilliSecsPerDay) ); //Reset date object to first day of the week
                }
                  

                  dStart.setHours(0);
                  dStart.setMinutes(0);
                  dStart.setSeconds(0);
                  dStart.setMilliseconds(0);
                  
                  var dEnd=   new Date();
                  
                  dEnd.setTime(dStart.getTime()+ (6* MilliSecsPerDay));
                  //dEnd.setHours(23);
                  //dEnd.setMinutes(59);
                  //dEnd.setSeconds(59);
                  
                  GenerateCalendar($( "#tblCalendar" )[0], dStart,dEnd);
                });
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
		
		<div id="top">
		
		<div id="leftTop">
			
			<ul id="menu" class="drop">
				<li id="add"><img src="Images/greyviewicon.png" width="13px" height="16px">&nbsp;View..
				<ul id="options">
					<li><a href="reservationsList.php">Reservations list</a></li>
					<li><a href="vendorEnvsReadOnly.php">Vendor environments</a></li>
					<li><a href="contractorMachines.php">Contractor machine reservations</a></li>
				</ul>
			</li>
		</ul>
			
			<ul id="menu" class="drop">
				<li id="add"><img src="Images/greyaddicon.png" width="13px" height="13px">&nbsp;Add...
					<ul id="options">
						<li><a href="addProject.php">a project</a></li>
						<li><a href="reserveEnviro.php">a reservation</a></li>
						<li><a href="addContractorMachine.php">a contractor machine reservation</a></li>
					</ul>
				</li>
			</ul>
			
		</div>
	
		<div id="topCenter">
			
			<h1 style="font-weight:normal;">Select date range</h1>
			<h2 id="fromDate" style="font-weight:normal;color:#414b56;">
				From:&nbsp;<input type="text" placeholder="YYYYMMDD" id="datepicker">&nbsp;</h2>
			
			<h2 id="toDate" style="font-weight:normal;color:#414b56;">
				To:&nbsp;<input type="text" placeholder="YYYYMMDD" id="datepicker2">
				<button type="button" id="goButton" style="outline:0;"
					onClick="
					if (validateInputBoxes()) {
						var start_date = $(datepicker).datepicker('getDate');
						var end_date = $(datepicker2).datepicker('getDate');
						clearAndGenerateCalendar(start_date,end_date);setDateRangeVisibility();
					  
					}
					else {
					alert('Enter a valid date range');
					}"
					>Go!</button>
			</h2>
		</div>
	
		</div>
		
		<div id="topBottom">	
			<a href="previous" id="lnkPrevious" onClick="return false;">&larr; Previous Week</a>
			<a href="current" id="lnkCurrent" onClick="setCurrentWeekVisibility();clearInputBoxes();return false;">Current Week</a>
			<a href="Next" id = "lnkNext" onClick="return false;">Next Week &rarr;</a>
		</div>
		
		<div id='dvCalendar' style='margin-right:20px; margin-left: 20px;'>
		  <table id="tblCalendar" class='calendar'></table>
		</div>
		
		</form>
		 </div>
		</body>
	</html>
