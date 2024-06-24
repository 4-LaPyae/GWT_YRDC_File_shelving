<?php
	$movepath = '';	
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	$file_bol = new file_bol();
	
	$file_flag = true;
	if(!isset($_GET['file_id']) || clean($_GET['file_id']) == 0 || clean($_GET['file_id']) == "" )
		$file_flag = false;
	else
	{
		$file_id = clean($_GET['file_id']);
		$result = $file_bol->select_file_byid($file_id);
		$folder_id = $result['folder_id'];
		$letter_no = $result['letter_no'].' ၏ စာများ အဝင်အထွက်စာရင်း';

		if(!$result)
			$file_flag = false;
	}

	if(!$file_flag)
	{
		echo '<script> window.location="file_list.php?folder_id='.$folder_id.'";</script>';
		exit();
	}
	
	$pgTitle = $letter_no;
	$currentPg = 'File Transaction List';
	
	require_once('admin_header.php');
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var file_id = "<?php echo $file_id; ?>";
	var folder_id = "<?php echo $folder_id; ?>";
	var cookie_name = 'file_transaction_list';

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
	
	jQuery(document).ready(function()
	{
		get_datatable_paging_cookie(cookie_name);
		get_datatable_sorting_cookie(cookie_name, "6,'desc'");

		jQuery("#frm_file_transaction_list").submit(function(e)
		{
			if(jQuery('#frm_file_transaction_list').valid())
			{
				getloading();
				jQuery("#frm_file_transaction_list").unbind('submit').submit();
				return true;
			}
			//if(e.preventDefault) e.preventDefault(); else e.returnValue = false;  //cancel submit
		});
		jQuery.fn.dataTableExt.sErrMode = 'throw';// To Control JSON Alert Error
		oTable = jQuery('#dtList').dataTable(
		{
			responsive: true,
			pageLength: ilength,
			displayStart: istart,
			aaSorting: aasorting,
			processing: true,
			serverSide: true,
			lengthChange: true,
			autoWidth: false,
			drawCallback: function()
			{
				var oSettings = oTable.fnSettings();
				var aaSorting = JSON.stringify(oSettings.aaSorting);
				get_datatable_paging_cookie(cookie_name, oSettings);
				get_datatable_sorting_cookie(cookie_name, "", aaSorting);
			},
			footerCallback: function ( nRow, aaData, iStart, iEnd, aiDisplay )
			{
				if( aaData.length > 0 )
				{
					if( aaData[0][5] == 1 )
						jQuery('#divwarningmsg').html('There are invalid records! Please Contact with Website Administrator.');
					else
						jQuery('#divwarningmsg').html('');
				}
			},
			"sAjaxSource": "file_transaction_getlist.php?file_id=<?php echo $file_id; ?>",
			columns: [
				{ "bSortable": false, "sWidth": "50px" },
				{ "bSortable": false, "sWidth": "10px" },
				{ "bSortable": false, "sWidth": "70px" },
				{ "bSortable": false, "sWidth": "70px" }, 
				{ "bSortable": false, "sWidth": "50px" },
				{ "bSortable": false, "sWidth": "50px" },
				{ "bSortable": true, "sWidth": "50px" },
				{ "bSortable": false, "sWidth": "50px" },
				{ "bSortable": false, "sWidth": "50px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});
	
	/** create save new user popup **/
	function create_new_file_transaction_popup(file_transaction_id)
	{
		create_loadingimage_dialog( 'modal-addnew', 'စာများ အ၀င်အထွက်စာရင်း အသစ်ထည့်သွင်းခြင်း', movepath);
		$.post('file_transaction_exec.php?authaction=add', {'file_transaction_popup':file_transaction_id}, function(rtn_obj)
		{
			select_data_exec_call_back(rtn_obj);
			AddValidation();
			createDatetimePicker();
			$('#takenfromdate').data('datetimepicker').defaultDate(new Date());
			$('#takenfromdate').data('datetimepicker').format( 'DD-MM-YYYY HH:mm:ss' );
		}, 'json');
	}

	function save_file_transaction()
	{
		if( jQuery('#frm_file_transaction_setup').valid() )
		{
			var file_transaction_id = '0';
			var taken_employeeid = $('#txt_taken_employeeid').val();
			var taken_employee_name = $('#txt_taken_employee_name').val();
			var taken_designation = $('#txt_taken_employee_designation').val();
			var taken_department = $('#txt_taken_employee_department').val();
			var taken_date = $('#txt_taken_date').val();
			var remark = $('#txt_remark').val();
			hidebutton_showloadingimage();
			$.post('file_transaction_exec.php?authaction=add', {'file_transaction_id':file_transaction_id, 'folder_id':folder_id, 'file_id':file_id, 'taken_employeeid':taken_employeeid, 'taken_employee_name':taken_employee_name, 'taken_designation':taken_designation, 'taken_department':taken_department, 'taken_date':taken_date, 'remark':remark}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create edit user popup **/
	function create_edit_file_transaction_popup(file_transaction_id)
	{
		create_loadingimage_dialog('modal-edit', 'စာများ အ၀င်အထွက်စာရင်း ပြင်ဆင်ခြင်း', movepath);
		$.post('file_transaction_exec.php?authaction=edit', {'file_transaction_popup':file_transaction_id}, function(data)
		{
			var taken_date_value = data.taken_date;
			select_data_exec_call_back(data);
			AddValidation();
			createDatetimePicker();
			$('#takenfromdate').data('datetimepicker').format( 'DD-MM-YYYY HH:mm:ss' );
			$('#txt_taken_date').val(taken_date_value);
		}, 'json');
	}

	function update_file_transaction()
	{
		if( jQuery('#frm_file_transaction_setup').valid())
		{
			var file_id = "<?php echo $file_id; ?>";
			var file_transaction_id = $('#hidfiletransactionid').val();
			var taken_employeeid = $('#txt_taken_employeeid').val();
			var taken_employee_name = $('#txt_taken_employee_name').val();
			var taken_designation = $('#txt_taken_employee_designation').val();
			var taken_department = $('#txt_taken_employee_department').val();
			var taken_date = $('#txt_taken_date').val();
			var remark = $('#txt_remark').val();
			
			hidebutton_showloadingimage();
			$.post('file_transaction_exec.php?authaction=edit', {'file_transaction_id':file_transaction_id, 'folder_id':folder_id, 'file_id':file_id, 'taken_employeeid':taken_employeeid, 'taken_employee_name':taken_employee_name, 'taken_designation':taken_designation, 'taken_department':taken_department, 'taken_date':taken_date, 'remark':remark}, add_and_update_exec_callback_dialog, 'json');
		}
	}
	
	/** create backup popup **/
	function create_add_given_date_popup(file_transaction_id, file_id)
	{
		create_loadingimage_dialog('modal-backup', 'ပြန်သွင်းသည့်နေ့စွဲ ထည့်ရန်', movepath);
		$.post('file_transaction_exec.php?authaction=backup', {'given_file_transaction_popup':file_transaction_id, 'file_id':file_id}, function(data)
		{
			select_data_exec_call_back(data);
			createDatetimePicker();
			$('#givenfromdate').data('datetimepicker').defaultDate(new Date());
			$('#givenfromdate').data('datetimepicker').format( 'DD-MM-YYYY HH:mm:ss' );
		}, 'json');
	}
	
	function save_given_file_transaction()
	{
		if( jQuery('#frm_file_transaction_setup').valid() )
		{
			var hidfile_id = $('#hidfile_id').val();
			var file_transaction_id = $('#hidfiletransactionid').val();
			var given_date = $('#txt_given_date').val();
			hidebutton_showloadingimage();
			$.post('file_transaction_exec.php?authaction=backup', {'file_transaction_id':file_transaction_id, 'file_id':file_id, 'given_date':given_date}, add_and_update_exec_callback_dialog, 'json');
		}
	}
	
	/** create delete user popup **/
	function delete_file_transaction(file_transaction_id, file_transaction_name)
	{
		delete_id = file_transaction_id;
		confirm_delete_popup('စာများ အ၀င်အထွက်စာရင်း '+decodeURIComponent(file_transaction_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('file_transaction_exec.php?authaction=delete', {'delete_file_transaction_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_file_transaction_list" name="frm_file_transaction_list" method="POST">
	<div id="divwarningmsg" class="securitywarning"></div>
	<br>
	<?php
		if( isset($_SESSION ['file_transaction_msg']) && $_SESSION ['file_transaction_msg'] != "" )
		{
			echo "<div id='div_sess_mes' class='alert alert-success'>". $_SESSION ['file_transaction_msg'] .'</div>';
			unset($_SESSION ['file_transaction_msg']);
		}
		
		if ( isset($pageenablearr["Add"])  || $usertypeid == 0 )
		{
	?>
	<div class="d-block float-none float-sm-right text-center d-sm-inline-block">
		<a href = "file_transaction_addnew.php?file_id=<?php echo $file_id; ?>&folder_id=<?php echo $folder_id; ?>"><button type="button" data-toggle="modal" data-target="#modal-addnew" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button></a>
	</div>
	<?php
		}
		echo create_actionmessage_container().
			create_dataTable_table( array('လုပ်ဆောင်ချက်', 'စဉ်', 'ကိုယ်ပိုင်အမှတ်', 'အမည်', 'ရာထူး', 'ဌာန', 'ထုတ်ယူသည့်နေ့စွဲ', 'ပြန်သွင်းသည့်နေ့စွဲ', 'မှတ်ချက်') );
	?>
	<input type="button" value="ရှေ့သို့" onclick="window.location='file_list.php?folder_id=<?php echo $folder_id;?>'" class="btn btn-info pull-left m-t" />

</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>