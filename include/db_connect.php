<?php
	$connection=mysqli_connect("localhost","root","") or die("Sorry, we cannot connect to the database right now, come back later.");
	if(!$connection){
		die("Data Base connection failed: ".mysql_error());
	}
	$db=mysqli_select_db($connection, "fbri") or die("Sorry, we cannot connect to the database right now, come back later.");
	if(!$db){
		die("Data Base selection failed: ".mysql_error());
	}
?>