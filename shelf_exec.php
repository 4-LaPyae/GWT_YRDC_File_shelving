<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$shelf_bol = new shelf_bol();
	$shelf_info = new shelf_info();
	$error_arr = array();
	
	//permission by usertype_department
	$dept_cri = '';
	if ( $usertypeid != 0 && $department_enables !='')
		$dept_cri = ' WHERE department_id IN ('.$department_enables.')';
	
	$json_return_arr['sessionexpire'] = 0;	
	// Select Shelf for pop up
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['shelf_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$shelf_id = $_POST['shelf_popup'];
			if($shelf_id>0)
			{
				$row = $shelf_bol->select_shelf_byid($shelf_id);
				$shelf_code = clean_jscode($row['shelf_code']);
				$shelf_name = clean_jscode($row['shelf_name']);
				$location_id = clean_jscode($row['location_id']);
				$department_id = clean_jscode($row['department_id']);
				$no_of_row = clean_jscode($row['no_of_row']);
				$no_of_column = clean_jscode($row['no_of_column']);
			}
			
			$return_str = '<div class="modal-body">		
									<form id="frm_shelf_setup" name="frm_shelf_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">';
			if($shelf_id>0) 
			{
				$return_str .='<input type="hidden" id="hidshelfid" name="hidshelfid" value="' . $shelf_id . '" >';
			}
			$return_str .='			<label class="col-form-label col-md-4 required">စင်ကုတ်နံပါတ်</label>
											<div class="col-md-7">
												<input type="text" name="txtshelfcode" id="txtshelfcode" class="form-control" ';
			if($shelf_id>0)
				$return_str .='value="'.$shelf_code.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">စင်အမည်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtshelfname" id="txtshelfname" class="form-control" ';
			if($shelf_id>0)
				$return_str .='value="'.$shelf_name.'" />';
			else
				$return_str .=' />';
			$return_str .='		</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">တည်နေရာ အမည်
											</label>
											<div class="col-md-7">';
			if($shelf_id>0)	
				$return_str .=get_location_optionstr("sellocationid", $location_id);
			else
				$return_str .=get_location_optionstr("sellocationid", "-1");
			$return_str .='		</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">ဌာန အမည်
											</label>
											<div class="col-md-7">';
			if($shelf_id>0)	
				$return_str .=get_department_optionstr("seldepartmentid", $dept_cri, $department_id);
			else
				$return_str .=get_department_optionstr("seldepartmentid", $dept_cri, "-1");
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">အထပ်အရေအတွက်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtno_of_row" id="txtno_of_row" class="form-control" ';
			if($shelf_id>0)
				$return_str .='value="'.$no_of_row.'" />';
			else
				$return_str .=' />';
			$return_str .='			</div>
										</div>
										<div class="form-group row">
											<label class="col-form-label col-md-4 required">အကန့်အရေအတွက်
											</label>
											<div class="col-md-7">
												<input type="text" name="txtno_of_column" id="txtno_of_column" class="form-control" ';
			if($shelf_id>0)
				$return_str .='value="'.$no_of_column.'" />';
			else
				$return_str .=' />';
			$return_str .='		</div>
										</div>
										<div id="divprogress"></div>
									</form>
								</div>';
			if($shelf_id>0)
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_shelf()">ပြင်ဆင်မည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မပြင်ဆင်ပါ</button>
								</div>';
			}
			else
			{
				$return_str .='<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_shelf()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			}
			$json_return_arr['popupdata'] = $return_str;
		}
	}

	// Save New Record and Edit
	if((isset($_GET['authaction']) && $_GET['authaction']=='add') || (isset($_GET['authaction']) && $_GET['authaction']=='edit'))
	{
		if( isset($_POST['shelf_id']) && isset($_POST['shelf_code']) && isset($_POST['shelf_name']))
		{
			$shelf_id = clean_jscode($_POST['shelf_id']);
			$shelf_code = clean_jscode($_POST['shelf_code']);
			$shelf_name = clean_jscode($_POST['shelf_name']);
			$location_id = clean_jscode($_POST['location_id']);
			$department_id = clean_jscode($_POST['department_id']);
			$no_of_row = clean_jscode($_POST['no_of_row']);
			$no_of_column = clean_jscode($_POST['no_of_column']);

			if( $shelf_code == '' )
				$error_arr[] = 'စင်ကုတ်နံပါတ် ထည့်ပေးပါရန်';
			elseif( $shelf_name == '' )
				$error_arr[] = 'စင်အမည် ထည့်ပေးပါရန်';
			elseif( $location_id == '' )
				$error_arr[] = 'တည်နေရာအမည် ထည့်ပေးပါရန်';
			elseif( $department_id == '' )
				$error_arr[] = 'တည်နေရာအမည် ထည့်ပေးပါရန်';
			elseif( $no_of_row == '' )
				$error_arr[] = 'တည်နေရာအမည် ထည့်ပေးပါရန်';
			elseif( $no_of_column == '' )
				$error_arr[] = 'တည်နေရာအမည် ထည့်ပေးပါရန်';
			if($shelf_id>0)
			{
				if( $shelf_bol->check_duplicate_shelf_name($shelf_code, $shelf_name, $location_id, $shelf_id) )
					$error_arr[] = 'ဤစင်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}
			else
			{
				if( $shelf_bol->check_duplicate_shelf_name($shelf_code, $shelf_name, $location_id) )
					$error_arr[] = 'ဤစင်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
			}

			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;
				
				if($shelf_id>0)
				{
					$shelf_info->set_shelf_id($shelf_id);
					$mess= 'ပြင်ဆင်ခြင်း';
				}
				else
					$mess = 'အသစ်ထည့်ခြင်း';
				$shelf_info->set_shelf_code($shelf_code);
				$shelf_info->set_shelf_name($shelf_name);
				$shelf_info->set_location_id($location_id);
				$shelf_info->set_department_id($department_id);
				$shelf_info->set_no_of_row($no_of_row);
				$shelf_info->set_no_of_column($no_of_column);
				if ( $shelf_bol->save_shelf($shelf_info) )
				{
					$json_return_arr['success'] = 1;
					$json_return_arr['alertclass'] = 'alert alert-success';
					$json_return_arr['message'] = 'စင် '.$mess.' အောင်မြင်သည်';
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'စင် '.$mess.' မအောင်မြင်ပါ';
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
		if( isset($_POST['delete_shelf_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$shelf_id = (int)($_POST['delete_shelf_id']);
			if( $shelf_bol->delete_shelf($shelf_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'စင် ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'စင် ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>