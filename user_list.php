<?php
	$movepath = '';
	$pgTitle = 'အသုံးပြုသူ စာရင်း';
	$currentPg = 'User List';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	require_once('adminauth.php');
	require_once('admin_header.php');
	
	/* 1. Root Admin, 2. Department, 3. User */
	$root_admin = 0;		
	if(isset($_SESSION ['YRDCFSH_ROOT_ADMIN']))
		$root_admin = $_SESSION ['YRDCFSH_ROOT_ADMIN'];
	// echo $root_admin;exit;
	
	$cri_str =" ";		
	// permission by usertype_department
	if ( $usertypeid != 0 )
	{
		if($root_admin == 1 || $root_admin == 2 || $root_admin == 3)
			$cri_str =" WHERE 1=1 AND department_id IN ($department_enables) AND is_root_admin >= $root_admin ";
	}
	// echo $cri_str;exit;
?>
<script language = "javascript">
	var movepath = '<?php echo $movepath; ?>';
	var cookie_name = 'user_list';

	function savepagestate()
	{
		var colarr = ['cri_username', 'cri_user_email', 'cri_usertype'];
		save_criteria_in_cookie(cookie_name, colarr);
		return true;
	}

	function loadpagestate()
	{
		var colarr = ['cri_username', 'cri_user_email', 'cri_usertype'];
		load_criteria_from_cookie(cookie_name, colarr);
	}

	function clearpagestate()
	{
		var colarr = ['cri_username', 'cri_user_email', 'cri_usertype', 'iDisplayStart', 'aaSorting'];
		clear_page_cookie(cookie_name, colarr);
	}

	function getFilter()
	{
		var colarr = ['cri_username', 'cri_user_email', 'cri_usertype'];
		return return_jsonstring_from_cookie(cookie_name, colarr);
	}

	function AddValidation()
	{
		jQuery("#frmuserlist").validate(
		{
			'rules':
			{
				'txtusername':{'required':true},
				'txtuseremail':{'required':true, 'email':true},
				'txtpassword':{'required':true, checkpassword:true},
				'updateconfrimpass':{'required':true, 'equalTo':'#txtpassword'},
				'sel_user_type':{'required':true},
			},
			'messages':
			{
				'txtusername':{'required':'အမည် ထည့်ပေးပါရန်!'},
				'txtuseremail':{'required':'အီးမေးလ် ထည့်ပေးပါရန်!', 'email': 'မှန်ကန်သည့်အီးမေးလ်ပုံစံထည့်သွင်းပါ'},
				'txtpassword':{'required':'လျှို့ဝှက်နံပါတ် ထည့်ပေးပါရန်!'},
				'updateconfrimpass':{'required':'လျှို့ဝှက်နံပါတ် ထည့်ပေးပါရန်!', 'equalTo':'လျှို့ဝှက်နံပါတ် မကိုက်ညီပါ!'},
				'sel_user_type':{'required':'အမျိုးအစား ရွေးချယ်ပေးပါရန်!'},
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

		jQuery("#frmuser_setup").submit(function(e)
		{
			if(jQuery('#frmuser_setup').valid())
			{
				getloading();
				jQuery("#frmuser_setup").unbind('submit').submit();
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
				// debugger;
				if( aaData.length > 0 )
				{
					if( aaData[0][5] == 1 )
						jQuery('#divwarningmsg').html('There are invalid records! Please contact website administrator.');
					else
						jQuery('#divwarningmsg').html('');
				}
			},
			"sAjaxSource": "user_getlist.php",
			columns: [
				{ "bSortable": false, "sWidth": "80px" },
				{ "bSortable": true, "sWidth": "250px" },
				{ "bSortable": true, "sWidth": "auto" },
				{ "bSortable": true, "sWidth": "auto" },
				{ "bSortable": false,"sWidth": "100px" },
				{"bVisible": false, "bSortable": false, "sWidth":"5px"}
			]
		});
		jQuery('.dataTables_filter').hide();
	});

	/** create save new user popup **/
	function create_new_user_popup()
	{
		create_loadingimage_dialog( 'modal-addnew', 'အသုံးပြုသူ အသစ် ထည့်သွင်းခြင်း', movepath);
		$.post('user_exec.php?authaction=add', {'user_popup':'user_popup'}, function(rtn_obj)
		{
			select_data_exec_call_back(rtn_obj);
			AddValidation();
			$.validator.addMethod("checkpassword", function(value, element) {
				  return  validate_password_rule(value);
			}, "လျှို့ဝှက်နံပါတ်သည် အနည်းဆုံးစာလုံးအရေအတွက်၈လုံးရှိရမည်၊ဂဏန်း ၁လုံး၊ အက္ခရာ ၁လုံးနှင့် အထူးစာလုံး !@$%^* များထဲမှ ၁လုံးပါရမည်");			
		}, 'json');
	}

	function save_user()
	{
		if( jQuery('#frmuserlist').valid() )
		{
			var user_name = $('#txtusername').val();
			var user_email = $('#txtuseremail').val();
			var user_password = $('#txtpassword').val();
			var user_type_id = $('#sel_user_type').val();
			hidebutton_showloadingimage();
			$.post('user_exec.php?authaction=add', {'new_username':user_name, 'new_useremail':user_email, 'new_userpassword':user_password, 'new_usertype':user_type_id}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create edit user popup **/
	function create_edit_user_popup(user_id)
	{
		create_loadingimage_dialog('modal-edit', 'အသုံးပြုသူ အချက်အလက် ပြင်ဆင်ခြင်း', movepath);
		$.post('user_exec.php?authaction=edit', {'edit_user_id':user_id}, function(data)
		{
			select_data_exec_call_back(data);
			AddValidation();
		}, 'json');
	}

	function update_user()
	{
		if( jQuery('#frmuserlist').valid())
		{
			var user_id = $('#hiduserid').val();
			var username = $('#txtusername').val();
			var user_email = $('#txtuseremail').val();
			var user_type_id = $('#sel_user_type').val();
			hidebutton_showloadingimage();
			$.post('user_exec.php?authaction=edit', {'update_user_id':user_id, 'update_username':username, 'update_useremail':user_email, 'update_usertype':user_type_id}, add_and_update_exec_callback_dialog, 'json');
		}
	}

	/** create delete user popup **/
	function delete_user(user_id, user_name)
	{
		delete_id = user_id;
		confirm_delete_popup('အသုံးပြုသူ '+decodeURIComponent(user_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('user_exec.php?authaction=delete', {'delete_user_id':delete_id}, delete_exec_callback, 'json');
	}

	// change user status active or inactive without dialog
	function change_user_status(user_id, user_name, user_status)
	{
		change_id = user_id;
		change_status = user_status;
		if( change_status == 0 )
		{
			change_status = 1;
			result = confirm_status_popup(hack_htmltag(decodeURIComponent(user_name)) + ' ကို အသုံးပြုခွင့်ပေးရန် သေချာလား?', 'modal-active');
		}
		else
		{
			change_status = 0;
			result = confirm_status_popup(hack_htmltag(decodeURIComponent(user_name)) + ' ကို အသုံးပြုခွင့်ပိတ်ရန် သေချာလား?', 'modal-active');
		}
	}

	function continue_changestatus()
	{
		$.post('user_exec.php?authaction=change_status', {'change_user_id':change_id, 'change_user_status':change_status}, delete_exec_callback, 'json');
	}

	//change user password with dynamic div dialog
	function create_change_password_popup(user_id, name)
	{
		var bodycontent = '<div class="modal-body">\
								<form id="frmuserlist" name="frmuserlist" class="form-material form-horizontal" role="form" method="post" enctype="multipart/form-data" action="">\
									<div id="alert_msg"></div>\
									<div class="form-group row">\
										<input type="hidden" id="hiduserid" name="hiduserid" value="'+user_id+'" />\
										<label class="col-form-label col-md-5 required">လျှို့ဝှက်နံပါတ်</label>\
										<div class="col-md-6">\
											<input type="password" id="txtpassword" name="txtpassword" class="form-control" value="" />\
										</div>\
									</div>\
									<div class="form-group row">\
										<label class="col-form-label col-md-5 required">လျှို့ဝှက်နံပါတ်အတည်ပြုရန်</label>\
										<div class="col-md-6">\
											<input type="password" id="updateconfrimpass" name="updateconfrimpass" class="form-control" value="" />\
										</div>\
									</div>\
									<div id="divprogress"></div>\
								</form>\
								</div>\
								<div class="modal-footer" id="divbuttons">\
									<button type="button" class="btn btn-success" id="btnnew" name="btnnew" onclick="update_user_password()">သိမ်းမည် </button>\
									<button type="button" class="btn btn-outline-secondary" id="btncancel" name="btncancel" data-dismiss="modal">မသိမ်းပါ</button>\
								</div>';
		create_dialog_html('modal-changepassword', hack_htmltag(decodeURIComponent(name)) + ' ကို လျှို့ဝှက်နံပါတ်ပြောင်းလဲရန်', bodycontent);
		AddValidation();
		$.validator.addMethod("checkpassword", function(value, element) {
				  return  validate_password_rule(value);
			}, "လျှို့ဝှက်နံပါတ်သည် အနည်းဆုံးစာလုံးအရေအတွက်၈လုံးရှိရမည်၊ဂဏန်း ၁လုံး၊ အက္ခရာ ၁လုံးနှင့် အထူးစာလုံး !@$%^* များထဲမှ ၁လုံးပါရမည်");
	}

	function update_user_password()
	{
		if( jQuery('#frmuserlist').valid() )
		{
			var user_id = $('#hiduserid').val();
			var update_user_password = $('#txtpassword').val();
			var confirm_user_password = $('#updateconfrimpass').val();
			hidebutton_showloadingimage();
			$.post('user_exec.php?authaction=change_password', {'user_id':user_id, 'update_user_password':update_user_password, 'confirm_user_password':confirm_user_password}, add_and_update_exec_callback_dialog, 'json');
		}
	}
	
	$(document.body).on('hidden.bs.modal', '.modeldiv', function () {
		$(this).remove();
	});
</script>

<form id="frmuser_setup" name="frmuser_setup" method="POST">
	<div class="form-material my-4">
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<!-- <label for="cri_username" class="col-form-label">အသုံးပြုသူအမည်</label> -->
					<input type="textbox" class="form-control" id="cri_username" name="cri_username" placeholder="အသုံးပြုသူအမည်" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!-- <label for="cri_user_email" class="col-form-label">အီးမေးလ်</label> -->
					<input type="email" class="form-control" id="cri_user_email" placeholder="အီးမေးလ်" name="cri_user_email">
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<!-- <label for="cri_usertype" class="col-form-label">အသုံးပြုသူအမျိုးအစား</label> -->
					<select name="cri_usertype" id="cri_usertype" class="form-control">
						<!-- <option value="" disabled selected>အသုံးပြုသူအမျိုးအစား</option> -->
						<?php echo get_usertype_optionstr($cri_str, -1); ?>
					</select>
				</div>
			</div>
		</div>
		<input type="submit" class="btn btn-success" name="btnsearch" value="ရှာရန်" onclick="return savepagestate();" />
		<input type="submit" class="btn btn-primary" name="btnshow" value="မှတ်တမ်းအားလုံး" onclick="clearpagestate();" />
	</div>

	<div class="clearfix m-t"></div>
	<div id="divwarningmsg" class="securitywarning"></div>	
	<?php
		echo create_actionmessage_container();

		if ( isset($pageenablearr["Add"])  || $usertypeid == 0 )
		{
	?>
	<div class="d-block float-none float-sm-right text-center d-sm-inline-block">
		<button type="button" data-toggle="modal" data-target="#modal-addnew" onclick="create_new_user_popup();" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည်</button>
	</div>
	<?php
		}
		
		echo create_dataTable_table( array('အမှတ်စဉ်', 'အသုံးပြုသူအမည်', 'အီးမေးလ်', 'အသုံးပြုသူအမျိုးအစား', 'လုပ်ဆောင်ချက်', '') );
	?>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once('admin_footer.php');
?>