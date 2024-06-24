<?php
	$movepath = '';
	$pgTitle = 'အသုံးပြုသူအမျိုးအစား အသစ်ထည့်ခြင်း';
	$currentPg = 'User Type Add New';
	require_once($movepath . "library/reference.php");
	require_once("autoload.php");
	require_once("adminauth.php");
	
	/* 1. Root Admin, 2. Department, 3. User */
	$root_admin = 0;		
	if(isset($_SESSION ['YRDCFSH_ROOT_ADMIN']))
		$root_admin = $_SESSION ['YRDCFSH_ROOT_ADMIN'];
	// echo $root_admin;exit;
	
	//permission by usertype_department
	$dept_cri = array();
	if($usertypeid != 0 && $root_admin == 2)
		$dept_cri[] = $department_enables;
	// print_r($dept_cri);exit;
	
	$usertypeinfo = new usertypeinfo();
	$usertypebol = new usertypebol();
	$user_type_menu_info = new user_type_menu_info();
	$user_type_department_info = new user_type_department_info();
	$user_type_application_type_info = new user_type_application_type_info();
	$user_type_security_type_info = new user_type_security_type_info();
	$user_type_menu_bol = new user_type_menu_bol();
	$user_type_department_bol = new user_type_department_bol();
	$user_type_application_type_bol = new user_type_application_type_bol();
	$user_type_security_type_bol = new user_type_security_type_bol();

	$errmsg = array();
	
	if(isset($_POST['btnsave']))
	{
		$usertype_name = clean($_POST['txtusertype_name']);		
		
		$application_type_id = 0;
		if(isset($_POST['chkapplication']) && $_POST['chkapplication'] !='' )
			$application_type_id = $_POST['chkapplication'];
		
		$security_type_id = 0;
		if(isset($_POST['chksecurity']) && $_POST['chksecurity'] !='' )
			$security_type_id = $_POST['chksecurity'];
		
		if($usertype_name == '')
			$errmsg[] = 'အသုံးပြုသူအမျိုးအစားအမည် ထည့်ပေးပါရန်!';
		if(isset($_POST['menu']) == '')
			$errmsg[] = 'မာတိကာစာရင်း ရွေးပေးပါရန်!';
		if( $usertypebol ->check_duplicate_usertype($usertype_name) )
			$errmsg[] = 'ဤအသုံးပြုသူအမျိုးအစားအမည် ရှိနှင့်ပြီးဖြစ်သည်!';

		if(count($errmsg) == 0)
		{
			$controlmenu = $_POST['menu'];
			$department_id = $_POST['chkdepartment'];			
			
			if($root_admin == 1 || $root_admin == 0)
			{
				$usertypeinfo->set_usertype_name($usertype_name);
				$usertypeinfo->set_is_root_admin(2);
				if($root_admin == 0)
					$usertypeinfo->set_is_root_admin(1);
				if($user_type_id = $usertypebol->saveusertype($usertypeinfo))
				{
					$user_type_menu_info->set_usertype_id($user_type_id);
					$user_type_menu_info->set_menu_id($controlmenu);
					if($user_type_menu_bol->saveuser_type_menu($user_type_menu_info))
					{						
						$user_type_department_info->set_usertype_id($user_type_id);
						$user_type_department_info->set_department_id($department_id);
						$result = $user_type_department_bol->saveuser_type_department($user_type_department_info);
						
						$_SESSION['ur_type_error'] = "<div class='alert alert-success'>အသုံးပြုသူအမျိုးအစား အသစ်ထည့်ခြင်း အောင်မြင်သည်</div>";
						session_write_close();
						header("location: usertype_list.php");
						exit();
					}
				}
			}
			else if($root_admin == 2)
			{
				$usertypeinfo->set_usertype_name($usertype_name);
				$usertypeinfo->set_is_root_admin(3);
				if($user_type_id = $usertypebol->saveusertype($usertypeinfo))
				{
					$user_type_menu_info->set_usertype_id($user_type_id);
					$user_type_menu_info->set_menu_id($controlmenu);
					if($user_type_menu_bol->saveuser_type_menu($user_type_menu_info))
					{
						$user_type_department_info->set_usertype_id($user_type_id);
						$user_type_department_info->set_department_id($dept_cri);
						if($user_type_department_bol->saveuser_type_department($user_type_department_info))
						{
							$user_type_application_type_info->set_usertype_id($user_type_id);
							$user_type_application_type_info->set_application_type_id($application_type_id);
							if($user_type_application_type_bol->saveuser_type_application($user_type_application_type_info))
							{
								$user_type_security_type_info->set_usertype_id($user_type_id);
								$user_type_security_type_info->set_security_type_id($security_type_id);
								if($user_type_security_type_bol->saveuser_type_security($user_type_security_type_info))
								{
									$_SESSION['ur_type_error'] = "<div class='alert alert-success'>အသုံးပြုသူအမျိုးအစား အသစ်ထည့်ခြင်း အောင်မြင်သည်</div>";
									session_write_close();
									header("location: usertype_list.php");
									exit();
								}
							}
						}
					}
				}
			}			
		}
	}
	require_once("admin_header.php");
?>

<script language="javascript">
	var root_admin = '<?php echo $root_admin; ?>';
	
	jQuery(document).ready(function()
	{
		$('#txtusertype_name').focus();
		
		if(root_admin)
		{			
			if(root_admin == 1 || root_admin == 0)
			{
				$('#div_dept').show();
				$('#div_application_type, #div_security_type').hide();
			}
			else if(root_admin == 2)
			{
				$('#div_application_type, #div_security_type').show();
				$('#div_dept').hide();
			}
		}		
		
		jQuery("#frmusertype").submit(function(e)
		{
			if(jQuery('#frmusertype').valid())
			{
				getloading();
				jQuery("#frmusertype").unbind('submit').submit();
				return true;
			}
			//if(e.preventDefault) e.preventDefault(); else e.returnValue = false;  //cancel submit
		});
	});

	function checkmnu(obj,oj)
	{
		var arrobjChilds;
		try
		{
			if(oj.checked == true)
			{
				checkmnuparent(oj);
			}
		}
		catch(error4)
		{}

		try
		{
			arrobjChilds = obj.childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes;
			for(i=0;i<arrobjChilds.length;i++)
			{
				if(arrobjChilds[i].id != undefined)
					arrobjChilds[i].checked = oj.checked;
			}
		}
		catch(err)
		{
			try
			{
				arrobjChilds = obj.childNodes[0].childNodes[0].childNodes;

				var i=0;
				for(i=0;i<arrobjChilds.length;i++)
				{
					arrchilds = arrobjChilds[i].childNodes;

					var j=0;
					for(j=0;j<arrchilds.length;j++)
					{
						arrch = arrchilds[j].childNodes;

						var k=0;
						for(k=0;k<arrch.length;k++)
						{
							if(arrch[k].id != undefined)
							{
								arrch[k].checked = oj.checked;
							}

							if(arrch[k].innerHTML != '' && arrch[k].innerHTML != undefined)
							{
								try{
									arrtblObj = arrch[k].childNodes[1].childNodes[0].childNodes[0].childNodes;
								}catch(e){
									arrtblObj = arrch[k].childNodes[0].childNodes[0].childNodes[0].childNodes;
								}

								var l=0;
								for(l=0;l<arrtblObj.length;l++)
								{
									if(arrtblObj[l].id != undefined)
									{
										arrtblObj[l].checked = oj.checked;
									}
								}

								for(l=0;l<arrtblObj.length;l++)
								{
									if(arrtblObj[l].id != undefined)
									{
										try{
											var objtrvtmpid = document.getElementById("trvm" + arrtblObj[l].value).id;
											var objtrvtmp = document.getElementById("trvm" + arrtblObj[l].value);
											checkmnu(objtrvtmp,oj);
										}
										catch(error2)
										{

										}
									}
								}
							}
						}
					}
				}
			}catch(error3)
			{
				//alert("error3 - " + error3);
			}
		}
	}

	function checkmnuparent(obj)
	{
		var pid;
		try
		{
			arrobjChilds = obj.parentNode.parentNode.parentNode.parentNode.parentNode;
			if(arrobjChilds.id == "")
				arrobjChilds = obj.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;

			pid = arrobjChilds.id;
			pid = pid.replace(/trvm/, "");
			objpar = document.getElementById("menu[" + pid + "]");
			objpar.checked = true;
			checkmnuparent(objpar);
		}
		catch(error5)
		{
		//alert("error5 - " + error5);
		}
	}

	function checkparent(obj)
	{
		try{
				parentObj = obj.parentNode.parentNode;
				parObj = document.getElementById("outlet[" + parentObj.id + "]");

				if(obj.checked == true)
					parObj.checked = true;
				checkparent(parObj);

			}catch(error10)
			{}
	}

	function addvalidate()
	{
		$('#diverr').html('');//to clear server error
		jQuery("#frmusertype").validate(
		{
			'rules':{
				'txtusertype_name':{'required':true, 'maxlength':150},
				'menu[]':{'required':true}
			},
			'messages': {
				'txtusertype_name':{'required':'အသုံးပြုသူအမျိုးအစားအမည် ထည့်ပေးပါရန်!', 'maxlength':'စာလုံးအရေအတွက် အများဆုံး ၁၅၀ သာလက်ခံသည်!'},
				'menu[]':{'required':'မာတိကာစာရင်း ရွေးပေးပါရန်!'}
			},
			errorElement:"span",
			errorClass: "error",
			errorPlacement: function(error, element) {
				if (element.attr("name") == "menu[]" )
					error.insertAfter(".menu-tree-view");
				else
					error.insertAfter(element);
			}
			/*errorContainer:"#validerror",
			errorLabelContainer: "#validerror ul",
			wrapper: "li"*/
		});
	}
</script>

<form id="frmusertype" name="frmusertype" method="POST" class="form-material form-horizontal">
	<!-- show errors here -->
	<div id="validerror"></div>
	<?php
		/*if( count($errmsg) )
		{
			echo "<div id='diverr' class='alert alert-danger'><ul>".
				"<li><label class='text-danger-dk'>". implode("</label></li><li><label class='text-danger-dk'>", $errmsg ).
			"</label></li></ul></div>";
		}*/
	?>
	<br>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group row">
				<label class="col-form-label col-md-6">အသုံးပြုသူအမျိုးအစားအမည်</label>
				<div class="col-md-6">
					<input type="text" name="txtusertype_name" id="txtusertype_name" maxlength="150" class="form-control">
				</div>
			</div>
		</div>
	</div>
	<br />	
	<div class="row">
		<div class="col-lg-5 col-md offset-md-1">
			<?php
				// echo makemenutreeupdate(0, 'မာတိကာစာရင်း', 0, true);
				echo makemenutreeupdate(0, 'မာတိကာစာရင်း', 0, true, 0, 0, $root_admin);
			?>
			<br>
		</div>
		<div class="col-lg-5 col-md" id="div_dept">
			<?php
				echo "<table class='tree_view_tbl table table-borderless table-sm mb-0'>";
				echo  make_department_tree();
				echo "</table>";
			?>
		</div>
		<div class="col-lg-5 col-md" id="div_application_type">
			<?php	
				echo "<table class='tree_view_tbl table table-borderless table-sm mb-0'>";
				echo  make_application_type_tree();
				echo "</table>";
			?>
		</div>
	</div>
	<br />	
	<div class="row">
		<div class="col-lg-5 col-md offset-md-1" id="div_security_type">
			<?php	
				echo "<table class='tree_view_tbl table table-borderless table-sm mb-0'>";
				echo  make_security_type_tree();
				echo "</table>";
			?>
		</div>
	</div>
	<br />
	<div class="form-group row" id="divbuttons">
		<div class="col-md-6 offset-md-1">
			<input type="submit" class="btn btn-success" id="btnsave" name="btnsave" value="သိမ်းမည်" onclick="return addvalidate();"/>
			<input type="button" class="btn btn-outline-secondary" value="မသိမ်းပါ" onclick="window.location='usertype_list.php'" />
		</div>
	</div>
</form>

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once("admin_footer.php");
?>