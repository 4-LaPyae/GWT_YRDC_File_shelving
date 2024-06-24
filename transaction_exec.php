<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	require_once ("adminauth.php");
	$is_ajaxcall = true;
	
	$userid = 0;
	if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
		$userid = clean($_SESSION['YRDCFSH_LOGIN_ID']);
	
	$employee_bol = new employee_bol();
	$folder_bol = new folder_bol();
	$transaction_bol = new transaction_bol();
	$transaction_info = new transaction_info();
	$error_arr = array();
	
	$json_return_arr['sessionexpire'] = 0;
	if( isset($_POST['add_employee_popup']) )
	{
		$return_str = '<div id="divdialog" class="modal-body">
			<form role="form" method="post" enctype="multipart/form-data" action="">
				<div id="alert_msg_check" class="error"></div>
				<div class="scrollable"><table class="table dataTable table-striped table-hover dt-responsive nowrap" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><input type="checkbox" id="all" onclick="$(this).attr(\'checked\')? $(\'.tddept :checkbox\').attr(\'checked\', true) : $(\'.tddept :checkbox\').attr(\'checked\', false);" /></th>
							<th>ကိုယ်ပိုင်အမှတ်</th>
							<th>၀န်ထမ်းအမည်</th>
							<th>ရာထူး</th>
							<th>ဌာန</th>
						</tr>
					</thead>
					<tbody>';
		$row = $employee_bol->get_all_employee();
		while($result = $row->getNext())
		{
			$employee_id = htmlspecialchars($result['employee_id']);
			$employee_name = htmlspecialchars($result['employee_name']);
			$designation_id = htmlspecialchars($result['designation_id']);
			$department_id = htmlspecialchars($result['department_id']);
			$return_str .= "<tr>
					<td class='tddept'><input type='checkbox' id='chkapprove' name='chkapprove[]' onclick='check_validation(this.id);' /></td>
					<td>".$employee_id."</td>
					<td>".$employee_name."</td>
					<td>".$designation_id."</td>
					<td>".$department_id."</td>";					
			$return_str .= "	</tr>";
		}
		$return_str .= '</tbody></table></div></form></div>
			<div class="modal-footer" id="divbuttons">
				<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="select_count_by_employee_id()">သိမ်းမည် </button>
			</div>';
		$json_return_arr['popupdata'] = $return_str;
	}
	
	// Delete
	if(isset($_GET['authaction']) && $_GET['authaction']=='delete')
	{
		if( isset($_POST['delete_transaction_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$transaction_id = (int)($_POST['delete_transaction_id']);
			if( $transaction_bol->delete_folder_transaction($transaction_id) )
			{
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'စာဖိုင်တွဲ အ၀င်အထွက် ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'စာဖိုင်တွဲ အ၀င်အထွက် ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	
	/* Backup Date */
	if((isset($_GET['authaction']) && $_GET['authaction']=='backup'))
	{  
		if( isset($_POST['given_transaction_popup']))
		{
			$json_return_arr['sessionexpire'] = 0;
			
			$transaction_id = clean($_POST['given_transaction_popup']);
			$folder_id = clean($_POST['folder_id']);
			$return_str = '<div class="modal-body">		
									<form id="frm_transaction_setup" name="frm_transaction_setup" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">
										<div id="alert_msg" class="error"></div>
										<div class="form-group row">
											<input type="hidden" id="hidfolder_id" name="hidfolder_id" value="' .$folder_id . '" >
											<input type="hidden" id="hidtransactionid" name="hidtransactionid" value="' .$transaction_id . '" >
											<label class="col-form-label col-md-4 required">ပြန်သွင်းသည့်နေ့စွဲ</label>
											<div class="col-md-7">
												<div class="input-group date datetimepicker-input" id="givenfromdate" data-target-input="nearest">
													<label class="input-group-addon p-2" for="givenfromdate" data-target="#givenfromdate" data-toggle="datetimepicker">
														<svg class="icon i-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-calendar" /></svg>
													</label>
													<input type="text" id="txt_given_date" name="txt_given_date" class="form-control datetimepicker-input" data-target="#givenfromdate" />
												</div>
											</div>
										</div>	
										<div id="divprogress"></div>
									</form>
								</div>
								<div class="modal-footer" id="divbuttons">
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="save_given_transaction()">သိမ်းမည် </button>
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>
								</div>';
			$json_return_arr['popupdata'] = $return_str;
		}
	}
	
	// Save Given Date
	if((isset($_GET['authaction']) && $_GET['authaction']=='backup'))
	{
		if( isset($_POST['transaction_id']) && isset($_POST['folder_id']) && isset($_POST['given_date']))
		{
			$folder_id = clean_jscode($_POST['folder_id']);
			$transaction_id = clean_jscode($_POST['transaction_id']);
			$given_date = date('Y-m-d h:i:s', strtotime($_POST['given_date']));

			if( $given_date == '' )
				$error_arr[] = 'ပြန်သွင်းသည့်နေ့စွဲ ထည့်ပေးပါရန်';			
			
			if( count($error_arr) == 0 )
			{
				$json_return_arr['sessionexpire'] = 0;			
				if ( $transaction_bol->save_given_date($given_date, $transaction_id) )
				{
					if($folder_bol->update_folder_status($folder_id, '1'))
					{
						$json_return_arr['success'] = 1;
						$json_return_arr['alertclass'] = 'alert alert-success';
						$json_return_arr['message'] = 'စာဖိုင် အ၀င်အထွက်ပြန်သွင်းခြင်း အောင်မြင်သည်';
					}
				}
				else
				{
					$json_return_arr['success'] = 0;
					$json_return_arr['alertclass'] = 'alert alert-danger';
					$json_return_arr['message'] = 'စာဖိုင် အ၀င်အထွက်ပြန်သွင်းခြင်း မအောင်မြင်ပါ';
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
	echo json_encode($json_return_arr);exit();
?>