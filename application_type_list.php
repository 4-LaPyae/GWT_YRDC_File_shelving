<?php
	$movepath = '';
	$pgTitle = 'လုပ်ငန်းအမျိုးအစား စာရင်း';
	$currentPg = 'Application Type List';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	require_once('admin_header.php');
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'application_type_list';
	
	function savepagestate()
	{
		var colarr = ['cri_application_type_name'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_application_type_name'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_application_type_name', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_application_type_name'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{		
		jQuery("#frm_application_type_setup").validate(
		{
			'rules':{
				'txtapplication_typecode':{'required':true},
				'txtapplication_typename':{'required':true}
			},
			'messages': {
				'txtapplication_typecode':{'required':'လုပ်ငန်းအမျိုးအစား ကုတ်နံပါတ် ထည့်ပေးပါရန်!'},
				'txtapplication_typename':{'required':'လုပ်ငန်းအမျိုးအစား အမည် ထည့်ပေးပါရန်!'}
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

		jQuery("#frm_application_type_list").submit(function(e)
		{
			if(jQuery('#frm_application_type_list').valid())
			{
				getloading();
				jQuery("#frm_application_type_list").unbind('submit').submit();
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
			"sAjaxSource": "application_type_getlist.php",
			columns: [
				{ "bSortable": false, "sWidth": "80px" },
				{ "bSortable": true,"sWidth": "100px" },
				{ "bSortable": false, "sWidth": "150px" },
				{ "bSortable": false,"sWidth": "100px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});
	
	/** create save new user popup **/
	function create_new_application_type_popup(application_type_id)
	{
		create_loadingimage_dialog( 'modal-addnew', 'လုပ်ငန်းအမျိုးအစား အသစ် ထည့်သွင်းခြင်း', movepath);
		$.post('application_type_exec.php?authaction=add', {'application_type_popup':application_type_id}, function(rtn_obj)
		{
			select_data_exec_call_back(rtn_obj);
			AddValidation();
		}, 'json');
	}

	function save_application_type()
	{
		if( jQuery('#frm_application_type_setup').valid() )
		{
			var application_type_id = '0';
			var application_type_code = $('#txtapplication_typecode').val();
			var application_type_name = $('#txtapplication_typename').val();
			hidebutton_showloadingimage();
			$.post('application_type_exec.php?authaction=add', {'application_type_id':application_type_id, 'application_type_code':application_type_code, 'application_type_name':application_type_name}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create edit user popup **/
	function create_edit_application_type_popup(application_type_id)
	{
		create_loadingimage_dialog('modal-edit', 'လုပ်ငန်းအမျိုးအစား ပြင်ဆင်ခြင်း', movepath);
		$.post('application_type_exec.php?authaction=edit', {'application_type_popup':application_type_id}, function(data)
		{
			select_data_exec_call_back(data);
			AddValidation();
		}, 'json');
	}

	function update_application_type()
	{
		if( jQuery('#frm_application_type_setup').valid())
		{
			var application_type_id = $('#hidapplication_typeid').val();
			var application_type_code = $('#txtapplication_typecode').val();
			var application_type_name = $('#txtapplication_typename').val();
			hidebutton_showloadingimage();
			$.post('application_type_exec.php?authaction=edit', {'application_type_id':application_type_id, 'application_type_code':application_type_code, 'application_type_name':application_type_name}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create delete user popup **/
	function delete_application_type(application_type_id, application_type_name)
	{
		delete_id = application_type_id;
		confirm_delete_popup('လုပ်ငန်းအမျိုးအစား '+decodeURIComponent(application_type_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('application_type_exec.php?authaction=delete', {'delete_application_type_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_application_type_list" name="frm_application_type_list" method="POST">
	<div class="form-material my-4">
		<div class="row">
			<div class="col-lg-4 col-sm-6">
				<div class="form-group">
					<!-- <label for="cri_application_type_name" class="col-form-label">လုပ်ငန်းအမျိုးအစား အမည်</label> -->
					<input type="textbox" class="form-control" id="cri_application_type_name" placeholder="လုပ်ငန်းအမျိုးအစား အမည်" name="cri_application_type_name" />
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
		<button type="button" data-toggle="modal" data-target="#modal-addnew" onclick="create_new_application_type_popup('0');" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button>
	</div>
	<?php
		}
		echo create_actionmessage_container().
			create_dataTable_table( array('အမှတ်စဉ်', 'လုပ်ငန်းအမျိုးအစား ကုတ်နံပါတ်', 'လုပ်ငန်းအမျိုးအစား အမည်', 'လုပ်ဆောင်ချက်') );
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>