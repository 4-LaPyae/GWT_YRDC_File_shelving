<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$ward_bol = new ward_bol();
	$ward_info = new ward_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	
	// Select Dept for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{  
		if( isset($_POST['ward_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$ward_id = $_POST['ward_popup'];
			if($ward_id>0)
			{
				$row = $ward_bol->select_ward_byid($ward_id);
				$ward_name = clean_jscode($row['ward_name']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_ward_setup" name="frm_ward_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($ward_id>0) 
			{
				$return_str .='<input type="hidden" id="hidwardid" name="hidwardid" value="' . $ward_id . '" >';
			}
			$return_str .='			<label class="col-form-label col-md-4 required">ရပ်ကွက်အမည်</label>
											<div class="col-md-7">
												<input type="text" name="txtwardname" id="txtwardname" class="form-control" ';
			if($ward_id>0)
				$return_str .='value="'.$ward_name.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($ward_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_ward()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_ward()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['ward_id']) && ($_POST['township_id']) && isset($_POST['ward_name']) )
		{
			$ward_id = clean_jscode($_POST['ward_id']);
			$township_id = clean_jscode($_POST['township_id']);
			$ward_name = clean_jscode($_POST['ward_name']);

			if( $ward_name == '' )
				$error_arr[] = 'ရပ်ကွက်အမည် ထည့်ပေးပါရန်';
			if($ward_id>0)
			{
				if( $ward_bol->check_duplicate_ward_name($ward_name, $township_id, $ward_id) )
					$error_arr[] = 'ဤရပ်ကွက်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $ward_bol->check_duplicate_ward_name($ward_name, $township_id) )
					$error_arr[] = 'ဤရပ်ကွက်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($ward_id>0)
				{
					$ward_info->set_ward_id($ward_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$ward_info->set_township_id($township_id);
				$ward_info->set_ward_name($ward_name);
				if ( $ward_bol->save_ward($ward_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'ရပ်ကွက် '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'ရပ်ကွက် '.$mess.' မအောင်မြင်ပါ';
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
		if( isset($_POST['delete_ward_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$ward_id = (int)($_POST['delete_ward_id']);
			if( $ward_bol->delete_ward($ward_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'ရပ်ကွက် ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'ရပ်ကွက် ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>