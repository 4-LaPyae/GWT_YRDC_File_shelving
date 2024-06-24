<?php
	$movepath = '';
	$pgTitle = 'စာဖိုင်တွဲ စာရင်း';
	$currentPg = 'Folder List';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	require_once('admin_header.php');
	
	//permission by usertype_department
	$dept_cri = '';
	if ( $usertypeid != 0 && $department_enables !='')
		$dept_cri = ' WHERE department_id IN ('.$department_enables.')';
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'folder_list';
	
	function savepagestate()
	{
		var colarr = ['cri_rfid_no', 'cri_folder_no', 'cri_file_type_id', 'cri_folder_description', 'cri_shelf_id'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_rfid_no', 'cri_folder_no', 'cri_file_type_id', 'cri_folder_description', 'cri_shelf_id'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_rfid_no', 'cri_folder_no', 'cri_file_type_id', 'cri_folder_description', 'cri_shelf_id', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_rfid_no', 'cri_folder_no', 'cri_file_type_id', 'cri_folder_description', 'cri_shelf_id'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{		
		jQuery("#frm_folder_setup").validate(
		{
			'rules':{
				'txtfoldercode':{'required':true},
				'txtfoldername':{'required':true},
				'sellocationid':{'required':true},
				'seldepartmentid':{'required':true},
				'txtno_of_row':{'required':true},
				'txtno_of_column':{'required':true}
			},
			'messages': {
				'txtfoldercode':{'required':'စာဖိုင်တွဲ ကုတ်နံပါတ် ထည့်ပေးပါရန်!'},
				'txtfoldername':{'required':'စာဖိုင်တွဲ အမည် ထည့်ပေးပါရန်!'},
				'sellocationid':{'required':'တည်နေရာ အမည် ထည့်ပေးပါရန်!'},
				'seldepartmentid':{'required':'ဌာန အမည် ထည့်ပေးပါရန်!'},
				'txtno_of_row':{'required':'တန်းအရေအတွက် ထည့်ပေးပါရန်!'},
				'txtno_of_column':{'required':'တိုင်အရေအတွက် ထည့်ပေးပါရန်!'}
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
		loadpagestate();
		sFilter = getFilter();
		get_datatable_paging_cookie(cookie_name);
		get_datatable_sorting_cookie(cookie_name, "2,'desc'");

		jQuery("#frm_folder_list").submit(function(e)
		{
			if(jQuery('#frm_folder_list').valid())
			{
				getloading();
				jQuery("#frm_folder_list").unbind('submit').submit();
				return true;
			}
			//if(e.preventDefault) e.preventDefault(); else e.returnValue = false;  //cancel submit
		});
		jQuery.fn.dataTableExt.sErrMode = 'throw';// To Control JSON Alert Error
		oTable = jQuery('#dtList').dataTable(
		{
			responsive: false,
			pageLength: ilength,
			displayStart: istart,
			aaSorting: aasorting,
			processing: true,
			serverSide: true,
			lengthChange: true,
			searching: true,
			search: {'sSearch': sFilter},
			autoWidth: false,
			scrollX: true,
			drawCallback: function()
			{
				var oSettings = oTable.fnSettings();
				var aaSorting = JSON.stringify(oSettings.aaSorting);
				get_datatable_paging_cookie(cookie_name, oSettings);
				get_datatable_sorting_cookie(cookie_name, "", aaSorting);
			},
			footerCallback: function ( nRow, aaData, iStart, iEnd, aiDisplay )
			{
				// debugger;
				if( aaData.length > 0 )
				{
					if( aaData[0][0] == 1 )
						jQuery('#divwarningmsg').html('There are invalid records! Please contact website administrator.');
					else
						jQuery('#divwarningmsg').html('');
				}
			},
			"sAjaxSource": "folder_getlist.php",
			columns: [
				{"bVisible": false, "bSortable": false, "sWidth":"5px"},
				{ "bSortable": false },
				{ "bSortable": false },
				{ "bSortable": true,"sWidth": "100px" },
				{ "bSortable": false, "sWidth": "100px" },
				{ "bSortable": false, "sWidth": "200px" },
				{ "bSortable": false,"sWidth": "120px" },
				{ "bSortable": false,"sWidth": "120px" }, 
				{ "bSortable": false,"sWidth": "80px" }, 
				{ "bSortable": false,"sWidth": "80px" }, 
				{ "bSortable": false,"sWidth": "100px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});
	
	/** create save new popup **/
	function create_new_folder_popup(folder_id)
	{
		create_loadingimage_dialog( 'modal-addnew', 'စာဖိုင်တွဲ အသစ် ထည့်သွင်းခြင်း', movepath);
		$.post('folder_exec.php?authaction=add', {'folder_popup':folder_id}, function(rtn_obj)
		{
			select_data_exec_call_back(rtn_obj);
			AddValidation();
		}, 'json');
	}

	function save_folder()
	{
		if( jQuery('#frm_folder_setup').valid() )
		{
			var folder_id = '0';
			var folder_code = $('#txtfoldercode').val();
			var folder_name = $('#txtfoldername').val();
			var location_id = $('#sellocationid').val();
			var department_id = $('#seldepartmentid').val();
			var no_of_row = $('#txtno_of_row').val();
			var no_of_column = $('#txtno_of_column').val();
			hidebutton_showloadingimage();
			$.post('folder_exec.php?authaction=add', {'folder_id':folder_id, 'folder_code':folder_code, 'folder_name':folder_name, 'location_id':location_id, 'department_id':department_id, 'no_of_row':no_of_row, 'no_of_column':no_of_column}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create edit popup **/
	function create_edit_folder_popup(folder_id)
	{
		create_loadingimage_dialog('modal-edit', 'စာဖိုင်တွဲ ပြင်ဆင်ခြင်း', movepath);
		$.post('folder_exec.php?authaction=edit', {'folder_popup':folder_id}, function(data)
		{
			select_data_exec_call_back(data);
			AddValidation();
		}, 'json');
	}

	function update_folder()
	{
		if( jQuery('#frm_folder_setup').valid())
		{
			var folder_id = $('#hidfolderid').val();
			var folder_code = $('#txtfoldercode').val();
			var folder_name = $('#txtfoldername').val();
			var location_id = $('#sellocationid').val();
			var department_id = $('#seldepartmentid').val();
			var no_of_row = $('#txtno_of_row').val();
			var no_of_column = $('#txtno_of_column').val();
			hidebutton_showloadingimage();
			$.post('folder_exec.php?authaction=edit', {'folder_id':folder_id, 'folder_code':folder_code, 'folder_name':folder_name, 'location_id':location_id, 'department_id':department_id, 'no_of_row':no_of_row, 'no_of_column':no_of_column}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create delete user popup **/
	function delete_folder(folder_id, folder_name)
	{
		delete_id = folder_id;
		confirm_delete_popup('စာဖိုင်တွဲ '+decodeURIComponent(folder_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('folder_exec.php?authaction=delete', {'delete_folder_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_folder_list" name="frm_folder_list" method="POST">
	<div class="form-material my-4">
		<div class="row">			
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<!-- <label for="cri_rfid_no" class="col-form-label"></label> -->
					<input type="textbox" id="cri_rfid_no" name="cri_rfid_no" class="form-control" placeholder="ID No" />
				</div>
			</div>
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<!-- <label for="cri_folder_no" class="col-form-label"></label> -->
					<input type="textbox" id="cri_folder_no" name="cri_folder_no" class="form-control" placeholder="စာဖိုင်တွဲ အမှတ်" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!-- <label for="cri_file_type_id" class="col-form-label">ဖိုင်တွဲအမျိုးအစား အမည်</label> -->
					<?php echo get_filetype_optionstr("cri_file_type_id", -1, "cri"); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<!-- <label for="cri_rfid_no" class="col-form-label">ဖိုင်တွဲအကြောင်းအရာ</label> -->
					<input type="textbox" id="cri_folder_description" name="cri_folder_description" class="form-control" placeholder="ဖိုင်တွဲအကြောင်းအရာ" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!-- <label for="cri_shelf_id" class="col-form-label">စင်နံပါတ်</label> -->
					<?php echo get_shelf_optionstr("cri_shelf_id", $dept_cri, -1, "cri"); ?>
				</div>
			</div>
		</div>
		<input type="submit" class="btn btn-success" name="btnsearch" value="ရှာရန်" onclick="return savepagestate();" />
		<input type="submit" class="btn btn-primary" name="btnshow" value="မှတ်တမ်းအားလုံး" onclick="clearpagestate();" />
	</div>

	<div id="divwarningmsg" class="securitywarning"></div>	
	<?php
		if( isset($_SESSION ['folder_msg']) && $_SESSION ['folder_msg'] != "" )
		{
			echo "<div id='div_sess_mes' class='alert alert-success'>". $_SESSION ['folder_msg'] .'</div>';
			unset($_SESSION ['folder_msg']);
		}
		
		if ( isset($pageenablearr["Add"])  || $usertypeid == 0 )
		{
	?>
	<div class="d-block float-none float-sm-right text-center d-sm-inline-block">
		<a href = "folder_addnew.php"><button type="button" data-toggle="modal" data-target="#modal-addnew" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button></a>
	</div>
	<?php
		}
		echo create_actionmessage_container();
	?>
	<table id="dtList" name="dtList" class="table dataTable table-striped table-hover dt-responsive nowrap" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th></th>
				<th>လုပ်ဆောင်ချက်</th>
				<th>အမှတ်စဉ်</th>
				<th>ID No.</th>
				<th>စာဖိုင်တွဲ အမှတ်</th>
				<th>အကြောင်းအရာ</th>
				<th>ဖိုင်တွဲအမျိုးအစား </th>
				<th>စင်နံပါတ်</th>
				<th>အထပ်</th>
				<th>အကန့်</th>
				<th>ဖိုင်အရေအတွက်</th>		
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="10" align="center">Loading data from server</td>
			</tr>
		</tbody>
	</table>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>