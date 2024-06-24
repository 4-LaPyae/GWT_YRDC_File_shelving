<?php
	$movepath = '';
	$pgTitle = 'စာဖိုင်တွဲ အသေးစိတ်စာရင်း';
	$currentPg = 'Folder Detail';
	require_once($movepath . "library/reference.php");
	require_once("autoload.php");
	require_once("adminauth.php");	
	
	$errors = array();
	$file_bol = new file_bol();
	$folder_bol = new folder_bol();

	$userid = 0;
	if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
		$userid = clean($_SESSION['YRDCFSH_LOGIN_ID']);
	
	$urlpath_flag = true;
	if(isset($_GET['folder_id']))
	{
		$folder_id = clean($_GET['folder_id']);
		if($folder_id == 0)
			$urlpath_flag = false;
		else
		{
			$urlpath_flag = true;
			$aRow = $folder_bol->select_folder_byid($folder_id);
			
			$folder_id = htmlspecialchars($aRow['folder_id']);
			$rfid_card_no = htmlspecialchars($aRow['rfid_no']);
			$folderno = htmlspecialchars($aRow['folder_no']);
			$description_name = htmlspecialchars($aRow['description']);
			$file_type_name = htmlspecialchars($aRow['file_type_name']);
			$folder_security_type_name = htmlspecialchars($aRow['security_type_name']);
			$shelf_name = htmlspecialchars($aRow['shelf_name']);
			$shelf_row = htmlspecialchars($aRow['shelf_row']);
			$shelf_column = htmlspecialchars($aRow['shelf_column']);
		}
	}
	else
		$urlpath_flag = false;
	if(!$urlpath_flag)
	{
		header("location: folder_list.php");
		exit();
	}
	require_once("admin_header.php");
?>
<script>
	$(document).ready(function(){
		$('#dtList').dataTable(
		{
			responsive: true,
			processing: false,
			paging: false,
			ordering: false,
			autoWidth: false,
			searching: false,
			info: false
		});
	})
</script>
<form name="frm_folder_setup" id="frm_folder_setup" class="">
	<div class="row justify-content-center mt-4">
		<div class="col-md-10">
			<h4 class="text-right">
				<label class="font-weight-bold">ID No. </label>
				<span class="font-weight-thin"><?php echo $rfid_card_no; ?></span>
			</h4>
			<h6 class="text-right">
				<label class="font-weight-bold">စာဖိုင်တွဲအမှတ် -</label>
				<span class=""><?php echo $folderno; ?></span>
			</h6>
			<br>
			<div class="form-group mb-4">
				<label class="font-weight-bold">အကြောင်းအရာ</label>
				<div class="form-text pos-rlt dotted"><?php echo $description_name; ?></div>
			</div>

			<div class="row">
				<div class="col-sm">
					<div class="form-group mb-4">
						<label class="font-weight-bold">ဖိုင်တွဲအမျိုးအစား</label>
						<div class="form-text pos-rlt dotted"><?php echo $file_type_name; ?></div>
					</div>
					<div class="form-group mb-4">
						<label class="font-weight-bold">စင်အမည်</label>
						<div class="form-text pos-rlt dotted"><?php echo $shelf_name; ?></div>
					</div>
				</div>
				<div class="col-sm">
					<div class="form-group mb-4">
						<label class="font-weight-bold">လုံခြုံမှုအဆင့်အတန်း</label>
						<div class="form-text pos-rlt dotted"><?php echo $folder_security_type_name; ?></div>
					</div>
					<div class="row">
						<div class="col">
							<div class="form-group">
								<label class="font-weight-bold">အထပ်</label>
								<div class="form-text pos-rlt dotted"><?php echo $shelf_row; ?></div>
							</div>
						</div>
						<div class="col">
							<div class="form-group">
								<label class="font-weight-bold">အကန့်</label>
								<div class="form-text pos-rlt dotted"><?php echo $shelf_column; ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br>
	<table id="dtList" class="table dataTable dt-responsive nowrap" cellspacing="0">
		<thead>
			<tr>
				<th>စာအမှတ်</th>
				<th>ရက်စွဲ</th>
				<th>ပေးပို့သည့်ဌာန</th>
				<th>ပေးပို့သူ</th>
				<th>လက်ခံသူ</th>
				<th>အကြောင်းအရာ</th>
				<th>ဖိုင်လုံခြုံမှုအဆင့်အတန်း</th>
				<th>လုပ်ငန်းအမျိုးအစား</th>
				<th>စာရွက်အရေအတွက်</th>
				<th>ဆောင်ရွက်ရန်</th>
				<th>မှတ်ချက်</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$result = $file_bol->select_file_byfolderid($folder_id);
				while($aRow = $result->getNext())
				{		
					$letter_no = htmlspecialchars($aRow['letter_no']);
					$letter_count = htmlspecialchars($aRow['letter_count']);
					$letter_date = htmlspecialchars($aRow['now_date']);
					$description = htmlspecialchars($aRow['description']);
					$to_do = htmlspecialchars($aRow['to_do']);
					$remark = htmlspecialchars($aRow['remark']);
					$from_department_name = htmlspecialchars($aRow['from_department_name']);
					$security_type_name = htmlspecialchars($aRow['security_type_name']);
					$application_type_name = htmlspecialchars($aRow['application_type_name']);
					$application_description = htmlspecialchars($aRow['application_description']);
					$application_references = htmlspecialchars($aRow['application_references']);
					$receiver_customer_name = htmlspecialchars($aRow['receiver_customer_name']);
					$sender_customer_name = htmlspecialchars($aRow['sender_customer_name']);
						
					echo '<tr>
							<td>'.$letter_no.'</td>
							<td>'.$letter_date.'</td>
							<td>'.$from_department_name.'</td>
							<td>'.$receiver_customer_name.'</td>
							<td>'.$sender_customer_name.'</td>
							<td>'.$description.'</td>
							<td>'.$security_type_name.'</td>
							<td>'.$application_type_name.'</td>
							<td>'.$letter_count.'</td>
							<td>'.$to_do.'</td>
							<td>'.$remark.'</td>
						</tr>';
				}
			?>
		</tbody>
	</table></br>
	<input type="button" value="ရှေ့သို့" onclick="window.location='folder_list.php'" class="btn btn-info pull-left m-t" />
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once("admin_footer.php");
?>