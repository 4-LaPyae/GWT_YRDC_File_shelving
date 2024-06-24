<?php
	 if(!isset($movepath, $pgTitle, $currentPg)){
		$movepath = '';
		$pgTitle = ''; $currentPg = '';
	}
	$login = $username = $logout = 	$changepassword = "";
	if (isset ($_SESSION ['YRDCFSH_LOGIN_NAME']))
	{
		$changepassword = '<a href="changepassword.php" class="dropdown-item text-gray-lter"><svg class="icon i-xs align-middle mr-2 text-uec-lter"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-settings" /></svg> Account Setting</a>';

		$logout = '<a href="admin_logout.php" class="dropdown-item text-gray-lter"><svg class="icon i-xs align-middle mr-2 text-uec-lter"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-logout" /></svg> Logout</a>';

		$username = '<span class="hidden-xs-down">Welcome, <b>'. $_SESSION ['YRDCFSH_LOGIN_NAME'].'</b></span>';
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
	<title>YRDC - File Shelving System</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="shortcut icon" type="icon/image" href="<?php echo $movepath; ?>images/logo.jpg">

	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Hind:300,400,500,600,700|Droid+Serif|Roboto+Condensed:300,400,700|Poppins" />

	<link href="<?php echo $movepath; ?>css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/animate.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/bootstrap-float-label.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/dataTables.bootstrap4.min.css?v1.10.16" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/responsive.bootstrap4.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/fixedColumns.bootstrap4.min.css?v3.2.4" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/tempusdominus-bootstrap-4.css?v5.0.0-alpha8" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/fileinput.min.css?v4.4.7" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/jquery.scombobox.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/multiple-select.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/theme.min.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/main.css" rel="stylesheet">

	<script src="<?php echo $movepath; ?>js/jquery.min.js?v3.2.1" crossorigin="anonymous"></script>
	<script src="<?php echo $movepath; ?>js/fileinput.min.js?v4.4.7"></script>
	<script src="<?php echo $movepath; ?>js/theme.min.js"></script>
	<script src="<?php echo $movepath; ?>js/general.js"></script>
	<script> var movepath = '<?php echo $movepath; ?>'; </script>

	<script type="text/javascript">
		$(document).ready(function ()
		{
			//Remove Append Modal Element from body after Close.
			$(document).on('hidden.bs.modal', '.modaldiv', function () {
				$(this).remove();
			});

			// add validation of english & myanmar number only
			var err_msg1;
			jQuery.validator.addMethod("unicode_number", function(value, element)
			{
				var flag = true;
				var return_arr = [];

				if( value )
				{
					for( i = 0; i < value.length; i++ )
					{
						var c = value.charCodeAt(i);
						if( c == 46 || ( c>=48 && c<=57 ) || ( c>=4160 && c<=4169 ) )  //digit ascii code range for english and myanmar (mm3 and zawgyi use same code range)
							var result = true;
						else
							var result = false;

						return_arr.push(result);
					}

					if( jQuery.inArray(false, return_arr) != -1 )
					{
						err_msg1 = "မြန်မာ (သို့) အင်္ဂလိပ်ဂဏန်းသာထည့်ပေးပါရန်!";
						var flag = false;
					}
				}

				return flag;
			}, function(){ return err_msg1 });

			// add validation of english words only
			var err_msg2;
			jQuery.validator.addMethod("english_word", function(value, element)
			{
				var flag = true;

				if( value )
				{
					if( ! /^([a-zA-Z0-9\-\/\\])+$/i.test(value) )
					{
						err_msg2 = 'အင်္ဂလိပ်စာလုံးသာ ထည့်ပေးပါရန်!';
						var flag = false;
					}
				}

				return flag;
			}, function(){ return err_msg2 });
		});
	</script>
</head>
<body class="bg-faded"><!-- flex-sm-row d-flex -->
	<header id="header" class="bg-darkblue sticky-top">
		<div class="container py-2">
			<div class="row no-gutters">
				<div class="col-3 col-lg-7">
					<div class="navbar-brand mr-0" href="#">
						<img src="<?php echo $movepath; ?>images/logo.jpg" alt="Union Election Commision" class="brand-logo mr-3">
						<h1 class="h3 align-middle d-inline-block brand-title text-white my-2 font-weight-bold text-uppercase hidden-md-down"><small class="d-block mb-2 pb-1">တိုင်းဒေသကြီးစည်ပင်သာယာရေးအဖွဲ့</small>
						File Shelving System</h1>
					</div>
				</div>
				<div class="col-9 col-lg-5">
					<ul class="navbar-nav navbar-topright flex-row float-right pt-3 text-success">
						<li><a class="d-block text-success">v1.0 Beta</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle text-success u-l-none d-block text-truncate" data-toggle="dropdown" href="#" aria-expanded="false">
								<svg class="icon i-md mr-0 mr-sm-2 align-middle text-white" style="opacity:.9"><use xlink:href="<?php echo $movepath; ?>js/symbol-defs.svg#icon-avatar-circle" /></svg>
								<?php
									if (isset ($_SESSION ['YRDCFSH_LOGIN_NAME'])) echo "$username";
									else echo "$username";
								?>
							</a>
							<div class="dropdown-menu dropdown-menu-right dropdown-profile animated bounceInDown mt-3 rounded-0">
								<?php
									if (isset ($_SESSION ['YRDCFSH_LOGIN_NAME']))
										echo "$changepassword $logout";
									else
										echo "$changepassword $logout";
								?>
							</div>
							<!-- /.dropdown-user -->
						</li>			
					</ul>
					<button class="navbar-toggler navbar-toggler-right border-0 hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
						<svg class="icon i-sm text-uec"><use xlink:href="<?php echo $movepath; ?>js/symbol-defs.svg#icon-bar" /></svg>
					</button>
				</div>
			</div>
		</div>
		<div class="bg-gray main-nav-wrapper">
			<div class="container">
				<nav class="navbar navbar-toggleable-md navbar-light p-0 main-nav">
					<div class="navbar-collapse collapse" id="navbarNav" aria-expanded="false">
						<ul class="navbar-nav pt-4 pt-lg-0 pb-2 pb-md-0">
							<?php
								if (isset ($_SESSION ['YRDCFSH_LOGIN_TYPE_ID']))
								{
									$usertypeid = $_SESSION ['YRDCFSH_LOGIN_TYPE_ID'];
									echo get_user_menu($movepath,$usertypeid);
								}
								else
								{
									echo '<li class="nav-item"><a href="admin_login.php" class="nav-link">Login</a></li>';
								}
							?>
						</ul>
					</div>
				</nav>
			</div>
		</div>
	</header>
	<div class="bg-white py-2 page-head">
		<div class="container">
			<div class="row">
				<div class="col-md-7">
					<h3 class="py-2 pt-3 m-0 text-gray-dker pg-title text-truncate"><?php echo $pgTitle; ?></h3>
				</div>
				<div class="col-md-5 text-md-right">
					<ol class="breadcrumb no-bg mb-0 pt-1 px-0 pb-3 pt-md-3 text-truncate">
						<li class="breadcrumb-item"><a href="<?php echo $movepath; ?>index.php" class="">Dashboard</a></li>
						<li class="breadcrumb-item active"><?php echo $currentPg; ?></li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<section class="container-fluid py-3">
		<div class="bg-white b p-3 content">
			<?php
				$securitybol = new securitybol();
				$status_count = $securitybol->check_invalid_log();
				if( $status_count > 0 )
					echo "<span class='securitywarning'>( $status_count ) invalid records found ! Please contact website administrator.</span>";
			?>


