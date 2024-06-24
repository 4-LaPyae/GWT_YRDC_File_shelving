<?php
	$movepath = '';
	$pgTitle = 'စင် စာရင်း';
	$currentPg = 'Shelf List';
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
	var cookie_name = 'shelf_list';
	
	function savepagestate()
	{
		var colarr = ['cri_shelf_name', 'cri_location_id', 'cri_department_id'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_shelf_name', 'cri_location_id', 'cri_department_id'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_shelf_name', 'cri_location_id', 'cri_department_id', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_shelf_name', 'cri_location_id', 'cri_department_id'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{		
		jQuery("#frm_shelf_setup").validate(
		{
			'rules':{
				'txtshelfcode':{'required':true},
				'txtshelfname':{'required':true},
				'sellocationid':{'required':true},
				'seldepartmentid':{'required':true},
				'txtno_of_row':{'required':true},
				'txtno_of_column':{'required':true}
			},
			'messages': {
				'txtshelfcode':{'required':'စင် ကုတ်နံပါတ် ထည့်ပေးပါရန်!'},
				'txtshelfname':{'required':'စင် အမည် ထည့်ပေးပါရန်!'},
				'sellocationid':{'required':'တည်နေရာ အမည် ထည့်ပေးပါရန်!'},
				'seldepartmentid':{'required':'ဌာန အမည် ထည့်ပေးပါရန်!'},
				'txtno_of_row':{'required':'အထပ်အရေအတွက် ထည့်ပေးပါရန်!'},
				'txtno_of_column':{'required':'အကန့်အရေအတွက် ထည့်ပေးပါရန်!'}
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
		get_datatable_sorting_cookie(cookie_name, "1,'desc'");

		jQuery("#frm_shelf_list").submit(function(e)
		{
			if(jQuery('#frm_shelf_list').valid())
			{
				getloading();
				jQuery("#frm_shelf_list").unbind('submit').submit();
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
			searching: true,
			search: {'sSearch': sFilter},
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
			"sAjaxSource": "shelf_getlist.php",
			columns: [
				{ "bSortable": false, "sWidth": "80px" },
				{ "bSortable": true,"sWidth": "100px" },
				{ "bSortable": false, "sWidth": "150px" },
				{ "bSortable": false, "sWidth": "150px" },
				{ "bSortable": false,"sWidth": "100px" },
				{ "bSortable": false,"sWidth": "100px" }, 
				{ "bSortable": false,"sWidth": "100px" }, 
				{ "bSortable": false,"sWidth": "100px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});
	
	/** create save new user popup **/
	function create_new_shelf_popup(shelf_id)
	{
		create_loadingimage_dialog( 'modal-addnew', 'စင် အသစ် ထည့်သွင်းခြင်း', movepath);
		$.post('shelf_exec.php?authaction=add', {'shelf_popup':shelf_id}, function(rtn_obj)
		{
			select_data_exec_call_back(rtn_obj);
			AddValidation();
		}, 'json');
	}

	function save_shelf()
	{
		if( jQuery('#frm_shelf_setup').valid() )
		{
			var shelf_id = '0';
			var shelf_code = $('#txtshelfcode').val();
			var shelf_name = $('#txtshelfname').val();
			var location_id = $('#sellocationid').val();
			var department_id = $('#seldepartmentid').val();
			var no_of_row = $('#txtno_of_row').val();
			var no_of_column = $('#txtno_of_column').val();
			hidebutton_showloadingimage();
			$.post('shelf_exec.php?authaction=add', {'shelf_id':shelf_id, 'shelf_code':shelf_code, 'shelf_name':shelf_name, 'location_id':location_id, 'department_id':department_id, 'no_of_row':no_of_row, 'no_of_column':no_of_column}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create edit user popup **/
	function create_edit_shelf_popup(shelf_id)
	{
		create_loadingimage_dialog('modal-edit', 'စင် ပြင်ဆင်ခြင်း', movepath);
		$.post('shelf_exec.php?authaction=edit', {'shelf_popup':shelf_id}, function(data)
		{
			select_data_exec_call_back(data);
			AddValidation();
		}, 'json');
	}

	function update_shelf()
	{
		if( jQuery('#frm_shelf_setup').valid())
		{
			var shelf_id = $('#hidshelfid').val();
			var shelf_code = $('#txtshelfcode').val();
			var shelf_name = $('#txtshelfname').val();
			var location_id = $('#sellocationid').val();
			var department_id = $('#seldepartmentid').val();
			var no_of_row = $('#txtno_of_row').val();
			var no_of_column = $('#txtno_of_column').val();
			hidebutton_showloadingimage();
			$.post('shelf_exec.php?authaction=edit', {'shelf_id':shelf_id, 'shelf_code':shelf_code, 'shelf_name':shelf_name, 'location_id':location_id, 'department_id':department_id, 'no_of_row':no_of_row, 'no_of_column':no_of_column}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create delete user popup **/
	function delete_shelf(shelf_id, shelf_name)
	{
		delete_id = shelf_id;
		confirm_delete_popup('စင် '+decodeURIComponent(shelf_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('shelf_exec.php?authaction=delete', {'delete_shelf_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_shelf_list" name="frm_shelf_list" method="POST">
	<div class="form-material my-4">
		<div class="row">
			<div class="col-md">
				<div class="form-group">
					<!-- <label for="cri_shelf_name" class="col-form-label">စင် အမည်</label> -->
					<input type="textbox" class="form-control" id="cri_shelf_name" placeholder="စင် အမည်" name="cri_shelf_name" />
				</div>
			</div>
			<div class="col-sm">
				<div class="form-group">
					<!-- <label for="cri_location_id" class="col-form-label">တည်နေရာ အမည်</label> -->
					<?php echo get_location_optionstr("cri_location_id", "-1", "cri"); ?>
				</div>
			</div>
			<div class="col-sm">
				<div class="form-group">
					<!-- <label for="cri_department_id" class="col-form-label">ဌာန အမည်</label> -->
					<?php echo get_department_optionstr("cri_department_id", $dept_cri, "-1", "cri"); ?>
				</div>
			</div>
		</div>
		<input type="submit" class="btn btn-success" name="btnsearch" value="ရှာရန်" onclick="return savepagestate();" />
		<input type="submit" class="btn btn-primary" name="btnshow" value="မှတ်တမ်းအားလုံး" onclick="clearpagestate();" />
	</div>

	<div id="divwarningmsg" class="securitywarning"></div>	
	<?php
		if ( isset($pageenablearr["Add"])  || $usertypeid == 0 )
		{
	?>
	<div class="d-block float-none float-sm-right text-center d-sm-inline-block">
		<button type="button" data-toggle="modal" data-target="#modal-addnew" onclick="create_new_shelf_popup('0');" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button>
	</div>
	<?php
		}
		echo create_actionmessage_container().
			create_dataTable_table( array('အမှတ်စဉ်', 'စင် ကုတ်နံပါတ်', 'စင် အမည်', 'တည်နေရာ အမည်', 'ဌာန အမည်', 'အထပ်အရေအတွက်', 'အကန့်အရေအတွက်', 'လုပ်ဆောင်ချက်') );
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>