<?php
	//session_start();
	$movepath = '';
	$pgTitle = 'လျှို့ဝှက်နံပါတ်ပြင်ဆင်ခြင်း';
	$currentPg = 'Change Password';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	include "adminauth.php";
	$userbol = new userbol();
	$errors_arr = array();

	 if( $user_result = $userbol->get_user_byid($userid) )
	{
		$user_name = $user_result['user_name'];
		$user_email = $user_result['user_email'];
		$oldpassword = $user_result['password'];
	}

	if(isset($_POST['btnconfirm']))
	{
		$old_password = clean(clean_jscode($_POST['txtoldpassword']));
		$new_password = clean(clean_jscode($_POST['txtnewpassword']));
		$confirm_password = clean(clean_jscode($_POST['txtconfirmpassword']));
		
		if(trim($old_password) == '' )
			$errors_arr[]='လျှို့ဝှက်နံပါတ်အဟောင်းထည့်ရန်!';
		if(trim($new_password) == '' )
			$errors_arr[]='လျှို့ဝှက်နံပါတ်အသစ်ထည့်ရန်!';
		elseif(!validate_password_rule(trim($new_password)))
			$errors_arr[] = 'လျှို့ဝှက်နံပါတ်သည် အနည်းဆုံးစာလုံးအရေအတွက်၈လုံးရှိရမည်၊ဂဏန်း ၁လုံး၊ အက္ခရာ ၁လုံးနှင့် အထူးစာလုံး !@$%^* များထဲမှ ၁လုံးပါရမည်';		
		if(trim($confirm_password) == '' )
			$errors_arr[]='အတည်ပြုလျှို့ဝှက်နံပါတ်ထည့်ရန်!';
		if(trim($confirm_password) != trim($new_password))
			$errors_arr[]='အတည်ပြုလျှို့ဝှက်နံပါတ်သည် လျှို့ဝှက်နံပါတ်အသစ်နှင့် တူညီစွာရိုက်ထည့်ပေးပါရန်!';
		
		if(!count($errors_arr))
		{
			$result = $userbol->change_password(trim($old_password), trim($confirm_password));
			if($result)
			{
				$errormsg_arr[] = "လျှို့ဝှက်နံပါတ် ပြင်ဆင်ခြင်းအောင်မြင်သည်!";
				$_SESSION['ERRMSG_ARR'] = $errormsg_arr;
				$_SESSION['YRDCFSH_REQUIRE_CHANGE_PASSWORD'] = 0;
				session_write_close();
				header("location:index.php");
			}
			else
			{
				$errormsg_arr[] = "လျှို့ဝှက်နံပါတ်အဟောင်း မမှန်ကန်ပါ!";
				$_SESSION['ERRMSG_ARR'] = $errormsg_arr;
				session_write_close();
				header("location:index.php");
			}
		}
	}
	include 'admin_header.php';
?>
<script language="javascript">
	jQuery(document).ready(function()
	{
		addvalidate();
		$.validator.addMethod("checkpassword", function(value, element) {
				  return  validate_password_rule(value);
			}, "လျှို့ဝှက်နံပါတ်သည် အနည်းဆုံးစာလုံးအရေအတွက်၈လုံးရှိရမည်၊ဂဏန်း ၁လုံး၊ အက္ခရာ ၁လုံးနှင့် အထူးစာလုံး !@$%^* များထဲမှ ၁လုံးပါရမည်");
				
		jQuery("#changepasswordForm").submit(function(e)
		{
			if(jQuery('#changepasswordForm').valid())
			{
				getloading();
				jQuery("#changepasswordForm").unbind('submit').submit();
				return true;
			}
			//if(e.preventDefault) e.preventDefault(); else e.returnValue = false;  //cancel submit
		});
	});
	
	function addvalidate()
	{
		$('#changepasswordForm').validate(
		{
			'rules':{
						'txtoldpassword':{'required':true},
						'txtnewpassword':{'required':true,checkpassword:true},
						'txtconfirmpassword':{'required':true,'equalTo':'#txtnewpassword'}
					},
			'messages':{
						'txtoldpassword':{'required':'လျှို့ဝှက်နံပါတ်အဟောင်းထည့်ရန်!'},
						'txtnewpassword':{'required':'လျှို့ဝှက်နံပါတ်အသစ်ထည့်ရန်!'},
						'txtconfirmpassword':{'required':'အတည်ပြုလျှို့ဝှက်နံပါတ်ထည့်ရန်!','equalTo':'အတည်ပြုလျှို့ဝှက်နံပါတ်နှင့် လျှို့ဝှက်နံပါတ်အသစ်အား တူညီစွာရိုက်ထည့်ပေးပါရန်!'},
			}
		});
	}
</script>
<?php show_errors_message($errors_arr); ?>

<form name="changepasswordForm" action="" id="changepasswordForm" method="post" class="form-material form-horizontal">
	<br>
	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5 required">လျှို့ဝှက်နံပါတ်အဟောင်း</label>
		<div class="col-md-5 col-sm-6">
			<input type="password" name="txtoldpassword" id="txtoldpassword" class="form-control" />
		</div>
	</div>
	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5 required">လျှို့ဝှက်နံပါတ်အသစ်</label>
		<div class="col-md-5 col-sm-6">
			<input type="password" name="txtnewpassword" id="txtnewpassword" class="form-control"/>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5 required">အတည်ပြုလျှို့ဝှက်နံပါတ်</label>
		<div class="col-md-5 col-sm-6">
			<input type="password" name="txtconfirmpassword" id="txtconfirmpassword" class="form-control"/>
		</div>
	</div>
	<div class="form-group row">
		<div class="offset-md-4 offset-sm-5 col">
			<input class="btn btn-success" type="submit" name="btnconfirm" value="ပြင်ဆင်မည်"/>
			<input  class="btn btn-outline-secondary" type="button" name="btncancel" value="မပြင်ဆင်ပါ" onClick="window.location='index.php'"/>
		</div>
	</div>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	include 'admin_footer.php';
?>