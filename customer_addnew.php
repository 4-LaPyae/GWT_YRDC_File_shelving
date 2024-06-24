<?php
	$movepath = '';
	$pgTitle = 'Customer အသစ်ထည့်ခြင်း';
	$currentPg = 'Customer Add New';
	require_once($movepath . "library/reference.php");
	require_once("autoload.php");
	require_once("adminauth.php");	
	
	$errors = array();
	$customer_info = new customer_info();
	$customer_bol = new customer_bol();

	$userid = 0;
	if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
		$userid = clean($_SESSION['YRDCFSH_LOGIN_ID']);

	if(isset($_POST['btnsave']))
	{
		$customer_name = $father_name = $date_of_birth = $street = $house_no = $division_id = $township_id = $ward_id = '';	
		if( isset($_POST['txt_customer_name'] ) && $_POST['txt_customer_name'] !='')
			$customer_name = clean($_POST['txt_customer_name']);		

		if( isset($_POST['txt_father_name'] ) && $_POST['txt_father_name'] !='')
			$father_name = clean($_POST['txt_father_name']);

		if( isset($_POST['txt_date_of_birth'] ) && $_POST['txt_date_of_birth'] !='')
			$date_of_birth = date('Y-m-d', strtotime($_POST['txt_date_of_birth']));

		if( isset($_POST['txt_street_name'] ) && $_POST['txt_street_name'] !='')
			$street = clean($_POST['txt_street_name']);

		if( isset($_POST['txt_house_no'] ) && $_POST['txt_house_no'] !='')
			$house_no = clean($_POST['txt_house_no']);

		if( isset($_POST['sel_division_name'] ) && $_POST['sel_division_name'] !='')
			$division_id = clean($_POST['sel_division_name']);

		if( isset($_POST['sel_township_name'] ) && $_POST['sel_township_name'] !='')
			$township_id = clean($_POST['sel_township_name']);
		
		if( isset($_POST['sel_ward_name'] ) && $_POST['sel_ward_name'] !='')
			$ward_id = clean($_POST['sel_ward_name']);
		
		if( isset($_POST['rdocustomerno']))
		{
			$nrc_division_code = $nrc_township_code = $nrc_citizen_type = $nrc_number = $nrc_text = $passport = '';
			
			if( $_POST['rdocustomerno'] == 'nrc_no' &&  $_POST['txtnrcnocustomernrcno'] != '' )
			{
				$nrc_division_code = clean(clean_jscode($_POST['selnrcdivisioncustomernrcno']));
				$nrc_township_code = clean(clean_jscode($_POST['selnrctownshipcustomernrcno']));
				$nrc_citizen_type = clean(clean_jscode($_POST['selnrctypecustomernrcno']));
				$nrc_number = clean(clean_jscode($_POST['txtnrcnocustomernrcno']));
			}
			else if( $_POST['rdocustomerno'] == 'national_no'  )
				$nrc_text = is_english_string(clean($_POST['txtcustomernationalcardno']));
			else
				$passport = is_english_string(clean($_POST['txtcustomerpassportno']));
		}
		
		if($customer_name == '')
			$errors[] = 'အမည် ထည့်ပေးပါရန်!';
		if( $customer_bol ->check_duplicate_customer_name(to_ymd($date_of_birth), $customer_name) )
			$errors[] = 'ဤ Customer စာရင်းရှိနှင့်ပြီးဖြစ်သည်';
		
		if( count($errors) ==  0)
		{
			$customer_info->set_customer_name($customer_name);
			$customer_info->set_nrc_division_code($nrc_division_code);
			$customer_info->set_nrc_township_code($nrc_township_code);
			$customer_info->set_nrc_citizen_type($nrc_citizen_type);
			$customer_info->set_nrc_number($nrc_number);
			$customer_info->set_nrc_text($nrc_text);
			$customer_info->set_passport($passport);
			$customer_info->set_father_name($father_name);
			$customer_info->set_date_of_birth($date_of_birth);
			$customer_info->set_street($street);
			$customer_info->set_house_no($house_no);
			$customer_info->set_division_id($division_id);
			$customer_info->set_township_id($township_id);
			$customer_info->set_ward_id($ward_id);
			$customer_info->set_created_by($userid);
			$customer_id = $customer_bol->save_customer($customer_info);
			if ($customer_id>0)
			{
				$_SESSION['customer_msg'] = "Customer အသစ်ထည့်ခြင်းအောင်မြင်သည်";
				header("location: customer_list.php");
				exit();
			}
		}
	}
	require_once("admin_header.php");
?>
<script language="javascript">
	var movepath = '<?php echo $movepath; ?>';	
	
	$(document).ready(function()
	{
		$('#birthdate').data('datetimepicker').defaultDate(new Date());
		$('#birthdate').data('datetimepicker').format( 'DD-MM-YYYY' );
		
		var id_arr = ['selnrcdivisioncustomernrcno', 'selnrctownshipcustomernrcno'];
		create_autocomplete(id_arr);
		
		Add_Validation();
		$("#frm_customer_setup").submit(function(e)
		{
			if($('#frm_customer_setup').valid())
			{
				create_dialog_html( 'modal-nrc', 'အချက်အလက်များသိမ်းဆည်းခြင်း', movepath);
				var customer_nrc_flag = customer_national_flag = customer_passport_flag = true;

				// Check Customer NRC
				var customer_nrc_str = jQuery('#txtnrcnocustomernrcno').val();
				if( jQuery('#customer_nrc').is(':checked') )
					customer_nrc_flag = is_unicodedigit(customer_nrc_str);

				// Check Customer National
				var customer_national_str = jQuery('#txtcustomernationalcardno').val();
				if( jQuery('#customer_national').is(':checked') )
					customer_national_flag = isEnglishOnly(customer_national_str);

				// Check Customer Passport
				var customer_passport_str = jQuery('#txtcustomerpassportno').val();
				if( jQuery('#customer_passport').is(':checked') )
					customer_passport_flag = isEnglishOnly(customer_passport_str);
					
				if( customer_nrc_flag && customer_national_flag && customer_passport_flag )
				{
					// jQuery('#divdialog').parents('.ui-dialog').css('display', 'none');
					$('#bodycontent').css('display', 'none');
					getloading();
					$("#frm_customer_setup").unbind('submit').submit();
					return true;
				}
				else
				{
					var old_mes = '';
					if( ! customer_nrc_flag )
						old_mes += 'Customer ၏ နိုင်ငံသားစိစစ်ရေးကတ်ပြားအမှတ်ကို ဂဏန်းသာ ထည့်ပေးပါရန်!';
					if( ! customer_national_flag )
						old_mes += 'Customer ၏ အမျိုးသားမှတ်ပုံတင်အမှတ်ကို အင်္ဂလိပ်ဂဏန်းသာ ထည့်ပေးပါရန်!';
					if( ! customer_passport_flag )
						old_mes += 'Customer ၏ Passport No. ကို အင်္ဂလိပ်ဂဏန်းသာ ထည့်ပေးပါရန်!';						
					
					$('#bodycontent').html(old_mes);
					// $('#dialog-title').html(old_mes);
					// jQuery('#divdialog').html("<span style='color:red'>"+old_mes+"</span>");
				}				
			}
			if(e.preventDefault) e.preventDefault(); else e.returnValue = false; //cancel submit
		});
	});
	
	function Add_Validation()
	{
		$("#frm_customer_setup").validate(
		{
			'rules':{
				'txt_customer_name':{'required':true},
				'txt_street_name':{'required':true},
				'txt_house_no':{'required':true},
				'sel_division_name':{'required':true},
				'sel_township_name':{'required':true},
				'sel_ward_name':{'required':true}
			},
			'messages': {
				'txt_customer_name':{'required':'အမည် ထည့်ပေးပါရန်!'},  
				'txt_street_name':{'required':'လမ်းအမည် ထည့်ပေးပါရန်!'},
				'txt_house_no':{'required':'အိမ်အမှတ် ထည့်ပေးပါရန်!'},
				'sel_division_name':{'required':'တိုင်း/ပြည်နယ် ရွေးပေးပါရန်!'},
				'sel_township_name':{'required':'မြို့နယ် ရွေးပေးပါရန်!'},
				'sel_ward_name':{'required':'ရပ်ကွက် ရွေးပေးပါရန်!'}
			},
		});
	}
	
	function save_customer_form()
	{
		Add_Validation();
		if($('#frm_customer_setup').valid())
		{
			$('#btnsave').submit();
		}
		else
			return false;
	}
</script>

<form name="frm_customer_setup" id="frm_customer_setup" method="POST" class="form-material form-horizontal" enctype="multipart/form-data">
	
	<!-- show errors here -->
	<div id="validerror"><ul></ul></div>
	<?php
		if(count($errors))
		{
			echo "<div id='diverr' class='alert alert-danger'><ul>".
				"<li><label class='text-danger-dk'>" . implode("</label></li><li><label class='text-danger-dk'>", $errors).
			"</label></li></ul></div>";
		}
	?>
	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5 required">အမည်</label>
		<div class="col-md-5 col-sm-6">
			<input type="text" name="txt_customer_name" id="txt_customer_name" maxlength="150" class="form-control">
		</div>
	</div>

	<?php echo create_nrc_information("customer", 0, 1); ?>

	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5">အဖအမည်</label>
		<div class="col-md-5 col-sm-6">
			<input type="text" name="txt_father_name" id="txt_father_name" maxlength="150" class="form-control">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5">မွေးသက္ကရာဇ်</label>
		<div class="col-md-5 col-sm-6 pos-unset">
			<div class="input-group date datetimepicker-input" id="birthdate" data-target-input="nearest">
				<label class="input-group-addon p-2" for="birthdate" data-target="#birthdate" data-toggle="datetimepicker">
					<svg class="icon i-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-calendar" /></svg>
				</label>
				<input type="text" id="txt_date_of_birth" name="txt_date_of_birth" class="form-control datetimepicker-input" data-target="#birthdate" />
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5 required">အိမ်အမှတ်</label>
		<div class="col-lg-1 col-md-5 col-sm-6 mb-lg-0 mb-3">
			<input type="text" name="txt_house_no" id="txt_house_no" maxlength="150" class="form-control">
		</div>
		<label class="col-form-label col-lg-auto col-md-4 col-sm-5 required">လမ်းအမည်</label>
		<div class="col-lg-3 col-md-5 col-sm-6">
			<input type="text" name="txt_street_name" id="txt_street_name" maxlength="150" class="form-control">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5 required">တိုင်း/ပြည်နယ်</label>
		<div class="col-md-5 col-sm-6" id="divdistrict">
			<select name="sel_division_name" id="sel_division_name" class="form-control" onchange="get_township_by_division_id(this.value,'')">
				<?php echo get_division_optionstr(); ?>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-form-label col-md-4 col-sm-5 required">မြို့နယ်</label>
		<div class="col-xl-2 col-md-5 col-sm-6 mb-xl-0 mb-3" id="divtownship">
			<select id="sel_township_name" name="sel_township_name" class="form-control" onchange="get_division_ward_by_township_id(this.value, '');" disabled>
				<?php echo get_township_optionstr(); ?>
			</select>
		</div>
		<label class="col-form-label col-xl-1 col-md-4 col-sm-5 required">ရပ်ကွက်</label>
		<div class="col-xl-2 col-md-5 col-sm-6" id="divward">
			<select id="sel_ward_name" name="sel_ward_name" class="form-control">
				<?php echo get_ward_optionstr(); ?>
			</select>
		</div>
	</div>

	<?php
		if( isset($pageenablearr["Add"]) || $usertypeid==0 )
		{
			echo '<div class="form-group row my-4" id="divbuttons">
				<div class="offset-md-4 offset-sm-5 col">
					<input type="submit" class="btn btn-success" id="btnsave" name="btnsave" value="သိမ်းမည်" onclick="save_customer_form();" />
					<input type="button" class="btn btn-outline-secondary" value="မသိမ်းပါ" onclick="window.location=\'customer_list.php\'" />
				</div>
			</div>';
		}
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once("admin_footer.php");
?>