<?php
	$movepath = '';
	require_once('autoload.php');
	require_once($movepath . 'library/reference.php');
	session_start() ;
	$errors = array();
	
	//echo encryptNET3DES(YRDCFSH_ENCRYPTION_KEY, YRDCFSH_ENCRYPTION_IV, 'global123');exit;
	// echo decryptNET3DES(YRDCFSH_ENCRYPTION_KEY, YRDCFSH_ENCRYPTION_IV, 'hOu9QirJLSIKpSBXuAKmIA==');exit;
	
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

	if(isset($_POST['btnlogin']))
	{
		$userbol = new userbol() ;
		$loginemail = clean($_POST['txtemail']);
		$loginpassword = clean($_POST['txtpassword']);

		if($loginemail == "")
			$errors[] = 'ဝင်ရောက်ခွင့် အီးမေးလ်ထည့်ရန်!';
		else if( !isemail($loginemail) )
			$errors[] = 'အီးမေးလ်ပုံစံမှန်ကန်စွာ ထည့်ပေးပါရန်!';

		if($loginpassword == "")
			$errors[] = 'လျှို့ဝှက်နံပါတ်ထည့်ရန်!';

		if(count($errors) == 0)
		{
			if($check_user = $userbol->checkuserlogin($loginemail, $loginpassword))
			{
				//$securitybol = new securitybol();
				//$change_user = $securitybol->check_and_change_invalid_record("user", " AND user_id = " . $check_user['user_id'], $_SERVER['PHP_SELF']);
				
				if($check_user['is_active'] == 2 /*  || $change_user */)
				{
					$errors[] = "This user's data is wrong. Please contact with site administrator !";
				}
				else if($check_user['is_active'] == 1)
				{
					$_SESSION['YRDCFSH_LOGIN_ID'] = $check_user['user_id'];
					$_SESSION['YRDCFSH_LOGIN_NAME'] = $check_user['user_name'];
					$_SESSION['YRDCFSH_LOGIN_TYPE_ID'] = $check_user['user_type_id'];
					$_SESSION['YRDCFSH_LOGIN_EMAIL'] = $loginemail;
					$_SESSION['YRDCFSH_ROOT_ADMIN'] = $check_user['is_root_admin'];
					$_SESSION['YRDCFSH_REQUIRE_CHANGE_PASSWORD'] = $check_user['require_changepassword'];

					$user_id = 0;
					if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
					{
						$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
						header("location: index.php");
						session_write_close();
					}
					//save eventlog//
					$type = 'Log In';
					$table ='user';
					$user = $email = '';
					if( isset($_SESSION['YRDCFSH_LOGIN_NAME']) )
						$user = $_SESSION['YRDCFSH_LOGIN_NAME'];
					if( isset($_SESSION['YRDCFSH_LOGIN_EMAIL']) )
						$email = $_SESSION['YRDCFSH_LOGIN_EMAIL'];
					$filter = "user_id=$user_id";
					$description = $user. ' logged in by using '.$email;
					$eventloginfo = new eventloginfo();
					$eventloginfo->setuser_id($user_id);
					$eventloginfo->setaction_type($type);
					$eventloginfo->settable_name($table);
					$eventloginfo->setfilter($filter);
					$eventloginfo->setdescription($description);
					$eventlogbol = new eventlogbol();
					$eventlogbol->save_eventlog($eventloginfo);
					//end//
				}
				else
					$errors[] = 'အသုံးပြုခွင့်မရှိပါ!';
			}
			else
				$errors[] = 'ဝင်ရောက်ခွင့် အီးမေးလ်နှင့် လျှို့ဝှက်နံပါတ်မကိုက်ညီပါ!';
		}
	}
	//include 'login_header.php';
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

	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Droid+Serif|Droid+Sans|Roboto+Condensed:300,400,700" />
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/earlyaccess/khyay.css" />
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/earlyaccess/myanmarsanspro.css" />
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/earlyaccess/notosansmyanmar.css" />

	<link href="<?php echo $movepath; ?>css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/animate.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/bootstrap-float-label.css" rel="stylesheet">
	<link href="<?php echo $movepath; ?>css/main.css" rel="stylesheet">

	<script src="<?php echo $movepath; ?>js/jquery.min.js" crossorigin="anonymous"></script>
	<script src="<?php echo $movepath; ?>js/general.js"></script>

	<script>
		$(function () {
			AddValidation();
		});

		function AddValidation()
		{
			jQuery("#loginForm").validate(
			{
				'rules':{
					'txtemail':{'required':true,'email':true},
					'txtpassword':{'required':true}
				},
				'messages': {
					'txtemail':{'required':'ဝင်ရောက်ခွင့် အီးမေးလ်ဖြည့်စွက်ပေးပါရန်!','email':'အီးမေးလ်ပုံစံမှန်ကန်စွာ ဖြည့်စွက်ပေးပါရန်!'},
					'txtpassword':{'required':'လျှို့ဝှက်နံပါတ် ဖြည့်စွက်ပေးပါရန်!'}
				},
				errorLabelContainer: "#fileerror",
				errorElement:"span"
				//wrapper: "li"
			})

			jQuery("#loginForm").click();
		}
	</script>
</head>
<body class="bg-faded">
	<div class="container">
		<h3 class="h5 text-center mt-5 mb-4 pb-2 text-uppercase text-serif">Login to Your Account</h3>
		<div class="row">
			<div class="col-sm-7 col-lg-5 mx-auto">
				<div class="bg-white p-4 b mb-4">
					<div class="text-center">
						<img src="<?php echo $movepath; ?>images/logo.jpg" alt="Union Election Commision" class="brand-logo">
						<h1 class="h5 brand-title text-uec mt-2">တိုင်းဒေသကြီးစည်ပင်သာယာရေးအဖွဲ့
						<small class="d-block text-md mt-2">File Shelving System</small></h1>
					</div>
					<form action="" method="POST" name="loginForm" id="loginForm" class="mt-5 mb-2 login-form">
						<div class="form-group has-float-label">
							<label for="txtemail">ဝင်ရောက်ခွင့် အီးမေးလ်</label>
							<input type="email" class="form-control" name="txtemail" id="txtemail" placeholder="Email Address" aria-describedby="email-help" pattern="/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/" required>
						</div>
						<div class="form-group has-float-label">
							<label for="txtpassword">လျှို့ဝှက်နံပါတ်</label>
							<input type="password" class="form-control" name="txtpassword" id="txtpassword" placeholder="Password" required>
						</div>
						<button type="submit" class="btn btn-primary btn-block mb-2" name="btnlogin" id="btnlogin">ဝင်မည်</button>
						<div id="errors" class="error">
							<?php
								if(count($errors))
								{
									echo implode('<br>', $errors);
								}
							?>
						</div>
					</form>
				</div>
			</div>
		</div>
		<p class="text-center text-gray text-sm"><?php echo date("Y") ?> &copy; YRDC. All rights reserved.</p>
	</div>
	
	<script src="<?php echo $movepath; ?>js/tether.min.js" crossorigin="anonymous"></script>
	<script src="<?php echo $movepath; ?>js/bootstrap.min.js"></script>
	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<script src="<?php echo $movepath; ?>js/ie10-viewport-bug-workaround.js"></script>
	<!-- <script src="<?php echo $movepath; ?>js/jquery.easing.min.js?v1.9.2"></script>
	<script src="<?php echo $movepath; ?>js/jquery.easeScroll.js"></script> -->
	<script src="<?php echo $movepath; ?>js/jquery.validate.min.js"></script>
	<script src="<?php echo $movepath; ?>js/system_general.js"></script>
	<script src="<?php echo $movepath; ?>js/jquery.cookie.js"></script>
	<!-- <script src="<?php echo $movepath; ?>js/json2.js"></script> -->
</body>
</html>