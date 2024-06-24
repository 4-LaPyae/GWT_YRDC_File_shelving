<?php
	$movepath = '';	
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	$division_bol = new division_bol();
	
	$township_flag = true;
	if(!isset($_GET['division_id']) || clean($_GET['division_id']) == 0 || clean($_GET['division_id']) == "" )
		$township_flag = false;
	else
	{
		$division_id = clean($_GET['division_id']);
		$result = $division_bol->select_division_byid($division_id);
		$division_name = $result['division_name'].' ၏ မြို့နယ် စာရင်း';

		if(!$result)
			$township_flag = false;
	}

	if(!$township_flag)
	{
		echo '<script> window.location="division_list.php";</script>';
		exit();
	}
	
	$pgTitle = $division_name;
	$currentPg = 'Township List';
	
	require_once('admin_header.php');
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'township_list';
	
	function savepagestate()
	{
		var colarr = ['cri_township_name'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_township_name'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_township_name', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_township_name'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{		
		jQuery("#frm_township_setup").validate(
		{
			'rules':{
				'txttownshipcode':{'required':true},
				'txttownshipname':{'required':true}
			},
			'messages': {
				'txttownshipcode':{'required':'မြို့နယ် ကုတ်နံပါတ် ထည့်ပေးပါရန်!'},
				'txttownshipname':{'required':'မြို့နယ် အမည် ထည့်ပေးပါရန်!'}
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

		jQuery("#frm_township_list").submit(function(e)
		{
			if(jQuery('#frm_township_list').valid())
			{
				getloading();
				jQuery("#frm_township_list").unbind('submit').submit();
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
			"sAjaxSource": "township_getlist.php?division_id=<?php echo $division_id; ?>",
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
	function create_new_township_popup(township_id)
	{
		create_loadingimage_dialog( 'modal-addnew', 'မြို့နယ် အသစ် ထည့်သွင်းခြင်း', movepath);
		$.post('township_exec.php?authaction=add', {'township_popup':township_id}, function(rtn_obj)
		{
			select_data_exec_call_back(rtn_obj);
			AddValidation();
		}, 'json');
	}

	function save_township()
	{
		if( jQuery('#frm_township_setup').valid() )
		{
			var township_id = '0';
			var division_id = "<?php echo $division_id; ?>";
			var township_code = $('#txttownshipcode').val();
			var township_name = $('#txttownshipname').val();
			hidebutton_showloadingimage();
			$.post('township_exec.php?authaction=add', {'township_id':township_id, 'division_id':division_id, 'township_code':township_code, 'township_name':township_name}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create edit user popup **/
	function create_edit_township_popup(township_id)
	{
		create_loadingimage_dialog('modal-edit', 'မြို့နယ် ပြင်ဆင်ခြင်း', movepath);
		$.post('township_exec.php?authaction=edit', {'township_popup':township_id}, function(data)
		{
			select_data_exec_call_back(data);
			AddValidation();
		}, 'json');
	}

	function update_township()
	{
		if( jQuery('#frm_township_setup').valid())
		{
			var division_id = "<?php echo $division_id; ?>";
			var township_id = $('#hidtownshipid').val();
			var township_code = $('#txttownshipcode').val();
			var township_name = $('#txttownshipname').val();
			hidebutton_showloadingimage();
			$.post('township_exec.php?authaction=edit', {'township_id':township_id, 'division_id':division_id, 'township_code':township_code, 'township_name':township_name}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create delete user popup **/
	function delete_township(township_id, township_name)
	{
		delete_id = township_id;
		confirm_delete_popup('မြို့နယ် '+decodeURIComponent(township_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('township_exec.php?authaction=delete', {'delete_township_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_township_list" name="frm_township_list" method="POST">
	<div class="form-material mb-4">
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_township_name" class="col-form-label">မြို့နယ် အမည်</label-->
					<input type="textbox" class="form-control" id="cri_township_name" placeholder="မြို့နယ် အမည်" name="cri_township_name" />
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
		<button type="button" data-toggle="modal" data-target="#modal-addnew" onclick="create_new_township_popup('0');" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button>
	</div>
	<?php
		}
		echo create_actionmessage_container().
			create_dataTable_table( array('အမှတ်စဉ်', 'မြို့နယ် ကုတ်နံပါတ်', 'မြို့နယ် အမည်', 'လုပ်ဆောင်ချက်') );
	?>
	<input type="button" value="ရှေ့သို့" onclick="window.location='division_list.php'" class="btn btn-info pull-left m-t" />

</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>