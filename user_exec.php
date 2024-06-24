<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$userbol = new userbol();
	$userinfo = new userinfo();
	$error_arr = array();
	
	/* 1. Root Admin, 2. Department, 3. User */
	$root_admin = 0;		
	if(isset($_SESSION ['YRDCFSH_ROOT_ADMIN']))
		$root_admin = $_SESSION ['YRDCFSH_ROOT_ADMIN'];
	// echo $root_admin;exit;
	
	$cri_str =" ";		
	// permission by usertype_department
	if ( $usertypeid != 0 )
	{
		if($root_admin == 1 || $root_admin == 2 || $root_admin == 3)
			$cri_str =" WHERE 1=1 AND department_id IN ($department_enables) AND is_root_admin >= $root_admin ";
	}
	// echo $cri_str;exit;
	
	$json_return_arr['sessionexpire'] = 0;	
	// Select user for pop up
	if(isset($_GET['authaction']) && $_GET['authaction']=='add')
	{  
		if( isset($_POST['user_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$return_str = '<div class="modal-body">		
									<form id="frmuserlist" name="frmuserlist" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">အသုံးပြုသူအမည်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtusername" id="txtusername" class="form-control" />
											</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">အီးမေးလ်</label>
											<div class="col-md-7">
												<input type="email" name="txtuseremail" id="txtuseremail" class="form-control">						
											</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">လျှို့ဝှက်နံပါတ်</label>
											<div class="col-md-7">
												<input type="password" name="txtpassword" id="txtpassword" class="form-control">
											</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">အသုံးပြုသူအမျိုးအစား</label>
											<div class="col-md-7">
												<select name="sel_user_type" id="sel_user_type" class="form-control">'. get_usertype_optionstr($cri_str) .'</select>
											</div>
										</div>	
										<div id="divprogress"></div>
									</form>
								</div>
								<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_user()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record
	if(isset($_GET['authaction']) && $_GET['authaction']=='add')
	{
		if( isset($_POST['new_username']) && isset($_POST['new_useremail']) && isset($_POST['new_userpassword']) && isset($_POST['new_usertype']) )
		{
			$user_name = clean_jscode($_POST['new_username']);
			$user_email = clean_jscode($_POST['new_useremail']);
			$userpassword = clean_jscode($_POST['new_userpassword']);
			$user_type_id = clean_jscode($_POST['new_usertype']);

			if( $user_name == '' )
				$error_arr[] = 'အမည် ထည့်ပေးပါရန်';
			else if( $user_email == '' )
				$error_arr[] = 'အီးမေးလ် ထည့်ပေးပါရန်';
			else if( trim($userpassword) == '' )
				$error_arr[] = 'လျှို့ဝှက်နံပါတ် ထည့်ပေးပါရန်';
			else if(!validate_password_rule(trim($userpassword)))
				$error_arr[] = 'လျှို့ဝှက်နံပါတ်သည် အနည်းဆုံးစာလုံးအရေအတွက်၈လုံးရှိရမည်၊ဂဏန်း ၁လုံး၊ အက္ခရာ ၁လုံးနှင့် အထူးစာလုံး !@$%^* များထဲမှ ၁လုံးပါရမည်';			
			else if( $user_type_id == '' )
				$error_arr[] = 'အမျိုးအစား ရွေးချယ်ပေးပါရန်';
			else if( $userbol->get_duplicate_useremail_count($user_email, 0) == FALSE)
				$error_arr[] = 'အသုံးပြုသူ အီးမေးလ် ရှိနှင့်ပြီးဖြစ်သည်';

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				$userinfo->set_user_name($user_name);
				$userinfo->set_user_email($user_email);
				$userinfo->set_password(trim($userpassword));
				$userinfo->set_user_type_id($user_type_id);
				if ( $userbol->save_user($userinfo) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'အသစ်ထည့်ခြင်း အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'အသစ်ထည့်ခြင်း မအောင်မြင်ပါ';
				}
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-warning';
				$json_return_arr['message'] = implode('<br>', $error_arr);
			}
		}
	}
	
	// edit popup
	if(isset($_GET['authaction']) && $_GET['authaction']=='edit')
	{
		if( isset($_POST['edit_user_id']) )
		{
			$json_return_arr['sessionexpire'] = 0;
			$user_id = $_POST['edit_user_id'];
			$row = $userbol->get_user_byid($user_id);
			$username = clean_jscode($row['user_name']);
			$user_email = clean_jscode($row['user_email']);
			$user_type_id = clean_jscode($row['user_type_id']);
			$edit_user_popup = '<div class="modal-body">		
								<form id="frmuserlist" name="frmuserlist" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
									<div id="alert_msg" class="error"></div>
									<div class="form-group row">
										<input type="hidden" id="hiduserid" name="hiduserid" value="' . $user_id . '" >
										<label class="col-form-label col-md-4 required">အသုံးပြုသူအမည်</label>
										<div class="col-md-7">
											<input type="text" name="txtusername" id="txtusername" class="form-control" value="' . htmlspecialchars($username) . '"  />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-md-4 required">အီးမေးလ်</label>
										<div class="col-md-7">
											<input type="email" name="txtuseremail" id="txtuseremail" class="form-control" value="' . $user_email . '" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-md-4 required">အသုံးပြုသူအမျိုးအစား</label>
										<div class="col-md-7">
											<select name="sel_user_type" id="sel_user_type"class="form-control" >'. get_usertype_optionstr($cri_str, $user_type_id) .'</select>
										</div>
									</div>
									<div id="divprogress"></div>
								</form>
								</div>
								<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_user()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			$json_return_arr['popupdata'] = $edit_user_popup;
		}
	}
	
	// Update Data
	if(isset($_GET['authaction']) && $_GET['authaction']=='edit')
	{
		if( isset($_POST['update_user_id']) && isset($_POST['update_username']) && isset($_POST['update_useremail']) && isset($_POST['update_usertype']))
		{
			$user_id = clean_jscode($_POST['update_user_id']);
			$username =  clean_jscode($_POST['update_username']);
			$user_email = clean_jscode($_POST['update_useremail']);
			$user_type_id = clean_jscode($_POST['update_usertype']);
			
			if( $username == '' )
				$error_arr[] = 'အမည် ထည့်ပေးပါရန်';
			else if( $user_email == '' )
				$error_arr[] = 'အီးမေးလ် ထည့်ပေးပါရန်';
			else if( $user_type_id == '' )
				$error_arr[] = 'အမျိုးအစား ရွေးချယ်ပေးပါရန်';
			else if( $userbol->get_duplicate_useremail_count($user_email, $user_id) == FALSE)
				$error_arr[] = 'အသုံးပြုသူ အီးမေးလ် ရှိနှင့်ပြီးဖြစ်သည်';

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				$userinfo->set_user_id($user_id);
				$userinfo->set_user_name($username);
				$userinfo->set_user_email($user_email);
				$userinfo->set_user_type_id($user_type_id);
				if ( $userbol->save_user($userinfo) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'ပြင်ဆင်ခြင်း အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'ပြင်ဆင်ခြင်း မအောင်မြင်ပါ';
				}
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-warning';
				$json_return_arr['message'] = implode('<br>', $error_arr);
			}
		}
	}
	
	// Delete User
	if(isset($_GET['authaction']) && $_GET['authaction']=='delete')
	{
		if( isset($_POST['delete_user_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$delete_user_id = (int)($_POST['delete_user_id']);
			if( $userbol->delete_user($delete_user_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}
	
	// Change User Status
	if(isset($_GET['authaction']) && $_GET['authaction']=='change_status')
	{
		if(isset ($_POST['change_user_id']) && isset ($_POST['change_user_status']))
		{
			if($userbol->change_user_status(trim($_POST ['change_user_id']) , trim($_POST ['change_user_status'])))
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'ပြောင်းလဲခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'ပြောင်းလဲခြင်း မအောင်မြင်ပါ';
			}
		}
	}
	
	// Change Password
	if(isset($_GET['authaction']) && $_GET['authaction']=='change_password')
	{
		if( isset($_POST['user_id']) && isset($_POST['update_user_password']) && isset($_POST['confirm_user_password']) )
		{
			$user_id = clean($_POST['user_id']);
			$new_user_password = clean(clean_jscode($_POST['update_user_password']));
			$confirm_user_password = clean(clean_jscode($_POST['confirm_user_password']));
			
			if( trim($new_user_password) == '' )
				$error_arr[] = 'လျှို့ဝှက်နံပါတ် ထည့်ပေးပါရန်';
			else if(!validate_password_rule(trim($new_user_password)))
				$error_arr[] = 'လျှို့ဝှက်နံပါတ်သည် အနည်းဆုံးစာလုံးအရေအတွက်၈လုံးရှိရမည်၊ဂဏန်း ၁လုံး၊ အက္ခရာ ၁လုံးနှင့် အထူးစာလုံး !@$%^* များထဲမှ ၁လုံးပါရမည်';			
			else if( trim($confirm_user_password) == '' )
				$error_arr[] = 'အတည်ပြုစကားဝှက် ထည့်ပေးပါရန်!';
			else if( trim($confirm_user_password) != trim($new_user_password))
				$error_arr[] = 'လျှို့ဝှက်နံပါတ် မကိုက်ညီပါ!';
			
			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				if( $userbol->change_password_byforce($user_id, trim($new_user_password)) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'ပြောင်းလဲခြင်း အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'ပြောင်းလဲခြင်း မအောင်မြင်ပါ';
				}
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-warning';
				$json_return_arr['message'] = implode('<br>', $error_arr);
			}
		}
	}
	
	/* if ( isset( $_POST['logoutdate']) )
	{
		if ( $userbol->update_logout_date() )
			$json_return_arr['success'] = 1;
		else
			$json_return_arr['success'] = 0;
	} */
	
	if ( count($json_return_arr) == 0 )
		header("location: index.php");
	else
	{
		echo json_encode($json_return_arr);
		exit();
	}
?>