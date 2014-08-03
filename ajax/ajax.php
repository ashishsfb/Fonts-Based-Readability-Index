<?php
	session_start();
	include("../include/db_connect.php");
	mysql_query("SET NAMES utf8");	
	
	if(isset($_POST['lang']))
		$lang = $_POST['lang'];
	else
		$lang = "English";
		
	$sql = "SELECT para, pid FROM paragraphs WHERE `language` = '".$lang."' ORDER BY RAND() LIMIT 1";
	$var = mysql_query($sql);
	$row = mysql_fetch_array($var);
	$_SESSION['pid'] = $row['pid'];
	echo $row['para'];
?>