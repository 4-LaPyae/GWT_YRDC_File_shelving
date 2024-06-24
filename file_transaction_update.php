<?php
	$movepath = '';
	$pgTitle = 'စာဖိုင် အ၀င်အထွက် ပြင်ဆင်ခြင်း';
	$currentPg = 'File Transaction Edit';
	require_once($movepath . "library/reference.php");
	require_once("autoload.php");
	require_once("adminauth.php");	
	$errors = array();
	$file_bol = new file_bol();
	$file_transaction_bol = new file_transaction_bol();
	$file_transaction_info = new file_transaction_info();
	$file_transaction_id = 0;

	$userid = 0;
	if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
		$userid = clean($_SESSION['YRDCFSH_LOGIN_ID']);
	
	$urlpath_flag = true;
	if(isset($_GET['file_transaction_id']))
	{
		$file_transaction_id = clean($_GET['file_transaction_id']);
		if($file_transaction_id == 0)
			$urlpath_flag = false;
		else
		{
			$urlpath_flag = true;
			$row = $file_transaction_bol->select_file_transaction_byid($file_transaction_id);
			$taken_date1 = ($row['taken_date']!="0000-00-00")?date('d-m-Y H:i:s', strtotime(trim($row['taken_date']))):'';
			$folder_id = clean_jscode($row['folder_transaction_id']);
			$file_id = clean_jscode($row['file_id']);
			$taken_employeeid1 = clean_jscode($row['taken_employeeid']);
			$taken_employee_name1 = clean_jscode($row['taken_employee_name']);
			$taken_designation1 = clean_jscode($row['taken_designation']);
			$taken_department1 = clean_jscode($row['taken_department']);				
			$given_date = clean_jscode($row['given_date']);
			$given_employeeid = clean_jscode($row['given_employeeid']);
			$given_employee_name = clean_jscode($row['given_employee_name']);
			$given_designation = clean_jscode($row['given_designation']);
			$given_department = clean_jscode($row['given_department']);
			$remark1 = clean_jscode($row['remark']);
		}
	}
	else
		$urlpath_flag = false;
	if(!$urlpath_flag)
	{
		$para = ("?folder_id=$folder_id");
		header("location: file_list.php$para");
		exit();
	}
	
	$taken_employeeid = $taken_employee_name = $taken_date = $taken_designation = $taken_department = $remark = '';	
	if(isset($_POST['btnupdate']))
	{
		$filetransactionid = clean($_POST['hidfiletransactionid']);
		
		if( isset($_POST['txt_taken_employeeid'] ) && $_POST['txt_taken_employeeid'] !='')
			$taken_employeeid = clean($_POST['txt_taken_employeeid']);

		if( isset($_POST['txt_taken_employee_name'] ) && $_POST['txt_taken_employee_name'] !='')
			$taken_employee_name = clean($_POST['txt_taken_employee_name']);

		if( isset($_POST['txt_taken_date'] ) && $_POST['txt_taken_date'] !='')
			$taken_date = date('Y-m-d h:i:s', strtotime($_POST['txt_taken_date']));

		if( isset($_POST['txt_taken_employee_designation'] ) && $_POST['txt_taken_employee_designation'] !='')
			$taken_designation = clean($_POST['txt_taken_employee_designation']);

		if( isset($_POST['txt_taken_employee_department'] ) && $_POST['txt_taken_employee_department'] !='')
			$taken_department = clean($_POST['txt_taken_employee_department']);

		if( isset($_POST['txt_remark'] ) && $_POST['txt_remark'] !='')
			$remark = clean($_POST['txt_remark']);

		if( $taken_employeeid == '' )
			$errors[] = 'ကိုယ်ပိုင်အမှတ် ထည့်ပေးပါရန်';
		if( $taken_employee_name == '' )
			$errors[] = 'အမည် ထည့်ပေးပါရန်';
		if( $taken_designation == '' )
			$errors[] = 'ရာထူး ထည့်ပေးပါရန်';
		if( $taken_department == '' )
			$errors[] = 'ဌာန ထည့်ပေးပါရန်';
		if( $taken_date == '' )
			$errors[] = 'ထုတ်ယူသည့်ရက်စွဲ ထည့်ပေးပါရန်';			
		if( $file_transaction_bol->check_duplicate_file_transaction_name($taken_employeeid, $taken_employee_name, $taken_designation, $taken_department, $file_transaction_id) )
			$errors[] = 'ဤစာဖိုင် အ၀င်အထွက်စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
		
		if( count($errors) ==  0)
		{
			$success = FALSE;
			$file_transaction_info->set_file_transaction_id($file_transaction_id);
			$file_transaction_info->set_folder_transaction_id($folder_id);
			$file_transaction_info->set_file_id($file_id);
			$file_transaction_info->set_taken_date($taken_date);
			$file_transaction_info->set_taken_employeeid($taken_employeeid);
			$file_transaction_info->set_taken_employee_name($taken_employee_name);
			$file_transaction_info->set_taken_designation($taken_designation);
			$file_transaction_info->set_taken_department($taken_department);				
			$file_transaction_info->set_remark($remark);
			$file_transaction_info->set_modified_by($userid);
			$success = $file_transaction_bol->save_file_transaction($file_transaction_info);
			if( $success == TRUE )
			{
				if($file_bol->update_file_status($file_id, '2'))
				{
					$_SESSION['file_transaction_msg'] = "စာဖိုင် အ၀င်အထွက် ပြင်ဆင်ခြင်းအောင်မြင်သည်";
					$para = ("?file_id=$file_id");
					header("location: file_transaction_list.php$para");
					exit();
				}
			}
		}
	}
	require_once("admin_header.php");
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	
	$(document).ready(function()
	{
		AddValidation();
		
		$('#txt_taken_date').val('<?php echo $taken_date1;?>');
		$('#takenfromdate').data('datetimepicker').format( 'DD-MM-YYYY HH:mm:ss' );
		
		$("#frm_file_transaction_setup").submit(function(e)
		{
			if($('#frm_file_transaction_setup').valid())
			{
				getloading();
				$("#frm_file_transaction_setup").unbind('submit').submit();				
				return true;
			}
		});
	});
	
	function AddValidation()
	{		
		jQuery("#frm_file_transaction_setup").validate(
		{
			'rules':{
				'txt_taken_employeeid':{'required':true},
				'txt_taken_employee_name':{'required':true},
				'txt_taken_employee_designation':{'required':true},
				'txt_taken_employee_department':{'required':true},
				'txt_taken_date':{'required':true}
			},
			'messages': {
				'txt_taken_employeeid':{'required':'ကိုယ်ပိုင်အမှတ် ထည့်ပေးပါရန်!'},
				'txt_taken_employee_name':{'required':'အမည် ထည့်ပေးပါရန်!'},
				'txt_taken_employee_designation':{'required':'ရာထူး ထည့်ပေးပါရန်!'},
				'txt_taken_employee_department':{'required':'ဌာန ထည့်ပေးပါရန်!'},
				'txt_taken_date':{'required':'ထုတ်ယူသည့်ရက်စွဲ ထည့်ပေးပါရန်!'}
			},
			errorPlacement: function (error, element) {
				if($(element).hasClass('qwertymulti') || $(element).hasClass('qwerty'))
					$(element).parents('.keywrapper').after(error);
				else
					$(element).after(error);
			},
			errorLabelContainer: "#fileerror",
			errorElement:"span"			
		});		
	}
	
	function go_to_employee_form()
	{
		create_loadingimage_dialog( 'modal-employee', '၀န်ထမ်း ရွေးပေးပါရန်', movepath, 'modal-lg');
		$.post('file_transaction_exec.php', {'add_employee_popup':"add_employee_popup"}, function(result)
		{
			select_data_exec_call_back(result);
			$('.scrollable').height($(window).height() - 220).css('margin-right', -15);
		}, 'json');
	}
	
	function check_validation(id)
	{
		if(jQuery("#"+ id+":checked").length > 1)
		{
			$("#alert_msg_check").html("အမျိုးအစားတစ်ခုသာရွေးပေးပါရန်!");
		}
		else
		{
			$("#alert_msg_check").html("");
		}
	}
	
	function select_count_by_employee_id(val)
	{
		var employee_id = '';
		var employee_name = '';
		var department = '';
		var designation = '';
		$('input[name="chkapprove[]').each(function() 
		{
			if ( $(this).is(':checked') == true )
			{
				employee_id += $(this).parent().parent().children().eq(1).html();
				employee_name += $(this).parent().parent().children().eq(2).html();
				department += $(this).parent().parent().children().eq(3).html();
				designation += $(this).parent().parent().children().eq(4).html();
			}
		});
		$('*').modal('hide');
		$('#txt_taken_employeeid').val(employee_id);
		$('#txt_taken_employee_name').val(employee_name);
		$('#txt_taken_employee_department').val(department);
		$('#txt_taken_employee_designation').val(designation);
	}
	
	function update_file_transaction_form()
	{
		// Add_Validation();
		if($('#frm_file_transaction_setup').valid())
		{
			$('#btnupdate').submit();
		}
		else
			return false;
	}
</script>

<form name="frm_file_transaction_setup" id="frm_file_transaction_setup" method="POST" class="form-material form-horizontal" enctype="multipart/form-data">
	<input type="hidden" name="hidfiletransactionid" id="hidfiletransactionid" value="<?php echo $file_transaction_id?>">
	
	<!-- show errors here -->
	<div id="validerror"><ul></ul></div>
	<?php
		if(count($errors))
		{
			echo "<div id='diverr' class='alert alert-danger'><ul>".
				"<li><label class='text-danger-dk'>" . implode("</label></li><li><label class='text-danger-dk'>", $errors).
			"</label></li></ul></div>";
		}
	?>	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">၀န်ထမ်းရွေးရန်</label>
		<div class="col-lg-6 col-md-7">
			<input type="button" class="btn btn-outline-secondary" value="၀န်ထမ်းရွေးချယ်မည်" data-toggle="modal" data-target="#modal-employee" onclick="go_to_employee_form()">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ကိုယ်ပိုင်အမှတ်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_taken_employeeid" id="txt_taken_employeeid" maxlength="150" class="form-control" value="<?php echo $taken_employeeid1; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">အမည်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_taken_employee_name" id="txt_taken_employee_name" maxlength="150" class="form-control" value="<?php echo $taken_employee_name1; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ထုတ်ယူသည့်ရက်စွဲ</label>
		<div class="col-lg-6 col-md-7 pos-unset">
			<div class="input-group date datetimepicker-input" id="takenfromdate" data-target-input="nearest">
				<label class="input-group-addon p-2" for="takenfromdate" data-target="#takenfromdate" data-toggle="datetimepicker">
					<svg class="icon i-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-calendar" /></svg>
				</label>
				<input type="text" id="txt_taken_date" name="txt_taken_date" class="form-control datetimepicker-input" data-target="#takenfromdate" />
			</div>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ရာထူး</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_taken_employee_designation" id="txt_taken_employee_designation" maxlength="150" class="form-control" value="<?php echo $taken_designation1; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ဌာန</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_taken_employee_department" id="txt_taken_employee_department" maxlength="150" class="form-control" value="<?php echo $taken_department1; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">မှတ်ချက်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_remark" id="txt_remark" maxlength="150" class="form-control" value="<?php echo $remark1; ?>">
		</div>
	</div>
	
	<?php
		if( isset($pageenablearr["Edit"]) || $usertypeid==0 )
		{
			echo '<div class="form-group mt-4 row" id="divbuttons">
				<div class="offset-md-4 col">
					<input type="submit" class="btn btn-success" id="btnupdate" name="btnupdate" value="ပြင်ဆင်မည်" onclick="update_file_transaction_form();" />
					<input type="button" class="btn btn-outline-secondary" value="မပြင်ဆင်ပါ" onclick="window.location=\'file_transaction_list.php?file_id='.$file_id.'\'" />
				</div>
			</div>';
		}
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once("admin_footer.php");
?>