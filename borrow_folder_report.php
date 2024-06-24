<?php
	$movepath = '';	
	$pgTitle = 'စာဖိုင်တွဲ အငှားစာရင်း';
	$currentPg = 'Folder Borrow Report';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	require_once('admin_header.php');
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'borrow_folder_report_list';
	
	function downloadfile()
	{
		postRedirectURL("borrow_folder_report_export.php", {"filter":getFilter(), "cri_text":cri_text});
	}
	
	function savepagestate()
	{
		var colarr = ['cri_rfid_no', 'cri_folder_no', 'cri_file_type_id', 'cri_folder_description'];
		save_criteria_in_cookie(cookie_name, colarr);		
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_rfid_no', 'cri_folder_no', 'cri_file_type_id', 'cri_folder_description'];
		load_criteria_from_cookie(cookie_name, colarr);
		
		var cri_arr ={ 'cri_rfid_no':'RFID No => ', 'cri_folder_no':'စာဖိုင်တွဲ အမှတ် => ', 'cri_file_type_id':'ဖိုင်တွဲအမျိုးအစား အမည် => ', 'cri_folder_description':'ဖိုင်တွဲအကြောင်းအရာ => '};
		cri_text = get_criteria_text(cookie_name, cri_arr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_rfid_no', 'cri_folder_no', 'cri_file_type_id', 'cri_folder_description', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_rfid_no', 'cri_folder_no', 'cri_file_type_id', 'cri_folder_description'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}
	
	jQuery(document).ready(function()
	{
		loadpagestate();
		sFilter = getFilter();
		get_datatable_paging_cookie(cookie_name);
		get_datatable_sorting_cookie(cookie_name, "4,'desc'");

		jQuery("#frm_folder_borrow_report_list").submit(function(e)
		{
			if(jQuery('#frm_folder_borrow_report_list').valid())
			{
				getloading();
				jQuery("#frm_folder_borrow_report_list").unbind('submit').submit();
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
			"sAjaxSource": "borrow_folder_report_getlist.php",
			columns: [
				{ "bSortable": false,"width": "80px" },
				{ "bSortable": false,"width": "100px" },
				{ "bSortable": false, "width": "100px" },
				{ "bSortable": false,"width": "100px" },
				{ "bSortable": true,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});
</script>
<form id="frm_folder_borrow_report_list" name="frm_folder_borrow_report_list" method="POST">
	<div class="form-material mb-4">
		<div class="row">
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<!--label for="cri_rfid_no" class="col-form-label">RFID No</label-->
					<input type="textbox" id="cri_rfid_no" name="cri_rfid_no" class="form-control" placeholder="ID No" />
				</div>
			</div>
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<!--label for="cri_folder_no" class="col-form-label">စာဖိုင်တွဲ အမှတ်</label-->
					<input type="textbox" id="cri_folder_no" name="cri_folder_no" class="form-control" placeholder="စာဖိုင်တွဲ အမှတ်" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_file_type_id" class="col-form-label">ဖိုင်တွဲအမျိုးအစား အမည်</label-->
					<?php echo get_filetype_optionstr("cri_file_type_id", -1, "cri"); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_rfid_no" class="col-form-label">ဖိုင်တွဲအကြောင်းအရာ</label-->
					<input type="textbox" id="cri_folder_description" name="cri_folder_description" class="form-control" placeholder="ဖိုင်တွဲအကြောင်းအရာ" />
				</div>
			</div>
		</div>
		<input type="submit" class="btn btn-success" name="btnsearch" value="ရှာရန်" onclick="return savepagestate();" />
		<input type="submit" class="btn btn-primary" name="btnshow" value="မှတ်တမ်းအားလုံး" onclick="clearpagestate();" />
	</div>

	<div class="clearfix m-t"></div>
	<?php
		echo create_actionmessage_container().
			create_dataTable_table( array('အမှတ်စဉ်', 'ကိုယ်ပိုင်အမှတ်', 'အမည်', 'ဌာန', 'ထုတ်ယူသည့်ရက်စွဲ', 'ID No.', 'စာဖိုင်တွဲ အမှတ်', 'ဖိုင်တွဲအကြောင်းအရာ', 'ဖိုင်တွဲအမျိုးအစား') );
	
		if(isset($pageenablearr["Download_Excel"]) || $usertypeid==0)
			echo '<input type="button" name="btndownload" id="btndownload" value="Download Excel" onclick="return downloadfile();" class="btn btn-danger mt-2" />';
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>