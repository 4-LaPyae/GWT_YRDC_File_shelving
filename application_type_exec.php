<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$application_type_bol = new application_type_bol();
	$application_type_info = new application_type_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	
	// Select Application Type for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['application_type_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$application_type_id = $_POST['application_type_popup'];
			if($application_type_id>0)
			{
				$row = $application_type_bol->select_application_type_byid($application_type_id);
				$application_type_code = clean_jscode($row['application_type_code']);
				$application_type_name = clean_jscode($row['application_type_name']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_application_type_setup" name="frm_application_type_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($application_type_id>0) 
			{
				$return_str .='<input type="hidden" id="hidapplication_typeid" name="hidapplication_typeid" value="' . $application_type_id . '" >';
			}
			$return_str .='			<label class="col-form-label col-md-4 required">လုပ်ငန်းအမျိုးအစားကုတ်နံပါတ်</label>
											<div class="col-md-7">
												<input type="text" name="txtapplication_typecode" id="txtapplication_typecode" class="form-control" ';
			if($application_type_id>0)
				$return_str .='value="'.$application_type_code.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">လုပ်ငန်းအမျိုးအစားအမည်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtapplication_typename" id="txtapplication_typename" class="form-control" ';
			if($application_type_id>0)
				$return_str .='value="'.$application_type_name.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($application_type_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_application_type()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_application_type()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['application_type_id']) && isset($_POST['application_type_code']) && isset($_POST['application_type_name']))
		{
			$application_type_id = clean_jscode($_POST['application_type_id']);
			$application_type_code = clean_jscode($_POST['application_type_code']);
			$application_type_name = clean_jscode($_POST['application_type_name']);

			if( $application_type_code == '' )
				$error_arr[] = 'လုပ်ငန်းအမျိုးအစားကုတ်နံပါတ် ထည့်ပေးပါရန်';
			elseif( $application_type_name == '' )
				$error_arr[] = 'လုပ်ငန်းအမျိုးအစားအမည် ထည့်ပေးပါရန်';
			if($application_type_id>0)
			{
				if( $application_type_bol->check_duplicate_application_type_name($application_type_code, $application_type_name, $application_type_id) )
					$error_arr[] = 'ဤလုပ်ငန်းအမျိုးအစားစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $application_type_bol->check_duplicate_application_type_name($application_type_code, $application_type_name) )
					$error_arr[] = 'ဤလုပ်ငန်းအမျိုးအစားစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($application_type_id>0)
				{
					$application_type_info->set_application_type_id($application_type_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$application_type_info->set_application_type_code($application_type_code);
				$application_type_info->set_application_type_name($application_type_name);
				if ( $application_type_bol->save_application_type($application_type_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'လုပ်ငန်းအမျိုးအစား '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'လုပ်ငန်းအမျိုးအစား '.$mess.' မအောင်မြင်ပါ';
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
	
	// Delete 
	if(isset($_GET['authaction']) && $_GET['authaction']=='delete')
	{
		if( isset($_POST['delete_application_type_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$application_type_id = (int)($_POST['delete_application_type_id']);
			if( $application_type_bol->delete_application_type($application_type_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'လုပ်ငန်းအမျိုးအစား ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'လုပ်ငန်းအမျိုးအစား ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>