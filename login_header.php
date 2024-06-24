<?php
	if(!isset($movepath))
		$movepath = '';
	
	$login = $username = $logout = "";
	if( isset($_SESSION['YRDCFSH_LOGIN_NAME']) )
	{
		$logout = '<a href="admin_logout.php" alt="Logout" >ထွက်မည်</a>';
		$username = '<span>Welcome&nbsp; '. $_SESSION ['YRDCFSH_LOGIN_NAME'].' | </span>';
	}
	else
	{
		$login = '<a href="admin_login.php" class="last">ဝင်မည်</a>';
	}
?>

<!DOCTYPE HTML>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		<title>UEC - Union Election Commision Myanmar</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="shortcut icon" type="icon/image" href="<?php echo $movepath; ?>images/favicon.ico">
		
		<!-- Le styles -->
		<link rel="stylesheet" type="text/css" href="<?php echo $movepath; ?>style/bootstrap.css" media="screen, projection" /> 
		<link rel="stylesheet" type="text/css" href="<?php echo $movepath; ?>style/custom.css" media="screen, projection" />
		<link rel="stylesheet" type="text/css" href="<?php echo $movepath; ?>style/jquery-ui-1.8.2.custom.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $movepath; ?>style/tablegrid_ui.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $movepath; ?>style/jquery.lightbox-0.5.css" media="screen"/>

		<script src="<?php echo $movepath; ?>js/jquery.js"></script>		
		<script src="<?php echo $movepath; ?>js/jquery.validate.min.js"></script>
		<script src="<?php echo $movepath; ?>js/jquery.elastic.js"></script>
		<script src="<?php echo $movepath; ?>js/general.js"></script>
		<script src="<?php echo $movepath; ?>js/system_general.js"></script>
		<script src="<?php echo $movepath; ?>js/screen.js"></script>
		<script src="<?php echo $movepath; ?>js/jquery.cookie.js"></script>
		<script src="<?php echo $movepath; ?>js/jquery.dataTables.min.js"></script>		
		<script src="<?php echo $movepath; ?>js/dataTables.fnStandingRedraw.js"></script>		
		<script src="<?php echo $movepath; ?>js/json2.js"></script>
		<script src="<?php echo $movepath; ?>js/jquery.uploadify-3.1.js"></script>
		<style type="text/css">
			#maincontent{ overflow: visible; }
			input{ color: #333333 !important; }
		</style>
	</head>
	<body>
		<div id="wrapper">
			<div class="login_header-wrap">				
				<div class="login_logo">
					<img src="<?php echo $movepath ?>images/YCDC-LOGO.png" />
				</div>		
				<div class="login_title">
					<img src="images/title.png" /> <br />
					<img src="images/title_branch.png" />
				</div>			
			</div>

			<div id="maincontent">