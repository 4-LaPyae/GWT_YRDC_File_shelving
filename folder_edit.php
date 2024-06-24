<?php
	$movepath = '';
	$pgTitle = 'စာဖိုင်တွဲ ပြင်ဆင်ခြင်း';
	$currentPg = 'Folder Edit';
	require_once($movepath . "library/reference.php");
	require_once("autoload.php");
	require_once("adminauth.php");	
	$errors = array();
	$folder_info = new folder_info();
	$folder_bol = new folder_bol();
	$folder_id = 0;

	$userid = 0;
	if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
		$userid = clean($_SESSION['YRDCFSH_LOGIN_ID']);
	
	//permission by usertype_department
	$dept_cri = '';
	if ( $usertypeid != 0 && $department_enables !='')
		$dept_cri = ' WHERE department_id IN ('.$department_enables.')';
	
	//permission by user_type_security_type
	$security_cri = '';
	if ( $usertypeid != 0 && $security_type_enables !='')
		$security_cri = ' WHERE security_type_id IN ('.$security_type_enables.')';
	
	$urlpath_flag = true;
	if(isset($_GET['folder_id']))
	{
		$folder_id = clean($_GET['folder_id']);
		if($folder_id == 0)
			$urlpath_flag = false;
		else
		{
			$urlpath_flag = true;
			$aRow = $folder_bol->select_folder_byid($folder_id);
			
			$folder_id = htmlspecialchars($aRow['folder_id']);
			$rfid_card_no = htmlspecialchars($aRow['rfid_no']);
			$folderno = htmlspecialchars($aRow['folder_no']);
			$description_name = htmlspecialchars($aRow['description']);
			$filetypeid = htmlspecialchars($aRow['file_type_id']);
			$folder_security_type_id = htmlspecialchars($aRow['security_type_id']);
			$shelfid = htmlspecialchars($aRow['shelf_id']);
			$shelfrow = htmlspecialchars($aRow['shelf_row']);
			$shelfcolumn = htmlspecialchars($aRow['shelf_column']);
		}
	}
	else
		$urlpath_flag = false;
	if(!$urlpath_flag)
	{
		header("location: folder_list.php");
		exit();
	}
	
		
	$rfid_no = $folder_no = $description = $shelf_row = $shelf_column ='';	
	$file_type_id = $security_type_id = $shelf_id = 0;
	if(isset($_POST['btnupdate']))
	{
		$folderid = clean($_POST['hidfolder_id']);
		
		if( isset($_POST['txtrfidno'] ) && $_POST['txtrfidno'] !='')
			$rfid_no = clean($_POST['txtrfidno']);

		if( isset($_POST['txtfolder_no'] ) && $_POST['txtfolder_no'] !='')
			$folder_no = clean($_POST['txtfolder_no']);

		if( isset($_POST['txtdescription'] ) && $_POST['txtdescription'] !='')
			$description = clean($_POST['txtdescription']);

		if( isset($_POST['selfiletype_id'] ) && $_POST['selfiletype_id'] !='')
			$file_type_id = clean($_POST['selfiletype_id']);

		if( isset($_POST['selsecuritytype_id'] ) && $_POST['selsecuritytype_id'] !='')
			$security_type_id = clean($_POST['selsecuritytype_id']);

		if( isset($_POST['selshelf_id'] ) && $_POST['selshelf_id'] !='')
			$shelf_id = clean($_POST['selshelf_id']);

		if( isset($_POST['txtshelf_row'] ) && $_POST['txtshelf_row'] !='')
			$shelf_row = clean($_POST['txtshelf_row']);

		if( isset($_POST['txtshelf_column'] ) && $_POST['txtshelf_column'] !='')
			$shelf_column = clean($_POST['txtshelf_column']);

		if($rfid_no == '')
			$errors[] = 'RFID No. ထည့်ပေးပါရန်!';
		
		if($folder_no == '')
			$errors[] = 'စာဖိုင်တွဲအမှတ် ထည့်ပေးပါရန်!';
		
		if($description == '')
			$errors[] = 'အကြောင်းအရာ ထည့်ပေးပါရန်!';
		
		if($file_type_id == '')
			$errors[] = 'ဖိုင်တွဲအမျိုးအစား ရွေးပေးပါရန်!';
		
		if($security_type_id == '')
			$errors[] = 'လုံခြုံမှုအဆင့်အတန်း ရွေးပေးပါရန်!';
		
		if($shelf_id == '')
			$errors[] = 'စင်အမည် ရွေးပေးပါရန်!';
		
		if($shelf_row == '')
			$errors[] = 'စင်အထပ် ထည့်ပေးပါရန်!';
		
		if($shelf_column == '')
			$errors[] = 'စင်အကန့် ထည့်ပေးပါရန်!';
		
		if( count($errors) ==  0)
		{
			$success = FALSE;
			$folder_info->set_folder_id($folderid);
			$folder_info->set_rfid_no($rfid_no);
			$folder_info->set_folder_no($folder_no);
			$folder_info->set_description($description);
			$folder_info->set_file_type_id($file_type_id);
			$folder_info->set_security_type_id($security_type_id);
			$folder_info->set_shelf_id($shelf_id);
			$folder_info->set_shelf_row($shelf_row);
			$folder_info->set_shelf_column($shelf_column);
			$folder_info->set_modified_by($userid);			
			$success = $folder_bol->save_folder($folder_info);
			if( $success == TRUE )
			{
				$_SESSION['folder_msg'] = "ဖိုင်တွဲ ပြင်ဆင်ခြင်းအောင်မြင်သည်";
				header("location: folder_list.php");
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
		Add_Validation();
		$("#frm_folder_setup").submit(function(e)
		{
			if($('#frm_folder_setup').valid())
			{
				getloading();
				$("#frm_folder_setup").unbind('submit').submit();				
				return true;
			}
		});
	});
	
	function Add_Validation()
	{
		$("#frm_folder_setup").validate(
		{
			'rules':{
				'txtfolder_no':{'required':true},
				'selfiletype_id':{'required':true}, 
				'selsecuritytype_id':{'required':true}, 
				'selshelf_id':{'required':true}, 
				'txtshelf_row':{'required':true}, 
				'txtshelf_column':{'required':true} 
			},
			'messages': {
				'txtfolder_no':{'required':'စာဖိုင်တွဲအမှတ် ထည့်ပေးပါရန်!'},  
				'selfiletype_id':{'required':'ဖိုင်တွဲအမျိုးအစား ရွေးပေးပါရန်!'},  
				'selsecuritytype_id':{'required':'လုံခြုံမှုအဆင့်အတန်း ရွေးပေးပါရန်!'},  
				'selshelf_id':{'required':'စင်အမည် ရွေးပေးပါရန်!'},  
				'txtshelf_row':{'required':'အထပ် ထည့်ပေးပါရန်!'},  
				'txtshelf_column':{'required':'အကန့် ထည့်ပေးပါရန်!'}
			},			
		});
		return false;
	}
	
	function update_folder_form()
	{
		Add_Validation();
		if($('#frm_folder_setup').valid())
		{
			$('#btnupdate').submit();
		}
		else
			return false;
	}
	
	function get_rfid_card_no()
	{
		jQuery.post('form_exec.php',{'rfid_card_no' : 'rfid_card_no'},
		function (result)
		{
			// console.log(result);
			jQuery('#txtrfidno').val(result);
		});
	}
</script>

<form name="frm_folder_setup" id="frm_folder_setup" method="POST" class="form-material form-horizontal" enctype="multipart/form-data">
	<input type="hidden" name="hidfolder_id" id="hidfolder_id" value="<?php echo $folder_id?>">
	
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
		<label class="col-form-label col-md-4">ID No.</label>
		<div class="col-md-4">
			<input type="text" name="txtrfidno" id="txtrfidno" maxlength="150" class="form-control" value="<?php echo $rfid_card_no; ?>">
		</div>
		<div class="col-md-4">
			<a href data-toggle='modal' data-target='#modal-rfid_card_no' onclick='get_rfid_card_no()' class='d-inline-flex'><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg></a>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">စာဖိုင်တွဲအမှတ်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtfolder_no" id="txtfolder_no" maxlength="150" class="form-control" value="<?php echo $folderno; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4">အကြောင်းအရာ</label>
		<div class="col-lg-6 col-md-7">
			<textarea name="txtdescription" id="txtdescription" class="form-control"><?php echo $description_name; ?></textarea>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">ဖိုင်တွဲအမျိုးအစား</label>
		<div class="col-lg-6 col-md-7">
			<?php echo get_filetype_optionstr('selfiletype_id', $filetypeid); ?>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">လုံခြုံမှုအဆင့်အတန်း</label>
		<div class="col-lg-6 col-md-7">
			<?php echo get_securitytype_optionstr('selsecuritytype_id', $security_cri, $folder_security_type_id); ?>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">စင်အမည်</label>
		<div class="col-lg-6 col-md-7">
			<?php echo get_shelf_optionstr('selshelf_id', $dept_cri, $shelfid); ?>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">အထပ်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtshelf_row" id="txtshelf_row" maxlength="150" class="form-control" value="<?php echo $shelfrow; ?>">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-form-label col-md-4 required">အကန့်</label>
		<div class="col-lg-6 col-md-7">
			<input type="text" name="txtshelf_column" id="txtshelf_column" maxlength="150" class="form-control" value="<?php echo $shelfcolumn; ?>">
		</div>
	</div>
	
	<?php
		if( isset($pageenablearr["Edit"]) || $usertypeid==0 )
		{
			echo '<div class="form-group mt-4 row" id="divbuttons">
				<div class="offset-md-4 col">
					<input type="submit" class="btn btn-success" id="btnupdate" name="btnupdate" value="ပြင်ဆင်မည်" onclick="update_folder_form();" />
					<input type="button" class="btn btn-outline-secondary" value="မပြင်ဆင်ပါ" onclick="window.location=\'folder_list.php\'" />
				</div>
			</div>';
		}
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once("admin_footer.php");
?>