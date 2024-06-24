<?php
	$movepath = '';	
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	$township_bol = new township_bol();
	
	$township_flag = true;
	if(!isset($_GET['township_id']) || clean($_GET['township_id']) == 0 || clean($_GET['township_id']) == "" )
		$township_flag = false;
	else
	{
		$township_id = clean($_GET['township_id']);
		$result = $township_bol->select_township_byid($township_id);
		$division_id = $result['division_id'];
		//echo 'ttt = '.$division_id;exit;
		$township_name = $result['township_name'].' ၏ ရပ်ကွက် စာရင်း';

		if(!$result)
			$township_flag = false;
	}

	if(!$township_flag)
	{
		echo '<script> window.location="township_list.php";</script>';
		exit();
	}
	
	$pgTitle = $township_name;
	$currentPg = 'Ward List';
	
	require_once('admin_header.php');
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'ward_list';
	
	function savepagestate()
	{
		var colarr = ['cri_ward_name'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_ward_name'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_ward_name', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_ward_name'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{		
		jQuery("#frm_ward_setup").validate(
		{
			'rules':{
				'txtwardname':{'required':true}
			},
			'messages': {
				'txtwardname':{'required':'ရပ်ကွက် အမည် ထည့်ပေးပါရန်!'}
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

		jQuery("#frm_ward_list").submit(function(e)
		{
			if(jQuery('#frm_ward_list').valid())
			{
				getloading();
				jQuery("#frm_ward_list").unbind('submit').submit();
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
			"sAjaxSource": "ward_getlist.php?township_id=<?php echo $township_id; ?>",
			columns: [
				{ "bSortable": false, "sWidth": "80px" },
				{ "bSortable": false, "sWidth": "150px" },
				{ "bSortable": false,"sWidth": "100px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});
	
	/** create save new user popup **/
	function create_new_ward_popup(ward_id)
	{
		create_loadingimage_dialog( 'modal-addnew', 'ရပ်ကွက် အသစ် ထည့်သွင်းခြင်း', movepath);
		$.post('ward_exec.php?authaction=add', {'ward_popup':ward_id}, function(rtn_obj)
		{
			select_data_exec_call_back(rtn_obj);
			AddValidation();
		}, 'json');
	}

	function save_ward()
	{
		if( jQuery('#frm_ward_setup').valid() )
		{
			var ward_id = '0';
			var township_id = "<?php echo $township_id; ?>";
			var ward_name = $('#txtwardname').val();
			hidebutton_showloadingimage();
			$.post('ward_exec.php?authaction=add', {'ward_id':ward_id, 'township_id':township_id, 'ward_name':ward_name}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create edit user popup **/
	function create_edit_ward_popup(ward_id)
	{
		create_loadingimage_dialog('modal-edit', 'ရပ်ကွက် ပြင်ဆင်ခြင်း', movepath);
		$.post('ward_exec.php?authaction=edit', {'ward_popup':ward_id}, function(data)
		{
			select_data_exec_call_back(data);
			AddValidation();
		}, 'json');
	}

	function update_ward()
	{
		if( jQuery('#frm_ward_setup').valid())
		{
			var township_id = "<?php echo $township_id; ?>";
			var ward_id = $('#hidwardid').val();
			var ward_name = $('#txtwardname').val();
			hidebutton_showloadingimage();
			$.post('ward_exec.php?authaction=edit', {'ward_id':ward_id, 'township_id':township_id, 'ward_name':ward_name}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create delete user popup **/
	function delete_ward(ward_id, ward_name)
	{
		delete_id = ward_id;
		confirm_delete_popup('ရပ်ကွက် '+decodeURIComponent(ward_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('ward_exec.php?authaction=delete', {'delete_ward_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_ward_list" name="frm_ward_list" method="POST">
	<div class="form-material mb-4">
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_ward_name" class="col-form-label">ရပ်ကွက် အမည်</label-->
					<input type="textbox" class="form-control" id="cri_ward_name" placeholder="ရပ်ကွက် အမည်" name="cri_ward_name" />
				</div>
			</div>
		</div>
		<input type="submit" class="btn btn-success" name="btnsearch" value="ရှာရန်" onclick="return savepagestate();" />
		<input type="submit" class="btn btn-primary" name="btnshow" value="မှတ်တမ်းအားလုံး" onclick="clearpagestate();" />
	</div>

	<div class="clearfix m-t"></div>
	<div id="divwarningmsg" class="securitywarning"></div>	
	<?php
		if ( isset($pageenablearr["Add"])  || $usertypeid == 0 )
		{
	?>
	<div class="d-block float-none float-sm-right text-center d-sm-inline-block">
		<button type="button" data-toggle="modal" data-target="#modal-addnew" onclick="create_new_ward_popup('0');" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button>
	</div>
	<?php
		}
		echo create_actionmessage_container().
			create_dataTable_table( array('အမှတ်စဉ်', 'ရပ်ကွက် အမည်', 'လုပ်ဆောင်ချက်') );
	?>
	<input type="button" value="ရှေ့သို့" onclick="window.location='township_list.php?division_id=<?php echo $division_id; ?>'" class="btn btn-info pull-left m-t" />

</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>