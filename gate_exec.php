<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$gate_bol = new gate_bol();
	$gate_info = new gate_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	
	// Select Gate for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['gate_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$gate_id = $_POST['gate_popup'];
			if($gate_id>0)
			{
				$row = $gate_bol->select_gate_byid($gate_id);
				$gate_code = clean_jscode($row['gate_code']);
				$gate_name = clean_jscode($row['gate_name']);
				$location_id = clean_jscode($row['location_id']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_gate_setup" name="frm_gate_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($gate_id>0) 
			{
				$return_str .='<input type="hidden" id="hidgateid" name="hidgateid" value="' . $gate_id . '" >';
			}
			$return_str .='			<label class="col-form-label col-md-4 required">ဂိတ်ကုတ်နံပါတ်</label>
											<div class="col-md-7">
												<input type="text" name="txtgatecode" id="txtgatecode" class="form-control" ';
			if($gate_id>0)
				$return_str .='value="'.$gate_code.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">ဂိတ်အမည်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtgatename" id="txtgatename" class="form-control" ';
			if($gate_id>0)
				$return_str .='value="'.$gate_name.'" />';
			else
				$return_str .=' />';
			$return_str .='		</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">တည်နေရာ အမည်
											</label>
											<div class="col-md-7">';
			if($gate_id>0)	
				$return_str .=get_location_optionstr("sellocationid", $location_id);
			else
				$return_str .=get_location_optionstr("sellocationid", "-1");
			$return_str .='		</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($gate_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_gate()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_gate()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['gate_id']) && isset($_POST['gate_code']) && isset($_POST['gate_name']))
		{
			$gate_id = clean_jscode($_POST['gate_id']);
			$gate_code = clean_jscode($_POST['gate_code']);
			$gate_name = clean_jscode($_POST['gate_name']);
			$location_id = clean_jscode($_POST['location_id']);

			if( $gate_code == '' )
				$error_arr[] = 'ဂိတ်ကုတ်နံပါတ် ထည့်ပေးပါရန်';
			elseif( $gate_name == '' )
				$error_arr[] = 'ဂိတ်အမည် ထည့်ပေးပါရန်';
			elseif( $location_id == '' )
				$error_arr[] = 'တည်နေရာအမည် ထည့်ပေးပါရန်';
			if($gate_id>0)
			{
				if( $gate_bol->check_duplicate_gate_name($gate_code, $gate_name, $location_id, $gate_id) )
					$error_arr[] = 'ဤဂိတ်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $gate_bol->check_duplicate_gate_name($gate_code, $gate_name, $location_id) )
					$error_arr[] = 'ဤဂိတ်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($gate_id>0)
				{
					$gate_info->set_gate_id($gate_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$gate_info->set_gate_code($gate_code);
				$gate_info->set_gate_name($gate_name);
				$gate_info->set_location_id($location_id);
				if ( $gate_bol->save_gate($gate_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'ဂိတ် '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'ဂိတ် '.$mess.' မအောင်မြင်ပါ';
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
		if( isset($_POST['delete_gate_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$gate_id = (int)($_POST['delete_gate_id']);
			if( $gate_bol->delete_gate($gate_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'ဂိတ် ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'ဂိတ် ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>