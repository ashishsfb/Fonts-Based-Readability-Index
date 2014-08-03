<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Readability Survey</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/survey.ico">
	<!-- Bootstrap Core CSS-->
    <link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom Core CSS-->
    <link href="Bootstrap/css/custom.css" rel="stylesheet">
    <script src="Bootstrap/js/respond.js"></script>
</head>


<?php
	include("include/db_connect.php");
	session_start();
	
	//////////FOR SECURITY/////////////////////
	//check for direct entry
	if(!isset($_SESSION['email'])){
		header("Location:index.php");
	}
	else{
		$sql = "SELECT * FROM main where email='".$_SESSION['email']."'";
		$result =  mysql_query($sql);
		$num=mysql_num_rows($result);
		$row=mysql_fetch_array($result);
		
		if($num!=1)
			header("Location:index.php");
	}
	
	if(!empty($_POST)){
		$font = $_POST["font"];
		$size = $_POST["size"];
		
		if(isset($_POST['line_height']))
			$line_height = $_POST["line_height"];
		if(isset($_POST['word_spacing']))
			$word_spacing = $_POST["word_spacing"];
		
		//storing form data in session array
		$_SESSION["font"] = $font;
		$_SESSION["size"] = $size;
		if(isset($_POST['line_height']))
			$_SESSION["line_height"] = $line_height;
		if(isset($_POST['word_spacing']))	
			$_SESSION["word_spacing"] = $word_spacing;
		
		if(isset($_POST['check_post'])){
			if( !($_POST['check_post'] == 1) ){
				$error = "Please click on any of these article buttons atleast once to generate a paragraph.";
			}
			else{
				if(isset($_POST['go'])){
					header("Location: survey.php");
				}
			}
		}
	}	
?>

<body>
	<div class="container">
		<div class="row well">
			
			<div class="col-md-4 col-lg-4" align="center">
				Hello User <?php echo stripslashes($_SESSION['email']);?>
			</div>
			
			<div class="col-md-3 col-lg-4">
				<div class="page-header"><h2 align="center">Readability Survey Home</h2></div>
			</div>
			
			<div class="col-md-4 col-lg-4" align="center">
				<?php echo date("jS \of F Y [l]", time());?>
			</div>
		</div>
		
		<div class="row">
			<div align="center">
				<!--The paragraph-->
				<p class="well" id="para">
					Click on the preview form, and then select your preferences then click on preview to see how your paragraph's gonna look !!
					Click on the go button when you are ready to take the survey.<br/>
					प्रीव्यू फॉर्म पर क्लिक करके देखिये की आपका पैराग्राफ कैसा दिखने वाला है | 
					गो बटन पे क्लिक करके सर्वे खत्म करिए |
				</p>
				
				<div class="row article_btns">
					<span class="eng_article_form">
						<input id="eng_article_btn" name="eng_article_btn" class="btn btn-info" type="button" value="English Article"/>	
					</span>
					
					<span class="hindi_article_form">
						<input id="hindi_article_btn" name="hindi_article_btn" class="btn btn-info" type="button" value="Hindi Article"/>	
					</span>
				</div>
				<br/>
				
				<!--Error Printing-->
				<?php
					if(isset($error)){
						echo "<div class='alert alert-danger' align='center' id='status-box'>";
							echo $error;
						echo "</div>";	
					}
					else{
						if(isset($success)){
							echo "<div class='alert alert-success' align='center' id='status-box'>";
								echo $success;
							echo "</div>";	
						}	
					}	
				?>
				<button id="preview-accordian" class="btn btn-primary btn-lg" data-toggle="collapse" data-target="#preview-box">
					Preview Form
				</button>
				
				<!--Our priview box/form-->
				<div class="row well collapse" align="center" id="preview-box">
					<form class="form-preview" method="POST" action="home.php">
						<table class="table table-striped">
							<tr>
							<div class="form-group">
								<td align="right"><label>Font Style</label></td>
								<td>
								<select name="font" class="span3" id="font">
									<option name="arial" value="Arial">Arial</option>
									<option name="calibri" value="Calibri">Calibri</option>
									<option name="comic_sans" value="Comic Sans MS">Comic Sans MS</option>
									<option name="times_new_roman" value="Times New Roman">Times New Roman</option>
									<option name="lucida_sans" value="Lucida Sans">Lucida Sans</option>
								</select>
								</td>
							</div>
							</tr>
							
							<tr>
							<div class="form-group">
								<td align="right"><label>Font Size (%)</label></td>
								<td><input name="size" id="size" type="number" class="span3" value="100" step="10" min="70" max="230"/></td>
							</div>
							</tr>
							
							<tr>
							<div class="form-group">
								<td align="right"><label>Line Height (px)</label></td>
								<td><input name="line_height" id="line_height" type="number" class="span3" value="23" step="1" min="20" max="50"/></td>
							</div>
							</tr>
							
							<tr>
							<div class="form-group">
								<td align="right"><label>Word Spacing (px)</label></td>
								<td><input name="word_spacing" id="word_spacing" type="number" class="span3" value="0" step="2" min="0" max="20" align="center"/></td>
							</div>
							</tr>
						</table>
						<!--Default values
						font - arial : changed in custom.css
						size - 14px = 100%
						word spacing - 0
						line hieght - 23
						-->		
						<input name="check_post" id="check_post" type="hidden"/>
						<input id="preview-btn" name="preview" class="btn btn-lg btn-warning" type="button" value="Preview" onclick="changeFont()"/>
						<input id="go-btn"name="go" class="btn btn-lg btn-success" type="submit" value="Go"/>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- javascript -->
	<script src="Bootstrap/js/jquery 2.1.1.min.js"></script>
    <script src="Bootstrap/js/bootstrap.min.js"></script>
    <script src="Bootstrap/js/custom.js"></script>
	<script type="text/javascript">
		function changeFont(){
			//when clicked on preview btn, but never on an article btn
			if(document.getElementById('check_post').value == 0){
				var error = $("<div class='alert alert-danger' align='center' id='status-box'>"+
					"Please click on any of these article buttons atleast once to generate a paragraph."+
				"</div>");
				
				$(error)
				.insertAfter(".article_btns")
				.delay(2000)
				.fadeOut(3000);
			}
			//when clicked on preview btn after atleast once click on article btn
			else{
				var font = document.getElementById("font");
				var size = document.getElementById("size");
				var line_height = document.getElementById("line_height");
				var word_spacing = document.getElementById("word_spacing");
				
				para.style.fontSize = size.value+"%";
				para.style.fontFamily = font.value;
				para.style.lineHeight = line_height.value + "px";
				para.style.wordSpacing = word_spacing.value + "px";
			}
		}
		function changeFontRandomly(){
			var size = ["70", "80", "90", "100", "110", "120", "130", "140", "150", "160", "170", "180", "190", "200", "210", "220", "230"];
			var rand_no = Math.floor(Math.random() * (17));
			para.style.fontSize = size[rand_no]+"%";
			document.getElementById("size").value = size[rand_no];
			
			
			var font=["Arial", "Calibri", "Comic Sans MS", "Times New Roman", "Lucida Sans"];
			var rand_no = Math.floor(Math.random() * (5));
			para.style.fontFamily = font[rand_no];			
			document.getElementById("font").value = font[rand_no];
		}
			
		$(".alert").alert();
		window.setTimeout(function() {
			$(".alert").alert('close'); 
		}, 5000);
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