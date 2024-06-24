<?php
	$movepath = '';	
	$pgTitle = 'စာဖိုင် အငှားစာရင်း';
	$currentPg = 'File Borrow Report';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	require_once('admin_header.php');
	
	//permission by user_type_application_type
	$application_cri = '';
	if ( $usertypeid != 0 && $application_type_enables !='')
		$application_cri = ' WHERE application_type_id IN ('.$application_type_enables.')';
	
	//permission by user_type_security_type
	$security_cri = '';
	if ( $usertypeid != 0 && $security_type_enables !='')
		$security_cri = ' WHERE security_type_id IN ('.$security_type_enables.')';
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'borrow_file_report_list';
	
	function downloadfile()
	{
		postRedirectURL("borrow_file_report_export.php", {"filter":getFilter(), "cri_text":cri_text});
	}
	
	function savepagestate()
	{
		var colarr = ['cri_letter_no', 'cri_txt_fromdate', 'cri_txt_todate', 'cri_file_description', 'cri_application_type_id', 'cri_security_type_id'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_letter_no', 'cri_txt_fromdate', 'cri_txt_todate', 'cri_file_description', 'cri_application_type_id', 'cri_security_type_id'];
		load_criteria_from_cookie(cookie_name, colarr);
		
		var cri_arr ={ 'cri_letter_no':'စာအမှတ် => ', 'cri_txt_fromdate':'စာရင်းသွင်းသည့်နေ့  ရက်စွဲ(မှ) => ', 'cri_txt_todate':'စာရင်းသွင်းသည့်နေ့  ရက်စွဲ(ထိ) => ', 'cri_file_description':'အကြောင်းအရာ => ', 'cri_application_type_id':'လုပ်ငန်းအမျိုးအစား => ', 'cri_security_type_id':'ဖိုင်လုံခြုံမှုအဆင့်အတန်း => '};
		cri_text = get_criteria_text(cookie_name, cri_arr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_letter_no', 'cri_txt_fromdate', 'cri_txt_todate', 'cri_file_description', 'cri_application_type_id', 'cri_security_type_id', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_letter_no', 'cri_txt_fromdate', 'cri_txt_todate', 'cri_file_description', 'cri_application_type_id', 'cri_security_type_id'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}
	
	jQuery(document).ready(function()
	{
		loadpagestate();
		
		var id_arr = ['cri_txt_fromdate', 'cri_txt_todate'];
		create_from_to_datetimepicker("fromdate", "todate", cookie_name, id_arr);
		
		sFilter = getFilter();
		get_datatable_paging_cookie(cookie_name);
		get_datatable_sorting_cookie(cookie_name, "4,'desc'");

		jQuery("#frm_file_borrow_report_list").submit(function(e)
		{
			if(jQuery('#frm_file_borrow_report_list').valid())
			{
				getloading();
				jQuery("#frm_file_borrow_report_list").unbind('submit').submit();
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
			"sAjaxSource": "borrow_file_report_getlist.php",
			columns: [
				{ "bSortable": false,"width": "80px" },
				{ "bSortable": false,"width": "100px" },
				{ "bSortable": false, "width": "100px" },
				{ "bSortable": false,"width": "100px" },
				{ "bSortable": true,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }, 
				{ "bSortable": false,"width": "100px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});
</script>
<form id="frm_file_borrow_report_list" name="frm_file_borrow_report_list" method="POST">
	<div class="form-material mb-4">
		<div class="row">
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<!--label for="cri_letter_no" class="col-form-label">စာအမှတ်</label-->
					<input type="textbox" class="form-control" id="cri_letter_no" placeholder="စာအမှတ်" name="cri_letter_no" />
				</div>
			</div>
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<!--label for="cri_rfid_no" class="col-form-label">အကြောင်းအရာ</label-->
					<input type="textbox" id="cri_file_description" name="cri_file_description" class="form-control" placeholder="အကြောင်းအရာ" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_department_id" class="col-form-label">ပေးပို့သည့်ဌာန</label-->
					<?php echo get_department_optionstr("cri_department_id", "", -1, "cri"); ?>
				</div>
			</div>
		</div>
		<div class="row">	
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_application_type_id" class="col-form-label">လုပ်ငန်းအမျိုးအစား</label-->
					<?php echo get_application_type_optionstr("cri_application_type_id", $application_cri, -1, "cri"); ?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_security_type_id" class="col-form-label">ဖိုင်လုံခြုံမှု့အဆင့်အတန်း</label-->
					<?php echo get_securitytype_optionstr("cri_security_type_id", $security_cri, -1, "cri"); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-8">
				<div class="form-group row no-gutters">
					<label for="cri_txt_fromdate" class="col-form-label mr-sm-2 col-sm-auto col-12">စာရင်းသွင်းသည့်နေ့ ရက်စွဲ</label>
					<div class="col col-sm pos-unset">
						<div class="input-group date datetimepicker-input" id="fromdate" data-target-input="nearest">
							<label class="input-group-addon p-2" for="fromdate" data-target="#fromdate" data-toggle="datetimepicker">
								<svg class="icon i-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-calendar" /></svg>
							</label>
							<input type="text" id="cri_txt_fromdate" name="cri_txt_fromdate" class="form-control datetimepicker-input" placeholder="" data-target="#fromdate" />
						</div>
					</div>
					<div class="col-sm-1 col-2 p-2 text-center">to</div>
					<div class="col col-sm pos-unset">
						<div class="input-group date datetimepicker-input" id="todate" data-target-input="nearest">
							<label class="input-group-addon p-2" for="todate" data-target="#todate" data-toggle="datetimepicker">
								<svg class="icon i-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-calendar" /></svg>
							</label>
							<input type="text" id="cri_txt_todate" name="cri_txt_todate" class="form-control datetimepicker-input" placeholder="" data-target="#todate" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="submit" class="btn btn-success" name="btnsearch" value="ရှာရန်" onclick="return savepagestate();" />
		<input type="submit" class="btn btn-primary" name="btnshow" value="မှတ်တမ်းအားလုံး" onclick="clearpagestate();" />
	</div>

	<div class="clearfix m-t"></div>
	<?php
		echo create_actionmessage_container().
		create_dataTable_table( array('အမှတ်စဉ်', 'ကိုယ်ပိုင်အမှတ်', 'အမည်', 'ဌာန', 'ထုတ်ယူသည့်ရက်စွဲ', 'စာအမှတ်', 'ရက်စွဲ', 'အကြောင်းအရာ', 'ဖိုင်လုံခြုံမှုအဆင့်အတန်း', 'လုပ်ငန်းအမျိုးအစား') );
	
		if(isset($pageenablearr["Download_Excel"]) || $usertypeid==0)
			echo '<input type="button" name="btndownload" id="btndownload" value="Download Excel" onclick="return downloadfile();" class="btn btn-danger mt-2" />';
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>