<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Readability Survey</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!--For Indian languages-->
	<meta http-equiv="Content-article_types" content="text/html;charset=UTF-8">
	<!--Website Icon-->
    <link rel="shortcut icon" href="images/survey.ico">
	<!-- Bootstrap Core CSS-->
    <link href="Bootstrap/css/bootstrap.css" rel="stylesheet">
	<!-- Custom Core CSS-->
    <link href="Bootstrap/css/custom.css" rel="stylesheet">
    <script src="Bootstrap/js/respond.min.js"></script>
	<!--FusionCharts-->
	<SCRIPT LANGUAGE="Javascript" SRC="FusionChartsFree/Code/FusionCharts/FusionCharts.js"></SCRIPT>
</head>

<?php
	include("include/db_connect.php");
	session_start();
	//Fusion Charts
	include("FusionChartsFree/Code/PHP/Includes/FusionCharts.php");
	
	//////////FOR SECURITY/////////////////////
	//check for direct entry
	if(!isset($_SESSION['email'])){
		header("Location:index.php");
	}
	if(isset($_POST['add_admin'])){
		//retrieving add new admin form variables
		$email = $_POST["email"];
		$password = $_POST["password"];
		$repassword = $_POST["repassword"];
		
		if($password != $repassword){
			$error = "Passwords doesn't match. Try again.";
		}
		else{
			$sql = "SELECT email FROM admins where email='".$email."'";
			$result =  mysql_query($sql);
			$row = mysql_fetch_array($result);
			if($row['email'] == $email){
				$error = "This email already exists, try something else.";
			}
			else{
				$sql = "INSERT INTO admins(`email`, `password`) VALUES('$email', '$password')";
				if(!$result = mysql_query($sql)){
					$error = "Error occured while adding new Admin.";
				}
			}
		}
	}
	else{
		$sql = "SELECT * FROM admins where email='".$_SESSION['email']."'";
		$result=  mysql_query($sql);
		$num=mysql_num_rows($result);
		$row=mysql_fetch_array($result);
			
		if(isset($_SESSION['password'])){
			if($_SESSION['password'] != $row['password'])
				header("Location:index.php");
		}
		else{
			session_destroy();
			header("Location:index.php");
		}
	}	
	
	$article_types = array('Newspaper', 'NCERT Text', 'Legal Document', 'Research Papers', 'Wikipedia Page');
	$view_count = array('Newspaper'=>'0', 'NCERT Text' => '0', 'Legal Document' =>'0', 'Research Papers'=>'0', 'Wikipedia Page'=>'0');
	$male_view_count = array('Newspaper'=>'0', 'NCERT Text' => '0', 'Legal Document' =>'0', 'Research Papers'=>'0', 'Wikipedia Page'=>'0');
	$female_view_count = array('Newspaper'=>'0', 'NCERT Text' => '0', 'Legal Document' =>'0', 'Research Papers'=>'0', 'Wikipedia Page'=>'0');
	
	$font_style = array('Arial', 'Calibri', 'Comic Sans MS', 'Times New Roman', 'Lucida Sans');
	$font_size = array('70-90','100-120','130-150','160-180','190-210','220-240');
	$line_height = array('20-24','25-29','30-34','35-39','40-44','45-50');
	$word_spacing = array('0-3','4-7','8-11','12-15','16-20');
	
	$net_view_count = 0;
	$net_male_view_count = 0;
	$net_female_view_count = 0;
	
    for($i = 0; $i < 5; $i++){
		$query = "SELECT * FROM paragraphs Where article_type = '$article_types[$i]'";
	    $result = mysql_query($query);
	    $numofrow = mysql_num_rows($result);
		
		while($row=mysql_fetch_array($result)){
		    $var1=$row['pid'];
			$query1="SELECT * FROM test_data Where `pid`='$var1'"; 
		    $result1=mysql_query($query1);
			$view_count[$article_types[$i]]+=  mysql_num_rows($result1);
			
			while($row1=mysql_fetch_array($result1)){
				$var2=$row1['uid'];
				$query2="SELECT * FROM main Where `user_id`='$var2'"; 
				$result2=mysql_query($query2);
				$row2=mysql_fetch_array($result2);
				
				if($row2['gender']=='1'){
					$male_view_count[$article_types[$i]]++;
				}
				else{
					$female_view_count[$article_types[$i]]++;
				}
			}
		}
	    $net_view_count+=$view_count[$article_types[$i]];
		$net_male_view_count+=$male_view_count[$article_types[$i]];
		$net_female_view_count+=$female_view_count[$article_types[$i]];
	}
       
	//number of paragraphs
	$query="SELECT MAX(pid) FROM paragraphs";
	$result=mysql_query($query);
	$numofpara=0;
	while($row=mysql_fetch_array($result)){
		$numofpara= $row[0];
	}
?>

<body>
	<div class="container">
		<div class="row well">
			<div class="col-md-4 col-lg-4" align="center">
				Hello Admin <?php echo stripslashes($_SESSION['email'])."<br/><br/>";?>
				<a href="index.php" class="btn btn-danger btn-lg" type="button">Sign Out</a>
				<!-- Button trigger modal -->
				<button class="btn btn-warning btn-lg" data-toggle="modal" data-target="#basicModal">
				  Add another Admin
				</button>
				<!-- Modal -->
				<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
								<h2 class="modal-title" id="myModalLabel">Readability Survey Add a new Admin</h2>
							</div>
							<div class="modal-body">
								<form class="form-signin" role="form" method="POST" action="analytics.php" enctype="multipart/form-data">
									<input name="email" type="email" class="form-control" placeholder="Email address" required/>
									<input name="password" type="password" class="form-control" placeholder="Password" required/>
									<input name="repassword" type="password" class="form-control" placeholder="Re-enter password" required/>
									<input name="add_admin" class="btn btn-lg btn-primary btn-block" type="submit" href="analytics.php" value="Add admin"/>	
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-4 col-lg-4">
				<div class="page-header"><h2 align="center">Readability Survey Analytics</h2></div>
			</div>
			
			<div class="col-md-4 col-lg-4" align="center">
				<?php echo date("jS \of F Y [l]", time())."<br/><br/>";?>
				<a href="admin.php" class="btn btn-primary btn-lg" type="button">Go to Admin Home</a>
				<a href="home.php" class="btn btn-primary btn-lg" type="button">Give a Test</a>
			</div>
		</div>
		
		<!--Error Printing-->
		<?php
			if(isset($error))
			{
				echo "<div class='alert alert-danger' align='center' id='status-box'>";
					echo $error;
				echo "</div>";	
			}	
		?>
		
		<!-- Nav tabs -->
		<div class="well">		
			<ul class="nav nav-tabs nav-justified" role="tablist">
				<li class="active in"><a href="#home" role="tab" data-toggle="tab">Home</a></li>
				<li><a href="#news" role="tab" data-toggle="tab">NewsPaper</a></li>
				<li><a href="#ncert" role="tab" data-toggle="tab">Ncert Text</a></li>
				<li><a href="#legal" role="tab" data-toggle="tab">Legal Documents</a></li>
				<li><a href="#research" role="tab" data-toggle="tab">Research Paper</a></li>
				<li><a href="#wiki" role="tab" data-toggle="tab">Wikipedia Pages</a></li>
			</ul>
		
			<!-- Tab panes -->
			<div class="tab-content">
				<!--Home Tab-->
				<div class="tab-pane fade active in" id="home">
					<?php
						echo "<div align=center>
							<h2><small>
								Total tests done yet - ".$net_view_count."<br/>{ M - ". $net_male_view_count.", F - ". $net_female_view_count." }
							</small></h2>
						</div>";
				
					
						//font style counts
						$font_style_count = array('0','0','0','0','0');
						$font_style_male = array('0','0','0','0','0');
						$font_style_female = array('0','0','0','0','0');
						$font_style_reading_time = array('0','0','0','0','0');
						$font_style_test_time = array('0','0','0','0','0');
						
						
						//font size counts
						$font_size_count = array('0', '0', '0', '0','0','0');
						$font_size_male = array('0','0','0','0','0','0');
						$font_size_female = array('0','0','0','0','0','0');
						$font_size_reading_time = array('0','0','0','0','0','0');
						$font_size_test_time = array('0','0','0','0','0','0');
						
						
						//Line Height counts
						$line_height_count = array('0', '0', '0', '0','0','0','0','0');
						$line_height_male = array('0','0','0','0','0','0','0','0');
						$line_height_female = array('0','0','0','0','0','0','0','0');
						$line_height_reading_time = array('0','0','0','0','0','0');
						$line_height_test_time = array('0','0','0','0','0','0');
						
						
						//Word Spacing counts
						$word_spacing_count = array('0', '0', '0', '0','0');
						$word_spacing_male = array('0','0','0','0','0');
						$word_spacing_female = array('0','0','0','0','0');
						$word_spacing_reading_time = array('0','0','0','0','0');
						$word_spacing_test_time = array('0','0','0','0','0');
					
						
					
						
					
						 $query= "SELECT MAX(tid)  FROM test_data";
						 $result=mysql_query($query);
						 $row=mysql_fetch_array($result);
						 $max=$row[0];
						 
						 for($p=1;$p<$max;$p++){
							
									$query= "select * from test_data WHERE tid='$p'";
									$result=mysql_query($query);
									$row1=mysql_fetch_array($result);
								
									///CALCULATING HOME FONT SIZE///////
								   
									if($row1['font']=='Arial'){
										$font_style_count[0]++;
										$font_style_reading_time[0]+=$row1['reading_time'];
										$font_style_test_time[0]+=$row1['test_time'];
										
										$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
										$result3=mysql_query($query3);
										$row3=mysql_fetch_array($result3);
											
										if($row3['gender']=='1'){
											$font_style_male[0]++;
										}
										else{
											$font_style_female[0]++;
										}
									}
									if($row1['font']=='Calibri'){
										$font_style_count[1]++;
										$font_style_reading_time[1]+=$row1['reading_time'];
										$font_style_test_time[1]+=$row1['test_time'];
										
										$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
										$result3=mysql_query($query3);
										$row3=mysql_fetch_array($result3);
											
										if($row3['gender']=='1'){
											$font_style_male[1]++;
										}
										else{
											$font_style_female[1]++;
										}
									}		
									if($row1['font']=='Comic Sans MS'){
										$font_style_count[2]++;
										$font_style_reading_time[2]+=$row1['reading_time'];
										$font_style_test_time[2]+=$row1['test_time'];
										
										$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
										$result3=mysql_query($query3);
										$row3=mysql_fetch_array($result3);
											
										if($row3['gender']=='1'){
											$font_style_male[2]++;
										}
										else{
											$font_style_female[2]++;
										}
									}		  
									if($row1['font']=='Times New Roman'){
										$font_style_count[3]++;
										$font_style_reading_time[3]+=$row1['reading_time'];
										$font_style_test_time[3]+=$row1['test_time'];
										
										$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
										$result3=mysql_query($query3);
										$row3=mysql_fetch_array($result3);
											
										if($row3['gender']=='1'){
											$font_style_male[3]++;
										}
										else{
											$font_style_female[3]++;
										}
									}		 
									if($row1['font']=='Lucida Sans'){
										$font_style_count[4]++;	
										$font_style_reading_time[4]+=$row1['reading_time'];
										$font_style_test_time[4]+=$row1['test_time'];
										
										$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
										$result3=mysql_query($query3);
										$row3=mysql_fetch_array($result3);
											
										if($row3['gender']=='1'){
											$font_style_male[4]++;
										}
										else{
											$font_style_female[4]++;
										}
									}
									///////////END OF CALCULATION OF  home FONT STYLE//////
									
									///////////END OF CALCULATION OF  home FONT Size//////
									switch($row1['size']){
										case (70<=$row1['size']&&$row1['size']<=90):
											$font_size_count[0]++;
											$font_size_reading_time[0]+=$row1['reading_time'];
											$font_size_test_time[0]+=$row1['test_time'];
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
											
											if($row3['gender']=='1'){
												$font_size_male[0]++;
											}
											else{
												$font_size_female[0]++;
											}
										break;
										
										case (100<=$row1['size']&&$row1['size']<=120):
											$font_size_count[1]++;
											$font_size_reading_time[1]+=$row1['reading_time'];
											$font_size_test_time[1]+=$row1['test_time'];
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$font_size_male[1]++;
											}
											else{
												$font_size_female[1]++;
											}
										break;
											
										case (130<=$row1['size']&&$row1['size']<=150):
											$font_size_count[2]++;
											$font_size_reading_time[2]+=$row1['reading_time'];
											$font_size_test_time[2]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$font_size_male[2]++;
											}
											else{
												$font_size_female[2]++;
											}
										break;
											
										case (160<=$row1['size']&&$row1['size']<=180):
											$font_size_count[3]++;
											$font_size_reading_time[3]+=$row1['reading_time'];
											$font_size_test_time[3]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$font_size_male[3]++;
											}
											else{
												$font_size_female[3]++;
											}
										break;
												
										case (190<=$row1['size']&&$row1['size']<=210):
											$font_size_count[4]++;
											$font_size_reading_time[4]+=$row1['reading_time'];
											$font_size_test_time[4]+=$row1['test_time'];
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
											
											if($row3['gender']=='1'){
												$font_size_male[4]++;
											}
											else{
												$font_size_female[4]++;
											}
										break;
											
										case (220<=$row1['size']&&$row1['size']<=240):
											$font_size_count[5]++;
											$font_size_reading_time[5]+=$row1['reading_time'];
											$font_size_test_time[5]+=$row1['test_time'];
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
											
											if($row3['gender']=='1'){
												$font_size_male[5]++;
											}
											else{
												$font_size_female[5]++;
											}
										break;
									}
									///////////END OF CALCULATION OF home font size//////
									
									///////////CALCULATION OF home LINE HEIGHT //////
									switch($row1['line_height']){
										case (20<=$row1['line_height']&&$row1['line_height']<=24):
											$line_height_count[0]++;
											$line_height_reading_time[0]+=$row1['reading_time'];
											$line_height_test_time[0]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
															
											if($row3['gender']=='1'){
												$line_height_male[0]++;
											}
											else{
												$line_height_female[0]++;
											}
										break;
										
										case (25<=$row1['line_height']&&$row1['line_height']<=29):
											$line_height_count[1]++;
											$line_height_reading_time[1]+=$row1['reading_time'];
											$line_height_test_time[1]+=$row1['test_time'];
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$line_height_male[1]++;
											}
											else{
												$line_height_female[1]++;
											}
										break;
										
										case (30<=$row1['line_height']&&$row1['line_height']<=34):
											$line_height_count[2]++;
											$line_height_reading_time[2]+=$row1['reading_time'];
											$line_height_test_time[2]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$line_height_male[2]++;
											}
											else{
												$line_height_female[2]++;
											}
										break;
								
										case (35<=$row1['line_height']&&$row1['line_height']<=39):
											$line_height_count[3]++;
											$line_height_reading_time[3]+=$row1['reading_time'];
											$line_height_test_time[3]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$line_height_male[3]++;
											}
											else{
												$line_height_female[3]++;
											}
										break;
										
										case (40<=$row1['size']&&$row1['line_height']<=44):
											$font_line_height_count[4]++;
											$line_height_reading_time[4]+=$row1['reading_time'];
											$line_height_test_time[4]+=$row1['test_time'];
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$line_height_male[4]++;
											}
											else{
												$line_height_female[4]++;
											}
										break;
											
										case (45<=$row1['line_height']&&$row1['line_height']<=50):
											$line_height_count[5]++;
											$line_height_reading_time[5]+=$row1['reading_time'];
											$line_height_test_time[5]+=$row1['test_time'];
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
															
											if($row3['gender']=='1'){
												$line_height_male[5]++;
											}
											else{
												$line_height_female[5]++;
											}
										break;
										
											
									}
									///////////END OF CALCULATION OF  home LINE HEIGHT //////
												 
									///////////CALCULATION OF home WORD SPACING //////
									switch($row1['word_spacing']){
										case (0<=$row1['word_spacing']&&$row1['word_spacing']<=3):
											$word_spacing_count[0]++;
											$word_spacing_reading_time[0]+=$row1['reading_time'];
											$word_spacing_test_time[0]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
											
											if($row3['gender']=='1'){
												$word_spacing_male[0]++;
											}
											else{
												$word_spacing_female[0]++;
											}
										break;

										case (4<=$row1['word_spacing']&&$row1['word_spacing']<=7):
											$word_spacing_count[1]++;
											$word_spacing_reading_time[1]+=$row1['reading_time'];
											$word_spacing_test_time[1]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$word_spacing_male[1]++;
											}
											else{
												$word_spacing_female[1]++;
											}
										break;
										
										case (8<=$row1['word_spacing']&&$row1['word_spacing']<=11):
											$word_spacing_count[2]++;
											$word_spacing_reading_time[2]+=$row1['reading_time'];
											$word_spacing_test_time[2]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$word_spacing_male[2]++;
											}
											else{
												$word_spacing_female[2]++;
											}
										break;
										
										case (12<=$row1['word_spacing']&&$row1['word_spacing']<=15):
											$word_spacing_count[3]++;
											$word_spacing_reading_time[3]+=$row1['reading_time'];
											$word_spacing_test_time[3]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$word_spacing_male[3]++;
											}
											else{
												$word_spacing_female[3]++;
											}
										break;
										
										case (16<=$row1['word_spacing']&&$row1['word_spacing']<=20):
											$word_spacing_count[4]++;
											$word_spacing_reading_time[4]+=$row1['reading_time'];
											$word_spacing_test_time[4]+=$row1['test_time'];
											
											$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
											$result3=mysql_query($query3);
											$row3=mysql_fetch_array($result3);
																
											if($row3['gender']=='1'){
												$word_spacing_male[4]++;
											}
											else{
												$word_spacing_female[4]++;
											}
										break;
									}
							///////////END OF CALCULATION OF home WORD SPACING //////
							}
					
					?>
					<?php
					echo "<table class='table table-bordered'><tr>";
					/////////////////////CHART FOR FONT STYLE in home Article type///////////
					echo "<td>";
					if(array_sum($font_style_count)!=0){
						$strXML= "<graph caption='Tests given in different Font styles' subcaption='with home Article Type' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Style</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$font_style[$t].
							"</td>
							
							<td>".
								$font_style_male[$t].
							"</td>
							
							<td>".
								$font_style_female[$t].
							"</td>";
							if($font_style_count[$t]!=0){
								$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
								$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
								echo "<td>".
									$font_style_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_style_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "home_font_style_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR FONT SIZE in home Article type///////////
					if(array_sum($font_size_count)!=0){
						$strXML= "<graph caption='Tests given in different Font sizes' subcaption='with home Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Size Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$font_size[$t].
							"</td>
							
							<td>".
								$font_size_male[$t].
							"</td>
							
							<td>".
								$font_size_female[$t].
							"</td>";
							if($font_size_count[$t]!=0){
								$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
								$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
								echo "<td>".
									$font_size_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_size_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "home_size_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td>";
					
					/////////////////////CHART FOR Line Height in home Article type///////////
					if(array_sum($line_height_count)!=0){
						$strXML= "<graph caption='Tests given in different Line heights' subcaption='with home Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Line Height Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$line_height[$t].
							"</td>
							
							<td>".
								$line_height_male[$t].
							"</td>
							
							<td>".
								$line_height_female[$t].
							"</td>";
							if($line_height_count[$t]!=0){
								$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
								$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
								echo "<td>".
									$line_height_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$line_height_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "home_line_height_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR Word Spacing in home Article type///////////
					if(array_sum($word_spacing_count)!=0){
						$strXML= "<graph caption='Tests given in different word spacing' subcaption='with home Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Word Spacing Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$word_spacing[$t].
							"</td>
							
							<td>".
								$word_spacing_male[$t].
							"</td>
							
							<td>".
								$word_spacing_female[$t].
							"</td>";
							if($word_spacing_count[$t]!=0){
								$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
								$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
								echo "<td>".
									$word_spacing_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$word_spacing_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
						}
						$strXML .= "</graph>";
	
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "home_word_spacing_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr></table>";
					?>
				</div>

				
				<!--Newspaper Tab-->
				<div class="tab-pane fade" id="news">
					<?php
					echo "<div align=center>
						<h2><small>
							Total tests done yet - ".$view_count['Newspaper']."<br/>{ M - ". $male_view_count['Newspaper'].", F - ". $female_view_count['Newspaper']." }
						</small></h2>
					</div>";
						
					//font style counts
					$font_style_count = array('0','0','0','0','0');
					$font_style_male = array('0','0','0','0','0');
					$font_style_female = array('0','0','0','0','0');
					$font_style_reading_time = array('0','0','0','0','0');
					$font_style_test_time = array('0','0','0','0','0');
					
					
					//font size counts
					$font_size_count = array('0', '0', '0', '0','0','0');
					$font_size_male = array('0','0','0','0','0','0');
					$font_size_female = array('0','0','0','0','0','0');
					$font_size_reading_time = array('0','0','0','0','0','0');
					$font_size_test_time = array('0','0','0','0','0','0');
					
					
					//Line Height counts
					$line_height_count = array('0', '0', '0', '0','0','0','0','0');
					$line_height_male = array('0','0','0','0','0','0','0','0');
					$line_height_female = array('0','0','0','0','0','0','0','0');
					$line_height_reading_time = array('0','0','0','0','0','0');
					$line_height_test_time = array('0','0','0','0','0','0');
					
					
					//Word Spacing counts
					$word_spacing_count = array('0', '0', '0', '0','0');
					$word_spacing_male = array('0','0','0','0','0');
					$word_spacing_female = array('0','0','0','0','0');
					$word_spacing_reading_time = array('0','0','0','0','0');
					$word_spacing_test_time = array('0','0','0','0','0');
						
					$i=1;
					$query="SELECT * FROM paragraphs WHERE `article_type`='Newspaper'";
					mysql_query("SET NAMES utf8");
					$result=mysql_query($query);
					
					while($row=mysql_fetch_array($result)){
						$para_font_style_count = array('0','0','0','0','0');
						$para_font_style_male = array('0','0','0','0','0');
						$para_font_style_female = array('0','0','0','0','0');
						$para_font_style_reading_time = array('0','0','0','0','0');
						$para_font_style_test_time = array('0','0','0','0','0');
										
						$para_font_size_count = array('0', '0', '0', '0','0','0');
						$para_font_size_male = array('0','0','0','0','0','0');
						$para_font_size_female = array('0','0','0','0','0','0');
						$para_font_size_reading_time = array('0','0','0','0','0','0');
						$para_font_size_test_time = array('0','0','0','0','0','0');
										
						$para_line_height_count = array('0', '0', '0', '0','0','0');
						$para_line_height_male = array('0','0','0','0','0','0');
						$para_line_height_female = array('0','0','0','0','0','0');
						$para_line_height_reading_time = array('0','0','0','0','0','0');
						$para_line_height_test_time = array('0','0','0','0','0','0');
										
						$para_word_spacing_count = array('0', '0', '0', '0','0');
						$para_word_spacing_male = array('0','0','0','0','0');
						$para_word_spacing_female = array('0','0','0','0','0');
						$para_word_spacing_reading_time = array('0','0','0','0','0');
						$para_word_spacing_test_time = array('0','0','0','0','0');
										
						$var1=$row['pid'];
						$query1="SELECT * FROM test_data Where `pid`='$var1'"; 
						$result1=mysql_query($query1);
						$totalnewsviewers=  mysql_num_rows($result1); 
						$newsmale=0;
						$newsfemale=0;
									
						while($row1=mysql_fetch_array($result1)){
							$var2=$row1['uid'];
							$query2="SELECT * FROM main Where `user_id`='$var2'"; 
							$result2=mysql_query($query2);
							$row2=mysql_fetch_array($result2);
									
							if($row2['gender']=='1'){
								$newsmale++;
							}
							else{
								$newsfemale++;
							}
							///////////CALCULATION OF  News DOCUMENT FONT STYLE//////
							if($row1['font']=='Arial'){
								$font_style_count[0]++;
								$font_style_reading_time[0]+=$row1['reading_time'];
								$font_style_test_time[0]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[0]++;
								}
								else{
									$font_style_female[0]++;
								}
							}
                            if($row1['font']=='Calibri'){
								$font_style_count[1]++;
								$font_style_reading_time[1]+=$row1['reading_time'];
								$font_style_test_time[1]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[1]++;
								}
								else{
									$font_style_female[1]++;
								}
							}		
							if($row1['font']=='Comic Sans MS'){
								$font_style_count[2]++;
								$font_style_reading_time[2]+=$row1['reading_time'];
								$font_style_test_time[2]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[2]++;
								}
								else{
									$font_style_female[2]++;
								}
							}		  
							if($row1['font']=='Times New Roman'){
								$font_style_count[3]++;
								$font_style_reading_time[3]+=$row1['reading_time'];
								$font_style_test_time[3]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[3]++;
								}
								else{
									$font_style_female[3]++;
								}
							}		 
							if($row1['font']=='Lucida Sans'){
								$font_style_count[4]++;	
								$font_style_reading_time[4]+=$row1['reading_time'];
								$font_style_test_time[4]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[4]++;
								}
								else{
									$font_style_female[4]++;
								}
 							}
							///////////END OF CALCULATION OF  News DOCUMENT FONT STYLE//////
									
							///////////CALCULATION OF  News DOCUMENT FONT SIZE//////
							switch($row1['size']){
								case (70<=$row1['size']&&$row1['size']<=90):
									$font_size_count[0]++;
									$font_size_reading_time[0]+=$row1['reading_time'];
									$font_size_test_time[0]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[0]++;
									}
									else{
										$font_size_female[0]++;
									}
								break;
								
								case (100<=$row1['size']&&$row1['size']<=120):
									$font_size_count[1]++;
									$font_size_reading_time[1]+=$row1['reading_time'];
									$font_size_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[1]++;
									}
									else{
										$font_size_female[1]++;
									}
								break;
									
								case (130<=$row1['size']&&$row1['size']<=150):
									$font_size_count[2]++;
									$font_size_reading_time[2]+=$row1['reading_time'];
									$font_size_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[2]++;
									}
									else{
										$font_size_female[2]++;
									}
								break;
									
								case (160<=$row1['size']&&$row1['size']<=180):
									$font_size_count[3]++;
									$font_size_reading_time[3]+=$row1['reading_time'];
									$font_size_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[3]++;
									}
									else{
										$font_size_female[3]++;
									}
								break;
										
								case (190<=$row1['size']&&$row1['size']<=210):
									$font_size_count[4]++;
									$font_size_reading_time[4]+=$row1['reading_time'];
									$font_size_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[4]++;
									}
									else{
										$font_size_female[4]++;
									}
								break;
									
								case (220<=$row1['size']&&$row1['size']<=240):
									$font_size_count[5]++;
									$font_size_reading_time[5]+=$row1['reading_time'];
									$font_size_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[5]++;
									}
									else{
										$font_size_female[5]++;
									}
								break;
							}
							///////////END OF CALCULATION OF  News DOCUMENT FONT line_height//////
							
							///////////CALCULATION OF  NEWS DOCUMENT LINE HEIGHT //////
							switch($row1['line_height']){
								case (20<=$row1['line_height']&&$row1['line_height']<=24):
									$line_height_count[0]++;
									$line_height_reading_time[0]+=$row1['reading_time'];
									$line_height_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[0]++;
									}
									else{
										$line_height_female[0]++;
									}
								break;
								
								case (25<=$row1['line_height']&&$row1['line_height']<=29):
									$line_height_count[1]++;
									$line_height_reading_time[1]+=$row1['reading_time'];
									$line_height_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[1]++;
									}
									else{
										$line_height_female[1]++;
									}
								break;
								
								case (30<=$row1['line_height']&&$row1['line_height']<=34):
									$line_height_count[2]++;
									$line_height_reading_time[2]+=$row1['reading_time'];
									$line_height_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[2]++;
									}
									else{
										$line_height_female[2]++;
									}
								break;
						
								case (35<=$row1['line_height']&&$row1['line_height']<=39):
									$line_height_count[3]++;
									$line_height_reading_time[3]+=$row1['reading_time'];
									$line_height_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[3]++;
									}
									else{
										$line_height_female[3]++;
									}
								break;
								
								case (40<=$row1['size']&&$row1['line_height']<=44):
									$font_line_height_count[4]++;
									$line_height_reading_time[4]+=$row1['reading_time'];
									$line_height_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[4]++;
									}
									else{
										$line_height_female[4]++;
									}
								break;
									
								case (45<=$row1['line_height']&&$row1['line_height']<=50):
									$line_height_count[5]++;
									$line_height_reading_time[5]+=$row1['reading_time'];
									$line_height_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[5]++;
									}
									else{
										$line_height_female[5]++;
									}
								break;
								
									
							}
							///////////END OF CALCULATION OF  News DOCUMENT LINE HEIGHT //////
										 
							///////////CALCULATION OF News DOCUMENT WORD SPACING //////
							switch($row1['word_spacing']){
								case (0<=$row1['word_spacing']&&$row1['word_spacing']<=3):
									$word_spacing_count[0]++;
									$word_spacing_reading_time[0]+=$row1['reading_time'];
									$word_spacing_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$word_spacing_male[0]++;
									}
									else{
										$word_spacing_female[0]++;
									}
								break;

								case (4<=$row1['word_spacing']&&$row1['word_spacing']<=7):
									$word_spacing_count[1]++;
									$word_spacing_reading_time[1]+=$row1['reading_time'];
									$word_spacing_test_time[1]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[1]++;
									}
									else{
										$word_spacing_female[1]++;
									}
								break;
								
								case (8<=$row1['word_spacing']&&$row1['word_spacing']<=11):
									$word_spacing_count[2]++;
									$word_spacing_reading_time[2]+=$row1['reading_time'];
									$word_spacing_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[2]++;
									}
									else{
										$word_spacing_female[2]++;
									}
								break;
								
								case (12<=$row1['word_spacing']&&$row1['word_spacing']<=15):
									$word_spacing_count[3]++;
									$word_spacing_reading_time[3]+=$row1['reading_time'];
									$word_spacing_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[3]++;
									}
									else{
										$word_spacing_female[3]++;
									}
								break;
								
								case (16<=$row1['word_spacing']&&$row1['word_spacing']<=20):
									$word_spacing_count[4]++;
									$word_spacing_reading_time[4]+=$row1['reading_time'];
									$word_spacing_test_time[4]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[4]++;
									}
									else{
										$word_spacing_female[4]++;
									}
								break;
							}
							///////////END OF CALCULATION OF News DOCUMENT WORD SPACING //////
						}
						//paragraphs table
						echo "<table class='table table-bordered'>";
							echo "<tr>
								<td>";
								//para_panel
									echo "<div class='para_panel' id='news_para_panel".$i."'>
										<button class='btn-primary btn-block btn-lg' data-toggle='collapse' data-target='#news_para".$i."' data-parent='#news_para_panel".$i."'>
											<div class='para_info1'>
												Paragraph : ".$i."
											</div>
											<div class='para_info'>".
													$totalnewsviewers."
													{ M - ".$newsmale.",  F - ".$newsfemale." }
											</div>
										</button>
											
										<div id='news_para".$i."' class='panel-collapse panel-body collapse'>".
											$row['para'];
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//////////////CALCULATION FOR CREATING CHART ON FONT SYTLE FOR EACH PARAGRAPH IN News DOCUMENT///////////
											for($k=0;$k<5;$k++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `font`='".$font_style[$k]."'";
												$para=mysql_query($query);
												$para_font_style_count[$k]=mysql_num_rows($para);
												
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_style_reading_time[$k]+=$row_para['reading_time'];
													$para_font_style_test_time[$k]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_style_male[$k]++;
													else
														$para_font_style_female[$k]++;  
										        }
											}   
											//////CALCULATION FOR CREATING CHART ON FONT SIZE AND LINE HEIGHT AND WORD-SPACING FOR EACH PARAGRAPH IN News DOCUMENT///////////
											$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."'";
											$para=mysql_query($query);
											while($row_para=mysql_fetch_array($para)){
												switch($row_para['size']){
													case (70<=$row_para['size']&&$row_para['size']<=90):
														$para_font_size_count[0]++;
														$para_font_size_reading_time[0]+=$row_para['reading_time'];
														$para_font_size_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[0]++;
														}
														else{
															$para_font_size_female[0]++;
														}
													break;
													
													case (100<=$row_para['size']&&$row_para['size']<=120):
														$para_font_size_count[1]++;
														$para_font_size_reading_time[1]+=$row_para['reading_time'];
														$para_font_size_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[1]++;
														}
														else{
															$para_font_size_female[1]++;
														}
													break;
														
													case (130<=$row_para['size']&&$row_para['size']<=150):
														$para_font_size_count[2]++;
														$para_font_size_reading_time[2]+=$row_para['reading_time'];
														$para_font_size_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[2]++;
														}
														else{
															$para_font_size_female[2]++;
														}
													break;
														
													case (160<=$row_para['size']&&$row_para['size']<=180):
														$para_font_size_count[3]++;
														$para_font_size_reading_time[3]+=$row_para['reading_time'];
														$para_font_size_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_font_size_male[3]++;
														}
														else{
															$para_font_size_female[3]++;
														}
													break;
														
													case (190<=$row_para['size']&&$row_para['size']<=210):
														$para_font_size_count[4]++;
														$para_font_size_reading_time[4]+=$row_para['reading_time'];
														$para_font_size_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[4]++;
														}
														else{
															$para_font_size_female[4]++;
														}
													break;
														
													case (220<=$row_para['size']&&$row_para['size']<=240):
														$para_font_size_count[5]++;
														$para_font_size_reading_time[5]+=$row_para['reading_time'];
														$para_font_size_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[5]++;
														}
														else{
															$para_para_font_size_female[5]++;
														}
													break;
												}
															
												///////////CALCULATION OF  LINE HEIGHT FOR EACH PARAGRAPH //////
												switch($row_para['line_height']){
													case (20<=$row_para['line_height']&&$row_para['line_height']<=24):
														$para_line_height_count[0]++;
														$para_line_height_reading_time[0]+=$row_para['reading_time'];
														$para_line_height_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[0]++;
														}
														else{
															$para_line_height_female[0]++;
														}
													break;
														
													case (25<=$row_para['line_height']&&$row_para['line_height']<=29):
														$para_line_height_count[1]++;
														$para_line_height_reading_time[1]+=$row_para['reading_time'];
														$para_line_height_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[1]++;
														}
														else{
															$para_line_height_female[1]++;
														}
														break;
													
													case (30<=$row_para['line_height']&&$row_para['line_height']<=34):
														$para_line_height_count[2]++;
														$para_line_height_reading_time[2]+=$row_para['reading_time'];
														$para_para_line_height_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[2]++;
														}
														else{
															$para_line_height_female[2]++;
														}
													break;
														
													case (35<=$row_para['line_height']&&$row_para['line_height']<=39):
														$para_line_height_count[3]++;
														$para_line_height_reading_time[3]+=$row_para['reading_time'];
														$para_line_height_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[3]++;
														}
														else{
															$para_line_height_female[3]++;
														}
													break;
														
													case (40<=$row_para['size']&&$row_para['line_height']<=44):
														$para_line_height_count[4]++;
														$para_line_height_reading_time[4]+=$row_para['reading_time'];
														$para_line_height_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[4]++;
														}
														else{
															$para_line_height_female[4]++;
														}
													break;
														
													case (45<=$row_para['line_height']&&$row_para['line_height']<=50):
														$para_line_height_count[5]++;
														$para_line_height_reading_time[5]+=$row_para['reading_time'];
														$para_line_height_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[5]++;
														}
														else{
															$para_line_height_female[5]++;
														}
													break;
														
													
												}
															
												///////////CALCULATION OF WORD SPACING FOR EACH PARAGRAPH//////
												switch($row_para['word_spacing']){
													case (0<=$row_para['word_spacing']&&$row_para['word_spacing']<=3):
														$para_word_spacing_count[0]++;
														$para_word_spacing_reading_time[0]+=$row_para['reading_time'];
														$para_word_spacing_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_word_spacing_male[0]++;
														}
														else{
															$para_word_spacing_female[0]++;
														}
													break;
													
													case (4<=$row_para['word_spacing']&&$row_para['word_spacing']<=7):
														$para_word_spacing_count[1]++;
														$para_word_spacing_reading_time[1]+=$row_para['reading_time'];
														$para_word_spacing_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[1]++;
														}
														else{
															$para_word_spacing_female[1]++;
														}
													break;
													
													case (8<=$row_para['word_spacing']&&$row_para['word_spacing']<=11):
														$para_word_spacing_count[2]++;
														$para_word_spacing_reading_time[2]+=$row_para['reading_time'];
														$para_word_spacing_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[2]++;
														}
														else{
															$para_word_spacing_female[2]++;
														}
													break;
													
													case (12<=$row_para['word_spacing']&&$row_para['word_spacing']<=15):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
													
													case (16<=$row_para['word_spacing']&&$row_para['word_spacing']<=20):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
												}
											}	
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//Nav tabs list -obj, sub and graphs
											echo "<ul class='nav nav-tabs nav-justified' role='tablist'>
												<li class='active in'><a href='#news_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
												<li><a href='#news_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
												<li><a href='#news_graphs".$i."' role='tab' data-toggle='tab'>Graphs and Charts</a></li>
											</ul>";
															
											//Tab panes
											echo "<div class='tab-content'>";
												//Objectives Tab
												echo "<div class='tab-pane fade active in' id='news_obj".$i."'>
													<table class = 'table table-hover table-bordered ques_table' id='news_para_obj_ques".$i."'>
														<tr align = 'center'>
															<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
															<td class = 'col-lg-6'><h4><big>Questions</big></h4></td>
															<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Skipped</big></h6></td>
														</tr>";
																					
														$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
														mysql_query("SET NAMES utf8");
														$result3 = mysql_query($query3);
							
														$j = 1;
														while($row3 = mysql_fetch_array($result3)){
															echo "<tr align = 'center'>";
																//S.No.
																echo "<td>".
																	$j.
																"</td>";
																							
																//Objective Questions
																echo "<td>".
																	$row3['ques'].
																"</td>";
																
																$j++;
																							
																//Options data
																$query4 = "SELECT * FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																mysql_query("SET NAMES utf8");
																$result4 = mysql_query($query4);
																								
																$opt1_select_count = 0;
																$opt2_select_count = 0;
																$opt3_select_count = 0;
																$opt4_select_count = 0;
																$skipped_count = 0;
																							
																if($row3['opt1'] == ""){
																	$opt1_select_count = -1;
																}
																if($row3['opt2'] == ""){
																	$opt2_select_count = -1;
																}
																if($row3['opt3'] == ""){
																	$opt3_select_count = -1;
																}
																if($row3['opt4'] == ""){
																	$opt4_select_count = -1;
																}
																								
																while($row4 = mysql_fetch_array($result4)){
																	if($row4['selected_option'] == $row3['opt1'])
																		$opt1_select_count++;
																	if($row4['selected_option'] == $row3['opt2'])
																		$opt2_select_count++;
																	if($row4['selected_option'] == $row3['opt3'])
																		$opt3_select_count++;
																	if($row4['selected_option'] == $row3['opt4'])
																		$opt4_select_count++;
																	if($row4['selected_option'] == "skipped")
																		$skipped_count++;
																}
																//opt1
																echo "<td>";
																	if($opt1_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt1']."<hr/>".
																		$opt1_select_count;
																	}
																echo "</td>";
																						
																//opt2
																echo "<td>";
																	if($opt2_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt2']."<hr/>".
																		$opt2_select_count;
																	}
																echo "</td>";
																					
																//opt3
																echo "<td>";
																	if($opt3_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt3']."<hr/>".
																		$opt3_select_count;
																	}
																echo "</td>";
																						
																//opt4
																echo "<td>";
																	if($opt4_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt4']."<hr/>".
																		$opt4_select_count;
																	}
																echo "</td>";
																							
																//skipped
																echo "<td>";
																	if($skipped_count == -1){
																		echo "-";
																	}
																	else{
																		echo $skipped_count;
																	}
																echo "</td>
															</tr>";	
														}
													echo "</table>
												</div>";
																		
												//Subjectives Tab
												echo "<div class='tab-pane fade active' id='news_sub".$i."'>";
													//querying all subjective questions of a particular para
													$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
													mysql_query("SET NAMES utf8");
													$result3 = mysql_query($query3);
																		
													$j = 1;
													while($row3 = mysql_fetch_array($result3)){
														echo "<table class = 'ques_table' id='news_para_sub_ques".$i."' border = 'solid'>";
															//subjective question Heading
															echo "<tr align = 'center'>
																<td>
																	<h4><big>
																		Question : ".$j.
																	"</big></h4>
																</td>
															</tr>";
																				
															//subjective Questions
															echo "<tr align = 'center'>
																<td>
																	<div class='ques_body'>
																		<h3><small>".
																			$row3['ques'].
																		"</small></h3>
																	</div>
																</td>
															</tr>";
																				
															//subjective answers row having table of answers
															echo "<tr align = 'center'>
																<td>
																	<table class='table' border = 'solid'>
																		<tr align='center'>
																			<td class='col-lg-3'>User</td>
																			<td class='col-lg-9'>Answers</td>
																		</tr>";
																							
																		//querying all users and all thier answrs of a question
																		//$query4 = "SELECT tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																		$query4 = "SELECT uid, tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."' GROUP BY uid";
																		mysql_query("SET NAMES utf8");
																		$result4 = mysql_query($query4);
																							
																		while($row4 = mysql_fetch_array($result4)){
																			//$query5 = "SELECT uid FROM test_data WHERE `tid` = '".$row4['tid']."'";
																			//mysql_query("SET NAMES utf8");
																			//$result5 = mysql_query($query5);
																			//$row5 = mysql_fetch_array($result5);
																									
																			//$query6 = "SELECT * FROM main WHERE `user_id` = '".$row5['uid']."'";
																			$query6 = "SELECT * FROM main WHERE `user_id` = '".$row4['uid']."'";
																			$result6 = mysql_query($query6);
																			$row6 = mysql_fetch_array($result6);
																									
																			echo "<tr align='center'>
																				<td>".$row6['email']."<br/>{ Age - ".$row6['age'].", ";
																					if($row6['gender'] == "1"){
																						echo "M, ";
																					}
																					else{
																						echo "F, ";
																					}
																					if($row6['edu_back'] == "higher_sec"){
																						echo "Higher Secondary }";
																					}
																					else if($row6['edu_back'] == "ug"){
																						echo "Undergraduate }";
																					}
																					else if($row6['edu_back'] == "pg"){
																						echo "Postgraduate }";
																					}
																					else{
																						echo "Other }";
																					}
																				echo "</td>
																				
																				<td>";
																					$query7 = "SELECT tid FROM test_data WHERE `uid` = '".$row4['uid']."' AND `pid` = '".$row['pid']."'";
																					$result7 = mysql_query($query7);
																					while($row7 = mysql_fetch_array($result7)){
																						$query8 = "SELECT selected_option FROM test_questions_data WHERE `tid` = '".$row7['tid']."' AND `qid` = '".$row3['qid']."'";
																						$result8 = mysql_query($query8);
																						$row8 = mysql_fetch_array($result8);
																						echo $row8['selected_option']."<hr/>";
																					}
																				echo "</td>
																			</tr>";
																		}
																	echo "</table>
																</td>
															</tr>";
														
														$j++;
														echo "</table>";	
													}
												echo "</div>";
																		
												//Graphs and Charts Tab
												echo "<div class='tab-pane fade active' id='news_graphs".$i."'>";
												//////////////////////////////////////////////////////////////////////////////////////////////////////////
													echo "<table class='table table-bordered'><tr>";
													//CREATING A CHART OF FONT STYLE FOR EACH PARAGRAPH In News Article Type
													echo "<td>";
													if(array_sum($para_font_style_count)!=0){
														$strXML= "<graph caption='Tests given in different Font Style' subCaption='with Legal Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
														echo "<table class='table table-bordered'>";
														echo "<tr>";
															echo "<td>Font Style</td>";
															echo "<td>Male views</td>";
															echo "<td>Female views</td>";
															echo "<td>Average Reading Time</td>";
															echo "<td>Average Test Time</td>";
														echo "</tr>";
														for($t=0;$t<5;$t++){
															echo "<tr>
															<td>".
																$font_style[$t].
															"</td>
															
															<td>".
																$para_font_style_male[$t].
															"</td>
															
															<td>".
																$para_font_style_female[$t].
															"</td>";
															if($para_font_style_count[$t]!=0){
																$para_font_style_reading_time[$t]=($para_font_style_reading_time[$t]/ $para_font_style_count[$t]);
																$para_font_style_test_time[$t]=($para_font_style_test_time[$t]/$para_font_style_count[$t]);
																echo "<td>".
																	$para_font_style_reading_time[$t].
																"</td>";
																
																echo "<td>".
																	$para_font_style_test_time[$t].
																"</td>";
															}
															else{
																echo "<td>-</td>";
																echo "<td>-</td>";
															}
															$strXML .= "<set name='" . $font_style[$t] . "' value='" . $para_font_style_count[$t] . "' />";
															echo "</tr>";
														}
													$strXML .= "</graph>";
													echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "news_font_style_para_chart".$i, 500, 400);
													echo "</table>";
												}
												echo "</td>";
												echo "<td>";			
												
												//CREATING A CHART OF FONT SIZE FOR EACH PARAGRAPH In News Article Type
												if(array_sum($para_font_size_count)!=0){
													$strXML= "<graph caption='Tests given in different Font Size' subCaption='with News Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Font Size Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$font_size[$t].
														"</td>
														
														<td>".
															$para_font_size_male[$t].
														"</td>
														
														<td>".
															$para_font_size_female[$t].
														"</td>";
														if($para_font_size_count[$t]!=0){
															$para_font_size_reading_time[$t]=($para_font_size_reading_time[$t]/ $para_font_size_count[$t]);
															$para_font_size_test_time[$t]=($para_font_size_test_time[$t]/$para_font_size_count[$t]);
															echo "<td>".
																$para_font_size_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_font_size_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $font_size[$t] . "' value='" . $para_font_size_count[$t] . "' />";
														echo "</tr>";				
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "news_font_size_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr>";
											
											echo "<tr>";
											echo "<td>";
											
											//CREATING A CHART OF LINE HEIG FOR EACH PARAGRAPH
											if(array_sum($para_line_height_count)!=0){
												$strXML= "<graph caption='Tests given in different line height' subCaption='with News Article type'pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Line Height Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$line_height[$t].
														"</td>
														
														<td>".
															$para_line_height_male[$t].
														"</td>
														
														<td>".
															$para_line_height_female[$t].
														"</td>";
														if($para_line_height_count[$t]!=0){
															$para_line_height_reading_time[$t]=($para_line_height_reading_time[$t]/ $para_line_height_count[$t]);
															$para_line_height_test_time[$t]=($para_line_height_test_time[$t]/$para_line_height_count[$t]);
															echo "<td>".
																$para_line_height_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_line_height_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $line_height[$t] . "' value='" . $para_line_height_count[$t] . "' />";
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "news_line_height_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "<td>";
											
											//CREATING A CHART OF WORD SPACING FOR EACH PARAGRAPH
											if(array_sum($para_word_spacing_count)!=0){
												$strXML= "<graph caption='Tests given in different line Word Spacing' subCaption='with News Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
												echo "<table class='table table-bordered'>";
												echo "<tr>";
													echo "<td>Word Spacing Ranges</td>";
													echo "<td>Male views</td>";
													echo "<td>Female views</td>";
													echo "<td>Average Reading Time</td>";
													echo "<td>Average Test Time</td>";
												echo "</tr>";				
												for($t=0;$t<5;$t++){
													echo "<tr>
													<td>".
														$word_spacing[$t].
													"</td>
													
													<td>".
														$word_spacing_male[$t].
													"</td>
													
													<td>".
														$word_spacing_female[$t].
													"</td>";
													
													if($word_spacing_count[$t]!=0){
														$para_word_spacing_reading_time[$t]=($para_word_spacing_reading_time[$t]/ $para_word_spacing_count[$t]);
														$para_word_spacing_test_time[$t]=($para_word_spacing_test_time[$t]/$para_word_spacing_count[$t]);
														echo "<td>".
															$para_word_spacing_reading_time[$t].
														"</td>";
														
														echo "<td>".
															$para_word_spacing_test_time[$t].
														"</td>";
													}
													else{
														echo "<td>-</td>";
														echo "<td>-</td>";
													}
													$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $para_word_spacing_count[$t] . "' />";
												}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "news_word_spacing_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr></table>";
											echo "</div>
												
											</div>";//end of div containing all 3 tabs of this para
										echo "</div>
									</div>
								</td>
							</tr>";
							$i++;
						}
					echo "</table>";
					?>
					<!--CHARTS FOR NEWS ARTICLE|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					<?php
					//Accordian for CHARTS FOR NEWS ARTICLE|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					echo '<button id=news_accordian" class="article_accordian_class btn btn-warning btn-lg btn-block" data-toggle="collapse" data-target="#news_graphs">
						Charts for News articles
					</button>';
					
					echo "<table id='news_graphs' class='collapse in table table-bordered'><tr>";
					/////////////////////CHART FOR FONT STYLE in News Article type///////////
					echo "<td>";
					if(array_sum($font_style_count)!=0){
						$strXML= "<graph caption='Tests given in different Font styles' subcaption='with News Article Type' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Style</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$font_style[$t].
							"</td>
							
							<td>".
								$font_style_male[$t].
							"</td>
							
							<td>".
								$font_style_female[$t].
							"</td>";
							if($font_style_count[$t]!=0){
								$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
								$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
								echo "<td>".
									$font_style_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_style_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "news_font_style_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR FONT SIZE in news Article type///////////
					if(array_sum($font_size_count)!=0){
						$strXML= "<graph caption='Tests given in different Font sizes' subcaption='with news Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Size Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$font_size[$t].
							"</td>
							
							<td>".
								$font_size_male[$t].
							"</td>
							
							<td>".
								$font_size_female[$t].
							"</td>";
							if($font_size_count[$t]!=0){
								$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
								$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
								echo "<td>".
									$font_size_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_size_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "news_size_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td>";
					
					/////////////////////CHART FOR Line Height in news Article type///////////
					if(array_sum($line_height_count)!=0){
						$strXML= "<graph caption='Tests given in different Line heights' subcaption='with news Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Line Height Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$line_height[$t].
							"</td>
							
							<td>".
								$line_height_male[$t].
							"</td>
							
							<td>".
								$line_height_female[$t].
							"</td>";
							if($line_height_count[$t]!=0){
								$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
								$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
								echo "<td>".
									$line_height_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$line_height_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "news_line_height_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR Word Spacing in news Article type///////////
					if(array_sum($word_spacing_count)!=0){
						$strXML= "<graph caption='Tests given in different word spacing' subcaption='with news Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Word Spacing Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$word_spacing[$t].
							"</td>
							
							<td>".
								$word_spacing_male[$t].
							"</td>
							
							<td>".
								$word_spacing_female[$t].
							"</td>";
							if($word_spacing_count[$t]!=0){
								$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
								$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
								echo "<td>".
									$word_spacing_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$word_spacing_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
						}
						$strXML .= "</graph>";
	
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "news_word_spacing_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr></table>";
					?>
				</div><!--End of news Article-->
				
				<!--Ncert Tab-->
				<div class="tab-pane fade" id="ncert">
					<?php
					echo "<div align=center>
						<h2><small>
							Total tests done yet - ".$view_count['NCERT Text']."<br/>{ M - ". $male_view_count['NCERT Text'].", F - ". $female_view_count['NCERT Text']." }
						</small></h2>
					</div>";
						
					//font style counts
					$font_style_count = array('0','0','0','0','0');
					$font_style_male = array('0','0','0','0','0');
					$font_style_female = array('0','0','0','0','0');
					$font_style_reading_time = array('0','0','0','0','0');
					$font_style_test_time = array('0','0','0','0','0');
					
					
					//font size counts
					$font_size_count = array('0', '0', '0', '0','0','0');
					$font_size_male = array('0','0','0','0','0','0');
					$font_size_female = array('0','0','0','0','0','0');
					$font_size_reading_time = array('0','0','0','0','0','0');
					$font_size_test_time = array('0','0','0','0','0','0');
					
					
					//Line Height counts
					$line_height_count = array('0', '0', '0', '0','0','0','0','0');
					$line_height_male = array('0','0','0','0','0','0','0','0');
					$line_height_female = array('0','0','0','0','0','0','0','0');
					$line_height_reading_time = array('0','0','0','0','0','0');
					$line_height_test_time = array('0','0','0','0','0','0');
					
					
					//Word Spacing counts
					$word_spacing_count = array('0', '0', '0', '0','0');
					$word_spacing_male = array('0','0','0','0','0');
					$word_spacing_female = array('0','0','0','0','0');
					$word_spacing_reading_time = array('0','0','0','0','0');
					$word_spacing_test_time = array('0','0','0','0','0');
						
					$i=1;
					$query="SELECT * FROM paragraphs WHERE `article_type`='NCERT Text'";
					mysql_query("SET NAMES utf8");
					$result=mysql_query($query);
					
					while($row=mysql_fetch_array($result)){
						$para_font_style_count = array('0','0','0','0','0');
						$para_font_style_male = array('0','0','0','0','0');
						$para_font_style_female = array('0','0','0','0','0');
						$para_font_style_reading_time = array('0','0','0','0','0');
						$para_font_style_test_time = array('0','0','0','0','0');
										
						$para_font_size_count = array('0', '0', '0', '0','0','0');
						$para_font_size_male = array('0','0','0','0','0','0');
						$para_font_size_female = array('0','0','0','0','0','0');
						$para_font_size_reading_time = array('0','0','0','0','0','0');
						$para_font_size_test_time = array('0','0','0','0','0','0');
										
						$para_line_height_count = array('0', '0', '0', '0','0','0');
						$para_line_height_male = array('0','0','0','0','0','0');
						$para_line_height_female = array('0','0','0','0','0','0');
						$para_line_height_reading_time = array('0','0','0','0','0','0');
						$para_line_height_test_time = array('0','0','0','0','0','0');
										
						$para_word_spacing_count = array('0', '0', '0', '0','0');
						$para_word_spacing_male = array('0','0','0','0','0');
						$para_word_spacing_female = array('0','0','0','0','0');
						$para_word_spacing_reading_time = array('0','0','0','0','0');
						$para_word_spacing_test_time = array('0','0','0','0','0');
										
						$var1=$row['pid'];
						$query1="SELECT * FROM test_data Where `pid`='$var1'"; 
						$result1=mysql_query($query1);
						$totalncertviewers=  mysql_num_rows($result1); 
						$ncertmale=0;
						$ncertfemale=0;
									
						while($row1=mysql_fetch_array($result1)){
							$var2=$row1['uid'];
							$query2="SELECT * FROM main Where `user_id`='$var2'"; 
							$result2=mysql_query($query2);
							$row2=mysql_fetch_array($result2);
									
							if($row2['gender']=='1'){
								$ncertmale++;
							}
							else{
								$ncertfemale++;
							}
							///////////CALCULATION OF  ncert DOCUMENT FONT STYLE//////
							if($row1['font']=='Arial'){
								$font_style_count[0]++;
								$font_style_reading_time[0]+=$row1['reading_time'];
								$font_style_test_time[0]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[0]++;
								}
								else{
									$font_style_female[0]++;
								}
							}
                            if($row1['font']=='Calibri'){
								$font_style_count[1]++;
								$font_style_reading_time[1]+=$row1['reading_time'];
								$font_style_test_time[1]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[1]++;
								}
								else{
									$font_style_female[1]++;
								}
							}		
							if($row1['font']=='Comic Sans MS'){
								$font_style_count[2]++;
								$font_style_reading_time[2]+=$row1['reading_time'];
								$font_style_test_time[2]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[2]++;
								}
								else{
									$font_style_female[2]++;
								}
							}		  
							if($row1['font']=='Times New Roman'){
								$font_style_count[3]++;
								$font_style_reading_time[3]+=$row1['reading_time'];
								$font_style_test_time[3]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[3]++;
								}
								else{
									$font_style_female[3]++;
								}
							}		 
							if($row1['font']=='Lucida Sans'){
								$font_style_count[4]++;	
								$font_style_reading_time[4]+=$row1['reading_time'];
								$font_style_test_time[4]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[4]++;
								}
								else{
									$font_style_female[4]++;
								}
 							}
							///////////END OF CALCULATION OF  ncert DOCUMENT FONT STYLE//////
									
							///////////CALCULATION OF  ncert DOCUMENT FONT SIZE//////
							switch($row1['size']){
								case (70<=$row1['size']&&$row1['size']<=90):
									$font_size_count[0]++;
									$font_size_reading_time[0]+=$row1['reading_time'];
									$font_size_test_time[0]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[0]++;
									}
									else{
										$font_size_female[0]++;
									}
								break;
								
								case (100<=$row1['size']&&$row1['size']<=120):
									$font_size_count[1]++;
									$font_size_reading_time[1]+=$row1['reading_time'];
									$font_size_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[1]++;
									}
									else{
										$font_size_female[1]++;
									}
								break;
									
								case (130<=$row1['size']&&$row1['size']<=150):
									$font_size_count[2]++;
									$font_size_reading_time[2]+=$row1['reading_time'];
									$font_size_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[2]++;
									}
									else{
										$font_size_female[2]++;
									}
								break;
									
								case (160<=$row1['size']&&$row1['size']<=180):
									$font_size_count[3]++;
									$font_size_reading_time[3]+=$row1['reading_time'];
									$font_size_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[3]++;
									}
									else{
										$font_size_female[3]++;
									}
								break;
										
								case (190<=$row1['size']&&$row1['size']<=210):
									$font_size_count[4]++;
									$font_size_reading_time[4]+=$row1['reading_time'];
									$font_size_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[4]++;
									}
									else{
										$font_size_female[4]++;
									}
								break;
									
								case (220<=$row1['size']&&$row1['size']<=240):
									$font_size_count[5]++;
									$font_size_reading_time[5]+=$row1['reading_time'];
									$font_size_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[5]++;
									}
									else{
										$font_size_female[5]++;
									}
								break;
							}
							///////////END OF CALCULATION OF  ncert DOCUMENT FONT line_height//////
							
							///////////CALCULATION OF  ncert DOCUMENT LINE HEIGHT //////
							switch($row1['line_height']){
								case (20<=$row1['line_height']&&$row1['line_height']<=24):
									$line_height_count[0]++;
									$line_height_reading_time[0]+=$row1['reading_time'];
									$line_height_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[0]++;
									}
									else{
										$line_height_female[0]++;
									}
								break;
								
								case (25<=$row1['line_height']&&$row1['line_height']<=29):
									$line_height_count[1]++;
									$line_height_reading_time[1]+=$row1['reading_time'];
									$line_height_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[1]++;
									}
									else{
										$line_height_female[1]++;
									}
								break;
								
								case (30<=$row1['line_height']&&$row1['line_height']<=34):
									$line_height_count[2]++;
									$line_height_reading_time[2]+=$row1['reading_time'];
									$line_height_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[2]++;
									}
									else{
										$line_height_female[2]++;
									}
								break;
						
								case (35<=$row1['line_height']&&$row1['line_height']<=39):
									$line_height_count[3]++;
									$line_height_reading_time[3]+=$row1['reading_time'];
									$line_height_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[3]++;
									}
									else{
										$line_height_female[3]++;
									}
								break;
								
								case (40<=$row1['size']&&$row1['line_height']<=44):
									$font_line_height_count[4]++;
									$line_height_reading_time[4]+=$row1['reading_time'];
									$line_height_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[4]++;
									}
									else{
										$line_height_female[4]++;
									}
								break;
									
								case (45<=$row1['line_height']&&$row1['line_height']<=50):
									$line_height_count[5]++;
									$line_height_reading_time[5]+=$row1['reading_time'];
									$line_height_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[5]++;
									}
									else{
										$line_height_female[5]++;
									}
								break;
								
									
							}
							///////////END OF CALCULATION OF  ncert DOCUMENT LINE HEIGHT //////
										 
							///////////CALCULATION OF ncert DOCUMENT WORD SPACING //////
							switch($row1['word_spacing']){
								case (0<=$row1['word_spacing']&&$row1['word_spacing']<=3):
									$word_spacing_count[0]++;
									$word_spacing_reading_time[0]+=$row1['reading_time'];
									$word_spacing_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$word_spacing_male[0]++;
									}
									else{
										$word_spacing_female[0]++;
									}
								break;

								case (4<=$row1['word_spacing']&&$row1['word_spacing']<=7):
									$word_spacing_count[1]++;
									$word_spacing_reading_time[1]+=$row1['reading_time'];
									$word_spacing_test_time[1]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[1]++;
									}
									else{
										$word_spacing_female[1]++;
									}
								break;
								
								case (8<=$row1['word_spacing']&&$row1['word_spacing']<=11):
									$word_spacing_count[2]++;
									$word_spacing_reading_time[2]+=$row1['reading_time'];
									$word_spacing_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[2]++;
									}
									else{
										$word_spacing_female[2]++;
									}
								break;
								
								case (12<=$row1['word_spacing']&&$row1['word_spacing']<=15):
									$word_spacing_count[3]++;
									$word_spacing_reading_time[3]+=$row1['reading_time'];
									$word_spacing_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[3]++;
									}
									else{
										$word_spacing_female[3]++;
									}
								break;
								
								case (16<=$row1['word_spacing']&&$row1['word_spacing']<=20):
									$word_spacing_count[4]++;
									$word_spacing_reading_time[4]+=$row1['reading_time'];
									$word_spacing_test_time[4]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[4]++;
									}
									else{
										$word_spacing_female[4]++;
									}
								break;
							}
							///////////END OF CALCULATION OF ncert DOCUMENT WORD SPACING //////
						}
						//paragraphs table
						echo "<table class='table table-bordered'>";
							echo "<tr>
								<td>";
								//para_panel
									echo "<div class='para_panel' id='ncert_para_panel".$i."'>
										<button class='btn-primary btn-block btn-lg' data-toggle='collapse' data-target='#ncert_para".$i."' data-parent='#ncert_para_panel".$i."'>
											<div class='para_info1'>
												Paragraph : ".$i."
											</div>
											<div class='para_info'>".
													$totalncertviewers."
													{ M - ".$ncertmale.",  F - ".$ncertfemale." }
											</div>
										</button>
											
										<div id='ncert_para".$i."' class='panel-collapse panel-body collapse'>".
											$row['para'];
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//////////////CALCULATION FOR CREATING CHART ON FONT SYTLE FOR EACH PARAGRAPH IN ncert DOCUMENT///////////
											for($k=0;$k<5;$k++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `font`='".$font_style[$k]."'";
												$para=mysql_query($query);
												$para_font_style_count[$k]=mysql_num_rows($para);
												
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_style_reading_time[$k]+=$row_para['reading_time'];
													$para_font_style_test_time[$k]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_style_male[$k]++;
													else
														$para_font_style_female[$k]++;  
										        }
											}   
											//////CALCULATION FOR CREATING CHART ON FONT SIZE AND LINE HEIGHT AND WORD-SPACING FOR EACH PARAGRAPH IN ncert DOCUMENT///////////
											$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."'";
											$para=mysql_query($query);
											while($row_para=mysql_fetch_array($para)){
												switch($row_para['size']){
													case (70<=$row_para['size']&&$row_para['size']<=90):
														$para_font_size_count[0]++;
														$para_font_size_reading_time[0]+=$row_para['reading_time'];
														$para_font_size_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[0]++;
														}
														else{
															$para_font_size_female[0]++;
														}
													break;
													
													case (100<=$row_para['size']&&$row_para['size']<=120):
														$para_font_size_count[1]++;
														$para_font_size_reading_time[1]+=$row_para['reading_time'];
														$para_font_size_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[1]++;
														}
														else{
															$para_font_size_female[1]++;
														}
													break;
														
													case (130<=$row_para['size']&&$row_para['size']<=150):
														$para_font_size_count[2]++;
														$para_font_size_reading_time[2]+=$row_para['reading_time'];
														$para_font_size_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[2]++;
														}
														else{
															$para_font_size_female[2]++;
														}
													break;
														
													case (160<=$row_para['size']&&$row_para['size']<=180):
														$para_font_size_count[3]++;
														$para_font_size_reading_time[3]+=$row_para['reading_time'];
														$para_font_size_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_font_size_male[3]++;
														}
														else{
															$para_font_size_female[3]++;
														}
													break;
														
													case (190<=$row_para['size']&&$row_para['size']<=210):
														$para_font_size_count[4]++;
														$para_font_size_reading_time[4]+=$row_para['reading_time'];
														$para_font_size_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[4]++;
														}
														else{
															$para_font_size_female[4]++;
														}
													break;
														
													case (220<=$row_para['size']&&$row_para['size']<=240):
														$para_font_size_count[5]++;
														$para_font_size_reading_time[5]+=$row_para['reading_time'];
														$para_font_size_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[5]++;
														}
														else{
															$para_para_font_size_female[5]++;
														}
													break;
												}
															
												///////////CALCULATION OF  LINE HEIGHT FOR EACH PARAGRAPH //////
												switch($row_para['line_height']){
													case (20<=$row_para['line_height']&&$row_para['line_height']<=24):
														$para_line_height_count[0]++;
														$para_line_height_reading_time[0]+=$row_para['reading_time'];
														$para_line_height_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[0]++;
														}
														else{
															$para_line_height_female[0]++;
														}
													break;
														
													case (25<=$row_para['line_height']&&$row_para['line_height']<=29):
														$para_line_height_count[1]++;
														$para_line_height_reading_time[1]+=$row_para['reading_time'];
														$para_line_height_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[1]++;
														}
														else{
															$para_line_height_female[1]++;
														}
														break;
													
													case (30<=$row_para['line_height']&&$row_para['line_height']<=34):
														$para_line_height_count[2]++;
														$para_line_height_reading_time[2]+=$row_para['reading_time'];
														$para_line_height_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[2]++;
														}
														else{
															$para_line_height_female[2]++;
														}
													break;
														
													case (35<=$row_para['line_height']&&$row_para['line_height']<=39):
														$para_line_height_count[3]++;
														$para_line_height_reading_time[3]+=$row_para['reading_time'];
														$para_line_height_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[3]++;
														}
														else{
															$para_line_height_female[3]++;
														}
													break;
														
													case (40<=$row_para['size']&&$row_para['line_height']<=44):
														$para_line_height_count[4]++;
														$para_line_height_reading_time[4]+=$row_para['reading_time'];
														$para_line_height_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[4]++;
														}
														else{
															$para_line_height_female[4]++;
														}
													break;
														
													case (45<=$row_para['line_height']&&$row_para['line_height']<=50):
														$para_line_height_count[5]++;
														$para_line_height_reading_time[5]+=$row_para['reading_time'];
														$para_line_height_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[5]++;
														}
														else{
															$para_line_height_female[5]++;
														}
													break;
														
													
												}
															
												///////////CALCULATION OF WORD SPACING FOR EACH PARAGRAPH//////
												switch($row_para['word_spacing']){
													case (0<=$row_para['word_spacing']&&$row_para['word_spacing']<=3):
														$para_word_spacing_count[0]++;
														$para_word_spacing_reading_time[0]+=$row_para['reading_time'];
														$para_word_spacing_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_word_spacing_male[0]++;
														}
														else{
															$para_word_spacing_female[0]++;
														}
													break;
													
													case (4<=$row_para['word_spacing']&&$row_para['word_spacing']<=7):
														$para_word_spacing_count[1]++;
														$para_word_spacing_reading_time[1]+=$row_para['reading_time'];
														$para_word_spacing_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[1]++;
														}
														else{
															$para_word_spacing_female[1]++;
														}
													break;
													
													case (8<=$row_para['word_spacing']&&$row_para['word_spacing']<=11):
														$para_word_spacing_count[2]++;
														$para_word_spacing_reading_time[2]+=$row_para['reading_time'];
														$para_word_spacing_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[2]++;
														}
														else{
															$para_word_spacing_female[2]++;
														}
													break;
													
													case (12<=$row_para['word_spacing']&&$row_para['word_spacing']<=15):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
													
													case (16<=$row_para['word_spacing']&&$row_para['word_spacing']<=20):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
												}
											}	
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//Nav tabs list -obj, sub and graphs
											echo "<ul class='nav nav-tabs nav-justified' role='tablist'>
												<li class='active in'><a href='#ncert_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
												<li><a href='#ncert_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
												<li><a href='#ncert_graphs".$i."' role='tab' data-toggle='tab'>Graphs and Charts</a></li>
											</ul>";
															
											//Tab panes
											echo "<div class='tab-content'>";
												//Objectives Tab
												echo "<div class='tab-pane fade active in' id='ncert_obj".$i."'>
													<table class = 'table table-hover table-bordered ques_table' id='ncert_para_obj_ques".$i."'>
														<tr align = 'center'>
															<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
															<td class = 'col-lg-6'><h4><big>Questions</big></h4></td>
															<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Skipped</big></h6></td>
														</tr>";
																					
														$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
														mysql_query("SET NAMES utf8");
														$result3 = mysql_query($query3);
							
														$j = 1;
														while($row3 = mysql_fetch_array($result3)){
															echo "<tr align = 'center'>";
																//S.No.
																echo "<td>".
																	$j.
																"</td>";
																							
																//Objective Questions
																echo "<td>".
																	$row3['ques'].
																"</td>";
																
																$j++;
																							
																//Options data
																$query4 = "SELECT * FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																mysql_query("SET NAMES utf8");
																$result4 = mysql_query($query4);
																								
																$opt1_select_count = 0;
																$opt2_select_count = 0;
																$opt3_select_count = 0;
																$opt4_select_count = 0;
																$skipped_count = 0;
																							
																if($row3['opt1'] == ""){
																	$opt1_select_count = -1;
																}
																if($row3['opt2'] == ""){
																	$opt2_select_count = -1;
																}
																if($row3['opt3'] == ""){
																	$opt3_select_count = -1;
																}
																if($row3['opt4'] == ""){
																	$opt4_select_count = -1;
																}
																								
																while($row4 = mysql_fetch_array($result4)){
																	if($row4['selected_option'] == $row3['opt1'])
																		$opt1_select_count++;
																	if($row4['selected_option'] == $row3['opt2'])
																		$opt2_select_count++;
																	if($row4['selected_option'] == $row3['opt3'])
																		$opt3_select_count++;
																	if($row4['selected_option'] == $row3['opt4'])
																		$opt4_select_count++;
																	if($row4['selected_option'] == "skipped")
																		$skipped_count++;
																}
																//opt1
																echo "<td>";
																	if($opt1_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt1']."<hr/>".
																		$opt1_select_count;
																	}
																echo "</td>";
																						
																//opt2
																echo "<td>";
																	if($opt2_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt2']."<hr/>".
																		$opt2_select_count;
																	}
																echo "</td>";
																					
																//opt3
																echo "<td>";
																	if($opt3_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt3']."<hr/>".
																		$opt3_select_count;
																	}
																echo "</td>";
																						
																//opt4
																echo "<td>";
																	if($opt4_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt4']."<hr/>".
																		$opt4_select_count;
																	}
																echo "</td>";
																							
																//skipped
																echo "<td>";
																	if($skipped_count == -1){
																		echo "-";
																	}
																	else{
																		echo $skipped_count;
																	}
																echo "</td>
															</tr>";	
														}
													echo "</table>
												</div>";
																		
												//Subjectives Tab
												echo "<div class='tab-pane fade active' id='ncert_sub".$i."'>";
													//querying all subjective questions of a particular para
													$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
													mysql_query("SET NAMES utf8");
													$result3 = mysql_query($query3);
																		
													$j = 1;
													while($row3 = mysql_fetch_array($result3)){
														echo "<table class = 'ques_table' id='ncert_para_sub_ques".$i."' border = 'solid'>";
															//subjective question Heading
															echo "<tr align = 'center'>
																<td>
																	<h4><big>
																		Question : ".$j.
																	"</big></h4>
																</td>
															</tr>";
																				
															//subjective Questions
															echo "<tr align = 'center'>
																<td>
																	<div class='ques_body'>
																		<h3><small>".
																			$row3['ques'].
																		"</small></h3>
																	</div>
																</td>
															</tr>";
																				
															//subjective answers row having table of answers
															echo "<tr align = 'center'>
																<td>
																	<table class='table' border = 'solid'>
																		<tr align='center'>
																			<td class='col-lg-3'>User</td>
																			<td class='col-lg-9'>Answers</td>
																		</tr>";
																							
																		//querying all users and all thier answrs of a question
																		//$query4 = "SELECT tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																		$query4 = "SELECT uid, tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."' GROUP BY uid";
																		mysql_query("SET NAMES utf8");
																		$result4 = mysql_query($query4);
																							
																		while($row4 = mysql_fetch_array($result4)){
																			//$query5 = "SELECT uid FROM test_data WHERE `tid` = '".$row4['tid']."'";
																			//mysql_query("SET NAMES utf8");
																			//$result5 = mysql_query($query5);
																			//$row5 = mysql_fetch_array($result5);
																									
																			//$query6 = "SELECT * FROM main WHERE `user_id` = '".$row5['uid']."'";
																			$query6 = "SELECT * FROM main WHERE `user_id` = '".$row4['uid']."'";
																			$result6 = mysql_query($query6);
																			$row6 = mysql_fetch_array($result6);
																									
																			echo "<tr align='center'>
																				<td>".$row6['email']."<br/>{ Age - ".$row6['age'].", ";
																					if($row6['gender'] == "1"){
																						echo "M, ";
																					}
																					else{
																						echo "F, ";
																					}
																					if($row6['edu_back'] == "higher_sec"){
																						echo "Higher Secondary }";
																					}
																					else if($row6['edu_back'] == "ug"){
																						echo "Undergraduate }";
																					}
																					else if($row6['edu_back'] == "pg"){
																						echo "Postgraduate }";
																					}
																					else{
																						echo "Other }";
																					}
																				echo "</td>
																				
																				<td>";
																					$query7 = "SELECT tid FROM test_data WHERE `uid` = '".$row4['uid']."' AND `pid` = '".$row['pid']."'";
																					$result7 = mysql_query($query7);
																					while($row7 = mysql_fetch_array($result7)){
																						$query8 = "SELECT selected_option FROM test_questions_data WHERE `tid` = '".$row7['tid']."' AND `qid` = '".$row3['qid']."'";
																						$result8 = mysql_query($query8);
																						$row8 = mysql_fetch_array($result8);
																						echo $row8['selected_option']."<hr/>";
																					}
																				echo "</td>
																			</tr>";
																		}
																	echo "</table>
																</td>
															</tr>";
														
														$j++;
														echo "</table>";	
													}
												echo "</div>";
																		
												//Graphs and Charts Tab
												echo "<div class='tab-pane fade active' id='ncert_graphs".$i."'>";
													echo "<table class='table table-bordered'><tr>";
													//CREATING A CHART OF FONT STYLE FOR EACH PARAGRAPH In ncert Article Type
													echo "<td>";
													if(array_sum($para_font_style_count)!=0){
														$strXML= "<graph caption='Tests given in different Font Style' subCaption='with Legal Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
														echo "<table class='table table-bordered'>";
														echo "<tr>";
															echo "<td>Font Style</td>";
															echo "<td>Male views</td>";
															echo "<td>Female views</td>";
															echo "<td>Average Reading Time</td>";
															echo "<td>Average Test Time</td>";
														echo "</tr>";
														for($t=0;$t<5;$t++){
															echo "<tr>
															<td>".
																$font_style[$t].
															"</td>
															
															<td>".
																$para_font_style_male[$t].
															"</td>
															
															<td>".
																$para_font_style_female[$t].
															"</td>";
															if($para_font_style_count[$t]!=0){
																$para_font_style_reading_time[$t]=($para_font_style_reading_time[$t]/ $para_font_style_count[$t]);
																$para_font_style_test_time[$t]=($para_font_style_test_time[$t]/$para_font_style_count[$t]);
																echo "<td>".
																	$para_font_style_reading_time[$t].
																"</td>";
																
																echo "<td>".
																	$para_font_style_test_time[$t].
																"</td>";
															}
															else{
																echo "<td>-</td>";
																echo "<td>-</td>";
															}
															$strXML .= "<set name='" . $font_style[$t] . "' value='" . $para_font_style_count[$t] . "' />";
															echo "</tr>";
														}
													$strXML .= "</graph>";
													echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_font_style_para_chart".$i, 500, 400);
													echo "</table>";
												}
												echo "</td>";
												echo "<td>";			
												
												//CREATING A CHART OF FONT SIZE FOR EACH PARAGRAPH In ncert Article Type
												if(array_sum($para_font_size_count)!=0){
													$strXML= "<graph caption='Tests given in different Font Size' subCaption='with ncert Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Font Size Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$font_size[$t].
														"</td>
														
														<td>".
															$para_font_size_male[$t].
														"</td>
														
														<td>".
															$para_font_size_female[$t].
														"</td>";
														if($para_font_size_count[$t]!=0){
															$para_font_size_reading_time[$t]=($para_font_size_reading_time[$t]/ $para_font_size_count[$t]);
															$para_font_size_test_time[$t]=($para_font_size_test_time[$t]/$para_font_size_count[$t]);
															echo "<td>".
																$para_font_size_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_font_size_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $font_size[$t] . "' value='" . $para_font_size_count[$t] . "' />";
														echo "</tr>";				
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_font_size_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr>";
											
											echo "<tr>";
											echo "<td>";
											
											//CREATING A CHART OF LINE HEIG FOR EACH PARAGRAPH
											if(array_sum($para_line_height_count)!=0){
												$strXML= "<graph caption='Tests given in different line height' subCaption='with ncert Article type'pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Line Height Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$line_height[$t].
														"</td>
														
														<td>".
															$para_line_height_male[$t].
														"</td>
														
														<td>".
															$para_line_height_female[$t].
														"</td>";
														if($para_line_height_count[$t]!=0){
															$para_line_height_reading_time[$t]=($para_line_height_reading_time[$t]/ $para_line_height_count[$t]);
															$para_line_height_test_time[$t]=($para_line_height_test_time[$t]/$para_line_height_count[$t]);
															echo "<td>".
																$para_line_height_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_line_height_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $line_height[$t] . "' value='" . $para_line_height_count[$t] . "' />";
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_line_height_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "<td>";
											
											//CREATING A CHART OF WORD SPACING FOR EACH PARAGRAPH
											if(array_sum($para_word_spacing_count)!=0){
												$strXML= "<graph caption='Tests given in different line Word Spacing' subCaption='with ncert Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
												echo "<table class='table table-bordered'>";
												echo "<tr>";
													echo "<td>Word Spacing Ranges</td>";
													echo "<td>Male views</td>";
													echo "<td>Female views</td>";
													echo "<td>Average Reading Time</td>";
													echo "<td>Average Test Time</td>";
												echo "</tr>";				
												for($t=0;$t<5;$t++){
													echo "<tr>
													<td>".
														$word_spacing[$t].
													"</td>
													
													<td>".
														$word_spacing_male[$t].
													"</td>
													
													<td>".
														$word_spacing_female[$t].
													"</td>";
													
													if($word_spacing_count[$t]!=0){
														$para_word_spacing_reading_time[$t]=($para_word_spacing_reading_time[$t]/ $para_word_spacing_count[$t]);
														$para_word_spacing_test_time[$t]=($para_word_spacing_test_time[$t]/$para_word_spacing_count[$t]);
														echo "<td>".
															$para_word_spacing_reading_time[$t].
														"</td>";
														
														echo "<td>".
															$para_word_spacing_test_time[$t].
														"</td>";
													}
													else{
														echo "<td>-</td>";
														echo "<td>-</td>";
													}
													$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $para_word_spacing_count[$t] . "' />";
												}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_word_spacing_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr></table>";
											echo "</div>
												
											</div>";//end of div containing all 3 tabs of this para
										echo "</div>
									</div>
								</td>
							</tr>";
							$i++;
						}
					echo "</table>";
					?>
					<!--CHARTS FOR NCERT Texts||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					<?php
					//Accordian for CHARTS FOR NCERT ARTICLE|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					echo '<button id=ncert_accordian" class="article_accordian_class btn btn-warning btn-lg btn-block" data-toggle="collapse" data-target="#ncert_graphs">
						Charts for NCERT Texts
					</button>';
					
					echo "<table id='ncert_graphs' class='collapse in table table-bordered'><tr>";
					/////////////////////CHART FOR FONT STYLE in NCERT Article type///////////
					echo "<td>";
					if(array_sum($font_style_count)!=0){
						$strXML= "<graph caption='Tests given in different Font styles' subcaption='with ncert Article Type' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Style</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$font_style[$t].
							"</td>
							
							<td>".
								$font_style_male[$t].
							"</td>
							
							<td>".
								$font_style_female[$t].
							"</td>";
							if($font_style_count[$t]!=0){
								$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
								$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
								echo "<td>".
									$font_style_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_style_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "ncert_font_style_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR FONT SIZE in ncert Article type///////////
					if(array_sum($font_size_count)!=0){
						$strXML= "<graph caption='Tests given in different Font sizes' subcaption='with ncert Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Size Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$font_size[$t].
							"</td>
							
							<td>".
								$font_size_male[$t].
							"</td>
							
							<td>".
								$font_size_female[$t].
							"</td>";
							if($font_size_count[$t]!=0){
								$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
								$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
								echo "<td>".
									$font_size_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_size_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_size_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td>";
					
					/////////////////////CHART FOR Line Height in ncert Article type///////////
					if(array_sum($line_height_count)!=0){
						$strXML= "<graph caption='Tests given in different Line heights' subcaption='with ncert Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Line Height Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$line_height[$t].
							"</td>
							
							<td>".
								$line_height_male[$t].
							"</td>
							
							<td>".
								$line_height_female[$t].
							"</td>";
							if($line_height_count[$t]!=0){
								$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
								$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
								echo "<td>".
									$line_height_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$line_height_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_line_height_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR Word Spacing in ncert Article type///////////
					if(array_sum($word_spacing_count)!=0){
						$strXML= "<graph caption='Tests given in different word spacing' subcaption='with ncert Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Word Spacing Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$word_spacing[$t].
							"</td>
							
							<td>".
								$word_spacing_male[$t].
							"</td>
							
							<td>".
								$word_spacing_female[$t].
							"</td>";
							if($word_spacing_count[$t]!=0){
								$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
								$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
								echo "<td>".
									$word_spacing_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$word_spacing_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
						}
						$strXML .= "</graph>";
	
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_word_spacing_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr></table>";
					?>
				</div><!--End of Ncert Text Article-->
				
				<!--legalpaper Tab-->
				<div class="tab-pane fade" id="legal">
					<?php
					echo "<div align=center>
						<h2><small>
							Total tests done yet - ".$view_count['Legal Document']."<br/>{ M - ". $male_view_count['Legal Document'].", F - ". $female_view_count['Legal Document']." }
						</small></h2>
					</div>";
						
					//font style counts
					$font_style_count = array('0','0','0','0','0');
					$font_style_male = array('0','0','0','0','0');
					$font_style_female = array('0','0','0','0','0');
					$font_style_reading_time = array('0','0','0','0','0');
					$font_style_test_time = array('0','0','0','0','0');
					
					
					//font size counts
					$font_size_count = array('0', '0', '0', '0','0','0');
					$font_size_male = array('0','0','0','0','0','0');
					$font_size_female = array('0','0','0','0','0','0');
					$font_size_reading_time = array('0','0','0','0','0','0');
					$font_size_test_time = array('0','0','0','0','0','0');
					
					
					//Line Height counts
					$line_height_count = array('0', '0', '0', '0','0','0','0','0');
					$line_height_male = array('0','0','0','0','0','0','0','0');
					$line_height_female = array('0','0','0','0','0','0','0','0');
					$line_height_reading_time = array('0','0','0','0','0','0');
					$line_height_test_time = array('0','0','0','0','0','0');
					
					
					//Word Spacing counts
					$word_spacing_count = array('0', '0', '0', '0','0');
					$word_spacing_male = array('0','0','0','0','0');
					$word_spacing_female = array('0','0','0','0','0');
					$word_spacing_reading_time = array('0','0','0','0','0');
					$word_spacing_test_time = array('0','0','0','0','0');
						
					$i=1;
					$query="SELECT * FROM paragraphs WHERE `article_type`='Legal Document'";
					mysql_query("SET NAMES utf8");
					$result=mysql_query($query);
					
					while($row=mysql_fetch_array($result)){
						$para_font_style_count = array('0','0','0','0','0');
						$para_font_style_male = array('0','0','0','0','0');
						$para_font_style_female = array('0','0','0','0','0');
						$para_font_style_reading_time = array('0','0','0','0','0');
						$para_font_style_test_time = array('0','0','0','0','0');
										
						$para_font_size_count = array('0', '0', '0', '0','0','0');
						$para_font_size_male = array('0','0','0','0','0','0');
						$para_font_size_female = array('0','0','0','0','0','0');
						$para_font_size_reading_time = array('0','0','0','0','0','0');
						$para_font_size_test_time = array('0','0','0','0','0','0');
										
						$para_line_height_count = array('0', '0', '0', '0','0','0');
						$para_line_height_male = array('0','0','0','0','0','0');
						$para_line_height_female = array('0','0','0','0','0','0');
						$para_line_height_reading_time = array('0','0','0','0','0','0');
						$para_line_height_test_time = array('0','0','0','0','0','0');
										
						$para_word_spacing_count = array('0', '0', '0', '0','0');
						$para_word_spacing_male = array('0','0','0','0','0');
						$para_word_spacing_female = array('0','0','0','0','0');
						$para_word_spacing_reading_time = array('0','0','0','0','0');
						$para_word_spacing_test_time = array('0','0','0','0','0');
										
						$var1=$row['pid'];
						$query1="SELECT * FROM test_data Where `pid`='$var1'"; 
						$result1=mysql_query($query1);
						$totallegalviewers=  mysql_num_rows($result1); 
						$legalmale=0;
						$legalfemale=0;
									
						while($row1=mysql_fetch_array($result1)){
							$var2=$row1['uid'];
							$query2="SELECT * FROM main Where `user_id`='$var2'"; 
							$result2=mysql_query($query2);
							$row2=mysql_fetch_array($result2);
									
							if($row2['gender']=='1'){
								$legalmale++;
							}
							else{
								$legalfemale++;
							}
							///////////CALCULATION OF  legal DOCUMENT FONT STYLE//////
							if($row1['font']=='Arial'){
								$font_style_count[0]++;
								$font_style_reading_time[0]+=$row1['reading_time'];
								$font_style_test_time[0]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[0]++;
								}
								else{
									$font_style_female[0]++;
								}
							}
                            if($row1['font']=='Calibri'){
								$font_style_count[1]++;
								$font_style_reading_time[1]+=$row1['reading_time'];
								$font_style_test_time[1]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[1]++;
								}
								else{
									$font_style_female[1]++;
								}
							}		
							if($row1['font']=='Comic Sans MS'){
								$font_style_count[2]++;
								$font_style_reading_time[2]+=$row1['reading_time'];
								$font_style_test_time[2]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[2]++;
								}
								else{
									$font_style_female[2]++;
								}
							}		  
							if($row1['font']=='Times New Roman'){
								$font_style_count[3]++;
								$font_style_reading_time[3]+=$row1['reading_time'];
								$font_style_test_time[3]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[3]++;
								}
								else{
									$font_style_female[3]++;
								}
							}		 
							if($row1['font']=='Lucida Sans'){
								$font_style_count[4]++;	
								$font_style_reading_time[4]+=$row1['reading_time'];
								$font_style_test_time[4]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[4]++;
								}
								else{
									$font_style_female[4]++;
								}
 							}
							///////////END OF CALCULATION OF  legal DOCUMENT FONT STYLE//////
									
							///////////CALCULATION OF  legal DOCUMENT FONT SIZE//////
							switch($row1['size']){
								case (70<=$row1['size']&&$row1['size']<=90):
									$font_size_count[0]++;
									$font_size_reading_time[0]+=$row1['reading_time'];
									$font_size_test_time[0]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[0]++;
									}
									else{
										$font_size_female[0]++;
									}
								break;
								
								case (100<=$row1['size']&&$row1['size']<=120):
									$font_size_count[1]++;
									$font_size_reading_time[1]+=$row1['reading_time'];
									$font_size_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[1]++;
									}
									else{
										$font_size_female[1]++;
									}
								break;
									
								case (130<=$row1['size']&&$row1['size']<=150):
									$font_size_count[2]++;
									$font_size_reading_time[2]+=$row1['reading_time'];
									$font_size_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[2]++;
									}
									else{
										$font_size_female[2]++;
									}
								break;
									
								case (160<=$row1['size']&&$row1['size']<=180):
									$font_size_count[3]++;
									$font_size_reading_time[3]+=$row1['reading_time'];
									$font_size_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[3]++;
									}
									else{
										$font_size_female[3]++;
									}
								break;
										
								case (190<=$row1['size']&&$row1['size']<=210):
									$font_size_count[4]++;
									$font_size_reading_time[4]+=$row1['reading_time'];
									$font_size_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[4]++;
									}
									else{
										$font_size_female[4]++;
									}
								break;
									
								case (220<=$row1['size']&&$row1['size']<=240):
									$font_size_count[5]++;
									$font_size_reading_time[5]+=$row1['reading_time'];
									$font_size_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[5]++;
									}
									else{
										$font_size_female[5]++;
									}
								break;
							}
							///////////END OF CALCULATION OF  legal DOCUMENT FONT line_height//////
							
							///////////CALCULATION OF  legal DOCUMENT LINE HEIGHT //////
							switch($row1['line_height']){
								case (20<=$row1['line_height']&&$row1['line_height']<=24):
									$line_height_count[0]++;
									$line_height_reading_time[0]+=$row1['reading_time'];
									$line_height_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[0]++;
									}
									else{
										$line_height_female[0]++;
									}
								break;
								
								case (25<=$row1['line_height']&&$row1['line_height']<=29):
									$line_height_count[1]++;
									$line_height_reading_time[1]+=$row1['reading_time'];
									$line_height_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[1]++;
									}
									else{
										$line_height_female[1]++;
									}
								break;
								
								case (30<=$row1['line_height']&&$row1['line_height']<=34):
									$line_height_count[2]++;
									$line_height_reading_time[2]+=$row1['reading_time'];
									$line_height_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[2]++;
									}
									else{
										$line_height_female[2]++;
									}
								break;
						
								case (35<=$row1['line_height']&&$row1['line_height']<=39):
									$line_height_count[3]++;
									$line_height_reading_time[3]+=$row1['reading_time'];
									$line_height_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[3]++;
									}
									else{
										$line_height_female[3]++;
									}
								break;
								
								case (40<=$row1['size']&&$row1['line_height']<=44):
									$font_line_height_count[4]++;
									$line_height_reading_time[4]+=$row1['reading_time'];
									$line_height_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[4]++;
									}
									else{
										$line_height_female[4]++;
									}
								break;
									
								case (45<=$row1['line_height']&&$row1['line_height']<=50):
									$line_height_count[5]++;
									$line_height_reading_time[5]+=$row1['reading_time'];
									$line_height_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[5]++;
									}
									else{
										$line_height_female[5]++;
									}
								break;
								
									
							}
							///////////END OF CALCULATION OF  legal DOCUMENT LINE HEIGHT //////
										 
							///////////CALCULATION OF legal DOCUMENT WORD SPACING //////
							switch($row1['word_spacing']){
								case (0<=$row1['word_spacing']&&$row1['word_spacing']<=3):
									$word_spacing_count[0]++;
									$word_spacing_reading_time[0]+=$row1['reading_time'];
									$word_spacing_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$word_spacing_male[0]++;
									}
									else{
										$word_spacing_female[0]++;
									}
								break;

								case (4<=$row1['word_spacing']&&$row1['word_spacing']<=7):
									$word_spacing_count[1]++;
									$word_spacing_reading_time[1]+=$row1['reading_time'];
									$word_spacing_test_time[1]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[1]++;
									}
									else{
										$word_spacing_female[1]++;
									}
								break;
								
								case (8<=$row1['word_spacing']&&$row1['word_spacing']<=11):
									$word_spacing_count[2]++;
									$word_spacing_reading_time[2]+=$row1['reading_time'];
									$word_spacing_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[2]++;
									}
									else{
										$word_spacing_female[2]++;
									}
								break;
								
								case (12<=$row1['word_spacing']&&$row1['word_spacing']<=15):
									$word_spacing_count[3]++;
									$word_spacing_reading_time[3]+=$row1['reading_time'];
									$word_spacing_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[3]++;
									}
									else{
										$word_spacing_female[3]++;
									}
								break;
								
								case (16<=$row1['word_spacing']&&$row1['word_spacing']<=20):
									$word_spacing_count[4]++;
									$word_spacing_reading_time[4]+=$row1['reading_time'];
									$word_spacing_test_time[4]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[4]++;
									}
									else{
										$word_spacing_female[4]++;
									}
								break;
							}
							///////////END OF CALCULATION OF legal DOCUMENT WORD SPACING //////
						}
						//paragraphs table
						echo "<table class='table table-bordered'>";
							echo "<tr>
								<td>";
								//para_panel
									echo "<div class='para_panel' id='legal_para_panel".$i."'>
										<button class='btn-primary btn-block btn-lg' data-toggle='collapse' data-target='#legal_para".$i."' data-parent='#legal_para_panel".$i."'>
											<div class='para_info1'>
												Paragraph : ".$i."
											</div>
											<div class='para_info'>".
													$totallegalviewers."
													{ M - ".$legalmale.",  F - ".$legalfemale." }
											</div>
										</button>
											
										<div id='legal_para".$i."' class='panel-collapse panel-body collapse'>".
											$row['para'];
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//////////////CALCULATION FOR CREATING CHART ON FONT SYTLE FOR EACH PARAGRAPH IN legal DOCUMENT///////////
											for($k=0;$k<5;$k++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `font`='".$font_style[$k]."'";
												$para=mysql_query($query);
												$para_font_style_count[$k]=mysql_num_rows($para);
												
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_style_reading_time[$k]+=$row_para['reading_time'];
													$para_font_style_test_time[$k]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_style_male[$k]++;
													else
														$para_font_style_female[$k]++;  
										        }
											}   
											//////CALCULATION FOR CREATING CHART ON FONT SIZE AND LINE HEIGHT AND WORD-SPACING FOR EACH PARAGRAPH IN legal DOCUMENT///////////
											$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."'";
											$para=mysql_query($query);
											while($row_para=mysql_fetch_array($para)){
												switch($row_para['size']){
													case (70<=$row_para['size']&&$row_para['size']<=90):
														$para_font_size_count[0]++;
														$para_font_size_reading_time[0]+=$row_para['reading_time'];
														$para_font_size_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[0]++;
														}
														else{
															$para_font_size_female[0]++;
														}
													break;
													
													case (100<=$row_para['size']&&$row_para['size']<=120):
														$para_font_size_count[1]++;
														$para_font_size_reading_time[1]+=$row_para['reading_time'];
														$para_font_size_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[1]++;
														}
														else{
															$para_font_size_female[1]++;
														}
													break;
														
													case (130<=$row_para['size']&&$row_para['size']<=150):
														$para_font_size_count[2]++;
														$para_font_size_reading_time[2]+=$row_para['reading_time'];
														$para_font_size_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[2]++;
														}
														else{
															$para_font_size_female[2]++;
														}
													break;
														
													case (160<=$row_para['size']&&$row_para['size']<=180):
														$para_font_size_count[3]++;
														$para_font_size_reading_time[3]+=$row_para['reading_time'];
														$para_font_size_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_font_size_male[3]++;
														}
														else{
															$para_font_size_female[3]++;
														}
													break;
														
													case (190<=$row_para['size']&&$row_para['size']<=210):
														$para_font_size_count[4]++;
														$para_font_size_reading_time[4]+=$row_para['reading_time'];
														$para_font_size_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[4]++;
														}
														else{
															$para_font_size_female[4]++;
														}
													break;
														
													case (220<=$row_para['size']&&$row_para['size']<=240):
														$para_font_size_count[5]++;
														$para_font_size_reading_time[5]+=$row_para['reading_time'];
														$para_font_size_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[5]++;
														}
														else{
															$para_para_font_size_female[5]++;
														}
													break;
												}
															
												///////////CALCULATION OF  LINE HEIGHT FOR EACH PARAGRAPH //////
												switch($row_para['line_height']){
													case (20<=$row_para['line_height']&&$row_para['line_height']<=24):
														$para_line_height_count[0]++;
														$para_line_height_reading_time[0]+=$row_para['reading_time'];
														$para_line_height_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[0]++;
														}
														else{
															$para_line_height_female[0]++;
														}
													break;
														
													case (25<=$row_para['line_height']&&$row_para['line_height']<=29):
														$para_line_height_count[1]++;
														$para_line_height_reading_time[1]+=$row_para['reading_time'];
														$para_line_height_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[1]++;
														}
														else{
															$para_line_height_female[1]++;
														}
														break;
													
													case (30<=$row_para['line_height']&&$row_para['line_height']<=34):
														$para_line_height_count[2]++;
														$para_line_height_reading_time[2]+=$row_para['reading_time'];
														$para_para_line_height_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[2]++;
														}
														else{
															$para_line_height_female[2]++;
														}
													break;
														
													case (35<=$row_para['line_height']&&$row_para['line_height']<=39):
														$para_line_height_count[3]++;
														$para_line_height_reading_time[3]+=$row_para['reading_time'];
														$para_line_height_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[3]++;
														}
														else{
															$para_line_height_female[3]++;
														}
													break;
														
													case (40<=$row_para['size']&&$row_para['line_height']<=44):
														$para_line_height_count[4]++;
														$para_line_height_reading_time[4]+=$row_para['reading_time'];
														$para_line_height_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[4]++;
														}
														else{
															$para_line_height_female[4]++;
														}
													break;
														
													case (45<=$row_para['line_height']&&$row_para['line_height']<=50):
														$para_line_height_count[5]++;
														$para_line_height_reading_time[5]+=$row_para['reading_time'];
														$para_line_height_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[5]++;
														}
														else{
															$para_line_height_female[5]++;
														}
													break;
														
													
												}
															
												///////////CALCULATION OF WORD SPACING FOR EACH PARAGRAPH//////
												switch($row_para['word_spacing']){
													case (0<=$row_para['word_spacing']&&$row_para['word_spacing']<=3):
														$para_word_spacing_count[0]++;
														$para_word_spacing_reading_time[0]+=$row_para['reading_time'];
														$para_word_spacing_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_word_spacing_male[0]++;
														}
														else{
															$para_word_spacing_female[0]++;
														}
													break;
													
													case (4<=$row_para['word_spacing']&&$row_para['word_spacing']<=7):
														$para_word_spacing_count[1]++;
														$para_word_spacing_reading_time[1]+=$row_para['reading_time'];
														$para_word_spacing_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[1]++;
														}
														else{
															$para_word_spacing_female[1]++;
														}
													break;
													
													case (8<=$row_para['word_spacing']&&$row_para['word_spacing']<=11):
														$para_word_spacing_count[2]++;
														$para_word_spacing_reading_time[2]+=$row_para['reading_time'];
														$para_word_spacing_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[2]++;
														}
														else{
															$para_word_spacing_female[2]++;
														}
													break;
													
													case (12<=$row_para['word_spacing']&&$row_para['word_spacing']<=15):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
													
													case (16<=$row_para['word_spacing']&&$row_para['word_spacing']<=20):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
												}
											}	
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//Nav tabs list -obj, sub and graphs
											echo "<ul class='nav nav-tabs nav-justified' role='tablist'>
												<li class='active in'><a href='#legal_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
												<li><a href='#legal_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
												<li><a href='#legal_graphs".$i."' role='tab' data-toggle='tab'>Graphs and Charts</a></li>
											</ul>";
															
											//Tab panes
											echo "<div class='tab-content'>";
												//Objectives Tab
												echo "<div class='tab-pane fade active in' id='legal_obj".$i."'>
													<table class = 'table table-hover table-bordered ques_table' id='legal_para_obj_ques".$i."'>
														<tr align = 'center'>
															<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
															<td class = 'col-lg-6'><h4><big>Questions</big></h4></td>
															<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Skipped</big></h6></td>
														</tr>";
																					
														$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
														mysql_query("SET NAMES utf8");
														$result3 = mysql_query($query3);
							
														$j = 1;
														while($row3 = mysql_fetch_array($result3)){
															echo "<tr align = 'center'>";
																//S.No.
																echo "<td>".
																	$j.
																"</td>";
																							
																//Objective Questions
																echo "<td>".
																	$row3['ques'].
																"</td>";
																
																$j++;
																							
																//Options data
																$query4 = "SELECT * FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																mysql_query("SET NAMES utf8");
																$result4 = mysql_query($query4);
																								
																$opt1_select_count = 0;
																$opt2_select_count = 0;
																$opt3_select_count = 0;
																$opt4_select_count = 0;
																$skipped_count = 0;
																							
																if($row3['opt1'] == ""){
																	$opt1_select_count = -1;
																}
																if($row3['opt2'] == ""){
																	$opt2_select_count = -1;
																}
																if($row3['opt3'] == ""){
																	$opt3_select_count = -1;
																}
																if($row3['opt4'] == ""){
																	$opt4_select_count = -1;
																}
																								
																while($row4 = mysql_fetch_array($result4)){
																	if($row4['selected_option'] == $row3['opt1'])
																		$opt1_select_count++;
																	if($row4['selected_option'] == $row3['opt2'])
																		$opt2_select_count++;
																	if($row4['selected_option'] == $row3['opt3'])
																		$opt3_select_count++;
																	if($row4['selected_option'] == $row3['opt4'])
																		$opt4_select_count++;
																	if($row4['selected_option'] == "skipped")
																		$skipped_count++;
																}
																//opt1
																echo "<td>";
																	if($opt1_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt1']."<hr/>".
																		$opt1_select_count;
																	}
																echo "</td>";
																						
																//opt2
																echo "<td>";
																	if($opt2_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt2']."<hr/>".
																		$opt2_select_count;
																	}
																echo "</td>";
																					
																//opt3
																echo "<td>";
																	if($opt3_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt3']."<hr/>".
																		$opt3_select_count;
																	}
																echo "</td>";
																						
																//opt4
																echo "<td>";
																	if($opt4_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt4']."<hr/>".
																		$opt4_select_count;
																	}
																echo "</td>";
																							
																//skipped
																echo "<td>";
																	if($skipped_count == -1){
																		echo "-";
																	}
																	else{
																		echo $skipped_count;
																	}
																echo "</td>
															</tr>";	
														}
													echo "</table>
												</div>";
																		
												//Subjectives Tab
												echo "<div class='tab-pane fade active' id='legal_sub".$i."'>";
													//querying all subjective questions of a particular para
													$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
													mysql_query("SET NAMES utf8");
													$result3 = mysql_query($query3);
																		
													$j = 1;
													while($row3 = mysql_fetch_array($result3)){
														echo "<table class = 'ques_table' id='legal_para_sub_ques".$i."' border = 'solid'>";
															//subjective question Heading
															echo "<tr align = 'center'>
																<td>
																	<h4><big>
																		Question : ".$j.
																	"</big></h4>
																</td>
															</tr>";
																				
															//subjective Questions
															echo "<tr align = 'center'>
																<td>
																	<div class='ques_body'>
																		<h3><small>".
																			$row3['ques'].
																		"</small></h3>
																	</div>
																</td>
															</tr>";
																				
															//subjective answers row having table of answers
															echo "<tr align = 'center'>
																<td>
																	<table class='table' border = 'solid'>
																		<tr align='center'>
																			<td class='col-lg-3'>User</td>
																			<td class='col-lg-9'>Answers</td>
																		</tr>";
																							
																		//querying all users and all thier answrs of a question
																		//$query4 = "SELECT tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																		$query4 = "SELECT uid, tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."' GROUP BY uid";
																		mysql_query("SET NAMES utf8");
																		$result4 = mysql_query($query4);
																							
																		while($row4 = mysql_fetch_array($result4)){
																			//$query5 = "SELECT uid FROM test_data WHERE `tid` = '".$row4['tid']."'";
																			//mysql_query("SET NAMES utf8");
																			//$result5 = mysql_query($query5);
																			//$row5 = mysql_fetch_array($result5);
																									
																			//$query6 = "SELECT * FROM main WHERE `user_id` = '".$row5['uid']."'";
																			$query6 = "SELECT * FROM main WHERE `user_id` = '".$row4['uid']."'";
																			$result6 = mysql_query($query6);
																			$row6 = mysql_fetch_array($result6);
																									
																			echo "<tr align='center'>
																				<td>".$row6['email']."<br/>{ Age - ".$row6['age'].", ";
																					if($row6['gender'] == "1"){
																						echo "M, ";
																					}
																					else{
																						echo "F, ";
																					}
																					if($row6['edu_back'] == "higher_sec"){
																						echo "Higher Secondary }";
																					}
																					else if($row6['edu_back'] == "ug"){
																						echo "Undergraduate }";
																					}
																					else if($row6['edu_back'] == "pg"){
																						echo "Postgraduate }";
																					}
																					else{
																						echo "Other }";
																					}
																				echo "</td>
																				
																				<td>";
																					$query7 = "SELECT tid FROM test_data WHERE `uid` = '".$row4['uid']."' AND `pid` = '".$row['pid']."'";
																					$result7 = mysql_query($query7);
																					while($row7 = mysql_fetch_array($result7)){
																						$query8 = "SELECT selected_option FROM test_questions_data WHERE `tid` = '".$row7['tid']."' AND `qid` = '".$row3['qid']."'";
																						$result8 = mysql_query($query8);
																						$row8 = mysql_fetch_array($result8);
																						echo $row8['selected_option']."<hr/>";
																					}
																				echo "</td>
																			</tr>";
																		}
																	echo "</table>
																</td>
															</tr>";
														
														$j++;
														echo "</table>";	
													}
												echo "</div>";
																		
												//Graphs and Charts Tab
												echo "<div class='tab-pane fade active' id='legal_graphs".$i."'>";
												//////////////////////////////////////////////////////////////////////////////////////////////////////////
													echo "<table class='table table-bordered'><tr>";
													//CREATING A CHART OF FONT STYLE FOR EACH PARAGRAPH In legal Article Type
													echo "<td>";
													if(array_sum($para_font_style_count)!=0){
														$strXML= "<graph caption='Tests given in different Font Style' subCaption='with Legal Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
														echo "<table class='table table-bordered'>";
														echo "<tr>";
															echo "<td>Font Style</td>";
															echo "<td>Male views</td>";
															echo "<td>Female views</td>";
															echo "<td>Average Reading Time</td>";
															echo "<td>Average Test Time</td>";
														echo "</tr>";
														for($t=0;$t<5;$t++){
															echo "<tr>
															<td>".
																$font_style[$t].
															"</td>
															
															<td>".
																$para_font_style_male[$t].
															"</td>
															
															<td>".
																$para_font_style_female[$t].
															"</td>";
															if($para_font_style_count[$t]!=0){
																$para_font_style_reading_time[$t]=($para_font_style_reading_time[$t]/ $para_font_style_count[$t]);
																$para_font_style_test_time[$t]=($para_font_style_test_time[$t]/$para_font_style_count[$t]);
																echo "<td>".
																	$para_font_style_reading_time[$t].
																"</td>";
																
																echo "<td>".
																	$para_font_style_test_time[$t].
																"</td>";
															}
															else{
																echo "<td>-</td>";
																echo "<td>-</td>";
															}
															$strXML .= "<set name='" . $font_style[$t] . "' value='" . $para_font_style_count[$t] . "' />";
															echo "</tr>";
														}
													$strXML .= "</graph>";
													echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "legal_font_style_para_chart".$i, 500, 400);
													echo "</table>";
												}
												echo "</td>";
												echo "<td>";			
												
												//CREATING A CHART OF FONT SIZE FOR EACH PARAGRAPH In legal Article Type
												if(array_sum($para_font_size_count)!=0){
													$strXML= "<graph caption='Tests given in different Font Size' subCaption='with legal Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Font Size Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$font_size[$t].
														"</td>
														
														<td>".
															$para_font_size_male[$t].
														"</td>
														
														<td>".
															$para_font_size_female[$t].
														"</td>";
														if($para_font_size_count[$t]!=0){
															$para_font_size_reading_time[$t]=($para_font_size_reading_time[$t]/ $para_font_size_count[$t]);
															$para_font_size_test_time[$t]=($para_font_size_test_time[$t]/$para_font_size_count[$t]);
															echo "<td>".
																$para_font_size_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_font_size_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $font_size[$t] . "' value='" . $para_font_size_count[$t] . "' />";
														echo "</tr>";				
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "legal_font_size_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr>";
											
											echo "<tr>";
											echo "<td>";
											
											//CREATING A CHART OF LINE HEIG FOR EACH PARAGRAPH
											if(array_sum($para_line_height_count)!=0){
												$strXML= "<graph caption='Tests given in different line height' subCaption='with legal Article type'pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Line Height Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$line_height[$t].
														"</td>
														
														<td>".
															$para_line_height_male[$t].
														"</td>
														
														<td>".
															$para_line_height_female[$t].
														"</td>";
														if($para_line_height_count[$t]!=0){
															$para_line_height_reading_time[$t]=($para_line_height_reading_time[$t]/ $para_line_height_count[$t]);
															$para_line_height_test_time[$t]=($para_line_height_test_time[$t]/$para_line_height_count[$t]);
															echo "<td>".
																$para_line_height_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_line_height_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $line_height[$t] . "' value='" . $para_line_height_count[$t] . "' />";
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "legal_line_height_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "<td>";
											
											//CREATING A CHART OF WORD SPACING FOR EACH PARAGRAPH
											if(array_sum($para_word_spacing_count)!=0){
												$strXML= "<graph caption='Tests given in different line Word Spacing' subCaption='with legal Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
												echo "<table class='table table-bordered'>";
												echo "<tr>";
													echo "<td>Word Spacing Ranges</td>";
													echo "<td>Male views</td>";
													echo "<td>Female views</td>";
													echo "<td>Average Reading Time</td>";
													echo "<td>Average Test Time</td>";
												echo "</tr>";				
												for($t=0;$t<5;$t++){
													echo "<tr>
													<td>".
														$word_spacing[$t].
													"</td>
													
													<td>".
														$word_spacing_male[$t].
													"</td>
													
													<td>".
														$word_spacing_female[$t].
													"</td>";
													
													if($word_spacing_count[$t]!=0){
														$para_word_spacing_reading_time[$t]=($para_word_spacing_reading_time[$t]/ $para_word_spacing_count[$t]);
														$para_word_spacing_test_time[$t]=($para_word_spacing_test_time[$t]/$para_word_spacing_count[$t]);
														echo "<td>".
															$para_word_spacing_reading_time[$t].
														"</td>";
														
														echo "<td>".
															$para_word_spacing_test_time[$t].
														"</td>";
													}
													else{
														echo "<td>-</td>";
														echo "<td>-</td>";
													}
													$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $para_word_spacing_count[$t] . "' />";
												}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "legal_word_spacing_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr></table>";
											echo "</div>
												
											</div>";//end of div containing all 3 tabs of this para
										echo "</div>
									</div>
								</td>
							</tr>";
							$i++;
						}
					echo "</table>";
					?>
					<!--CHARTS|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					<?php
					//Accordian for CHARTS FOR Legal Documents|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					echo '<button id=legal_accordian" class="article_accordian_class btn btn-warning btn-lg btn-block" data-toggle="collapse" data-target="#legal_graphs">
						Charts for Legal Documents
					</button>';
					echo "<table id='legal_graphs' class='collapse in table table-bordered'><tr>";
					/////////////////////CHART FOR FONT STYLE in legal Article type///////////
					echo "<td>";
					if(array_sum($font_style_count)!=0){
						$strXML= "<graph caption='Tests given in different Font styles' subcaption='with legal Article Type' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Style</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$font_style[$t].
							"</td>
							
							<td>".
								$font_style_male[$t].
							"</td>
							
							<td>".
								$font_style_female[$t].
							"</td>";
							if($font_style_count[$t]!=0){
								$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
								$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
								echo "<td>".
									$font_style_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_style_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "legal_font_style_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR FONT SIZE in legal Article type///////////
					if(array_sum($font_size_count)!=0){
						$strXML= "<graph caption='Tests given in different Font sizes' subcaption='with legal Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Size Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$font_size[$t].
							"</td>
							
							<td>".
								$font_size_male[$t].
							"</td>
							
							<td>".
								$font_size_female[$t].
							"</td>";
							if($font_size_count[$t]!=0){
								$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
								$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
								echo "<td>".
									$font_size_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_size_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "legal_size_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td>";
					
					/////////////////////CHART FOR Line Height in legal Article type///////////
					if(array_sum($line_height_count)!=0){
						$strXML= "<graph caption='Tests given in different Line heights' subcaption='with legal Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Line Height Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$line_height[$t].
							"</td>
							
							<td>".
								$line_height_male[$t].
							"</td>
							
							<td>".
								$line_height_female[$t].
							"</td>";
							if($line_height_count[$t]!=0){
								$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
								$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
								echo "<td>".
									$line_height_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$line_height_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "legal_line_height_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR Word Spacing in legal Article type///////////
					if(array_sum($word_spacing_count)!=0){
						$strXML= "<graph caption='Tests given in different word spacing' subcaption='with legal Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Word Spacing Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$word_spacing[$t].
							"</td>
							
							<td>".
								$word_spacing_male[$t].
							"</td>
							
							<td>".
								$word_spacing_female[$t].
							"</td>";
							if($word_spacing_count[$t]!=0){
								$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
								$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
								echo "<td>".
									$word_spacing_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$word_spacing_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
						}
						$strXML .= "</graph>";
	
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "legal_word_spacing_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr></table>";
					?>
				</div><!--End of legl document Article-->
				
				<!--Research papers Tab-->
				<div class="tab-pane fade" id="research">
					<?php
					echo "<div align=center>
						<h2><small>
							Total tests done yet - ".$view_count['Research Papers']."<br/>{ M - ". $male_view_count['Research Papers'].", F - ". $female_view_count['Research Papers']." }
						</small></h2>
					</div>";
						
					//font style counts
					$font_style_count = array('0','0','0','0','0');
					$font_style_male = array('0','0','0','0','0');
					$font_style_female = array('0','0','0','0','0');
					$font_style_reading_time = array('0','0','0','0','0');
					$font_style_test_time = array('0','0','0','0','0');
					
					
					//font size counts
					$font_size_count = array('0', '0', '0', '0','0','0');
					$font_size_male = array('0','0','0','0','0','0');
					$font_size_female = array('0','0','0','0','0','0');
					$font_size_reading_time = array('0','0','0','0','0','0');
					$font_size_test_time = array('0','0','0','0','0','0');
					
					
					//Line Height counts
					$line_height_count = array('0', '0', '0', '0','0','0','0','0');
					$line_height_male = array('0','0','0','0','0','0','0','0');
					$line_height_female = array('0','0','0','0','0','0','0','0');
					$line_height_reading_time = array('0','0','0','0','0','0');
					$line_height_test_time = array('0','0','0','0','0','0');
					
					
					//Word Spacing counts
					$word_spacing_count = array('0', '0', '0', '0','0');
					$word_spacing_male = array('0','0','0','0','0');
					$word_spacing_female = array('0','0','0','0','0');
					$word_spacing_reading_time = array('0','0','0','0','0');
					$word_spacing_test_time = array('0','0','0','0','0');
						
					$i=1;
					$query="SELECT * FROM paragraphs WHERE `article_type`='Research Papers'";
					mysql_query("SET NAMES utf8");
					$result=mysql_query($query);
					
					while($row=mysql_fetch_array($result)){
						$para_font_style_count = array('0','0','0','0','0');
						$para_font_style_male = array('0','0','0','0','0');
						$para_font_style_female = array('0','0','0','0','0');
						$para_font_style_reading_time = array('0','0','0','0','0');
						$para_font_style_test_time = array('0','0','0','0','0');
										
						$para_font_size_count = array('0', '0', '0', '0','0','0');
						$para_font_size_male = array('0','0','0','0','0','0');
						$para_font_size_female = array('0','0','0','0','0','0');
						$para_font_size_reading_time = array('0','0','0','0','0','0');
						$para_font_size_test_time = array('0','0','0','0','0','0');
										
						$para_line_height_count = array('0', '0', '0', '0','0','0');
						$para_line_height_male = array('0','0','0','0','0','0');
						$para_line_height_female = array('0','0','0','0','0','0');
						$para_line_height_reading_time = array('0','0','0','0','0','0');
						$para_line_height_test_time = array('0','0','0','0','0','0');
										
						$para_word_spacing_count = array('0', '0', '0', '0','0');
						$para_word_spacing_male = array('0','0','0','0','0');
						$para_word_spacing_female = array('0','0','0','0','0');
						$para_word_spacing_reading_time = array('0','0','0','0','0');
						$para_word_spacing_test_time = array('0','0','0','0','0');
										
						$var1=$row['pid'];
						$query1="SELECT * FROM test_data Where `pid`='$var1'"; 
						$result1=mysql_query($query1);
						$totalresearchviewers=  mysql_num_rows($result1); 
						$researchmale=0;
						$researchfemale=0;
									
						while($row1=mysql_fetch_array($result1)){
							$var2=$row1['uid'];
							$query2="SELECT * FROM main Where `user_id`='$var2'"; 
							$result2=mysql_query($query2);
							$row2=mysql_fetch_array($result2);
									
							if($row2['gender']=='1'){
								$researchmale++;
							}
							else{
								$researchfemale++;
							}
							///////////CALCULATION OF  research DOCUMENT FONT STYLE//////
							if($row1['font']=='Arial'){
								$font_style_count[0]++;
								$font_style_reading_time[0]+=$row1['reading_time'];
								$font_style_test_time[0]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[0]++;
								}
								else{
									$font_style_female[0]++;
								}
							}
                            if($row1['font']=='Calibri'){
								$font_style_count[1]++;
								$font_style_reading_time[1]+=$row1['reading_time'];
								$font_style_test_time[1]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[1]++;
								}
								else{
									$font_style_female[1]++;
								}
							}		
							if($row1['font']=='Comic Sans MS'){
								$font_style_count[2]++;
								$font_style_reading_time[2]+=$row1['reading_time'];
								$font_style_test_time[2]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[2]++;
								}
								else{
									$font_style_female[2]++;
								}
							}		  
							if($row1['font']=='Times New Roman'){
								$font_style_count[3]++;
								$font_style_reading_time[3]+=$row1['reading_time'];
								$font_style_test_time[3]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[3]++;
								}
								else{
									$font_style_female[3]++;
								}
							}		 
							if($row1['font']=='Lucida Sans'){
								$font_style_count[4]++;	
								$font_style_reading_time[4]+=$row1['reading_time'];
								$font_style_test_time[4]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[4]++;
								}
								else{
									$font_style_female[4]++;
								}
 							}
							///////////END OF CALCULATION OF  research DOCUMENT FONT STYLE//////
									
							///////////CALCULATION OF  research DOCUMENT FONT SIZE//////
							switch($row1['size']){
								case (70<=$row1['size']&&$row1['size']<=90):
									$font_size_count[0]++;
									$font_size_reading_time[0]+=$row1['reading_time'];
									$font_size_test_time[0]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[0]++;
									}
									else{
										$font_size_female[0]++;
									}
								break;
								
								case (100<=$row1['size']&&$row1['size']<=120):
									$font_size_count[1]++;
									$font_size_reading_time[1]+=$row1['reading_time'];
									$font_size_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[1]++;
									}
									else{
										$font_size_female[1]++;
									}
								break;
									
								case (130<=$row1['size']&&$row1['size']<=150):
									$font_size_count[2]++;
									$font_size_reading_time[2]+=$row1['reading_time'];
									$font_size_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[2]++;
									}
									else{
										$font_size_female[2]++;
									}
								break;
									
								case (160<=$row1['size']&&$row1['size']<=180):
									$font_size_count[3]++;
									$font_size_reading_time[3]+=$row1['reading_time'];
									$font_size_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[3]++;
									}
									else{
										$font_size_female[3]++;
									}
								break;
										
								case (190<=$row1['size']&&$row1['size']<=210):
									$font_size_count[4]++;
									$font_size_reading_time[4]+=$row1['reading_time'];
									$font_size_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[4]++;
									}
									else{
										$font_size_female[4]++;
									}
								break;
									
								case (220<=$row1['size']&&$row1['size']<=240):
									$font_size_count[5]++;
									$font_size_reading_time[5]+=$row1['reading_time'];
									$font_size_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[5]++;
									}
									else{
										$font_size_female[5]++;
									}
								break;
							}
							///////////END OF CALCULATION OF  research DOCUMENT FONT line_height//////
							
							///////////CALCULATION OF  research DOCUMENT LINE HEIGHT //////
							switch($row1['line_height']){
								case (20<=$row1['line_height']&&$row1['line_height']<=24):
									$line_height_count[0]++;
									$line_height_reading_time[0]+=$row1['reading_time'];
									$line_height_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[0]++;
									}
									else{
										$line_height_female[0]++;
									}
								break;
								
								case (25<=$row1['line_height']&&$row1['line_height']<=29):
									$line_height_count[1]++;
									$line_height_reading_time[1]+=$row1['reading_time'];
									$line_height_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[1]++;
									}
									else{
										$line_height_female[1]++;
									}
								break;
								
								case (30<=$row1['line_height']&&$row1['line_height']<=34):
									$line_height_count[2]++;
									$line_height_reading_time[2]+=$row1['reading_time'];
									$line_height_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[2]++;
									}
									else{
										$line_height_female[2]++;
									}
								break;
						
								case (35<=$row1['line_height']&&$row1['line_height']<=39):
									$line_height_count[3]++;
									$line_height_reading_time[3]+=$row1['reading_time'];
									$line_height_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[3]++;
									}
									else{
										$line_height_female[3]++;
									}
								break;
								
								case (40<=$row1['size']&&$row1['line_height']<=44):
									$font_line_height_count[4]++;
									$line_height_reading_time[4]+=$row1['reading_time'];
									$line_height_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[4]++;
									}
									else{
										$line_height_female[4]++;
									}
								break;
									
								case (45<=$row1['line_height']&&$row1['line_height']<=50):
									$line_height_count[5]++;
									$line_height_reading_time[5]+=$row1['reading_time'];
									$line_height_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[5]++;
									}
									else{
										$line_height_female[5]++;
									}
								break;
								
									
							}
							///////////END OF CALCULATION OF  research DOCUMENT LINE HEIGHT //////
										 
							///////////CALCULATION OF research DOCUMENT WORD SPACING //////
							switch($row1['word_spacing']){
								case (0<=$row1['word_spacing']&&$row1['word_spacing']<=3):
									$word_spacing_count[0]++;
									$word_spacing_reading_time[0]+=$row1['reading_time'];
									$word_spacing_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$word_spacing_male[0]++;
									}
									else{
										$word_spacing_female[0]++;
									}
								break;

								case (4<=$row1['word_spacing']&&$row1['word_spacing']<=7):
									$word_spacing_count[1]++;
									$word_spacing_reading_time[1]+=$row1['reading_time'];
									$word_spacing_test_time[1]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[1]++;
									}
									else{
										$word_spacing_female[1]++;
									}
								break;
								
								case (8<=$row1['word_spacing']&&$row1['word_spacing']<=11):
									$word_spacing_count[2]++;
									$word_spacing_reading_time[2]+=$row1['reading_time'];
									$word_spacing_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[2]++;
									}
									else{
										$word_spacing_female[2]++;
									}
								break;
								
								case (12<=$row1['word_spacing']&&$row1['word_spacing']<=15):
									$word_spacing_count[3]++;
									$word_spacing_reading_time[3]+=$row1['reading_time'];
									$word_spacing_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[3]++;
									}
									else{
										$word_spacing_female[3]++;
									}
								break;
								
								case (16<=$row1['word_spacing']&&$row1['word_spacing']<=20):
									$word_spacing_count[4]++;
									$word_spacing_reading_time[4]+=$row1['reading_time'];
									$word_spacing_test_time[4]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[4]++;
									}
									else{
										$word_spacing_female[4]++;
									}
								break;
							}
							///////////END OF CALCULATION OF research DOCUMENT WORD SPACING //////
						}
						//paragraphs table
						echo "<table class='table table-bordered'>";
							echo "<tr>
								<td>";
								//para_panel
									echo "<div class='para_panel' id='research_para_panel".$i."'>
										<button class='btn-primary btn-block btn-lg' data-toggle='collapse' data-target='#research_para".$i."' data-parent='#research_para_panel".$i."'>
											<div class='para_info1'>
												Paragraph : ".$i."
											</div>
											<div class='para_info'>".
													$totalresearchviewers."
													{ M - ".$researchmale.",  F - ".$researchfemale." }
											</div>
										</button>
											
										<div id='research_para".$i."' class='panel-collapse panel-body collapse'>".
											$row['para'];
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//////////////CALCULATION FOR CREATING CHART ON FONT SYTLE FOR EACH PARAGRAPH IN research DOCUMENT///////////
											for($k=0;$k<5;$k++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `font`='".$font_style[$k]."'";
												$para=mysql_query($query);
												$para_font_style_count[$k]=mysql_num_rows($para);
												
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_style_reading_time[$k]+=$row_para['reading_time'];
													$para_font_style_test_time[$k]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_style_male[$k]++;
													else
														$para_font_style_female[$k]++;  
										        }
											}   
											//////CALCULATION FOR CREATING CHART ON FONT SIZE AND LINE HEIGHT AND WORD-SPACING FOR EACH PARAGRAPH IN research DOCUMENT///////////
											$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."'";
											$para=mysql_query($query);
											while($row_para=mysql_fetch_array($para)){
												switch($row_para['size']){
													case (70<=$row_para['size']&&$row_para['size']<=90):
														$para_font_size_count[0]++;
														$para_font_size_reading_time[0]+=$row_para['reading_time'];
														$para_font_size_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[0]++;
														}
														else{
															$para_font_size_female[0]++;
														}
													break;
													
													case (100<=$row_para['size']&&$row_para['size']<=120):
														$para_font_size_count[1]++;
														$para_font_size_reading_time[1]+=$row_para['reading_time'];
														$para_font_size_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[1]++;
														}
														else{
															$para_font_size_female[1]++;
														}
													break;
														
													case (130<=$row_para['size']&&$row_para['size']<=150):
														$para_font_size_count[2]++;
														$para_font_size_reading_time[2]+=$row_para['reading_time'];
														$para_font_size_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[2]++;
														}
														else{
															$para_font_size_female[2]++;
														}
													break;
														
													case (160<=$row_para['size']&&$row_para['size']<=180):
														$para_font_size_count[3]++;
														$para_font_size_reading_time[3]+=$row_para['reading_time'];
														$para_font_size_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_font_size_male[3]++;
														}
														else{
															$para_font_size_female[3]++;
														}
													break;
														
													case (190<=$row_para['size']&&$row_para['size']<=210):
														$para_font_size_count[4]++;
														$para_font_size_reading_time[4]+=$row_para['reading_time'];
														$para_font_size_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[4]++;
														}
														else{
															$para_font_size_female[4]++;
														}
													break;
														
													case (220<=$row_para['size']&&$row_para['size']<=240):
														$para_font_size_count[5]++;
														$para_font_size_reading_time[5]+=$row_para['reading_time'];
														$para_font_size_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[5]++;
														}
														else{
															$para_para_font_size_female[5]++;
														}
													break;
												}
															
												///////////CALCULATION OF  LINE HEIGHT FOR EACH PARAGRAPH //////
												switch($row_para['line_height']){
													case (20<=$row_para['line_height']&&$row_para['line_height']<=24):
														$para_line_height_count[0]++;
														$para_line_height_reading_time[0]+=$row_para['reading_time'];
														$para_line_height_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[0]++;
														}
														else{
															$para_line_height_female[0]++;
														}
													break;
														
													case (25<=$row_para['line_height']&&$row_para['line_height']<=29):
														$para_line_height_count[1]++;
														$para_line_height_reading_time[1]+=$row_para['reading_time'];
														$para_line_height_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[1]++;
														}
														else{
															$para_line_height_female[1]++;
														}
														break;
													
													case (30<=$row_para['line_height']&&$row_para['line_height']<=34):
														$para_line_height_count[2]++;
														$para_line_height_reading_time[2]+=$row_para['reading_time'];
														$para_para_line_height_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[2]++;
														}
														else{
															$para_line_height_female[2]++;
														}
													break;
														
													case (35<=$row_para['line_height']&&$row_para['line_height']<=39):
														$para_line_height_count[3]++;
														$para_line_height_reading_time[3]+=$row_para['reading_time'];
														$para_line_height_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[3]++;
														}
														else{
															$para_line_height_female[3]++;
														}
													break;
														
													case (40<=$row_para['size']&&$row_para['line_height']<=44):
														$para_line_height_count[4]++;
														$para_line_height_reading_time[4]+=$row_para['reading_time'];
														$para_line_height_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[4]++;
														}
														else{
															$para_line_height_female[4]++;
														}
													break;
														
													case (45<=$row_para['line_height']&&$row_para['line_height']<=50):
														$para_line_height_count[5]++;
														$para_line_height_reading_time[5]+=$row_para['reading_time'];
														$para_line_height_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[5]++;
														}
														else{
															$para_line_height_female[5]++;
														}
													break;
														
													
												}
															
												///////////CALCULATION OF WORD SPACING FOR EACH PARAGRAPH//////
												switch($row_para['word_spacing']){
													case (0<=$row_para['word_spacing']&&$row_para['word_spacing']<=3):
														$para_word_spacing_count[0]++;
														$para_word_spacing_reading_time[0]+=$row_para['reading_time'];
														$para_word_spacing_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_word_spacing_male[0]++;
														}
														else{
															$para_word_spacing_female[0]++;
														}
													break;
													
													case (4<=$row_para['word_spacing']&&$row_para['word_spacing']<=7):
														$para_word_spacing_count[1]++;
														$para_word_spacing_reading_time[1]+=$row_para['reading_time'];
														$para_word_spacing_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[1]++;
														}
														else{
															$para_word_spacing_female[1]++;
														}
													break;
													
													case (8<=$row_para['word_spacing']&&$row_para['word_spacing']<=11):
														$para_word_spacing_count[2]++;
														$para_word_spacing_reading_time[2]+=$row_para['reading_time'];
														$para_word_spacing_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[2]++;
														}
														else{
															$para_word_spacing_female[2]++;
														}
													break;
													
													case (12<=$row_para['word_spacing']&&$row_para['word_spacing']<=15):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
													
													case (16<=$row_para['word_spacing']&&$row_para['word_spacing']<=20):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
												}
											}	
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//Nav tabs list -obj, sub and graphs
											echo "<ul class='nav nav-tabs nav-justified' role='tablist'>
												<li class='active in'><a href='#research_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
												<li><a href='#research_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
												<li><a href='#research_graphs".$i."' role='tab' data-toggle='tab'>Graphs and Charts</a></li>
											</ul>";
															
											//Tab panes
											echo "<div class='tab-content'>";
												//Objectives Tab
												echo "<div class='tab-pane fade active in' id='research_obj".$i."'>
													<table class = 'table table-hover table-bordered ques_table' id='research_para_obj_ques".$i."'>
														<tr align = 'center'>
															<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
															<td class = 'col-lg-6'><h4><big>Questions</big></h4></td>
															<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Skipped</big></h6></td>
														</tr>";
																					
														$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
														mysql_query("SET NAMES utf8");
														$result3 = mysql_query($query3);
							
														$j = 1;
														while($row3 = mysql_fetch_array($result3)){
															echo "<tr align = 'center'>";
																//S.No.
																echo "<td>".
																	$j.
																"</td>";
																							
																//Objective Questions
																echo "<td>".
																	$row3['ques'].
																"</td>";
																
																$j++;
																							
																//Options data
																$query4 = "SELECT * FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																mysql_query("SET NAMES utf8");
																$result4 = mysql_query($query4);
																								
																$opt1_select_count = 0;
																$opt2_select_count = 0;
																$opt3_select_count = 0;
																$opt4_select_count = 0;
																$skipped_count = 0;
																							
																if($row3['opt1'] == ""){
																	$opt1_select_count = -1;
																}
																if($row3['opt2'] == ""){
																	$opt2_select_count = -1;
																}
																if($row3['opt3'] == ""){
																	$opt3_select_count = -1;
																}
																if($row3['opt4'] == ""){
																	$opt4_select_count = -1;
																}
																								
																while($row4 = mysql_fetch_array($result4)){
																	if($row4['selected_option'] == $row3['opt1'])
																		$opt1_select_count++;
																	if($row4['selected_option'] == $row3['opt2'])
																		$opt2_select_count++;
																	if($row4['selected_option'] == $row3['opt3'])
																		$opt3_select_count++;
																	if($row4['selected_option'] == $row3['opt4'])
																		$opt4_select_count++;
																	if($row4['selected_option'] == "skipped")
																		$skipped_count++;
																}
																//opt1
																echo "<td>";
																	if($opt1_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt1']."<hr/>".
																		$opt1_select_count;
																	}
																echo "</td>";
																						
																//opt2
																echo "<td>";
																	if($opt2_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt2']."<hr/>".
																		$opt2_select_count;
																	}
																echo "</td>";
																					
																//opt3
																echo "<td>";
																	if($opt3_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt3']."<hr/>".
																		$opt3_select_count;
																	}
																echo "</td>";
																						
																//opt4
																echo "<td>";
																	if($opt4_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt4']."<hr/>".
																		$opt4_select_count;
																	}
																echo "</td>";
																							
																//skipped
																echo "<td>";
																	if($skipped_count == -1){
																		echo "-";
																	}
																	else{
																		echo $skipped_count;
																	}
																echo "</td>
															</tr>";	
														}
													echo "</table>
												</div>";
																		
												//Subjectives Tab
												echo "<div class='tab-pane fade active' id='research_sub".$i."'>";
													//querying all subjective questions of a particular para
													$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
													mysql_query("SET NAMES utf8");
													$result3 = mysql_query($query3);
																		
													$j = 1;
													while($row3 = mysql_fetch_array($result3)){
														echo "<table class = 'ques_table' id='research_para_sub_ques".$i."' border = 'solid'>";
															//subjective question Heading
															echo "<tr align = 'center'>
																<td>
																	<h4><big>
																		Question : ".$j.
																	"</big></h4>
																</td>
															</tr>";
																				
															//subjective Questions
															echo "<tr align = 'center'>
																<td>
																	<div class='ques_body'>
																		<h3><small>".
																			$row3['ques'].
																		"</small></h3>
																	</div>
																</td>
															</tr>";
																				
															//subjective answers row having table of answers
															echo "<tr align = 'center'>
																<td>
																	<table class='table' border = 'solid'>
																		<tr align='center'>
																			<td class='col-lg-3'>User</td>
																			<td class='col-lg-9'>Answers</td>
																		</tr>";
																							
																		//querying all users and all thier answrs of a question
																		//$query4 = "SELECT tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																		$query4 = "SELECT uid, tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."' GROUP BY uid";
																		mysql_query("SET NAMES utf8");
																		$result4 = mysql_query($query4);
																							
																		while($row4 = mysql_fetch_array($result4)){
																			//$query5 = "SELECT uid FROM test_data WHERE `tid` = '".$row4['tid']."'";
																			//mysql_query("SET NAMES utf8");
																			//$result5 = mysql_query($query5);
																			//$row5 = mysql_fetch_array($result5);
																									
																			//$query6 = "SELECT * FROM main WHERE `user_id` = '".$row5['uid']."'";
																			$query6 = "SELECT * FROM main WHERE `user_id` = '".$row4['uid']."'";
																			$result6 = mysql_query($query6);
																			$row6 = mysql_fetch_array($result6);
																									
																			echo "<tr align='center'>
																				<td>".$row6['email']."<br/>{ Age - ".$row6['age'].", ";
																					if($row6['gender'] == "1"){
																						echo "M, ";
																					}
																					else{
																						echo "F, ";
																					}
																					if($row6['edu_back'] == "higher_sec"){
																						echo "Higher Secondary }";
																					}
																					else if($row6['edu_back'] == "ug"){
																						echo "Undergraduate }";
																					}
																					else if($row6['edu_back'] == "pg"){
																						echo "Postgraduate }";
																					}
																					else{
																						echo "Other }";
																					}
																				echo "</td>
																				
																				<td>";
																					$query7 = "SELECT tid FROM test_data WHERE `uid` = '".$row4['uid']."' AND `pid` = '".$row['pid']."'";
																					$result7 = mysql_query($query7);
																					while($row7 = mysql_fetch_array($result7)){
																						$query8 = "SELECT selected_option FROM test_questions_data WHERE `tid` = '".$row7['tid']."' AND `qid` = '".$row3['qid']."'";
																						$result8 = mysql_query($query8);
																						$row8 = mysql_fetch_array($result8);
																						echo $row8['selected_option']."<hr/>";
																					}
																				echo "</td>
																			</tr>";
																		}
																	echo "</table>
																</td>
															</tr>";
														
														$j++;
														echo "</table>";	
													}
												echo "</div>";
																		
												//Graphs and Charts Tab
												echo "<div class='tab-pane fade active' id='research_graphs".$i."'>";
												//////////////////////////////////////////////////////////////////////////////////////////////////////////
													echo "<table class='table table-bordered'><tr>";
													//CREATING A CHART OF FONT STYLE FOR EACH PARAGRAPH In research Article Type
													echo "<td>";
													if(array_sum($para_font_style_count)!=0){
														$strXML= "<graph caption='Tests given in different Font Style' subCaption='with Legal Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
														echo "<table class='table table-bordered'>";
														echo "<tr>";
															echo "<td>Font Style</td>";
															echo "<td>Male views</td>";
															echo "<td>Female views</td>";
															echo "<td>Average Reading Time</td>";
															echo "<td>Average Test Time</td>";
														echo "</tr>";
														for($t=0;$t<5;$t++){
															echo "<tr>
															<td>".
																$font_style[$t].
															"</td>
															
															<td>".
																$para_font_style_male[$t].
															"</td>
															
															<td>".
																$para_font_style_female[$t].
															"</td>";
															if($para_font_style_count[$t]!=0){
																$para_font_style_reading_time[$t]=($para_font_style_reading_time[$t]/ $para_font_style_count[$t]);
																$para_font_style_test_time[$t]=($para_font_style_test_time[$t]/$para_font_style_count[$t]);
																echo "<td>".
																	$para_font_style_reading_time[$t].
																"</td>";
																
																echo "<td>".
																	$para_font_style_test_time[$t].
																"</td>";
															}
															else{
																echo "<td>-</td>";
																echo "<td>-</td>";
															}
															$strXML .= "<set name='" . $font_style[$t] . "' value='" . $para_font_style_count[$t] . "' />";
															echo "</tr>";
														}
													$strXML .= "</graph>";
													echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "research_font_style_para_chart".$i, 500, 400);
													echo "</table>";
												}
												echo "</td>";
												echo "<td>";			
												
												//CREATING A CHART OF FONT SIZE FOR EACH PARAGRAPH In research Article Type
												if(array_sum($para_font_size_count)!=0){
													$strXML= "<graph caption='Tests given in different Font Size' subCaption='with research Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Font Size Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$font_size[$t].
														"</td>
														
														<td>".
															$para_font_size_male[$t].
														"</td>
														
														<td>".
															$para_font_size_female[$t].
														"</td>";
														if($para_font_size_count[$t]!=0){
															$para_font_size_reading_time[$t]=($para_font_size_reading_time[$t]/ $para_font_size_count[$t]);
															$para_font_size_test_time[$t]=($para_font_size_test_time[$t]/$para_font_size_count[$t]);
															echo "<td>".
																$para_font_size_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_font_size_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $font_size[$t] . "' value='" . $para_font_size_count[$t] . "' />";
														echo "</tr>";				
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "research_font_size_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr>";
											
											echo "<tr>";
											echo "<td>";
											
											//CREATING A CHART OF LINE HEIG FOR EACH PARAGRAPH
											if(array_sum($para_line_height_count)!=0){
												$strXML= "<graph caption='Tests given in different line height' subCaption='with research Article type'pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Line Height Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$line_height[$t].
														"</td>
														
														<td>".
															$para_line_height_male[$t].
														"</td>
														
														<td>".
															$para_line_height_female[$t].
														"</td>";
														if($para_line_height_count[$t]!=0){
															$para_line_height_reading_time[$t]=($para_line_height_reading_time[$t]/ $para_line_height_count[$t]);
															$para_line_height_test_time[$t]=($para_line_height_test_time[$t]/$para_line_height_count[$t]);
															echo "<td>".
																$para_line_height_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_line_height_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $line_height[$t] . "' value='" . $para_line_height_count[$t] . "' />";
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "research_line_height_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "<td>";
											
											//CREATING A CHART OF WORD SPACING FOR EACH PARAGRAPH
											if(array_sum($para_word_spacing_count)!=0){
												$strXML= "<graph caption='Tests given in different line Word Spacing' subCaption='with research Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
												echo "<table class='table table-bordered'>";
												echo "<tr>";
													echo "<td>Word Spacing Ranges</td>";
													echo "<td>Male views</td>";
													echo "<td>Female views</td>";
													echo "<td>Average Reading Time</td>";
													echo "<td>Average Test Time</td>";
												echo "</tr>";				
												for($t=0;$t<5;$t++){
													echo "<tr>
													<td>".
														$word_spacing[$t].
													"</td>
													
													<td>".
														$word_spacing_male[$t].
													"</td>
													
													<td>".
														$word_spacing_female[$t].
													"</td>";
													
													if($word_spacing_count[$t]!=0){
														$para_word_spacing_reading_time[$t]=($para_word_spacing_reading_time[$t]/ $para_word_spacing_count[$t]);
														$para_word_spacing_test_time[$t]=($para_word_spacing_test_time[$t]/$para_word_spacing_count[$t]);
														echo "<td>".
															$para_word_spacing_reading_time[$t].
														"</td>";
														
														echo "<td>".
															$para_word_spacing_test_time[$t].
														"</td>";
													}
													else{
														echo "<td>-</td>";
														echo "<td>-</td>";
													}
													$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $para_word_spacing_count[$t] . "' />";
												}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "research_word_spacing_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr></table>";
											echo "</div>
												
											</div>";//end of div containing all 3 tabs of this para
										echo "</div>
									</div>
								</td>
							</tr>";
							$i++;
						}
					echo "</table>";
					?>
					<!--CHARTS|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					<?php
					//Accordian for CHARTS FOR Research Article Types|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					echo '<button id=research_accordian" class="article_accordian_class btn btn-warning btn-lg btn-block" data-toggle="collapse" data-target="#research_graphs">
						Charts for Research Articles
					</button>';
					echo "<table id='research_graphs' class='collapse in table table-bordered'><tr>";
					/////////////////////CHART FOR FONT STYLE in research Article type///////////
					echo "<td>";
					if(array_sum($font_style_count)!=0){
						$strXML= "<graph caption='Tests given in different Font styles' subcaption='with research Article Type' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Style</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$font_style[$t].
							"</td>
							
							<td>".
								$font_style_male[$t].
							"</td>
							
							<td>".
								$font_style_female[$t].
							"</td>";
							if($font_style_count[$t]!=0){
								$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
								$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
								echo "<td>".
									$font_style_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_style_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "research_font_style_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR FONT SIZE in research Article type///////////
					if(array_sum($font_size_count)!=0){
						$strXML= "<graph caption='Tests given in different Font sizes' subcaption='with research Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Size Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$font_size[$t].
							"</td>
							
							<td>".
								$font_size_male[$t].
							"</td>
							
							<td>".
								$font_size_female[$t].
							"</td>";
							if($font_size_count[$t]!=0){
								$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
								$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
								echo "<td>".
									$font_size_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_size_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "research_size_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td>";
					
					/////////////////////CHART FOR Line Height in research Article type///////////
					if(array_sum($line_height_count)!=0){
						$strXML= "<graph caption='Tests given in different Line heights' subcaption='with research Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Line Height Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$line_height[$t].
							"</td>
							
							<td>".
								$line_height_male[$t].
							"</td>
							
							<td>".
								$line_height_female[$t].
							"</td>";
							if($line_height_count[$t]!=0){
								$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
								$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
								echo "<td>".
									$line_height_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$line_height_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "research_line_height_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR Word Spacing in research Article type///////////
					if(array_sum($word_spacing_count)!=0){
						$strXML= "<graph caption='Tests given in different word spacing' subcaption='with research Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Word Spacing Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$word_spacing[$t].
							"</td>
							
							<td>".
								$word_spacing_male[$t].
							"</td>
							
							<td>".
								$word_spacing_female[$t].
							"</td>";
							if($word_spacing_count[$t]!=0){
								$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
								$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
								echo "<td>".
									$word_spacing_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$word_spacing_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
						}
						$strXML .= "</graph>";
	
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "research_word_spacing_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr></table>";
					?>
				</div><!--End of research paper Article-->
				
				<!--Wikipedia Page papers Tab-->
				<div class="tab-pane fade" id="wiki">
					<?php
					echo "<div align=center>
						<h2><small>
							Total tests done yet - ".$view_count['Wikipedia Page']."<br/>{ M - ". $male_view_count['Wikipedia Page'].", F - ". $female_view_count['Wikipedia Page']." }
						</small></h2>
					</div>";
						
					//font style counts
					$font_style_count = array('0','0','0','0','0');
					$font_style_male = array('0','0','0','0','0');
					$font_style_female = array('0','0','0','0','0');
					$font_style_reading_time = array('0','0','0','0','0');
					$font_style_test_time = array('0','0','0','0','0');
					
					
					//font size counts
					$font_size_count = array('0', '0', '0', '0','0','0');
					$font_size_male = array('0','0','0','0','0','0');
					$font_size_female = array('0','0','0','0','0','0');
					$font_size_reading_time = array('0','0','0','0','0','0');
					$font_size_test_time = array('0','0','0','0','0','0');
					
					
					//Line Height counts
					$line_height_count = array('0', '0', '0', '0','0','0','0','0');
					$line_height_male = array('0','0','0','0','0','0','0','0');
					$line_height_female = array('0','0','0','0','0','0','0','0');
					$line_height_reading_time = array('0','0','0','0','0','0');
					$line_height_test_time = array('0','0','0','0','0','0');
					
					
					//Word Spacing counts
					$word_spacing_count = array('0', '0', '0', '0','0');
					$word_spacing_male = array('0','0','0','0','0');
					$word_spacing_female = array('0','0','0','0','0');
					$word_spacing_reading_time = array('0','0','0','0','0');
					$word_spacing_test_time = array('0','0','0','0','0');
						
					$i=1;
					$query="SELECT * FROM paragraphs WHERE `article_type`='Wikipedia Page'";
					mysql_query("SET NAMES utf8");
					$result=mysql_query($query);
					
					while($row=mysql_fetch_array($result)){
						$para_font_style_count = array('0','0','0','0','0');
						$para_font_style_male = array('0','0','0','0','0');
						$para_font_style_female = array('0','0','0','0','0');
						$para_font_style_reading_time = array('0','0','0','0','0');
						$para_font_style_test_time = array('0','0','0','0','0');
										
						$para_font_size_count = array('0', '0', '0', '0','0','0');
						$para_font_size_male = array('0','0','0','0','0','0');
						$para_font_size_female = array('0','0','0','0','0','0');
						$para_font_size_reading_time = array('0','0','0','0','0','0');
						$para_font_size_test_time = array('0','0','0','0','0','0');
										
						$para_line_height_count = array('0', '0', '0', '0','0','0');
						$para_line_height_male = array('0','0','0','0','0','0');
						$para_line_height_female = array('0','0','0','0','0','0');
						$para_line_height_reading_time = array('0','0','0','0','0','0');
						$para_line_height_test_time = array('0','0','0','0','0','0');
										
						$para_word_spacing_count = array('0', '0', '0', '0','0');
						$para_word_spacing_male = array('0','0','0','0','0');
						$para_word_spacing_female = array('0','0','0','0','0');
						$para_word_spacing_reading_time = array('0','0','0','0','0');
						$para_word_spacing_test_time = array('0','0','0','0','0');
										
						$var1=$row['pid'];
						$query1="SELECT * FROM test_data Where `pid`='$var1'"; 
						$result1=mysql_query($query1);
						$totalwikiviewers=  mysql_num_rows($result1); 
						$wikimale=0;
						$wikifemale=0;
									
						while($row1=mysql_fetch_array($result1)){
							$var2=$row1['uid'];
							$query2="SELECT * FROM main Where `user_id`='$var2'"; 
							$result2=mysql_query($query2);
							$row2=mysql_fetch_array($result2);
									
							if($row2['gender']=='1'){
								$wikimale++;
							}
							else{
								$wikifemale++;
							}
							///////////CALCULATION OF  wiki DOCUMENT FONT STYLE//////
							if($row1['font']=='Arial'){
								$font_style_count[0]++;
								$font_style_reading_time[0]+=$row1['reading_time'];
								$font_style_test_time[0]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[0]++;
								}
								else{
									$font_style_female[0]++;
								}
							}
                            if($row1['font']=='Calibri'){
								$font_style_count[1]++;
								$font_style_reading_time[1]+=$row1['reading_time'];
								$font_style_test_time[1]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[1]++;
								}
								else{
									$font_style_female[1]++;
								}
							}		
							if($row1['font']=='Comic Sans MS'){
								$font_style_count[2]++;
								$font_style_reading_time[2]+=$row1['reading_time'];
								$font_style_test_time[2]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[2]++;
								}
								else{
									$font_style_female[2]++;
								}
							}		  
							if($row1['font']=='Times New Roman'){
								$font_style_count[3]++;
								$font_style_reading_time[3]+=$row1['reading_time'];
								$font_style_test_time[3]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[3]++;
								}
								else{
									$font_style_female[3]++;
								}
							}		 
							if($row1['font']=='Lucida Sans'){
								$font_style_count[4]++;	
								$font_style_reading_time[4]+=$row1['reading_time'];
								$font_style_test_time[4]+=$row1['test_time'];
								
								$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
								$result3=mysql_query($query3);
								$row3=mysql_fetch_array($result3);
									
								if($row3['gender']=='1'){
									$font_style_male[4]++;
								}
								else{
									$font_style_female[4]++;
								}
 							}
							///////////END OF CALCULATION OF  wiki DOCUMENT FONT STYLE//////
									
							///////////CALCULATION OF  wiki DOCUMENT FONT SIZE//////
							switch($row1['size']){
								case (70<=$row1['size']&&$row1['size']<=90):
									$font_size_count[0]++;
									$font_size_reading_time[0]+=$row1['reading_time'];
									$font_size_test_time[0]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[0]++;
									}
									else{
										$font_size_female[0]++;
									}
								break;
								
								case (100<=$row1['size']&&$row1['size']<=120):
									$font_size_count[1]++;
									$font_size_reading_time[1]+=$row1['reading_time'];
									$font_size_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[1]++;
									}
									else{
										$font_size_female[1]++;
									}
								break;
									
								case (130<=$row1['size']&&$row1['size']<=150):
									$font_size_count[2]++;
									$font_size_reading_time[2]+=$row1['reading_time'];
									$font_size_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[2]++;
									}
									else{
										$font_size_female[2]++;
									}
								break;
									
								case (160<=$row1['size']&&$row1['size']<=180):
									$font_size_count[3]++;
									$font_size_reading_time[3]+=$row1['reading_time'];
									$font_size_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$font_size_male[3]++;
									}
									else{
										$font_size_female[3]++;
									}
								break;
										
								case (190<=$row1['size']&&$row1['size']<=210):
									$font_size_count[4]++;
									$font_size_reading_time[4]+=$row1['reading_time'];
									$font_size_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[4]++;
									}
									else{
										$font_size_female[4]++;
									}
								break;
									
								case (220<=$row1['size']&&$row1['size']<=240):
									$font_size_count[5]++;
									$font_size_reading_time[5]+=$row1['reading_time'];
									$font_size_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$font_size_male[5]++;
									}
									else{
										$font_size_female[5]++;
									}
								break;
							}
							///////////END OF CALCULATION OF  wiki DOCUMENT FONT line_height//////
							
							///////////CALCULATION OF  wiki DOCUMENT LINE HEIGHT //////
							switch($row1['line_height']){
								case (20<=$row1['line_height']&&$row1['line_height']<=24):
									$line_height_count[0]++;
									$line_height_reading_time[0]+=$row1['reading_time'];
									$line_height_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[0]++;
									}
									else{
										$line_height_female[0]++;
									}
								break;
								
								case (25<=$row1['line_height']&&$row1['line_height']<=29):
									$line_height_count[1]++;
									$line_height_reading_time[1]+=$row1['reading_time'];
									$line_height_test_time[1]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[1]++;
									}
									else{
										$line_height_female[1]++;
									}
								break;
								
								case (30<=$row1['line_height']&&$row1['line_height']<=34):
									$line_height_count[2]++;
									$line_height_reading_time[2]+=$row1['reading_time'];
									$line_height_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[2]++;
									}
									else{
										$line_height_female[2]++;
									}
								break;
						
								case (35<=$row1['line_height']&&$row1['line_height']<=39):
									$line_height_count[3]++;
									$line_height_reading_time[3]+=$row1['reading_time'];
									$line_height_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[3]++;
									}
									else{
										$line_height_female[3]++;
									}
								break;
								
								case (40<=$row1['size']&&$row1['line_height']<=44):
									$font_line_height_count[4]++;
									$line_height_reading_time[4]+=$row1['reading_time'];
									$line_height_test_time[4]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$line_height_male[4]++;
									}
									else{
										$line_height_female[4]++;
									}
								break;
									
								case (45<=$row1['line_height']&&$row1['line_height']<=50):
									$line_height_count[5]++;
									$line_height_reading_time[5]+=$row1['reading_time'];
									$line_height_test_time[5]+=$row1['test_time'];
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
													
									if($row3['gender']=='1'){
										$line_height_male[5]++;
									}
									else{
										$line_height_female[5]++;
									}
								break;
								
									
							}
							///////////END OF CALCULATION OF  wiki DOCUMENT LINE HEIGHT //////
										 
							///////////CALCULATION OF wiki DOCUMENT WORD SPACING //////
							switch($row1['word_spacing']){
								case (0<=$row1['word_spacing']&&$row1['word_spacing']<=3):
									$word_spacing_count[0]++;
									$word_spacing_reading_time[0]+=$row1['reading_time'];
									$word_spacing_test_time[0]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
									
									if($row3['gender']=='1'){
										$word_spacing_male[0]++;
									}
									else{
										$word_spacing_female[0]++;
									}
								break;

								case (4<=$row1['word_spacing']&&$row1['word_spacing']<=7):
									$word_spacing_count[1]++;
									$word_spacing_reading_time[1]+=$row1['reading_time'];
									$word_spacing_test_time[1]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[1]++;
									}
									else{
										$word_spacing_female[1]++;
									}
								break;
								
								case (8<=$row1['word_spacing']&&$row1['word_spacing']<=11):
									$word_spacing_count[2]++;
									$word_spacing_reading_time[2]+=$row1['reading_time'];
									$word_spacing_test_time[2]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[2]++;
									}
									else{
										$word_spacing_female[2]++;
									}
								break;
								
								case (12<=$row1['word_spacing']&&$row1['word_spacing']<=15):
									$word_spacing_count[3]++;
									$word_spacing_reading_time[3]+=$row1['reading_time'];
									$word_spacing_test_time[3]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[3]++;
									}
									else{
										$word_spacing_female[3]++;
									}
								break;
								
								case (16<=$row1['word_spacing']&&$row1['word_spacing']<=20):
									$word_spacing_count[4]++;
									$word_spacing_reading_time[4]+=$row1['reading_time'];
									$word_spacing_test_time[4]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
														
									if($row3['gender']=='1'){
										$word_spacing_male[4]++;
									}
									else{
										$word_spacing_female[4]++;
									}
								break;
							}
							///////////END OF CALCULATION OF wiki DOCUMENT WORD SPACING //////
						}
						//paragraphs table
						echo "<table class='table table-bordered'>";
							echo "<tr>
								<td>";
								//para_panel
									echo "<div class='para_panel' id='wiki_para_panel".$i."'>
										<button class='btn-primary btn-block btn-lg' data-toggle='collapse' data-target='#wiki_para".$i."' data-parent='#wiki_para_panel".$i."'>
											<div class='para_info1'>
												Paragraph : ".$i."
											</div>
											<div class='para_info'>".
													$totalwikiviewers."
													{ M - ".$wikimale.",  F - ".$wikifemale." }
											</div>
										</button>
											
										<div id='wiki_para".$i."' class='panel-collapse panel-body collapse'>".
											$row['para'];
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//////////////CALCULATION FOR CREATING CHART ON FONT SYTLE FOR EACH PARAGRAPH IN wiki DOCUMENT///////////
											for($k=0;$k<5;$k++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `font`='".$font_style[$k]."'";
												$para=mysql_query($query);
												$para_font_style_count[$k]=mysql_num_rows($para);
												
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_style_reading_time[$k]+=$row_para['reading_time'];
													$para_font_style_test_time[$k]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_style_male[$k]++;
													else
														$para_font_style_female[$k]++;  
										        }
											}   
											//////CALCULATION FOR CREATING CHART ON FONT SIZE AND LINE HEIGHT AND WORD-SPACING FOR EACH PARAGRAPH IN wiki DOCUMENT///////////
											$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."'";
											$para=mysql_query($query);
											while($row_para=mysql_fetch_array($para)){
												switch($row_para['size']){
													case (70<=$row_para['size']&&$row_para['size']<=90):
														$para_font_size_count[0]++;
														$para_font_size_reading_time[0]+=$row_para['reading_time'];
														$para_font_size_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[0]++;
														}
														else{
															$para_font_size_female[0]++;
														}
													break;
													
													case (100<=$row_para['size']&&$row_para['size']<=120):
														$para_font_size_count[1]++;
														$para_font_size_reading_time[1]+=$row_para['reading_time'];
														$para_font_size_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[1]++;
														}
														else{
															$para_font_size_female[1]++;
														}
													break;
														
													case (130<=$row_para['size']&&$row_para['size']<=150):
														$para_font_size_count[2]++;
														$para_font_size_reading_time[2]+=$row_para['reading_time'];
														$para_font_size_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[2]++;
														}
														else{
															$para_font_size_female[2]++;
														}
													break;
														
													case (160<=$row_para['size']&&$row_para['size']<=180):
														$para_font_size_count[3]++;
														$para_font_size_reading_time[3]+=$row_para['reading_time'];
														$para_font_size_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_font_size_male[3]++;
														}
														else{
															$para_font_size_female[3]++;
														}
													break;
														
													case (190<=$row_para['size']&&$row_para['size']<=210):
														$para_font_size_count[4]++;
														$para_font_size_reading_time[4]+=$row_para['reading_time'];
														$para_font_size_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[4]++;
														}
														else{
															$para_font_size_female[4]++;
														}
													break;
														
													case (220<=$row_para['size']&&$row_para['size']<=240):
														$para_font_size_count[5]++;
														$para_font_size_reading_time[5]+=$row_para['reading_time'];
														$para_font_size_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																				
														if($row3['gender']=='1'){
															$para_font_size_male[5]++;
														}
														else{
															$para_para_font_size_female[5]++;
														}
													break;
												}
															
												///////////CALCULATION OF  LINE HEIGHT FOR EACH PARAGRAPH //////
												switch($row_para['line_height']){
													case (20<=$row_para['line_height']&&$row_para['line_height']<=24):
														$para_line_height_count[0]++;
														$para_line_height_reading_time[0]+=$row_para['reading_time'];
														$para_line_height_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[0]++;
														}
														else{
															$para_line_height_female[0]++;
														}
													break;
														
													case (25<=$row_para['line_height']&&$row_para['line_height']<=29):
														$para_line_height_count[1]++;
														$para_line_height_reading_time[1]+=$row_para['reading_time'];
														$para_line_height_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[1]++;
														}
														else{
															$para_line_height_female[1]++;
														}
														break;
													
													case (30<=$row_para['line_height']&&$row_para['line_height']<=34):
														$para_line_height_count[2]++;
														$para_line_height_reading_time[2]+=$row_para['reading_time'];
														$para_para_line_height_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[2]++;
														}
														else{
															$para_line_height_female[2]++;
														}
													break;
														
													case (35<=$row_para['line_height']&&$row_para['line_height']<=39):
														$para_line_height_count[3]++;
														$para_line_height_reading_time[3]+=$row_para['reading_time'];
														$para_line_height_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[3]++;
														}
														else{
															$para_line_height_female[3]++;
														}
													break;
														
													case (40<=$row_para['size']&&$row_para['line_height']<=44):
														$para_line_height_count[4]++;
														$para_line_height_reading_time[4]+=$row_para['reading_time'];
														$para_line_height_test_time[4]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[4]++;
														}
														else{
															$para_line_height_female[4]++;
														}
													break;
														
													case (45<=$row_para['line_height']&&$row_para['line_height']<=50):
														$para_line_height_count[5]++;
														$para_line_height_reading_time[5]+=$row_para['reading_time'];
														$para_line_height_test_time[5]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_line_height_male[5]++;
														}
														else{
															$para_line_height_female[5]++;
														}
													break;
														
													
												}
															
												///////////CALCULATION OF WORD SPACING FOR EACH PARAGRAPH//////
												switch($row_para['word_spacing']){
													case (0<=$row_para['word_spacing']&&$row_para['word_spacing']<=3):
														$para_word_spacing_count[0]++;
														$para_word_spacing_reading_time[0]+=$row_para['reading_time'];
														$para_word_spacing_test_time[0]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
														
														if($row3['gender']=='1'){
															$para_word_spacing_male[0]++;
														}
														else{
															$para_word_spacing_female[0]++;
														}
													break;
													
													case (4<=$row_para['word_spacing']&&$row_para['word_spacing']<=7):
														$para_word_spacing_count[1]++;
														$para_word_spacing_reading_time[1]+=$row_para['reading_time'];
														$para_word_spacing_test_time[1]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[1]++;
														}
														else{
															$para_word_spacing_female[1]++;
														}
													break;
													
													case (8<=$row_para['word_spacing']&&$row_para['word_spacing']<=11):
														$para_word_spacing_count[2]++;
														$para_word_spacing_reading_time[2]+=$row_para['reading_time'];
														$para_word_spacing_test_time[2]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[2]++;
														}
														else{
															$para_word_spacing_female[2]++;
														}
													break;
													
													case (12<=$row_para['word_spacing']&&$row_para['word_spacing']<=15):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
													
													case (16<=$row_para['word_spacing']&&$row_para['word_spacing']<=20):
														$para_word_spacing_count[3]++;
														$para_word_spacing_reading_time[3]+=$row_para['reading_time'];
														$para_word_spacing_test_time[3]+=$row_para['test_time'];
														
														$query3="SELECT * FROM main Where `user_id`='".$row_para['uid']."'"; 
														$result3=mysql_query($query3);
														$row3=mysql_fetch_array($result3);
																			
														if($row3['gender']=='1'){
															$para_word_spacing_male[3]++;
														}
														else{
															$para_word_spacing_female[3]++;
														}
													break;
												}
											}	
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//Nav tabs list -obj, sub and graphs
											echo "<ul class='nav nav-tabs nav-justified' role='tablist'>
												<li class='active in'><a href='#wiki_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
												<li><a href='#wiki_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
												<li><a href='#wiki_graphs".$i."' role='tab' data-toggle='tab'>Graphs and Charts</a></li>
											</ul>";
															
											//Tab panes
											echo "<div class='tab-content'>";
												//Objectives Tab
												echo "<div class='tab-pane fade active in' id='wiki_obj".$i."'>
													<table class = 'table table-hover table-bordered ques_table' id='wiki_para_obj_ques".$i."'>
														<tr align = 'center'>
															<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
															<td class = 'col-lg-6'><h4><big>Questions</big></h4></td>
															<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Skipped</big></h6></td>
														</tr>";
																					
														$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
														mysql_query("SET NAMES utf8");
														$result3 = mysql_query($query3);
							
														$j = 1;
														while($row3 = mysql_fetch_array($result3)){
															echo "<tr align = 'center'>";
																//S.No.
																echo "<td>".
																	$j.
																"</td>";
																							
																//Objective Questions
																echo "<td>".
																	$row3['ques'].
																"</td>";
																
																$j++;
																							
																//Options data
																$query4 = "SELECT * FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																mysql_query("SET NAMES utf8");
																$result4 = mysql_query($query4);
																								
																$opt1_select_count = 0;
																$opt2_select_count = 0;
																$opt3_select_count = 0;
																$opt4_select_count = 0;
																$skipped_count = 0;
																							
																if($row3['opt1'] == ""){
																	$opt1_select_count = -1;
																}
																if($row3['opt2'] == ""){
																	$opt2_select_count = -1;
																}
																if($row3['opt3'] == ""){
																	$opt3_select_count = -1;
																}
																if($row3['opt4'] == ""){
																	$opt4_select_count = -1;
																}
																								
																while($row4 = mysql_fetch_array($result4)){
																	if($row4['selected_option'] == $row3['opt1'])
																		$opt1_select_count++;
																	if($row4['selected_option'] == $row3['opt2'])
																		$opt2_select_count++;
																	if($row4['selected_option'] == $row3['opt3'])
																		$opt3_select_count++;
																	if($row4['selected_option'] == $row3['opt4'])
																		$opt4_select_count++;
																	if($row4['selected_option'] == "skipped")
																		$skipped_count++;
																}
																//opt1
																echo "<td>";
																	if($opt1_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt1']."<hr/>".
																		$opt1_select_count;
																	}
																echo "</td>";
																						
																//opt2
																echo "<td>";
																	if($opt2_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt2']."<hr/>".
																		$opt2_select_count;
																	}
																echo "</td>";
																					
																//opt3
																echo "<td>";
																	if($opt3_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt3']."<hr/>".
																		$opt3_select_count;
																	}
																echo "</td>";
																						
																//opt4
																echo "<td>";
																	if($opt4_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt4']."<hr/>".
																		$opt4_select_count;
																	}
																echo "</td>";
																							
																//skipped
																echo "<td>";
																	if($skipped_count == -1){
																		echo "-";
																	}
																	else{
																		echo $skipped_count;
																	}
																echo "</td>
															</tr>";	
														}
													echo "</table>
												</div>";
																		
												//Subjectives Tab
												echo "<div class='tab-pane fade active' id='wiki_sub".$i."'>";
													//querying all subjective questions of a particular para
													$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
													mysql_query("SET NAMES utf8");
													$result3 = mysql_query($query3);
																		
													$j = 1;
													while($row3 = mysql_fetch_array($result3)){
														echo "<table class = 'ques_table' id='wiki_para_sub_ques".$i."' border = 'solid'>";
															//subjective question Heading
															echo "<tr align = 'center'>
																<td>
																	<h4><big>
																		Question : ".$j.
																	"</big></h4>
																</td>
															</tr>";
																				
															//subjective Questions
															echo "<tr align = 'center'>
																<td>
																	<div class='ques_body'>
																		<h3><small>".
																			$row3['ques'].
																		"</small></h3>
																	</div>
																</td>
															</tr>";
																				
															//subjective answers row having table of answers
															echo "<tr align = 'center'>
																<td>
																	<table class='table' border = 'solid'>
																		<tr align='center'>
																			<td class='col-lg-3'>User</td>
																			<td class='col-lg-9'>Answers</td>
																		</tr>";
																							
																		//querying all users and all thier answrs of a question
																		//$query4 = "SELECT tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																		$query4 = "SELECT uid, tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."' GROUP BY uid";
																		mysql_query("SET NAMES utf8");
																		$result4 = mysql_query($query4);
																							
																		while($row4 = mysql_fetch_array($result4)){
																			//$query5 = "SELECT uid FROM test_data WHERE `tid` = '".$row4['tid']."'";
																			//mysql_query("SET NAMES utf8");
																			//$result5 = mysql_query($query5);
																			//$row5 = mysql_fetch_array($result5);
																									
																			//$query6 = "SELECT * FROM main WHERE `user_id` = '".$row5['uid']."'";
																			$query6 = "SELECT * FROM main WHERE `user_id` = '".$row4['uid']."'";
																			$result6 = mysql_query($query6);
																			$row6 = mysql_fetch_array($result6);
																									
																			echo "<tr align='center'>
																				<td>".$row6['email']."<br/>{ Age - ".$row6['age'].", ";
																					if($row6['gender'] == "1"){
																						echo "M, ";
																					}
																					else{
																						echo "F, ";
																					}
																					if($row6['edu_back'] == "higher_sec"){
																						echo "Higher Secondary }";
																					}
																					else if($row6['edu_back'] == "ug"){
																						echo "Undergraduate }";
																					}
																					else if($row6['edu_back'] == "pg"){
																						echo "Postgraduate }";
																					}
																					else{
																						echo "Other }";
																					}
																				echo "</td>
																				
																				<td>";
																					$query7 = "SELECT tid FROM test_data WHERE `uid` = '".$row4['uid']."' AND `pid` = '".$row['pid']."'";
																					$result7 = mysql_query($query7);
																					while($row7 = mysql_fetch_array($result7)){
																						$query8 = "SELECT selected_option FROM test_questions_data WHERE `tid` = '".$row7['tid']."' AND `qid` = '".$row3['qid']."'";
																						$result8 = mysql_query($query8);
																						$row8 = mysql_fetch_array($result8);
																						echo $row8['selected_option']."<hr/>";
																					}
																				echo "</td>
																			</tr>";
																		}
																	echo "</table>
																</td>
															</tr>";
														
														$j++;
														echo "</table>";	
													}
												echo "</div>";
																		
												//Graphs and Charts Tab
												echo "<div class='tab-pane fade active' id='wiki_graphs".$i."'>";
												//////////////////////////////////////////////////////////////////////////////////////////////////////////
													echo "<table class='table table-bordered'><tr>";
													//CREATING A CHART OF FONT STYLE FOR EACH PARAGRAPH In wiki Article Type
													echo "<td>";
													if(array_sum($para_font_style_count)!=0){
														$strXML= "<graph caption='Tests given in different Font Style' subCaption='with Legal Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
														echo "<table class='table table-bordered'>";
														echo "<tr>";
															echo "<td>Font Style</td>";
															echo "<td>Male views</td>";
															echo "<td>Female views</td>";
															echo "<td>Average Reading Time</td>";
															echo "<td>Average Test Time</td>";
														echo "</tr>";
														for($t=0;$t<5;$t++){
															echo "<tr>
															<td>".
																$font_style[$t].
															"</td>
															
															<td>".
																$para_font_style_male[$t].
															"</td>
															
															<td>".
																$para_font_style_female[$t].
															"</td>";
															if($para_font_style_count[$t]!=0){
																$para_font_style_reading_time[$t]=($para_font_style_reading_time[$t]/ $para_font_style_count[$t]);
																$para_font_style_test_time[$t]=($para_font_style_test_time[$t]/$para_font_style_count[$t]);
																echo "<td>".
																	$para_font_style_reading_time[$t].
																"</td>";
																
																echo "<td>".
																	$para_font_style_test_time[$t].
																"</td>";
															}
															else{
																echo "<td>-</td>";
																echo "<td>-</td>";
															}
															$strXML .= "<set name='" . $font_style[$t] . "' value='" . $para_font_style_count[$t] . "' />";
															echo "</tr>";
														}
													$strXML .= "</graph>";
													echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_font_style_para_chart".$i, 500, 400);
													echo "</table>";
												}
												echo "</td>";
												echo "<td>";			
												
												//CREATING A CHART OF FONT SIZE FOR EACH PARAGRAPH In wiki Article Type
												if(array_sum($para_font_size_count)!=0){
													$strXML= "<graph caption='Tests given in different Font Size' subCaption='with wiki Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Font Size Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$font_size[$t].
														"</td>
														
														<td>".
															$para_font_size_male[$t].
														"</td>
														
														<td>".
															$para_font_size_female[$t].
														"</td>";
														if($para_font_size_count[$t]!=0){
															$para_font_size_reading_time[$t]=($para_font_size_reading_time[$t]/ $para_font_size_count[$t]);
															$para_font_size_test_time[$t]=($para_font_size_test_time[$t]/$para_font_size_count[$t]);
															echo "<td>".
																$para_font_size_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_font_size_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $font_size[$t] . "' value='" . $para_font_size_count[$t] . "' />";
														echo "</tr>";				
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_font_size_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr>";
											
											echo "<tr>";
											echo "<td>";
											
											//CREATING A CHART OF LINE HEIG FOR EACH PARAGRAPH
											if(array_sum($para_line_height_count)!=0){
												$strXML= "<graph caption='Tests given in different line height' subCaption='with wiki Article type'pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
													echo "<table class='table table-bordered'>";
													echo "<tr>";
														echo "<td>Line Height Ranges</td>";
														echo "<td>Male views</td>";
														echo "<td>Female views</td>";
														echo "<td>Average Reading Time</td>";
														echo "<td>Average Test Time</td>";
													echo "</tr>";
													for($t=0;$t<6;$t++){
														echo "<tr>
														<td>".
															$line_height[$t].
														"</td>
														
														<td>".
															$para_line_height_male[$t].
														"</td>
														
														<td>".
															$para_line_height_female[$t].
														"</td>";
														if($para_line_height_count[$t]!=0){
															$para_line_height_reading_time[$t]=($para_line_height_reading_time[$t]/ $para_line_height_count[$t]);
															$para_line_height_test_time[$t]=($para_line_height_test_time[$t]/$para_line_height_count[$t]);
															echo "<td>".
																$para_line_height_reading_time[$t].
															"</td>";
															
															echo "<td>".
																$para_line_height_test_time[$t].
															"</td>";
														}
														else{
															echo "<td>-</td>";
															echo "<td>-</td>";
														}
														$strXML .= "<set name='" . $line_height[$t] . "' value='" . $para_line_height_count[$t] . "' />";
													}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_line_height_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "<td>";
											
											//CREATING A CHART OF WORD SPACING FOR EACH PARAGRAPH
											if(array_sum($para_word_spacing_count)!=0){
												$strXML= "<graph caption='Tests given in different line Word Spacing' subCaption='with wiki Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
												echo "<table class='table table-bordered'>";
												echo "<tr>";
													echo "<td>Word Spacing Ranges</td>";
													echo "<td>Male views</td>";
													echo "<td>Female views</td>";
													echo "<td>Average Reading Time</td>";
													echo "<td>Average Test Time</td>";
												echo "</tr>";				
												for($t=0;$t<5;$t++){
													echo "<tr>
													<td>".
														$word_spacing[$t].
													"</td>
													
													<td>".
														$word_spacing_male[$t].
													"</td>
													
													<td>".
														$word_spacing_female[$t].
													"</td>";
													
													if($word_spacing_count[$t]!=0){
														$para_word_spacing_reading_time[$t]=($para_word_spacing_reading_time[$t]/ $para_word_spacing_count[$t]);
														$para_word_spacing_test_time[$t]=($para_word_spacing_test_time[$t]/$para_word_spacing_count[$t]);
														echo "<td>".
															$para_word_spacing_reading_time[$t].
														"</td>";
														
														echo "<td>".
															$para_word_spacing_test_time[$t].
														"</td>";
													}
													else{
														echo "<td>-</td>";
														echo "<td>-</td>";
													}
													$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $para_word_spacing_count[$t] . "' />";
												}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_word_spacing_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</td>";
											echo "</tr></table>";
											echo "</div>
												
											</div>";//end of div containing all 3 tabs of this para
										echo "</div>
									</div>
								</td>
							</tr>";
							$i++;
						}
					echo "</table>";
					?>
					<!--CHARTS|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					<?php
					//Accordian for CHARTS FOR Research Article Types|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					echo '<button id=wiki_accordian" class="article_accordian_class btn btn-warning btn-lg btn-block" data-toggle="collapse" data-target="#wiki_graphs">
						Charts for Wikipedia Documents
					</button>';
					echo "<table id='wiki_graphs' class='collapse in table table-bordered'><tr>";
					/////////////////////CHART FOR FONT STYLE in wiki Article type///////////
					echo "<td>";
					if(array_sum($font_style_count)!=0){
						$strXML= "<graph caption='Tests given in different Font styles' subcaption='with wiki Article Type' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Style</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$font_style[$t].
							"</td>
							
							<td>".
								$font_style_male[$t].
							"</td>
							
							<td>".
								$font_style_female[$t].
							"</td>";
							if($font_style_count[$t]!=0){
								$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
								$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
								echo "<td>".
									$font_style_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_style_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "wiki_font_style_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR FONT SIZE in wiki Article type///////////
					if(array_sum($font_size_count)!=0){
						$strXML= "<graph caption='Tests given in different Font sizes' subcaption='with wiki Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Font Size Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$font_size[$t].
							"</td>
							
							<td>".
								$font_size_male[$t].
							"</td>
							
							<td>".
								$font_size_female[$t].
							"</td>";
							if($font_size_count[$t]!=0){
								$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
								$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
								echo "<td>".
									$font_size_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$font_size_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
							echo "</tr>";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_size_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td>";
					
					/////////////////////CHART FOR Line Height in wiki Article type///////////
					if(array_sum($line_height_count)!=0){
						$strXML= "<graph caption='Tests given in different Line heights' subcaption='with wiki Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Line Height Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<6;$t++){
							echo "<tr>
							<td>".
								$line_height[$t].
							"</td>
							
							<td>".
								$line_height_male[$t].
							"</td>
							
							<td>".
								$line_height_female[$t].
							"</td>";
							if($line_height_count[$t]!=0){
								$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
								$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
								echo "<td>".
									$line_height_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$line_height_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
						}
						$strXML .= "</graph>";
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_line_height_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "<td>";
					
					/////////////////////CHART FOR Word Spacing in wiki Article type///////////
					if(array_sum($word_spacing_count)!=0){
						$strXML= "<graph caption='Tests given in different word spacing' subcaption='with wiki Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' Test' decimalPrecision='0'>";
						echo "<table class='table table-bordered'>";
						echo "<tr>";
							echo "<td>Word Spacing Ranges</td>";
							echo "<td>Male views</td>";
							echo "<td>Female views</td>";
							echo "<td>Average Reading Time</td>";
							echo "<td>Average Test Time</td>";
						echo "</tr>";
						for($t=0;$t<5;$t++){
							echo "<tr>
							<td>".
								$word_spacing[$t].
							"</td>
							
							<td>".
								$word_spacing_male[$t].
							"</td>
							
							<td>".
								$word_spacing_female[$t].
							"</td>";
							if($word_spacing_count[$t]!=0){
								$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
								$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
								echo "<td>".
									$word_spacing_reading_time[$t].
								"</td>";
								
								echo "<td>".
									$word_spacing_test_time[$t].
								"</td>";
							}
							else{
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
							$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
						}
						$strXML .= "</graph>";
	
						echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_word_spacing_chart", 500, 400);
						echo "</table>";
					}
					echo "</td>";
					echo "</tr></table>";
					?>
				</div><!--End of wikipedia  Article-->
				
			</div>	
		</div>
	</div>	
	<!-- javascript -->
	<script src="Bootstrap/js/jquery 2.1.1.min.js"></script>
    <script src="Bootstrap/js/bootstrap.min.js"></script>
	<script>
		$(".alert").alert();
		window.setTimeout(function() { 
			$(".alert").alert('close'); 
		}, 5000);
		$( ".panel-body" ).accordion({ autoHeight: false });
	</script>
<!---footer---->
<nav class=" navbar-fixed-bottom footer " role="navigation" >
  <div class="container footer" align="right";  >
    <font color="#04B404"><b> Developer-</b></font>
	 <a href=http://about.me/jain_nikhil><b>Nikhil Jain </b></a>
	 <font color="#2E2EFE"> &</font>
	<a href=http://about.me/ashish_dubey><b>Ashish Dubey</b></a>
	 
  </div>
</nav>
</body>
</html>
