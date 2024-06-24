<?php		
	$movepath = '';
	require_once ("autoload.php");
	require_once($movepath . 'library/reference.php');	
	$is_ajaxcall = true;
	require_once ("adminauth.php");
	
	$usertypebol = new usertypebol();
	$error = array();
	
	if(isset($_GET['authaction']) && $_GET['authaction']=='delete')
	{
		if(isset($_POST['delete_user_type_id']))
		{
			$json_return_arr['sessionexpire'] = 0;
			$user_type_id = $_POST['delete_user_type_id'];
			if( is_used_in_table('user', " user_type_id = $user_type_id") )
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'ဤအသုံးပြုသူအမျိုးအစားအား အသုံးပြုထားပါသဖြင့် ပယ်ဖျက်၍မရနိုင်ပါ!';
			}
			else if($usertypebol->delete_user_type($user_type_id))
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-warning';
				$json_return_arr['message'] = implode('<br>', $error);
			}
			echo json_encode($json_return_arr);
			exit();
		}
	}
?>