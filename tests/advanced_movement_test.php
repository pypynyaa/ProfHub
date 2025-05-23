<?php
session_start();
require '../db_connect.php';
require '../widgets/pulse-widget/pulse-widget-include.php';
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced movement test</title>
    <link href='https://fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="../css/advanced_movement_test.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
</head>

<body>
    <div id="time-input" style="padding-top:20px">
		Enter time: <input type="number" id="minutes" min="0" max="45"> minutes
		<input type="number" id="seconds" min="0" max="59"> seconds
	</div>
	<div id="start">
		<button id="start">Start</button>
	</div>
	<div id="container1">
		<div id="circle2">
		    <div id="pointer"></div>
		</div>
		<p></p>
		<div id="circle">
		    <div class="point"></div>
		</div>
		<div class ="key-label">PRESS W</div>
        <div id="user-result1">Result: <span id="result"></span></div>

	</div>
    <div id="container2">
		<div id="circle4">
		    <div id="pointer2"></div>
		</div>
		<p></p>
		<div id="circle3">
		    <div class="point2"></div>
		</div>
		<div class ="key-label">PRESS D</div>
        <div id="user-result2">Result: <span id="result2"></span></div>
	</div>
    <div id="container3">
		<div id="circle6">
		    <div id="pointer3"></div>
		</div>
		<p></p>
		<div id="circle5">
		    <div class="point3"></div>
		</div>
		<div class ="key-label">PRESS A</div>
        <div id="user-result3">Result: <span id="result3"></span></div>
	</div>
	<div id="end" class="hidden">game over</div>
    <div id="time"><span id="timer"></span></div>
	<div class="container">
            <div class="score" style="background: #FFF5EE;">
                <div class="section-0">
                
                </div>
            </div>
    </div>  
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
            $("#container1 > p").html("<br><h4> <span id='result'>NaN</span></h4>");
            $("#container2 > p").html("<br><h4> <span id='result2'>NaN</span></h4>");
            $("#container3 > p").html("<br><h4> <span id='result3'>NaN</span></h4>");
            $("#time").html("<br><h4> <span id='timer'>00:00</span></h4>");
        </script>
    <script src="../js/advanced_movement_test.js"></script>
</body>

</html>
