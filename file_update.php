<?php
	$movepath = '';
	$pgTitle = 'ဖိုင်ပြင်ဆင်ခြင်း';
	$currentPg = 'File Edit';
	require_once($movepath . "library/reference.php");
	require_once("autoload.php");
	require_once("adminauth.php");	
	
	//permission by user_type_application_type
	$application_cri = '';
	if ( $usertypeid != 0 && $application_type_enables !='')
		$application_cri = ' WHERE application_type_id IN ('.$application_type_enables.')';
	
	//permission by user_type_security_type
	$security_cri = '';
	if ( $usertypeid != 0 && $security_type_enables !='')
		$security_cri = ' WHERE security_type_id IN ('.$security_type_enables.')';
	
	$errors = array();
	$file_info = new file_info();
	$file_bol = new file_bol();
	$folder_name = rand(5, 15).substr(microtime(), 2,6);
	
	$file_id = 0;
	$userid = 0;
	if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
		$userid = clean($_SESSION['YRDCFSH_LOGIN_ID']);
	
	if( isset($_POST['hidfdate'] ) && $_POST['hidfdate'] !=''){
		$file_id =  clean($_POST['hidfdate']);
	}
	$pos = strpos($file_id,"..");
	if ($pos !== false) {
		die("UnAuthorized Access");
	}
	
	$urlpath_flag = true;
	if(isset($_GET['file_id']))
	{
		$file_id = clean($_GET['file_id']);
		if($file_id == 0)
			$urlpath_flag = false;
		else
		{
			$urlpath_flag = true;
			$aRow = $file_bol->select_file_byid($file_id);
			
			$file_id = htmlspecialchars($aRow['file_id']);
			$folder_id = htmlspecialchars($aRow['folder_id']);
			$letter_no1 = htmlspecialchars($aRow['letter_no']);
			$letter_count1 = htmlspecialchars($aRow['letter_count']);
			$letter_date1 = ($aRow['letter_date']!="0000-00-00")?date('d-m-Y H:i:s', strtotime(trim($aRow['letter_date']))):'';
			$description1 = htmlspecialchars($aRow['description']);
			$to_do1 = htmlspecialchars($aRow['to_do']);
			$remark1 = htmlspecialchars($aRow['remark']);
			$from_department_type1 = htmlspecialchars($aRow['from_department_type']);
			$from_department_type_name = 'ကော်မတီတွင်း';
			if($from_department_type1 != 0)
				$from_department_type_name = 'ပြင်ပ';
			
			$to_department_type1 = htmlspecialchars($aRow['to_department_type']);
			$to_department_type_name = 'ကော်မတီတွင်း';
			if($to_department_type1 != 0)
				$to_department_type_name = 'ပြင်ပ';
			
			$from_department_id1 = htmlspecialchars($aRow['from_department_id']);
			$from_department_name = htmlspecialchars($aRow['department_name']);
			$security_type_id1 = htmlspecialchars($aRow['security_type_id']);
			$application_type_id1 = htmlspecialchars($aRow['application_type_id']);
			$application_description1 = htmlspecialchars($aRow['application_description']);
			$application_reference1 = htmlspecialchars($aRow['application_references']);
			$receiver_customer_id1 = htmlspecialchars($aRow['receiver_customer_id']);
			$receiver_customer_name1 = htmlspecialchars($aRow['receiver_customer_name']);
			$sender_customer_id1 = htmlspecialchars($aRow['sender_customer_id']);
			$sender_customer_name1 = htmlspecialchars($aRow['sender_customer_name']);
			
			$to_department_arr1 = '';
			$result = $file_bol->select_file_to_department($file_id);
			if($result->rowCount() > 0)
			{
				while($row = $result->getNext())
				{
					$to_department_id[] = htmlspecialchars($row['to_department_id']);
					$to_department_arr1 = join(',', $to_department_id);
				}
			}
			//print_r($to_department_arr1);exit;
		}
	}
	else
		$urlpath_flag = false;
	if(!$urlpath_flag)
	{
		$para = ("?folder_id=$folder_id");
		header("location: file_list.php$para");
		exit();
	}
			
	$letter_no = $letter_date = $description = $application_description = $application_references = $letter_count = $to_do = $remark = $from_department_type = $to_department_type = '';	
	$from_department_id = $sender_customer_id = $receiver_customer_id = $security_type_id = $application_type_id = 0;	
	if(isset($_POST['btnupdate']))
	{
		// upload folder
		$hidfoldername = "";
		if(isset($_POST['hidfoldername']))
		{
			$hidfoldername = clean($_POST['hidfoldername']);
			$pos = strpos($hidfoldername,"..");
			if ($pos !== false) {
				die("UnAuthorized Access");
			}
			$hidfoldername = "".$hidfoldername."/";
		}
		
		$fileid = clean($_POST['hidfileid']);
		
		if( isset($_POST['txt_letter_no'] ) && $_POST['txt_letter_no'] !='')
			$letter_no = clean($_POST['txt_letter_no']);

		if( isset($_POST['txt_letter_date'] ) && $_POST['txt_letter_date'] !='')
			$letter_date = date('Y-m-d h:i:s', strtotime($_POST['txt_letter_date']));
		
		if(isset($_POST['sel_from_department_type']) && $_POST['sel_from_department_type'] !='' )
			$from_department_type = clean( clean_jscode($_POST['sel_from_department_type']));
				
		if( isset($_POST['sel_from_department'] ) && $_POST['sel_from_department'] !='')
			$from_department_id = clean($_POST['sel_from_department']);
		
		if(isset($_POST['sel_to_department_type']) && $_POST['sel_to_department_type'] !='' )
			$to_department_type = clean( clean_jscode($_POST['sel_to_department_type']));

		$to_department_arr = 0;
		if( isset($_POST['sel_to_department'] ) && $_POST['sel_to_department'] !='')
			$to_department_arr = $_POST['sel_to_department'];
		//print_r($to_department_arr);exit;

		if( isset($_POST['sel_sender_customer'] ) && $_POST['sel_sender_customer'] !='')
			$sender_customer_id = clean($_POST['sel_sender_customer']);

		if( isset($_POST['sel_receiver_customer'] ) && $_POST['sel_receiver_customer'] !='')
			$receiver_customer_id = clean($_POST['sel_receiver_customer']);

		if( isset($_POST['txt_description'] ) && $_POST['txt_description'] !='')
			$description = clean($_POST['txt_description']);

		if( isset($_POST['sel_security_type'] ) && $_POST['sel_security_type'] !='')
			$security_type_id = clean($_POST['sel_security_type']);
		
		if( isset($_POST['sel_application_type'] ) && $_POST['sel_application_type'] !='')
			$application_type_id = clean($_POST['sel_application_type']);
		
		if( isset($_POST['txt_application_description'] ) && $_POST['txt_application_description'] !='')
			$application_description = clean($_POST['txt_application_description']);
		
		if( isset($_POST['txt_application_references'] ) && $_POST['txt_application_references'] !='')
			$application_references = clean($_POST['txt_application_references']);
		
		if( isset($_POST['txt_letter_count'] ) && $_POST['txt_letter_count'] !='')
			$letter_count = clean($_POST['txt_letter_count']);
		
		if( isset($_POST['txt_to_do'] ) && $_POST['txt_to_do'] !='')
			$to_do = clean($_POST['txt_to_do']);
		
		if( isset($_POST['txt_remark'] ) && $_POST['txt_remark'] !='')
			$remark = clean($_POST['txt_remark']);

		if($letter_no == '')
			$errors[] = 'စာအမှတ် ထည့်ပေးပါရန်!';
		
		if($letter_date == '')
			$errors[] = 'ရက်စွဲ ထည့်ပေးပါရန်!';
		
		if($description == '')
			$errors[] = 'အကြောင်းအရာ ရွေးပေးပါရန်!';
		
		if($security_type_id == '')
			$errors[] = 'လုံခြုံမှုအဆင့်အတန်း ရွေးပေးပါရန်!';
		
		if($letter_count == '')
			$errors[] = 'စာရွက်အရေအတွက် ထည့်ပေးပါရန်!';
		
		if( $file_bol ->check_duplicate_file_name(to_ymd($letter_date), $letter_no, $fileid) )
			$errors[] = 'ဤဖိုင်အမျိုးအစားစာရင်းရှိနှင့်ပြီးဖြစ်သည်';
		
		if( count($errors) ==  0)
		{
			$success = FALSE;
			$file_info->set_file_id($fileid);
			$file_info->set_folder_id($folder_id);
			$file_info->set_letter_no($letter_no);
			$file_info->set_letter_date($letter_date);
			$file_info->set_from_department_type($from_department_type);
			$file_info->set_from_department_id($from_department_id);
			$file_info->set_to_department_type($to_department_type);
			$file_info->set_sender_customer_id($sender_customer_id);
			$file_info->set_receiver_customer_id($receiver_customer_id);
			$file_info->set_description($description);
			$file_info->set_security_type_id($security_type_id);
			$file_info->set_application_type_id($application_type_id);
			$file_info->set_application_description($application_description);
			$file_info->set_application_references($application_references);
			$file_info->set_letter_count($letter_count);
			$file_info->set_to_do($to_do);
			$file_info->set_remark($remark);
			$file_info->set_modified_by($userid);
			$success = $file_bol->save_file($file_info);
			if( $success == TRUE )
			{
				if(count($to_department_arr) > 0)
				{
					$file_bol->update_file_to_department($file_id, $to_department_arr);
				}
				
				$base_g_upload_path = "upload_document/file/";
		
				if( is_dir($g_upload_path.$base_g_upload_path.$file_id) == FALSE )
					mkdir($g_upload_path.$base_g_upload_path.$file_id, 0755, true);
				
				// to delete old photo
				$delfilelist = trim($_POST['web_uploadfile_hiddelfileslist']); 	
				$pos = strpos($delfilelist,"..");
				if ($pos !== false) {
					die("UnAuthorized Access");
				}
				if($delfilelist!=""){
					$filepath1_arr = explode(',',$delfilelist);
					foreach ($filepath1_arr as $filepath1) {
						$oldfilepath = $g_upload_path . $base_g_upload_path . $file_id. "/".$filepath1;
						if(file_exists($oldfilepath))
						{
							unlink($oldfilepath);
						}
					}
					sleep(1); //wait to delete old
				}
				
				// to save upload photo
				$hidfilepath = trim($_POST['web_uploadfile_hidfileinputpath']); 	
				$pos = strpos($hidfilepath,"..");
				if ($pos !== false) {
					die("UnAuthorized Access");
				}
				if($hidfilepath!=""){
					$filepath1_arr = explode(',',$hidfilepath);
					foreach ($filepath1_arr as $filepath1) {
						$newfilename = getfilename_fromtmp($filepath1 , "__rnd__");
						$filepath1 = "tmp/".$hidfoldername.$filepath1;
						$filepath1_arr = explode('.',$filepath1);
						$extension = strtolower(end($filepath1_arr));
						if($extension != '')
							$filepath1 = $g_upload_path.$filepath1;
						if(file_exists($filepath1))
						{
							copy($filepath1, $g_upload_path . $base_g_upload_path .  $file_id ."/".$newfilename);
							unlink($filepath1);
						}
					}
				}
				
				//echo $g_upload_path."/upload_document/tmp/".$hidfoldername;
				if (is_dir($g_upload_path."/upload_document/tmp/".$hidfoldername)) {
					rmdir($g_upload_path."/upload_document/tmp/".$hidfoldername);
				}
		
				$_SESSION['file_msg'] = "ဖိုင်ပြင်ဆင်ခြင်းအောင်မြင်သည်";
				$para = ("?folder_id=$folder_id");
				header("location: file_list.php$para");
				exit();
			}
		}
	}
	require_once("admin_header.php");
?>
<script language="javascript">
	var cookie_name = 'file_update';
	var movepath = '<?php echo $movepath; ?>';
	var file_id = '<?php echo $file_id; ?>';
	var folder_name = '<?php echo $folder_name; ?>';	
	var from_department_type1 = '<?php echo $from_department_type1; ?>';	
	var to_department_type1 = '<?php echo $to_department_type1; ?>';	
	var to_department_arr1 = '<?php echo $to_department_arr1; ?>';	
	
	$(document).ready(function()
	{
		Add_Validation();		
		
		$('#hidfoldername').val(folder_name);		
		var show_attwebimg1 = '<?php echo get_attachments_list($file_id,'file'); ?>';
		var show_attwebimg1_downloadlink = '<?php echo get_download_attachments_list($file_id,'file'); ?>';
		var folder_token = '<?php echo getsaltfolder_token($folder_name,'file'); ?>';
		$("#web_uploadfile").fileinput('destroy');
	    file_upload_image_upd("#web_uploadfile", show_attwebimg1 , show_attwebimg1_downloadlink, movepath , false , folder_name , 20 , 100000, ["jpg", "jpeg", "png", "gif", "pdf"], false, 'explorer', 'file', folder_token);
				
		$('#letterfromdate').data('datetimepicker').format( 'DD-MM-YYYY HH:mm:ss' );
		$('#txt_letter_date').val('<?php echo $letter_date1;?>');
		
		if( file_id != 0 && to_department_arr1 != "" )
		{
			jQuery.cookie('file_update[sel_to_department]', to_department_arr1); 
		}
		else
			jQuery.cookie('file_update[sel_to_department]', null ); 		
		
		/* Auto Complete */
		create_autocomplete(['sel_from_department_type'], "<?php echo $from_department_type1; ?>", "<?php echo $from_department_type_name; ?>");
		create_autocomplete(['sel_from_department'], "<?php echo $from_department_id1; ?>", "<?php echo $from_department_name; ?>");
		create_autocomplete(['sel_to_department_type'], "<?php echo $to_department_type1; ?>", "<?php echo $to_department_type_name; ?>");
		create_autocomplete(['sel_sender_customer'], "<?php echo $sender_customer_id1; ?>", "<?php echo $sender_customer_name1; ?>");
		create_autocomplete(['sel_receiver_customer'], "<?php echo $receiver_customer_id1; ?>", "<?php echo $receiver_customer_name1; ?>");

		/* MultiSelect */
		var array = {"sel_to_department":"ဖြန့်ဝေသည့်ဌာန အားလုံး"};
		create_multi_selectbox(array, cookie_name);
		
		if(to_department_arr1 == "")
			$("#sel_to_department").multipleSelect("uncheckAll");
	});
	
	function Add_Validation()
	{
		$("#frm_file_setup").validate(
		{
			'rules':{
				'txt_letter_no':{'required':true},
				'txt_letter_date':{'required':true},
				'txt_description':{'required':true}, 
				'sel_security_type':{'required':true}, 
				'txt_letter_count':{'required':true},
				'web_uploadfile[]': {'extension': 'jpg,jpeg,gif,png,pdf'}				
			},
			'messages': {
				'txt_letter_no':{'required':'စာအမှတ် ထည့်ပေးပါရန်!'},  
				'txt_letter_date':{'required':'ရက်စွဲ ထည့်ပေးပါရန်!'},  
				'txt_description':{'required':'အကြောင်းအရာ ရွေးပေးပါရန်!'},  
				'sel_security_type':{'required':'လုံခြုံမှုအဆင့်အတန်း ရွေးပေးပါရန်!'},  
				'txt_letter_count':{'required':'စာရွက်အရေအတွက် ထည့်ပေးပါရန်!'},
				'web_uploadfile[]': {'accept': 'Approve to upload only jpg,jpeg,gif,png,pdf!'}
			},			
		});
	}
	
	function update_file_form()
	{
		Add_Validation();
		if($('#frm_file_setup').valid())
		{
			$('#btnupdate').submit();
		}
		else
			return false;
	}
</script>

<form name="frm_file_setup" id="frm_file_setup" method="POST" class="form-material form-horizontal" enctype="multipart/form-data">
	<input type="hidden" name="hidfileid" id="hidfileid" value="<?php echo $file_id?>">
	<input type="hidden" id="hidfdate" name="hidfdate" value="<?php echo $file_id?>">
	<input type="hidden" id="hidfoldername" name="hidfoldername">
	<input type="hidden" id="web_uploadfile_hidfileinputpath" name="web_uploadfile_hidfileinputpath">
	<input type="hidden" id="web_uploadfile_hiddelfileslist" name="web_uploadfile_hiddelfileslist">
	
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
		<label class="col-form-label col-md-4 required">စာအမှတ်</label>
		<div class="col-lg-2 col-md-7 mb-lg-0 mb-3">
			<input type="text" name="txt_letter_no" id="txt_letter_no" maxlength="150" class="form-control" value="<?php echo $letter_no1; ?>">
		</div>
		<label class="col-form-label col-lg-1 col-md-4 required">ရက်စွဲ</label>
		<div class="col-lg-3 col-md-7 pos-unset">
			<div class="input-group date datetimepicker-input" id="letterfromdate" data-target-input="nearest">
				<label class="input-group-addon p-2" for="letterfromdate" data-target="#letterfromdate" data-toggle="datetimepicker">
					<svg class="icon i-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-calendar" /></svg>
				</label>
				<input type="text" id="txt_letter_date" name="txt_letter_date" class="form-control datetimepicker-input" data-target="#letterfromdate" value="<?php echo $letter_date1; ?>"/>
			</div>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">ပေးပို့သည့်ဌာန အမျိုးအစား</label>
		<div class="col-lg-6 col-md-7">
			<select name="sel_from_department_type" id="sel_from_department_type" class="form-control" onchange="show_selected_department(this.value, 'sel_from_department');">
				<?php echo get_dept_type_optionstr($from_department_type1) ; ?>
			</select>
		</div>
	</div>
		
	<div class="form-group row">
		<label class="col-form-label col-md-4">ပေးပို့သည့်ဌာန</label>
		<div class="col-lg-6 col-md-7">
			<?php echo get_department_optionstr_bydepttype('sel_from_department', $from_department_id1, $from_department_type1); ?>	
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">ဖြန့်ဝေသည့်ဌာန အမျိုးအစား</label>
		<div class="col-lg-6 col-md-7">
			<select name="sel_to_department_type" id="sel_to_department_type" class="form-control" onchange="show_selected_to_department(this.value, 'sel_to_department');">
				<?php echo get_dept_type_optionstr($to_department_type1) ; ?>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">ဖြန့်ဝေသည့်ဌာန</label>
		<div class="col-lg-6 col-md-7">
			<select id="sel_to_department" name ="sel_to_department[]" class="form-control" multiple="multiple">
				<?php echo get_to_department_optionstr_bydepttype($to_department_arr1, $to_department_type1); ?>	
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">ပေးပို့သူ</label>
		<div class="col-lg-6 col-md-7">
			<?php echo get_customer_optionstr("sel_sender_customer", $sender_customer_id1); ?>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">လက်ခံသူ</label>
		<div class="col-lg-6 col-md-7">
			<?php echo get_customer_optionstr("sel_receiver_customer", $receiver_customer_id1); ?>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">အကြောင်းအရာ</label>
		<div class="col-lg-6 col-md-7">
			<textarea name="txt_description" id="txt_description" class="form-control"><?php echo $description1; ?></textarea>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">လုံခြုံမှုအဆင့်အတန်း</label>
		<div class="col-lg-6 col-md-7">
			<?php echo get_securitytype_optionstr("sel_security_type", $security_cri, $security_type_id1); ?>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">လုပ်ငန်းအမျိုးအစား</label>
		<div class="col-lg-6 col-md-7">
			<?php echo get_application_type_optionstr("sel_application_type", $application_cri, $application_type_id1); ?>
		</div>
	</div>
		
	<div class="form-group row">
		<label class="col-form-label col-md-4">လုပ်ငန်း အကြောင်းအရာ</label>
		<div class="col-lg-6 col-md-7">
			<textarea name="txt_application_description" id="txt_application_description" class="form-control"><?php echo $application_description1; ?></textarea>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">လုပ်ငန်း ဖော်ပြချက်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_application_references" id="txt_application_references" maxlength="150" class="form-control" value="<?php echo $application_reference1; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">စာရွက်အရေအတွက်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_letter_count" id="txt_letter_count" maxlength="150" class="form-control" value="<?php echo $letter_count1; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">ဆောင်ရွက်ရန်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_to_do" id="txt_to_do" maxlength="150" class="form-control" value="<?php echo $to_do1; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">မှတ်ချက်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txt_remark" id="txt_remark" maxlength="150" class="form-control" value="<?php echo $remark1; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">Attachments File <small class="d-block text-muted">(Max size:2MB)</small></label>
		<div class="col-lg-6 col-md-7">
			<input type="file" id="web_uploadfile" name="web_uploadfile[]" accept="image/*,application/pdf" class="file-loading" multiple />
			<span class="d-block pos-rlt text-sm text-danger mt-2"><b class="required pr-3"></b> ပုံမှန်ဖိုင်များအတွက် တင်မည်ဆိုပါလျှင် ဓါတ်ပုံဆိုဒ်ကို <b>(220 * 150)</b> အနည်းဆုံးရွေးချယ်၍ တင်ပေးရပါမည်။</span>			
		</div>
	</div>
	
	<?php
		if( isset($pageenablearr["Edit"]) || $usertypeid==0 )
		{
			echo '<div class="form-group mt-4 row" id="divbuttons">
				<div class="offset-md-4 offset-sm-5 col">
					<input type="submit" class="btn btn-success" id="btnupdate" name="btnupdate" value="ပြင်ဆင်မည်" onclick="update_file_form();" />
					<input type="button" class="btn btn-outline-secondary" value="မပြင်ဆင်ပါ" onclick="window.location=\'file_list.php?folder_id='.$folder_id.'\'" />
				</div>
			</div>';
		}
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once("admin_footer.php");
?>