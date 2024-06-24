<?php
	$movepath = '';
	require_once('autoload.php');	
	require_once($movepath.'library/reference.php');
	$is_ajaxcall = true;
	
	$employee_bol = new employee_bol();
	$file_bol = new file_bol();
	$error_arr = array();	
	
	$json_return_arr['sessionexpire'] = 0;
	
	if( isset($_POST['add_order_popup']) || isset($_POST['add_duty_popup']) )
	{
		if(isset($_POST['add_order_popup']))
			$value = 1;
		else
			$value = 2;
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
				<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="select_count_by_employee_id('.$value.')">သိမ်းမည် </button>
			</div>
		</div>';
		$json_return_arr['popupdata'] = $return_str;
	}
	
	// Delete 
	if(isset($_GET['authaction']) && $_GET['authaction']=='delete')
	{
		if( isset($_POST['delete_file_id']) )
		{  
			$json_return_arr['sessionexpire'] = 0;
			$file_id = (int)($_POST['delete_file_id']);
			if( $file_bol->delete_file($file_id) )
			{
				$imgpath = $g_upload_path."upload_document/file/".$file_id."/";
				$files = glob($imgpath.'/{,.}*', GLOB_BRACE);
				foreach($files as $file)
				{
					if(is_file($file))
						unlink($file);
				}
				rmdir($imgpath);
				$json_return_arr['success'] = 1;
				$json_return_arr['alertclass'] = 'alert alert-success';
				$json_return_arr['message'] = 'ဖိုင် ပယ်ဖျက်ခြင်း အောင်မြင်သည်';
			}
			else
			{
				$json_return_arr['success'] = 0;
				$json_return_arr['alertclass'] = 'alert alert-danger';
				$json_return_arr['message'] = 'ဖိုင် ပယ်ဖျက်ခြင်း မအောင်မြင်ပါ';
			}
		}
	}	
	echo json_encode($json_return_arr);exit();
?>