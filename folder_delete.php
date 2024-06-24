<?php
	$movepath = '';
	$pgTitle = 'စာဖိုင်တွဲဖျက်သိမ်းမှု့ အသစ်ထည့်ခြင်း';
	$currentPg = 'Folder Delete Add New';
	require_once($movepath . "library/reference.php");
	require_once("autoload.php");
	require_once("adminauth.php");
	
	$errors = array();
	$folder_info = new folder_info();
	$folder_bol = new folder_bol();

	$userid = 0;
	if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
		$userid = clean($_SESSION['YRDCFSH_LOGIN_ID']);
	
	if(isset($_GET['folder_id']))
		$folder_id = clean($_GET['folder_id']);
		
	$destroy_order_employeeid = $destroy_order_employee_name = $destroy_order_designation = $destroy_order_department = $destroy_duty_employeeid = $destroy_duty_employee_name = $destroy_duty_designation = $destroy_duty_department = $destroy_date = $destroy_order_no = $destroy_remark = '';	
	if(isset($_POST['btnsave']))
	{
		if( isset($_POST['txtdestroy_order_employeeid'] ) && $_POST['txtdestroy_order_employeeid'] !='')
			$destroy_order_employeeid = clean($_POST['txtdestroy_order_employeeid']);

		if( isset($_POST['txtdestroy_order_employee_name'] ) && $_POST['txtdestroy_order_employee_name'] !='')
			$destroy_order_employee_name = clean($_POST['txtdestroy_order_employee_name']);

		if( isset($_POST['txtdestroy_order_designation'] ) && $_POST['txtdestroy_order_designation'] !='')
			$destroy_order_designation = clean($_POST['txtdestroy_order_designation']);

		if( isset($_POST['txtdestroy_order_department'] ) && $_POST['txtdestroy_order_department'] !='')
			$destroy_order_department = clean($_POST['txtdestroy_order_department']);

		if( isset($_POST['txtdestroy_duty_employeeid'] ) && $_POST['txtdestroy_duty_employeeid'] !='')
			$destroy_duty_employeeid = clean($_POST['txtdestroy_duty_employeeid']);

		if( isset($_POST['txtdestroy_duty_employee_name'] ) && $_POST['txtdestroy_duty_employee_name'] !='')
			$destroy_duty_employee_name = clean($_POST['txtdestroy_duty_employee_name']);

		if( isset($_POST['txtdestroy_duty_designation'] ) && $_POST['txtdestroy_duty_designation'] !='')
			$destroy_duty_designation = clean($_POST['txtdestroy_duty_designation']);

		if( isset($_POST['txtdestroy_duty_department'] ) && $_POST['txtdestroy_duty_department'] !='')
			$destroy_duty_department = clean($_POST['txtdestroy_duty_department']);
		
		if( isset($_POST['txtdestroy_date'] ) && $_POST['txtdestroy_date'] !='')
			$destroy_date = date('Y-m-d h:i:s', strtotime($_POST['txtdestroy_date']));
		
		if( isset($_POST['txtdestroy_order_no'] ) && $_POST['txtdestroy_order_no'] !='')
			$destroy_order_no = clean($_POST['txtdestroy_order_no']);
		
		if( isset($_POST['txtdestroy_remark'] ) && $_POST['txtdestroy_remark'] !='')
			$destroy_remark = clean($_POST['txtdestroy_remark']);

		if($destroy_order_employeeid == '')
			$errors[] = 'ခွင့်ပြုသူ ကိုယ်ပိုင်အမှတ် ထည့်ပေးပါရန်!';
		
		if($destroy_order_employee_name == '')
			$errors[] = 'ခွင့်ပြုသူ ၀န်ထမ်းအမည် ထည့်ပေးပါရန်!';
		
		if($destroy_order_designation == '')
			$errors[] = 'ခွင့်ပြုသူ ရာထူး ထည့်ပေးပါရန်!';
		
		if($destroy_order_department == '')
			$errors[] = 'ခွင့်ပြုသူ ဌာန ထည့်ပေးပါရန်!';
		
		if($destroy_duty_employeeid == '')
			$errors[] = 'တာ၀န်ခံရသူ ကိုယ်ပိုင်အမှတ် ထည့်ပေးပါရန်!';
		
		if($destroy_duty_employee_name == '')
			$errors[] = 'တာ၀န်ခံရသူ ၀န်ထမ်းအမည် ထည့်ပေးပါရန်!';
		
		if($destroy_duty_designation == '')
			$errors[] = 'တာ၀န်ခံရသူ ရာထူး ထည့်ပေးပါရန်!';
		
		if($destroy_duty_department == '')
			$errors[] = 'တာ၀န်ခံရသူ ဌာန ထည့်ပေးပါရန်!';
		
		if($destroy_date == '')
			$errors[] = 'ဖျက်သိမ်းရက်စွဲ ထည့်ပေးပါရန်!';
		
		if($destroy_order_no == '')
			$errors[] = 'အမိန့်အမှတ် ထည့်ပေးပါရန်!';
		
		if($destroy_remark == '')
			$errors[] = 'အကြာင်းအရာ ထည့်ပေးပါရန်!';
		
		if( count($errors) ==  0)
		{
			$success = FALSE;
			$folder_info->set_folder_id($folder_id);
			$folder_info->set_destroy_order_employeeid($destroy_order_employeeid);
			$folder_info->set_destroy_order_employee_name($destroy_order_employee_name);
			$folder_info->set_destroy_order_designation($destroy_order_designation);
			$folder_info->set_destroy_order_department($destroy_order_department);
			$folder_info->set_destroy_duty_employeeid($destroy_duty_employeeid);
			$folder_info->set_destroy_duty_employee_name($destroy_duty_employee_name);
			$folder_info->set_destroy_duty_designation($destroy_duty_designation);
			$folder_info->set_destroy_duty_department($destroy_duty_department);
			$folder_info->set_destroy_date($destroy_date);
			$folder_info->set_destroy_order_no($destroy_order_no);
			$folder_info->set_destroy_remark($destroy_remark);
			$folder_info->set_modified_by($userid);
			$success = $folder_bol->update_folder_destroy($folder_info);
			if( $success == TRUE )
			{
				$_SESSION['folder_msg'] = "ဖိုင်တွဲဖျက်သိမ်းမှု့ အသစ်ထည့်ခြင်းအောင်မြင်သည်";
				header("location: folder_list.php");
				exit();
			}
		}
	}
	require_once("admin_header.php");
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';	
	
	$(document).ready(function()
	{
		Add_Validation();
		
		$('#destroyfromdate').data('datetimepicker').defaultDate(new Date());
		$('#destroyfromdate').data('datetimepicker').format( 'DD-MM-YYYY HH:mm:ss' );
		
		$("#frm_folder_delete_setup").submit(function(e)
		{
			if($('#frm_folder_delete_setup').valid())
			{
				getloading();
				$("#frm_folder_delete_setup").unbind('submit').submit();				
				return true;
			}
		});
	});
	
	function Add_Validation()
	{
		$("#frm_folder_delete_setup").validate(
		{
			'rules':{
				'txtdestroy_order_employeeid':{'required':true},
				'txtdestroy_order_employee_name':{'required':true},
				'txtdestroy_order_designation':{'required':true}, 
				'txtdestroy_order_department':{'required':true}, 
				'txtdestroy_duty_employeeid':{'required':true}, 
				'txtdestroy_duty_employee_name':{'required':true}, 
				'txtdestroy_duty_designation':{'required':true}, 
				'txtdestroy_duty_department':{'required':true}, 
				'txtdestroy_date':{'required':true},
				'txtdestroy_remark':{'required':true}
			},
			'messages': {
				'txtdestroy_order_employeeid':{'required':'ခွင့်ပြုသူ ကိုယ်ပိုင်အမှတ် ထည့်ပေးပါရန်!'},  
				'txtdestroy_order_employee_name':{'required':'ခွင့်ပြုသူ ၀န်ထမ်းအမည်  ထည့်ပေးပါရန်!'},  
				'txtdestroy_order_designation':{'required':'ခွင့်ပြုသူ ရာထူး  ရွေးပေးပါရန်!'},  
				'txtdestroy_order_department':{'required':'ခွင့်ပြုသူ ဌာန  ရွေးပေးပါရန်!'},  
				'txtdestroy_duty_employeeid':{'required':'တာ၀န်ခံရသူ ကိုယ်ပိုင်အမှတ်  ရွေးပေးပါရန်!'},  
				'txtdestroy_duty_employee_name':{'required':'တာ၀န်ခံရသူ ၀န်ထမ်းအမည်  ရွေးပေးပါရန်!'},  
				'txtdestroy_duty_designation':{'required':'တာ၀န်ခံရသူ ရာထူး  ထည့်ပေးပါရန်!'},  
				'txtdestroy_duty_department':{'required':'တာ၀န်ခံရသူ ဌာန  ထည့်ပေးပါရန်!'},
				'txtdestroy_date':{'required':'ဖျက်သိမ်းရက်စွဲ ထည့်ပေးပါရန်!'},
				'txtdestroy_remark':{'required':'အကြာင်းအရာ ထည့်ပေးပါရန်!'}
			},			
		});
		return false;
	}
	
	function go_to_order_form()
	{
		create_loadingimage_dialog( 'modal-order', '၀န်ထမ်း ရွေးပေးပါရန်', movepath, 'modal-lg');
		$.post('folder_exec.php', {'add_order_popup':"add_order_popup"}, function(result)
		{
			select_data_exec_call_back(result);
			$('.scrollable').height($(window).height() - 220).css('margin-right', -15);
		}, 'json');
	}
	
	function go_to_duty_form()
	{
		create_loadingimage_dialog( 'modal-duty', '၀န်ထမ်း ရွေးပေးပါရန်', movepath, 'modal-lg');
		$.post('folder_exec.php', {'add_duty_popup':"add_duty_popup"}, function(result)
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
		if(val == 1)
		{
			$('#txtdestroy_order_employeeid').val(employee_id);
			$('#txtdestroy_order_employee_name').val(employee_name);
			$('#txtdestroy_order_department').val(department);
			$('#txtdestroy_order_designation').val(designation);
		}
		else
		{
			$('#txtdestroy_duty_employeeid').val(employee_id);
			$('#txtdestroy_duty_employee_name').val(employee_name);
			$('#txtdestroy_duty_department').val(department);		
			$('#txtdestroy_duty_designation').val(designation);
		}
	}
	
	function save_folder_delete_form()
	{
		Add_Validation();
		if($('#frm_folder_delete_setup').valid())
		{
			$('#btnsave').submit();
		}
		else
			return false;
	}
</script>

<form name="frm_folder_delete_setup" id="frm_folder_delete_setup" method="POST" class="form-material form-horizontal" enctype="multipart/form-data">

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
		<label class="col-form-label col-md-4 required">ဖျက်သိမ်းရက်စွဲ</label>
		<div class="col-lg-6 col-md-7">
			<div class="input-group date datetimepicker-input" id="destroyfromdate" data-target-input="nearest">
				<label class="input-group-addon p-2" for="destroyfromdate" data-target="#destroyfromdate" data-toggle="datetimepicker">
					<svg class="icon i-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-calendar" /></svg>
				</label>
				<input type="text" id="txtdestroy_date" name="txtdestroy_date" class="form-control datetimepicker-input" data-target="#destroyfromdate" />
			</div>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">အကြာင်းအရာ</label>
		<div class="col-lg-6 col-md-7">			
			<textarea name="txtdestroy_order_no" id="txtdestroy_order_no" class="form-control"></textarea>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">အမိန့်အမှတ်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_remark" id="txtdestroy_remark" maxlength="150" class="form-control">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ခွင့်ပြုသူ</label>
		<div class="col-lg-6 col-md-7">
			<input type="button" class="btn btn-outline-secondary" value="ခွင့်ပြုသူရွေးချယ်မည်" data-toggle="modal" data-target="#modal-order" onclick="go_to_order_form()">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ကိုယ်ပိုင်အမှတ်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_order_employeeid" id="txtdestroy_order_employeeid" maxlength="150" class="form-control">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">အမည်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_order_employee_name" id="txtdestroy_order_employee_name" maxlength="150" class="form-control">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ရာထူး</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_order_designation" id="txtdestroy_order_designation" maxlength="150" class="form-control">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ဌာန</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_order_department" id="txtdestroy_order_department" maxlength="150" class="form-control">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">တာ၀န်ခံရသူ</label>
		<div class="col-lg-6 col-md-7">
			<input type="button" class="btn btn-outline-secondary" value="တာ၀န်ခံရသူရွေးချယ်မည်" data-toggle="modal" data-target="#modal-duty" onclick="go_to_duty_form()">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ကိုယ်ပိုင်အမှတ်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_duty_employeeid" id="txtdestroy_duty_employeeid" maxlength="150" class="form-control">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">အမည်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_duty_employee_name" id="txtdestroy_duty_employee_name" maxlength="150" class="form-control">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ရာထူး</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_duty_designation" id="txtdestroy_duty_designation" maxlength="150" class="form-control">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ဌာန</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtdestroy_duty_department" id="txtdestroy_duty_department" maxlength="150" class="form-control">
		</div>
	</div>
	<?php
		if( isset($pageenablearr["Add"]) || $usertypeid==0 )
		{
			echo '<div class="form-group mt-4 row" id="divbuttons">
				<div class="offset-md-4 col">
					<input type="submit" class="btn btn-success" id="btnsave" name="btnsave" value="သိမ်းမည်" onclick="save_folder_delete_form();" />
					<input type="button" class="btn btn-outline-secondary" value="မသိမ်းပါ" onclick="window.location=\'folder_list.php\'" />
				</div>
			</div>';
		}
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once("admin_footer.php");
?>