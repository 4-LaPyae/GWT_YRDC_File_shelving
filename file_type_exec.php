<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$file_type_bol = new file_type_bol();
	$file_type_info = new file_type_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	
	// Select File Type for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['file_type_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$file_type_id = $_POST['file_type_popup'];
			if($file_type_id>0)
			{
				$row = $file_type_bol->select_file_type_byid($file_type_id);
				$file_type_code = clean_jscode($row['file_type_code']);
				$file_type_name = clean_jscode($row['file_type_name']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_file_type_setup" name="frm_file_type_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($file_type_id>0) 
			{
				$return_str .='<input type="hidden" id="hidfile_typeid" name="hidfile_typeid" value="' . $file_type_id . '" >';
			}
			$return_str .='			<label class="col-form-label col-md-4 required">စာဖိုင်တွဲအမျိုးအစားကုတ်နံပါတ်</label>
											<div class="col-md-7">
												<input type="text" name="txtfile_typecode" id="txtfile_typecode" class="form-control" ';
			if($file_type_id>0)
				$return_str .='value="'.$file_type_code.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">စာဖိုင်တွဲအမျိုးအစားအမည်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtfile_typename" id="txtfile_typename" class="form-control" ';
			if($file_type_id>0)
				$return_str .='value="'.$file_type_name.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($file_type_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_file_type()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_file_type()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['file_type_id']) && isset($_POST['file_type_code']) && isset($_POST['file_type_name']))
		{
			$file_type_id = clean_jscode($_POST['file_type_id']);
			$file_type_code = clean_jscode($_POST['file_type_code']);
			$file_type_name = clean_jscode($_POST['file_type_name']);

			if( $file_type_code == '' )
				$error_arr[] = 'စာဖိုင်တွဲအမျိုးအစားကုတ်နံပါတ် ထည့်ပေးပါရန်';
			elseif( $file_type_name == '' )
				$error_arr[] = 'စာဖိုင်တွဲအမျိုးအစားအမည် ထည့်ပေးပါရန်';
			if($file_type_id>0)
			{
				if( $file_type_bol->check_duplicate_file_type_name($file_type_code, $file_type_name, $file_type_id) )
					$error_arr[] = 'ဤစာဖိုင်တွဲအမျိုးအစားစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $file_type_bol->check_duplicate_file_type_name($file_type_code, $file_type_name) )
					$error_arr[] = 'ဤစာဖိုင်တွဲအမျိုးအစားစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($file_type_id>0)
				{
					$file_type_info->set_file_type_id($file_type_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$file_type_info->set_file_type_code($file_type_code);
				$file_type_info->set_file_type_name($file_type_name);
				if ( $file_type_bol->save_file_type($file_type_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'စာဖိုင်တွဲအမျိုးအစား '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'စာဖိုင်တွဲအမျိုးအစား '.$mess.' မအောင်မြင်ပါ';
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
		if( isset($_POST['delete_file_type_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$file_type_id = (int)($_POST['delete_file_type_id']);
			if( $file_type_bol->delete_file_type($file_type_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'စာဖိုင်တွဲအမျိုးအစား ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'စာဖိုင်တွဲအမျိုးအစား ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>