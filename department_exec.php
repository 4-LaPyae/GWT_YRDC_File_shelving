<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$department_bol = new department_bol();
	$department_info = new department_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	
	// Select Dept for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['dept_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$department_id = $_POST['dept_popup'];
			if($department_id>0)
			{
				$row = $department_bol->select_department_byid($department_id);
				$department_name = clean_jscode($row['department_name']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_department_setup" name="frm_department_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($department_id>0) 
			{
				$return_str .='<input type="hidden" id="hiddeptid" name="hiddeptid" value="' . $department_id . '" >';
			}
			$return_str .='		<label class="col-form-label col-md-4 required">ဌာနအမည်</label>
											<div class="col-md-7">
												<input type="text" name="txtdeptname" id="txtdeptname" class="form-control" ';
			if($department_id>0)
				$return_str .='value="'.$department_name.'" />';
			else
				$return_str .=' />';
			$return_str .='		</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($department_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_department()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_department()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['department_id']) && isset($_POST['department_name']))
		{
			$department_id = clean_jscode($_POST['department_id']);
			$department_name = clean_jscode($_POST['department_name']);

			if( $department_name == '' )
				$error_arr[] = 'ဌာနအမည် ထည့်ပေးပါရန်';
			if($department_id>0)
			{
				if( $department_bol->check_duplicate_department_name($department_name, $department_id) )
					$error_arr[] = 'ဤဌာနစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $department_bol->check_duplicate_department_name($department_name) )
					$error_arr[] = 'ဤဌာနစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($department_id>0)
				{
					$department_info->set_department_id($department_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$department_info->set_department_name($department_name);
				if ( $department_bol->save_department($department_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'ဌာန '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'ဌာန '.$mess.' မအောင်မြင်ပါ';
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
		if( isset($_POST['delete_department_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$department_id = (int)($_POST['delete_department_id']);
			if( $department_bol->delete_department($department_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'ဌာန ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'ဌာန ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>