<?php
	$movepath = '';	
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	$folder_bol = new folder_bol();
		
	//permission by user_type_application_type
	$application_cri = '';
	if ( $usertypeid != 0 && $application_type_enables !='')
		$application_cri = ' WHERE application_type_id IN ('.$application_type_enables.')';
	
	//permission by user_type_security_type
	$security_cri = '';
	if ( $usertypeid != 0 && $security_type_enables !='')
		$security_cri = ' WHERE security_type_id IN ('.$security_type_enables.')';
	
	$file_flag = true;
	if(!isset($_GET['folder_id']) || clean($_GET['folder_id']) == 0 || clean($_GET['folder_id']) == "" )
		$file_flag = false;
	else
	{
		$folder_id = clean($_GET['folder_id']);
		$result = $folder_bol->select_folder_byid($folder_id);
		$folder_no = $result['folder_no'].' ၏ ဖိုင် စာရင်း';

		if(!$result)
			$file_flag = false;
	}

	if(!$file_flag)
	{
		echo '<script> window.location="folder_list.php";</script>';
		exit();
	}
	
	$pgTitle = $folder_no;
	$currentPg = 'File List';
	
	require_once('admin_header.php');
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'file_list';
	
	function savepagestate()
	{
		var colarr = ['cri_letter_no', 'cri_txt_fromdate', 'cri_txt_todate', 'cri_file_description', 'cri_department_id', 'cri_sender_customer_id', 'cri_receiver_customer_id', 'cri_application_type_id', 'cri_security_type_id'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_letter_no', 'cri_txt_fromdate', 'cri_txt_todate', 'cri_file_description', 'cri_department_id', 'cri_sender_customer_id', 'cri_receiver_customer_id', 'cri_application_type_id', 'cri_security_type_id'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_letter_no', 'cri_txt_fromdate', 'cri_txt_todate', 'cri_file_description', 'cri_department_id', 'cri_sender_customer_id', 'cri_receiver_customer_id', 'cri_application_type_id', 'cri_security_type_id', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_letter_no', 'cri_txt_fromdate', 'cri_txt_todate', 'cri_file_description', 'cri_department_id', 'cri_sender_customer_id', 'cri_receiver_customer_id', 'cri_application_type_id', 'cri_security_type_id'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{		
		jQuery("#frm_file_setup").validate(
		{
			'rules':{
					'txt_letter_no':{'required':true},
					'txtletterfromdate':{'required':true},
					'txt_description':{'required':true},
					'sel_security_type':{'required':true},
					'txt_letter_count':{'required':true}
					},
			'messages': {
					'txt_letter_no':{'required':'စာအမှတ် ထည့်ပေးပါရန်!'},
					'txtletterfromdate':{'required':'ရက်စွဲ ထည့်ပေးပါရန်!'},
					'txt_description':{'required':'အကြောင်းအရာ ထည့်ပေးပါရန်!'},
					'sel_security_type':{'required':'ဖိုင်လုံခြုံမှုအဆင့်အတန်း ရွေးပေးပါရန် !'},
					'txt_letter_count':{'required':'စာရွက်အရေအတွက် ထည့်ပေးပါရန်!'}
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
		
		var id_arr = ['cri_txt_fromdate', 'cri_txt_todate'];
		create_from_to_datetimepicker("fromdate", "todate", cookie_name, id_arr);
		
		sFilter = getFilter();
		get_datatable_paging_cookie(cookie_name);
		get_datatable_sorting_cookie(cookie_name, "3,'desc'");

		jQuery("#frm_file_list").submit(function(e)
		{
			if(jQuery('#frm_file_list').valid())
			{
				getloading();
				jQuery("#frm_file_list").unbind('submit').submit();
				return true;
			}
			//if(e.preventDefault) e.preventDefault(); else e.returnValue = false;  //cancel submit
		});
		jQuery.fn.dataTableExt.sErrMode = 'throw';// To Control JSON Alert Error
		oTable = jQuery('#dtList').dataTable(
		{
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
				if( aaData.length > 0 )
				{
					if( aaData[0][5] == 1 )
						jQuery('#divwarningmsg').html('There are invalid records! Please Contact with Website Administrator.');
					else
						jQuery('#divwarningmsg').html('');
				}
			},
			"sAjaxSource": "file_getlist.php?folder_id=<?php echo $folder_id; ?>",
			columns: [
				{ "bSortable": false,"width": "80px" },
				{ "bSortable": false },
				{ "bSortable": false,"width": "100px" },
				{ "bSortable": true, "width": "100px" },
				{ "bSortable": false,"width": "300px" },
				{ "bSortable": false,"width": "150px" }, 
				{ "bSortable": false,"width": "150px" }, 
				{ "bSortable": false,"width": "300px" }, 
				{ "bSortable": false,"width": "200px" }, 
				{ "bSortable": false,"width": "200px" }, 
				{ "bSortable": false,"width": "150px" }, 
				{ "bSortable": false,"width": "150px" }, 
				{ "bSortable": false,"width": "200px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});

	/** create delete user popup **/
	function delete_file(file_id, file_name)
	{
		delete_id = file_id;
		confirm_delete_popup('ဖိုင် '+decodeURIComponent(file_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('file_exec.php?authaction=delete', {'delete_file_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_file_list" name="frm_file_list" method="POST">
	<div class="form-material my-4">
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
					<!--label for="cri_sender_customer_id" class="col-form-label">ပေးပို့သူ</label-->
					<?php echo get_customer_optionstr("cri_sender_customer_id", -1, "cri"); ?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_receiver_customer_id" class="col-form-label">လက်ခံသူ</label-->
					<?php echo get_customer_optionstr("cri_receiver_customer_id", -1, "cri"); ?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_application_type_id" class="col-form-label">လုပ်ငန်းအမျိုးအစား</label-->
					<?php echo get_application_type_optionstr("cri_application_type_id", $application_cri, -1, "cri"); ?>
				</div>
			</div>
		</div>
		<div class="row">	
			<div class="col-md-4">
				<div class="form-group">
					<!--label for="cri_security_type_id" class="col-form-label">ဖိုင်လုံခြုံမှု့အဆင့်အတန်း</label-->
					<?php echo get_securitytype_optionstr("cri_security_type_id", $security_cri, -1, "cri"); ?>
				</div>
			</div>
			<div class="col-xl-5 col-md-8">
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

	<div id="divwarningmsg" class="securitywarning"></div>	
	<?php
		if( isset($_SESSION ['file_msg']) && $_SESSION ['file_msg'] != "" )
		{
			echo "<div id='div_sess_mes' class='alert alert-success'>". $_SESSION ['file_msg'] .'</div>';
			unset($_SESSION ['file_msg']);
		}
		
		if ( isset($pageenablearr["Add"])  || $usertypeid == 0 )
		{
	?>
	<div class="d-block float-none float-sm-right text-center d-sm-inline-block">
		<a href = "file_addnew.php?folder_id=<?php echo $folder_id; ?>"><button type="button" data-toggle="modal" data-target="#modal-addnew" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button></a>
	</div>
	<?php
		}
		echo create_actionmessage_container();
	?>
	<table id="dtList" name="dtList" class="table dataTable table-striped table-hover nowrap" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>လုပ်ဆောင်ချက်</th>
				<th>အမှတ်စဉ်</th>
				<th>စာအမှတ်</th>
				<th>ရက်စွဲ</th>
				<th>ပေးပို့သည့်ဌာန </th>
				<th>ပေးပို့သူ</th>
				<th>လက်ခံသူ</th>
				<th>အကြောင်းအရာ</th>
				<th>ဖိုင်လုံခြုံမှုအဆင့်အတန်း</th>
				<th>လုပ်ငန်းအမျိုးအစား</th>
				<th>စာရွက်အရေအတွက်</th>
				<th>ဆောင်ရွက်ရန်</th>
				<th>မှတ်ချက်</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="13" align="center">Loading data from server</td>
			</tr>
		</tbody>
	</table>
	<input type="button" value="ရှေ့သို့" onclick="window.location='folder_list.php'" class="btn btn-info pull-left m-t" />
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>