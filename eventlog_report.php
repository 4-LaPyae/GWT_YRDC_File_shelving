<?php
	$movepath = '';
	$pgTitle = 'ဆော့ဖ်ဝဲလ် အသုံးပြုခဲ့သူ၏ လုပ်ဆောင်ချက်မှတ်တမ်း';
	$currentPg = 'Event Log Report';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	require_once('admin_header.php');
?>

<script language="javascript">
	var cookie_name = 'eventlog_report';
	
	function savepagestate()
	{
		var colarr = ['cri_action_type', 'cri_txtusername', 'cri_txt_fromdate', 'cri_txt_todate'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var cookie_arr = ['cri_action_type', 'cri_txtusername', 'cri_txt_fromdate', 'cri_txt_todate'];
		load_criteria_from_cookie(cookie_name, cookie_arr);
	}

	function clearpagestate()
	{
		var cookie_arr = ['cri_action_type', 'cri_txtusername', 'cri_txt_fromdate', 'cri_txt_todate', 'iDisplayStart'];
		clear_page_cookie(cookie_name, cookie_arr);
	}

	function getFilter()
	{
		var cookie_arr = ['cri_action_type', 'cri_txtusername', 'cri_txt_fromdate', 'cri_txt_todate'];
		return return_jsonstring_from_cookie(cookie_name, cookie_arr);
	}
	
	function detail_popup(id)
	{
		create_loadingimage_dialog( 'modal-description', 'အသေးစိတ်အကြောင်းအရာ', movepath);
		$.post('eventlog_report_exec.php', {'detail_id':id}, function(result)
		{
			select_data_exec_call_back(result);
		}, 'json');
	}
	
	$(document).ready(function()
	{
		var id_arr = ['cri_txt_fromdate', 'cri_txt_todate'];
		create_from_to_datetimepicker("fromdate", "todate", cookie_name, id_arr);
		
		loadpagestate();
		jQuery("#frmeventlog_report").submit(function(e)
		{
			if(jQuery('#frmeventlog_report').valid())
			{
				getloading();
				jQuery("#frmeventlog_report").unbind('submit').submit();
				return true;
			}
			//if(e.preventDefault) e.preventDefault(); else e.returnValue = false;  //cancel submit
		});
		
		var sWidth= jQuery('body').width();
		sWidth = sWidth - 100;
		jQuery('#dtList2, .content table').attr('width',sWidth);
		 
		get_datatable_paging_cookie(cookie_name);
		get_datatable_sorting_cookie(cookie_name, "1,'desc'");

		sFilter = getFilter();
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
			asSorting: [ 'asc', 'desc' ],
			pagingType: 'simple_numbers',
			searching: true,
			autoWidth: false,
			search: {'sSearch': sFilter},
			drawCallback: function()
			{
				var oSettings = oTable.fnSettings();
				var aaSorting = JSON.stringify(oSettings.aaSorting);
				get_datatable_paging_cookie(cookie_name, oSettings);
				get_datatable_sorting_cookie(cookie_name, "", aaSorting);
			},
			"sAjaxSource": "eventlog_getreport.php",
			columns: [
				{ "bSortable": false, "sWidth": "50px" },
				{ "bSortable": true, "sWidth": "150px" },
				{ "bSortable": true, "sWidth": "100px" },
				{ "bSortable": true,"sWidth": "200px" },
				{ "bSortable": true,"sWidth": "180px" },
				{ "bSortable": false,"sWidth": "auto" }
			]
		});
		$('.dataTables_filter').hide();
	});
</script>

<form id="frmeventlog_report" name="frmeventlog_report" method="POST">
	<div class="form-material my-4">
		<div class="row">
			<div class="col-lg-6 col-md-12">
				<div class="form-group row no-gutters">
					<label for="cri_txt_fromdate" class="col-form-label mr-sm-2 col-sm-auto col-12">လုပ်ဆောင်သည့် ရက်စွဲ</label>
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
			<div class="col-lg-3 col-md-6">
				<div class="form-group">
					<!-- <label for="cri_action_type" class="col-form-label">လုပ်ဆောင်ချက်အမျိုးအစား</label> -->
					<select name="cri_action_type" id="cri_action_type" class="form-control">
						<option value="0" selected default disabled>လုပ်ဆောင်ချက်အမျိုးအစားအားလုံး</option>
						<option value="Insert" >အသစ်ထည့်ခြင်း</option>
						<option value="Update" >ပြင်ဆင်ခြင်း</option>
						<option value="Delete" >ဖျက်ခြင်း</option>
						<option value="Log In" >Log In</option>
						<option class="font" value="Download Excel" >Download Excel</option>
					</select>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="form-group">
					<!-- <label for="cri_txtusername" class="col-form-label">အသုံးပြုသူအမည်</label> -->
					<input type="textbox" class="form-control"  id="cri_txtusername" placeholder="အသုံးပြုသူအမည်" name="cri_txtusername">
				</div>
			</div>
		</div>
		<input type="submit" class="btn btn-success" name="btnsearch" value="ရှာရန်" onclick="return savepagestate();" />
		<input type="submit" class="btn btn-info" name="btnshow" value="မှတ်တမ်းအားလုံး" onclick="clearpagestate();" />
	</div>
	<?php
		echo create_actionmessage_container();
		echo create_dataTable_table( array('စဉ်', 'အသုံးပြုသူအမည်', 'ရက်စွဲ', 'လုပ်ဆောင်ချက်အမျိုးအစား', '​တေဘယ်လ် အမည်', 'လုပ်ဆောင်သည့် အကြောင်းအရာ') );
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>