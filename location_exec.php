<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$location_bol = new location_bol();
	$location_info = new location_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	
	// Select Location for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['location_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$location_id = $_POST['location_popup'];
			if($location_id>0)
			{
				$row = $location_bol->select_location_byid($location_id);
				$location_code = clean_jscode($row['location_code']);
				$location_name = clean_jscode($row['location_name']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_location_setup" name="frm_location_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($location_id>0) 
			{
				$return_str .='<input type="hidden" id="hidlocationid" name="hidlocationid" value="' . $location_id . '" >';
			}
			$return_str .='			<label class="col-form-label col-md-4 required">တည်နေရာကုတ်နံပါတ်</label>
											<div class="col-md-7">
												<input type="text" name="txtlocationcode" id="txtlocationcode" class="form-control" ';
			if($location_id>0)
				$return_str .='value="'.$location_code.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">တည်နေရာအမည်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtlocationname" id="txtlocationname" class="form-control" ';
			if($location_id>0)
				$return_str .='value="'.$location_name.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($location_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_location()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_location()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['location_id']) && isset($_POST['location_code']) && isset($_POST['location_name']))
		{
			$location_id = clean_jscode($_POST['location_id']);
			$location_code = clean_jscode($_POST['location_code']);
			$location_name = clean_jscode($_POST['location_name']);

			if( $location_code == '' )
				$error_arr[] = 'တည်နေရာကုတ်နံပါတ် ထည့်ပေးပါရန်';
			elseif( $location_name == '' )
				$error_arr[] = 'တည်နေရာအမည် ထည့်ပေးပါရန်';
			if($location_id>0)
			{
				if( $location_bol->check_duplicate_location_name($location_code, $location_name, $location_id) )
					$error_arr[] = 'ဤတည်နေရာစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $location_bol->check_duplicate_location_name($location_code, $location_name) )
					$error_arr[] = 'ဤတည်နေရာစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($location_id>0)
				{
					$location_info->set_location_id($location_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$location_info->set_location_code($location_code);
				$location_info->set_location_name($location_name);
				if ( $location_bol->save_location($location_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'တည်နေရာ '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'တည်နေရာ '.$mess.' မအောင်မြင်ပါ';
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
		if( isset($_POST['delete_location_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$location_id = (int)($_POST['delete_location_id']);
			if( $location_bol->delete_location($location_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'တည်နေရာ ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'တည်နေရာ ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>