<?php
	$movepath = '';
	require_once($movepath.'library/reference.php');
	require_once('autoload.php');
	$customer_bol = new customer_bol();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	if(isset($_GET['authaction']) && $_GET['authaction']=='delete')
	{
		if(isset ($_POST ['delete_customer_id']))
		{
			$customer_id = (int)($_POST['delete_customer_id']);
			if($customer_bol->delete_customer($customer_id))
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
	echo json_encode($json_return_arr);
	exit();
?>