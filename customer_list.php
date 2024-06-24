<?php
	$movepath = '';
	$pgTitle = 'Customer သတ်မှတ်ခြင်း စာရင်း';
	$currentPg = 'Customer List';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	require_once('admin_header.php');
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'customer_list';
	
	function savepagestate()
	{
		if( jQuery("[value='nrc_no']").is(':checked') )
		{
			$('#txt_cri_nationalcardno').val('');
			$('#txt_cri_passportno').val('');
		}
		else if( jQuery("[value='national_no']").is(':checked') )
		{
			$('#txtnrcno_cri_nrcno').val('');
			$('#txt_cri_passportno').val('');
		}
		else
		{
			$('#txtnrcno_cri_nrcno').val('');
			$('#txt_cri_nationalcardno').val('');
		}
		
		var colarr = ['cri_customer_name', 'cri_father_name', 'cri_txt_birthdate', 'selnrcdivision_cri_nrcno', 'selnrctownship_cri_nrcno', 'selnrctype_cri_nrcno', 
		'txtnrcno_cri_nrcno', 'txt_cri_nationalcardno', 'txt_cri_passportno'];
		save_criteria_in_cookie(cookie_name, colarr);
		jQuery.cookie(cookie_name + '[nrc]', jQuery(':radio:checked').val() );
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_customer_name', 'cri_father_name', 'cri_txt_birthdate', 'selnrcdivision_cri_nrcno', 'selnrctownship_cri_nrcno', 'selnrctype_cri_nrcno', 
		'txtnrcno_cri_nrcno', 'txt_cri_nationalcardno', 'txt_cri_passportno'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_customer_name', 'cri_father_name', 'cri_txt_birthdate', 'selnrcdivision_cri_nrcno', 'selnrctownship_cri_nrcno', 'selnrctype_cri_nrcno', 
		'txtnrcno_cri_nrcno', 'txt_cri_nationalcardno', 'txt_cri_passportno', 'iDisplayStart', 'aaSorting'];
		jQuery.cookie(cookie_name + '[cri_txt_birthdate]', '');
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_customer_name', 'cri_father_name', 'cri_txt_birthdate', 'selnrcdivision_cri_nrcno', 'selnrctownship_cri_nrcno', 'selnrctype_cri_nrcno', 
		'txtnrcno_cri_nrcno', 'txt_cri_nationalcardno', 'txt_cri_passportno'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{
		jQuery("#frm_customer_setup").validate(
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
		// Auto Checked NRC After Page Load
		var nrc_type = jQuery.cookie(cookie_name + '[nrc]');
		jQuery("[value='" + nrc_type + "']").click();
		var cook_nrc_div = jQuery.cookie(cookie_name + '[selnrcdivision_cri_nrcno]');
		var cook_nrc_tsp = jQuery.cookie(cookie_name + '[selnrctownship_cri_nrcno]');
		if( cook_nrc_div != null )
			getcri_nrc_township(cook_nrc_div, '_cri_nrcno', cook_nrc_tsp);
		
		$('#birthdate').data('datetimepicker').defaultDate(new Date());
		$('#birthdate').data('datetimepicker').format( 'DD-MM-YYYY' );
		
		if( jQuery.cookie(cookie_name + '[cri_txt_birthdate]') == null || jQuery.cookie(cookie_name + '[cri_txt_birthdate]') == "" )
		{
			jQuery.cookie(cookie_name + '[cri_txt_birthdate]', '');
			jQuery('#cri_txt_birthdate').val('');
		}

		loadpagestate();
		sFilter = getFilter();
		get_datatable_paging_cookie(cookie_name);
		get_datatable_sorting_cookie(cookie_name, "6,'desc'");

		jQuery("#frm_customer_list").submit(function(e)
		{
			if(jQuery('#frm_customer_list').valid())
			{
				getloading();
				jQuery("#frm_customer_list").unbind('submit').submit();
				return true;
			}
			//if(e.preventDefault) e.preventDefault(); else e.returnValue = false;  //cancel submit
		});
		jQuery.fn.dataTableExt.sErrMode = 'throw';// To Control JSON Alert Error
		oTable = jQuery('#dtList').dataTable(
		{
			//responsive: true,
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
			"sAjaxSource": "customer_getlist.php",
			columns: [
				{"bVisible": false, "bSortable": false, "sWidth":"5px"},
				{ "bSortable": false,"width": "80px" },
				{ "bSortable": false, "width": "60px" },
				{ "bSortable": false,"width": "150px" },
				{ "bSortable": false, "width": "150px" },
				{ "bSortable": false, "width": "150px" },
				{ "bSortable": true, "width": "100px" },
				{ "bSortable": false,"width": "250px" }
			]
		});
		jQuery('.dataTables_filter').hide();
	});

	/** create delete popup **/
	function delete_customer(customer_id, customer_name)
	{
		delete_id = customer_id;
		confirm_delete_popup('Customer '+decodeURIComponent(customer_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('customer_exec.php?authaction=delete', {'delete_customer_id':delete_id}, delete_exec_callback, 'json');
	}
</script>
<form id="frm_customer_list" name="frm_customer_list" method="POST">
	<div class="form-material mb-4">
		<div class="row">			
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<!-- <label for="cri_customer_name" class="col-form-label">အမည်</label> -->
					<input type="textbox" id="cri_customer_name" name="cri_customer_name" class="form-control" placeholder="အမည်" />
				</div>
			</div>
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<!-- <label for="cri_father_name" class="col-form-label">အဖအမည်</label> -->
					<input type="textbox" id="cri_father_name" name="cri_father_name" class="form-control" placeholder="အဖအမည်" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!-- <label class="col-form-label">မွေးသက္ကရာဇ်</label> -->
					<div class="input-group date datetimepicker-input" id="birthdate" data-target-input="nearest">
						<label class="input-group-addon p-2" for="birthdate" data-target="#birthdate" data-toggle="datetimepicker">
							<svg class="icon i-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-calendar" /></svg>
						</label>
						<input type="text" id="cri_txt_birthdate" name="cri_txt_birthdate" class="form-control datetimepicker-input" placeholder="မွေးသက္ကရာဇ်" data-target="#birthdate" />
					</div>
				</div>
			</div>			
		</div>

		<?php echo create_nrc_information("_cri_", 1, 1, '', ' style="width:380px" '); ?>
		
		<input type="submit" class="btn btn-success" name="btnsearch" value="ရှာရန်" onclick="return savepagestate();" />
		<input type="submit" class="btn btn-primary" name="btnshow" value="မှတ်တမ်းအားလုံး" onclick="clearpagestate();" />
	</div>

	<div class="clearfix m-t"></div>
	<div id="divwarningmsg" class="securitywarning"></div>
	<?php
		if( isset($_SESSION ['customer_msg']) && $_SESSION ['customer_msg'] != "" )
		{
			echo "<div id='div_sess_mes' class='alert alert-success'>". $_SESSION ['customer_msg'] .'</div>';
			unset($_SESSION ['customer_msg']);
		}
		
		if ( isset($pageenablearr["Add"])  || $usertypeid == 0 )
		{
	?>
	<div class="d-block float-none float-sm-right text-center d-sm-inline-block">
		<a href = "customer_addnew.php"><button type="button" data-toggle="modal" data-target="#modal-addnew" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button></a>
	</div>
	<?php
		}
		echo create_actionmessage_container();
	?>
	<table id="dtList" name="dtList" class="table dataTable table-striped table-hover nowrap" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th></th>
				<th>လုပ်ဆောင်ချက်</th>
				<th>စဉ်</th>
				<th>အမည်</th>
				<th>မှတ်ပုံတင်</th>
				<th>အဖအမည်</th>
				<th>မွေးသက္ကရာဇ်</th>
				<th>နေရပ်လိပ်စာ </th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="7" align="center">Loading data from server</td>
			</tr>
		</tbody>
	</table>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>