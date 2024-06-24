<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$division_bol = new division_bol();
	$division_info = new division_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	
	// Select Division for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{ 
		if( isset($_POST['division_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$division_id = $_POST['division_popup'];
			if($division_id>0)
			{
				$row = $division_bol->select_division_byid($division_id);
				$division_code = clean_jscode($row['division_code']);
				$division_name = clean_jscode($row['division_name']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_division_setup" name="frm_division_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($division_id>0) 
			{
				$return_str .='<input type="hidden" id="hiddivisionid" name="hiddivisionid" value="' . $division_id . '" >';
			}
			$return_str .='			<label class="col-form-label col-md-4 required">တိုင်း/ပြည်နယ်ကုတ်နံပါတ်</label>
											<div class="col-md-7">
												<input type="text" name="txtdivisioncode" id="txtdivisioncode" class="form-control" ';
			if($division_id>0)
				$return_str .='value="'.$division_code.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">တိုင်း/ပြည်နယ်အမည်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtdivisionname" id="txtdivisionname" class="form-control" ';
			if($division_id>0)
				$return_str .='value="'.$division_name.'" />';
			else
				$return_str .=' />';
			$return_str .='			
											</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($division_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_division()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_division()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['division_id']) && isset($_POST['division_code']) && isset($_POST['division_name']))
		{
			$division_id = clean_jscode($_POST['division_id']);
			$division_code = clean_jscode($_POST['division_code']);
			$division_name = clean_jscode($_POST['division_name']);

			if( $division_code == '' )
				$error_arr[] = 'တိုင်း/ပြည်နယ်ကုတ်နံပါတ် ထည့်ပေးပါရန်';
			elseif( $division_name == '' )
				$error_arr[] = 'တိုင်း/ပြည်နယ်အမည် ထည့်ပေးပါရန်';
			if($division_id>0)
			{
				if( $division_bol->check_duplicate_division_name($division_code, $division_name, $division_id) )
					$error_arr[] = 'ဤတိုင်း/ပြည်နယ်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $division_bol->check_duplicate_division_name($division_code, $division_name) )
					$error_arr[] = 'ဤတိုင်း/ပြည်နယ်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($division_id>0)
				{
					$division_info->set_division_id($division_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$division_info->set_division_code($division_code);
				$division_info->set_division_name($division_name);
				if ( $division_bol->save_division($division_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'တိုင်း/ပြည်နယ် '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'တိုင်း/ပြည်နယ် '.$mess.' မအောင်မြင်ပါ';
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
		if( isset($_POST['delete_division_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$division_id = (int)($_POST['delete_division_id']);
			if( $division_bol->delete_division($division_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'တိုင်း/ပြည်နယ် ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'တိုင်း/ပြည်နယ် ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>