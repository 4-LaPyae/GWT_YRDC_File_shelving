<?php
	$movepath = '';
	$pgTitle = 'တည်နေရာ စာရင်း';
	$currentPg = 'Location List';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	require_once('admin_header.php');
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'location_list';
	
	function savepagestate()
	{
		var colarr = ['cri_location_name'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_location_name'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_location_name', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_location_name'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{		
		jQuery("#frm_location_setup").validate(
		{
			'rules':{
				'txtlocationcode':{'required':true},
				'txtlocationname':{'required':true}
			},
			'messages': {
				'txtlocationcode':{'required':'တည်နေရာ ကုတ်နံပါတ် ထည့်ပေးပါရန်!'},
				'txtlocationname':{'required':'တည်နေရာ အမည် ထည့်ပေးပါရန်!'}
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

		jQuery("#frm_location_list").submit(function(e)
		{
			if(jQuery('#frm_location_list').valid())
			{
				getloading();
				jQuery("#frm_location_list").unbind('submit').submit();
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
			"sAjaxSource": "location_getlist.php",
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
	function create_new_location_popup(location_id)
	{
		create_loadingimage_dialog( 'modal-addnew', 'တည်နေရာ အသစ် ထည့်သွင်းခြင်း', movepath);
		$.post('location_exec.php?authaction=add', {'location_popup':location_id}, function(rtn_obj)
		{
			select_data_exec_call_back(rtn_obj);
			AddValidation();
		}, 'json');
	}

	function save_location()
	{
		if( jQuery('#frm_location_setup').valid() )
		{
			var location_id = '0';
			var location_code = $('#txtlocationcode').val();
			var location_name = $('#txtlocationname').val();
			hidebutton_showloadingimage();
			$.post('location_exec.php?authaction=add', {'location_id':location_id, 'location_code':location_code, 'location_name':location_name}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create edit user popup **/
	function create_edit_location_popup(location_id)
	{
		create_loadingimage_dialog('modal-edit', 'တည်နေရာ ပြင်ဆင်ခြင်း', movepath);
		$.post('location_exec.php?authaction=edit', {'location_popup':location_id}, function(data)
		{
			select_data_exec_call_back(data);
			AddValidation();
		}, 'json');
	}

	function update_location()
	{
		if( jQuery('#frm_location_setup').valid())
		{
			var location_id = $('#hidlocationid').val();
			var location_code = $('#txtlocationcode').val();
			var location_name = $('#txtlocationname').val();
			hidebutton_showloadingimage();
			$.post('location_exec.php?authaction=edit', {'location_id':location_id, 'location_code':location_code, 'location_name':location_name}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create delete user popup **/
	function delete_location(location_id, location_name)
	{
		delete_id = location_id;
		confirm_delete_popup('တည်နေရာ '+decodeURIComponent(location_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('location_exec.php?authaction=delete', {'delete_location_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_location_list" name="frm_location_list" method="POST">
	<div class="form-material my-4">
		<div class="row">
			<div class="col-lg-4 col-sm-6">
				<div class="form-group">
					<!-- <label for="cri_location_name" class="col-form-label"></label> -->
					<input type="textbox" class="form-control" id="cri_location_name" placeholder="တည်နေရာ အမည်" name="cri_location_name" />
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
		<button type="button" data-toggle="modal" data-target="#modal-addnew" onclick="create_new_location_popup('0');" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button>
	</div>
	<?php
		}
		echo create_actionmessage_container().
			create_dataTable_table( array('အမှတ်စဉ်', 'တည်နေရာ ကုတ်နံပါတ်', 'တည်နေရာ အမည်', 'လုပ်ဆောင်ချက်') );
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>