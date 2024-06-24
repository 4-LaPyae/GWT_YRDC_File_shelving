<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$township_bol = new township_bol();
	$township_info = new township_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	
	// Select Township for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{  
		if( isset($_POST['township_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$township_id = $_POST['township_popup'];
			if($township_id>0)
			{
				$row = $township_bol->select_township_byid($township_id);
				$township_code = clean_jscode($row['township_code']);
				$township_name = clean_jscode($row['township_name']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_township_setup" name="frm_township_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($township_id>0) 
			{
				$return_str .='<input type="hidden" id="hidtownshipid" name="hidtownshipid" value="' . $township_id . '" >';
			}
			$return_str .='			<label class="col-form-label col-md-4 required">မြို့နယ်ကုတ်နံပါတ်</label>
											<div class="col-md-7">
												<input type="text" name="txttownshipcode" id="txttownshipcode" class="form-control" ';
			if($township_id>0)
				$return_str .='value="'.$township_code.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">မြို့နယ်အမည်</label>
											<div class="col-md-7">
												<input type="text" name="txttownshipname" id="txttownshipname" class="form-control" ';
			if($township_id>0)
				$return_str .='value="'.$township_name.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($township_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_township()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_township()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['township_id']) && isset($_POST['division_id']) && isset($_POST['township_code']) && isset($_POST['township_name']))
		{
			$township_id = clean_jscode($_POST['township_id']);
			$division_id = clean_jscode($_POST['division_id']);
			$township_code = clean_jscode($_POST['township_code']);
			$township_name = clean_jscode($_POST['township_name']);

			if( $township_code == '' )
				$error_arr[] = 'မြို့နယ်ကုတ်နံပါတ် ထည့်ပေးပါရန်';
			elseif( $township_name == '' )
				$error_arr[] = 'မြို့နယ်အမည် ထည့်ပေးပါရန်';
			if($township_id>0)
			{
				if( $township_bol->check_duplicate_township_name($township_code, $township_name, $division_id, $township_id) )
					$error_arr[] = 'ဤမြို့နယ်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $township_bol->check_duplicate_township_name($township_code, $township_name, $division_id) )
					$error_arr[] = 'ဤမြို့နယ်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($township_id>0)
				{
					$township_info->set_township_id($township_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$township_info->set_division_id($division_id);
				$township_info->set_township_code($township_code);
				$township_info->set_township_name($township_name);
				if ( $township_bol->save_township($township_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'မြို့နယ် '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'မြို့နယ် '.$mess.' မအောင်မြင်ပါ';
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
		if( isset($_POST['delete_township_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$township_id = (int)($_POST['delete_township_id']);
			if( $township_bol->delete_township($township_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'မြို့နယ် ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'မြို့နယ် ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>