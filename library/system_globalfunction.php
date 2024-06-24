<?php
	// to show nested checkbox under master checkbox, use in usertype.php
	function makemenutreeupdate($nodeid, $nodename, $value, $show, $usertype=0, $parent_id=0, $is_root_admin=0)
	{
		global $usertypebol;
		$tmpstr1 = '';
		$tmpstr2 = '';
		$tmpstr = '';
		$retstr2 = '';
		$checkstring="";
		
		// echo $is_root_admin;exit;
		$menu_type = ' AND menu_type IN (1, 3)';
		if($is_root_admin !=2)
			$menu_type = ' AND menu_type IN (1, 2)';
		
		$result = $usertypebol->getmenutree($nodeid, $menu_type);
		$rownum = $result->rowCount();
		
		while( $row=$result->getNext() )
		{
			if( $row['submenu'] == 1 )
			{		
				$tmpstr1 .= makemenutreeupdate($row['menu_id'], $row['menu_name'], $row['menu_id'], $show, $usertype, $row['parent_id'], $is_root_admin);
			}
			else 
			{
				$checkstring = "";
				if($usertype>0)
				{
					$menu=$usertypebol->getusermenu($usertype);
				
					if(count($menu)>0)
					{
						foreach ($menu as $mval) 
						{
							if($row['menu_id']==$mval)
							{
								$checkstring="checked";
								break;
							}
						}
					}
				}
				
				$tmpstr2 .= makemenutreeupdate($row['menu_id'], $row['menu_name'], $row['menu_id'], $usertype, $row['parent_id'], $is_root_admin);
				$retstr2 .= "<input type ='checkbox' id ='menu[".$row['menu_id']."]' name ='menu[]' ";
				$cancel_check = ($show==false) ? "return false;":"checkmnu(document.getElementById(\"trvm$row[menu_id]\"),this);"; 
				$retstr2 .= " value='".$row['menu_id']."' " . $checkstring . " onclick='$cancel_check' class='align-middle' /> $row[menu_name] <div class='clearfix mb-2'></div>";
			}
		}
		$checkstring = "";
		if( $usertype > 0 )
		{
			$menu=$usertypebol->getusermenu($usertype);
		
			if(count($menu)>0)
			{
				foreach ($menu as $mval) 
				{
					if($value==$mval)
					{
						$checkstring="checked";
						break;
					} 
				}		
			}
		}		
		if($tmpstr1!='')
		{
			if($nodeid==0)
				$disrow = "";
			else $disrow = "style='display:none;'";
			if($nodename=="မာတိကာစာရင်း")
			{
				$retstr="<h5 class='menu-tree-view'>$nodename</h5>";
			}
			else
			{
				$retstr = "<tr><td name='tdmeu$parent_id' id='tdmeu$value'>";
				$retstr .= "<img id= 'img$parent_id' border='0' src='images/plus-square.svg' value='$value' onclick='changeimageroot(this,\"trvm$value\");' class='i-xs mr-2' />";
				$retstr .= "<input type ='checkbox' ";					
				$cancel_check = ($show==false) ? "return false;":"checkmnu(document.getElementById(\"trvm$value\"),this);"; 					
				$retstr .= " id='menu[$value]' name ='menu[]' value='$value' " . $checkstring . " onclick='$cancel_check' class='align-middle' /> $nodename</td></tr>";
			}
			
			$retstr .= "<tr><td $disrow id='trvm$value'><table class='tree_view_tbl table table-borderless table-sm mb-0'>";
			if($retstr2 != "")
			{
					$retstr .= "<tr><td><table class='tree_view_tbl table table-borderless table-sm mb-0'>
					<tr><td>$retstr2</td></tr></table></td></tr>";
			}
			$retstr .= "$tmpstr1</table></td></tr>";
			
		}
		else
		{
			$retstr = "<tr><td>";
			if($tmpstr2 != "")
				$retstr .= "<img id= 'img$parent_id' border='0' src='images/plus-square.svg' value='$value' onclick='changeimageroot(this,\"trvm$value\");' class='i-xs mr-2' />";
				
			$retstr .= "<input type='checkbox' ";				
			$cancel_check = ($show==false) ? "return false;":"checkmnu(document.getElementById(\"trvm$value\"),this);"; 					
			$retstr .= " id ='menu[$value]' name ='menu[]' value='$value' " . $checkstring . " onclick='$cancel_check' class='align-middle' /> $nodename</td></tr>";
			
			if($retstr2 != "")
			{
					$retstr .= "<tr><td style='display:none' id='trvm$value'><table class='tree_view_tbl table table-borderless table-sm mb-0'>
					<tr><td>$retstr2</td></tr></table></td></tr>";
			}
		}
		return $retstr;
	}
	
	function make_department_tree($usertype_id = 0)
	{
		$retstr = '';
		$department_arr = array();
		$checkstring = '';

		if($usertype_id != 0)
		{
			$user_type_department_bol = new user_type_department_bol();
			$usertype_result = $user_type_department_bol->select_department_by_usertype($usertype_id);
			if($usertype_result->rowCount() > 0)
			{
				while($usertype_row = $usertype_result->getNext())
				{
					$department_arr[] = $usertype_row['department_id'];
				}
			}
		}
		
		$all_department_chk = '';
		if ( count($department_arr) > 0)
			$all_department_chk = 'checked';
		$department_bol = new department_bol();
		$department_result = $department_bol->get_all_department('WHERE is_external <> 1');
		$retstr = "<h5 class='dept-tree-view'>ဌာနစာရင်း</h5>";
		$retstr .= '<thead><tr><th><input type="checkbox" id="chkdept"  '.$all_department_chk.' onclick="$(this).prop(\'checked\')? $(\'.tddept input[type=checkbox]\').prop(\'checked\', true) : $(\'.tddept input[type=checkbox]\').prop(\'checked\', false)" class="align-middle" /> ဌာနအားလုံး </th></tr></thead>';
		
		while($department_row = $department_result->getNext())
		{
			if (in_array($department_row['department_id'] ,$department_arr))
				$checkstring = " checked ";
			else
				$checkstring = "  ";
			$retstr .= '<tr><td class="pl-4"><input type="checkbox" name="chkdepartment[]" id="chkdepartment'.$department_row['department_id'] . '" value="' . $department_row['department_id'] . '" '.$checkstring.' class="align-middle" /> '. htmlspecialchars($department_row['department_name']) .'</td></tr>';
		}
		return $retstr;
	}
	
	/* Application Type Tree */
	function make_application_type_tree($usertype_id = 0)
	{
		$retstr = '';
		$application_type_arr = array();
		$checkstring = '';

		if($usertype_id != 0)
		{
			$user_type_application_type_bol = new user_type_application_type_bol();
			$usertype_result = $user_type_application_type_bol->select_application_by_usertype($usertype_id);
			if($usertype_result->rowCount() > 0)
			{
				while($usertype_row = $usertype_result->getNext())
				{
					$application_type_arr[] = $usertype_row['application_type_id'];
				}
			}
		}
		
		$all_application_type_chk = '';
		if ( count($application_type_arr) > 0)
			$all_application_type_chk = 'checked';
		
		$application_type_bol = new application_type_bol();
		$application_result = $application_type_bol->get_all_application_type();
		$retstr = "<h5 class='application-type-tree-view'>လုပ်ငန်းအမျိုးအစားစာရင်း</h5>";
		$retstr .= '<thead><tr><th><input type="checkbox" id="chkapp"  '.$all_application_type_chk.' onclick="$(this).prop(\'checked\')? $(\'.tdapp input[type=checkbox]\').prop(\'checked\', true) : $(\'.tdapp input[type=checkbox]\').prop(\'checked\', false)" class="align-middle" /> လုပ်ငန်းအမျိုးအစား အားလုံး</th></tr></thead>';
		
		while($application_row = $application_result->getNext())
		{
			if (in_array($application_row['application_type_id'], $application_type_arr))
				$checkstring = " checked ";
			else
				$checkstring = "  ";
			$retstr .= '<tr><td class="pl-4"><input type="checkbox" name="chkapplication[]" id="chkapplication'.$application_row['application_type_id'] . '" value="' . $application_row['application_type_id'] . '" '.$checkstring.' class="align-middle" /> '. htmlspecialchars($application_row['application_type_name']) .'</td></tr>';
		}
		return $retstr;
	}
	
	/* Security Type Tree */
	function make_security_type_tree($usertype_id = 0)
	{
		$retstr = '';
		$security_type_arr = array();
		$checkstring = '';

		if($usertype_id != 0)
		{
			$user_type_security_type_bol = new user_type_security_type_bol();
			$usertype_result = $user_type_security_type_bol->select_security_by_usertype($usertype_id);
			if($usertype_result->rowCount() > 0)
			{
				while($usertype_row = $usertype_result->getNext())
				{
					$security_type_arr[] = $usertype_row['security_type_id'];
				}
			}
		}
		
		$all_security_type_chk = '';
		if ( count($security_type_arr) > 0)
			$all_security_type_chk = 'checked';
		
		$security_type_bol = new security_type_bol();
		$security_result = $security_type_bol->get_all_security_type();
		$retstr = "<h5 class='security-type-tree-view'>လုံခြုံမှု့အဆင့်အတန်း စာရင်း</h5>";
		$retstr .= '<thead><tr><th style="text-align:left;"><input type="checkbox" id="chksec"  '.$all_security_type_chk.' onclick="$(this).prop(\'checked\')? $(\'.tdsecurity input[type=checkbox]\').prop(\'checked\', true) : $(\'.tdsecurity input[type=checkbox]\').prop(\'checked\', false)"/> လုံခြုံမှု့အဆင့်အတန်း အားလုံး </th></tr></thead>';
		
		while($security_row = $security_result->getNext())
		{
			if (in_array($security_row['security_type_id'], $security_type_arr))
				$checkstring = " checked ";
			else
				$checkstring = "  ";
			$retstr .= '<tr><td style="vertical-align:top;padding:2px 0 0 25px;" class="tdsecurity"><input type="checkbox" name="chksecurity[]" id="chksecurity'.$security_row['security_type_id'] . '" value="' . $security_row['security_type_id'] . '" '.$checkstring.' />'. htmlspecialchars($security_row['security_type_name']) .'</td></tr>';
		}
		return $retstr;
	}
	
	function show_errors_message($errors_arr)
	{
		if( count($errors_arr) )
			echo "<div  class='alert alert-danger'><ul>". "<li><label class='text-danger-dk'>". implode("</label></li><li><label class='text-danger-dk'>", $errors_arr ).
			"</label></li></ul></div>";
	}
	
	function get_number_decimal($number)
	{
		if($number == 0)
			return '';
		else
		{
			$number_arr = explode('.', $number);	
			if(count($number_arr) == 1)
				return $number;
			else
				return number_format($number, 2, '.', '');
		}
	}
	
	// show successful message in datatable
	function create_actionmessage_container($id = 'validateTips', $class = '')
	{
		return '<div id="'. $id .'" class="'. $class .'"></div>';
	}

	function is_used_in_table($table_name, $filter_str)
	{
		$userbol = new userbol();
		return $userbol->is_used_in_table($table_name, $filter_str);
	}

	// create select box for usertype, use in user_list.php
	function get_usertype_optionstr($cri_str, $selected = '')
	{
		$usertypebol = new usertypebol();
		$user_option_obj = $usertypebol->select_all_usertype($cri_str);
		$return_str = '<option value="" default selected disabled>-- အသုံးပြုသူအမျိုးအစား ရွေးချယ်ရန် --</option>';
		while( $row = $user_option_obj->getNext() ) 
		{
			$selectedid = ( $selected == $row['user_type_id'] ) ? 'selected':'';
			$return_str .= '<option value="'. $row['user_type_id'] .'"  '. $selectedid .'>'. htmlspecialchars($row['user_type_name']) .'</option>';			
		}
		return $return_str;
	}
	
	// show datatable, use in both admin & public pages
	function create_dataTable_table($col_arr, $id = 'dtList' )
	{
		$table_str = '<table id="'. $id .'" name="'. $id .'" class="table dataTable table-striped table-hover dt-responsive nowrap" cellspacing="0" width="100%">
						<thead>
							<tr>';
			$table_str .= '<th>'. implode("</th><th>", $col_arr) . '</th>';							
			$table_str .= '</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="'.count($col_arr).'" align="center">Loading data from server</td>
							</tr>
						</tbody>
					</table>';
		return $table_str;
	}
	
	function get_division_optionstr($selected = '')
	{
		$division_bol = new division_bol();
		$result = $division_bol->get_all_division();
		$return_str = '<option value="">-- ရွေးရန် --</option>';
		while( $row = $result->getNext() ) 
		{
			$selectedid = ( $selected == $row['division_id'] ) ? 'selected':'';
			$return_str .= '<option value="'. $row['division_id'] .'"  '. $selectedid .'>'. htmlspecialchars($row['division_name']) .'</option>';			
		}
		return $return_str;
	}
	
	function get_township_optionstr($selected = '')
	{
		$township_bol = new township_bol();
		$result = $township_bol->get_all_township();
		$return_str = '<option value="">-- ရွေးရန် --</option>';
		while( $row = $result->getNext() ) 
		{
			$selectedid = ( $selected == $row['township_id'] ) ? 'selected':'';
			$return_str .= '<option value="'. $row['township_id'] .'"  '. $selectedid .'>'. htmlspecialchars($row['township_name']) .'</option>';			
		}
		return $return_str;
	}
	
	function get_ward_optionstr($selected = '')
	{
		$ward_bol = new ward_bol();
		$result = $ward_bol->get_all_ward();
		$return_str = '<option value="">-- ရွေးရန် --</option>';
		while( $row = $result->getNext() ) 
		{
			$selectedid = ( $selected == $row['ward_id'] ) ? 'selected':'';
			$return_str .= '<option value="'. $row['ward_id'] .'"  '. $selectedid .'>'. htmlspecialchars($row['ward_name']) .'</option>';			
		}
		return $return_str;
	}
	
	function create_nrc_information($id, $cri = 0, $hide_star = 0, $label = '', $attr = '')
	{
		/*$star_str = '<span class="star">*</span>';
		if( $hide_star == 1 )
			$star_str = '';*/
		$return_str = '';
		$star_str = '<label class="col-form-label col-md-4 col-sm-5 required">';
		$star_div = '<div class="col-md-8 col-sm-7">';
		if( $hide_star == 1 )
			$star_str = '<label class="col-form-label col-md-4 col-sm-5">'; //col-lg-6 col-md-7
		
		if( $cri == 1 )
			$star_div = '<div class="col-md-8">';

		$return_str = '<div class="form-group row">'.
			$star_str. $label .' မှတ်ပုံတင်ကတ်ပြားအမျိုးအစား</label>'.
			$star_div.'<div class="form-check"><label class="form-check-label"><input type="radio" value="nrc_no" id="'. $id .'_nrc" name="rdo'.$id.'no" checked onclick="show_card_table(\'div_nrc_'.$id.'\', \'div_national_'.$id.'\', \'div_passport_'.$id.'\')" class="form-check-input">
					နိုင်ငံသားစိစစ်ရေးကတ်ပြားအမှတ်
				</label></div>
				<div class="form-check"><label class="form-check-label"><input type="radio" value="national_no" id="'. $id .'_national"  name="rdo'.$id.'no" onclick="show_card_table(\'div_national_'.$id.'\', \'div_nrc_'.$id.'\', \'div_passport_'.$id.'\')" class="form-check-input">
					အမျိုးသားမှတ်ပုံတင်အမှတ် <i class="text-info">(နိုင်ငံသားစိစစ်ရေးကတ်ပြားမရှိလျှင်)</i>
				</label></div>
				<div class="form-check"><label class="form-check-label"><input type="radio" value="passport_no" id="'. $id .'_passport"  name="rdo'.$id.'no" onclick="show_card_table(\'div_passport_'.$id.'\', \'div_nrc_'.$id.'\', \'div_national_'.$id.'\')" class="form-check-input">
					Passport No. <i class="text-info">(နိုင်ငံခြားသားဖြစ်လျှင်)</i>
				</label></div>
			</div>
		</div><!-- /form-group -->

		<div id="div_nrc_'.$id.'"  class="form-group row scombo_dropbox"> 
			'.$star_str.'နိုင်ငံသားစိစစ်ရေးကတ်ပြားအမှတ် </label>'.
			$star_div . get_nrc_option($id.'nrcno', $cri) .'
			<div id="duplicatenrcnomsg" class="error"></div>
			</div>
		</div>
		<div id="div_national_'.$id.'" class="form-group row" style="display:none">
			'.$star_str.'အမျိုးသားမှတ်ပုံတင်အမှတ်</label>
			<div class="col-md-5 col-sm-6">
				<input type="text" id="txt'.$id.'nationalcardno" name="txt'.$id.'nationalcardno" maxlength="25" class="form-control" onchange="isEnglishOnly(this.value, \'အမျိုးသားမှတ်ပုံတင်အမှတ်\')"  />
				<div id="duplicatenationalnomsg" class="error"></div>
			</div> <!-- /nationalcardno -->
		</div>
		<div id="div_passport_'.$id.'" class="form-group row" style="display:none">
			'.$star_str.'Passport No. </label>
			<div class="col-md-5 col-sm-6">
				<input type="text" id="txt'.$id.'passportno" name="txt'.$id.'passportno" maxlength="25" class="form-control" onchange="isEnglishOnly(this.value, \'Passport No.\')" />
				<div id="duplicatepassportmsg" class="error"></div>
			</div> <!-- /passportno -->
		</div>';
		return $return_str;
	}
	
	function get_nrc_option($id, $cri_status = 0, $nrc1 = 0, $nrc2 = 0)
	{
		global $division_arr;
		$nrcbol = new nrcbol();
		if($nrc1 != '')
			$result = $nrcbol->get_nrc_division_township_byid($nrc1);
		else
			$result = $nrcbol->get_nrc_division_township();
		if( $cri_status == 1 || $nrc1 != '' )
			$division_str ="<select id='selnrcdivision$id' name='selnrcdivision$id'  class='form-control w-auto mr-1' onchange=getcri_nrc_township(this.value,'$id','')>";
		else
			$division_str ="<select id='selnrcdivision$id' name='selnrcdivision$id'  class='form-control w-auto mr-1' onchange=get_nrc_township(this.value,'$id','')>";
			$township_str = "<select id='selnrctownship$id' name='selnrctownship$id'  class='form-control w-auto mr-1'>";
		foreach($division_arr as $val)
		{
			$division_str .= "<option value='$val'> $val </option>";
		}
		 if($result->rowCount() > 0)
		{
			while($row = $result->getNext())
			{
				if($row['type'] == 'township')
					$township_str .= "<option value='$row[value]'> $row[value] </option>";
			}
		} 

		$division_str .= "</select>";
		$township_str .= "</select>";
		$type = "<select id='selnrctype$id' name='selnrctype$id' class='form-control w-auto'>".
					"<option value='နိုင်'> နိုင် </option>".
					"<option value='ဧည့်'> ဧည့် </option>".
					"<option value='ပြု'> ပြု </option>".
				"</select>";
		$no = "<input type='text' name='txtnrcno$id' id='txtnrcno$id' maxlength='6' class='form-control ml-1' style='width:20%' onchange=is_unicodedigit(this.value,'နိုင်ငံသားစိစစ်ရေးကတ်ပြားအမှတ်')>";

		$str = "<div class='form-inline flex-nowrap'>" . $division_str. " /  ". $township_str . " (". $type .")" . $no . "</div>";
		return $str;
	}
	
	function is_english_string($str)
	{
		$flag =  $str;
		if( $str != '' )
		{
			if( preg_match('/^([a-zA-Z\/\\])+(\-)+([0-9])+$/', $str) == FALSE )
				$flag = 'FALSE';
		}
		return $flag;
	}
	
	function get_location_optionstr($id, $sel_id = -1, $cri="")
	{
		$location_bol = new location_bol();
		$result = $location_bol->get_all_location();
		$str = "<select id='$id' name='$id' class='form-control'>";
		if($cri == "cri")
			$str .="<option value='' default selected disabled>တည်နေရာအမည် အားလုံး</option>";
		else
			$str .="<option value='0'></option>";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['location_id']."'";
			if( $data['location_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['location_name'].'</option>';
		}		
		$str .= "</select>";
		return $str;
	}
	
	function get_dept_type_optionstr($dept_type_id = 0)
	{
		global $department_type_arr;
		$dept_type_str = "<option value=''>-- ရွေးရန် --</option>";
		foreach($department_type_arr as $key=>$value)
		{
			$dept_type_str .= "<option value = '$key'";
			if($dept_type_id == $key)
				$dept_type_str .= " selected ";
			$dept_type_str .= "'>" . htmlspecialchars($value) . "</option>";
		}
		return $dept_type_str;
	}
	
	function get_department_optionstr($id, $department_enables='', $sel_id = -1, $cri="")
	{
		$department_bol = new department_bol();
		$result = $department_bol->get_all_department($department_enables);
		$str = "<select id='$id' name='$id' class='form-control'>";
		if($cri == "cri")
			$str .="<option value='' default selected disabled>ဌာနအမည် အားလုံး</option>";
		else
			$str .="<option value='0'></option>";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['department_id']."'";
			if( $data['department_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['department_name'].'</option>';
		}		
		$str .= "</select>";
		return $str;
	}
	
	function get_to_department_optionstr($selected_id = 0)
	{ 
		$department_bol = new department_bol();
		$result = $department_bol->get_all_department('');
		
		$return_str = '';
		if( $result->rowCount() > 0 )
		{
			while( $row = $result->getNext() )
			{
				$select_id = ( $selected_id == $row['department_id'] ) ? 'selected ':'';
				$return_str .= "<option value='".$row['department_id']."' $select_id >". htmlspecialchars($row['department_name']) ."</option>";
			}
		}
		return $return_str;
	}
	
	function get_department_optionstr_bydepttype($id, $sel_id = -1, $type, $cri="")
	{
		$department_bol = new department_bol();
		$result = $department_bol->get_department_by_depttype($type);
		$str = "<select id='$id' name='$id' class='form-control'>";
		if($cri == "cri")
			$str .="<option value=''>အားလုံး</option>";
		else
			$str .="<option value=''></option>";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['department_id']."'";
			if( $data['department_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['department_name'].'</option>';
		}		
		$str .= "</select>";
		return $str;
	}
	
	function get_to_department_optionstr_bydepttype($sel_id = -1, $type)
	{
		$department_bol = new department_bol();
		$result = $department_bol->get_department_by_depttype($type);
		$str = "";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['department_id']."'";
			if( $data['department_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['department_name'].'</option>';
		}
		return $str;
	}	
	
	function get_customer_optionstr($id, $sel_id = -1, $cri="")
	{
		$option_name = 'ပေးပို့သူ';
		if($id == "cri_receiver_customer_id")
			$option_name = 'လက်ခံသူ';
		
		$customer_bol = new customer_bol();
		$result = $customer_bol->get_all_customer();
		$str = "<select id='$id' name='$id' class='form-control'>";
		if($cri == "cri")
			$str .="<option value='' default selected disabled>$option_name အားလုံး</option>";
		else
			$str .="<option value=''></option>";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['customer_id']."'";
			if( $data['customer_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['customer_name'].'</option>';
		}		
		$str .= "</select>";
		return $str;
	}
	
	function get_filetype_optionstr($id, $sel_id = -1, $cri="")
	{
		$file_type_bol = new file_type_bol();
		$result = $file_type_bol->get_all_file_type();
		$str = "<select id='$id' name='$id' class='form-control'>";
		if($cri == "cri")
			$str .="<option value='' default selected disabled>ဖိုင်တွဲအမျိုးအစားအမည် အားလုံး</option>";
		else
			$str .="<option value=''></option>";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['file_type_id']."'";
			if( $data['file_type_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['file_type_name'].'</option>';
		}		
		$str .= "</select>";
		return $str;
	}
	
	function get_securitytype_optionstr($id, $security_type_enables='', $sel_id = -1, $cri="")
	{
		$security_type_bol = new security_type_bol();
		$result = $security_type_bol->get_all_security_type($security_type_enables);
		$str = "<select id='$id' name='$id' class='form-control'>";
		if($cri == "cri")
			$str .="<option value='' default selected disabled>ဖိုင်လုံခြုံမှုအဆင့်အတန်း အားလုံး</option>";
		else
			$str .="<option value=''></option>";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['security_type_id']."'";
			if( $data['security_type_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['security_type_name'].'</option>';
		}		
		$str .= "</select>";
		return $str;
	}
	
	function get_application_type_optionstr($id, $application_type_enables='', $sel_id = -1, $cri="")
	{
		$application_type_bol = new application_type_bol();
		$result = $application_type_bol->get_all_application_type($application_type_enables);
		$str = "<select id='$id' name='$id' class='form-control'>";
		if($cri == "cri")
			$str .="<option value='' default selected disabled>လုပ်ငန်းအမျိုးအစား အားလုံး</option>";
		else
			$str .="<option value=''></option>";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['application_type_id']."'";
			if( $data['application_type_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['application_type_name'].'</option>';
		}		
		$str .= "</select>";
		return $str;
	}
	
	function get_shelf_optionstr($id, $department_enables='', $sel_id = -1, $cri="")
	{
		$shelf_bol = new shelf_bol();
		$result = $shelf_bol->get_all_shelf($department_enables);
		$str = "<select id='$id' name='$id' class='form-control'>";
		if($cri == "cri")
			$str .="<option value='' default selected disabled>စင်နံပါတ် အားလုံး</option>";
		else
			$str .="<option value=''></option>";
		while($data = $result->getNext())
		{
			$str .= "<option value='".$data['shelf_id']."'";
			if( $data['shelf_id'] == $sel_id )
				$str .= " selected";
			$str .= '>'.$data['shelf_name'].'</option>';
		}		
		$str .= "</select>";
		return $str;
	}
	
	 /*
     * Create a class to handle management of this hash:
     *
     * @param   string  $foldername		foldername
     * @param   string  $uploadfiletype	uploadfiletype defined at config
     * @return  string  $hash			token string
     */
	class GWTFixedSaultHashPassword {
		const SALT = 'In0V@t!V3G10bA7W@V32018!';
	 
		 /*
		 * To generate hash
		 *
		 * @param   string  $password		password or data to make salt
		 * @return  string  $hash			token string
		 */
		public static function hash($password) {
			return hash('sha512', self::SALT . $password);
		}
	 
		 /*
		 * To verify hash
		 *
		 * @param   string  $password	password or data to make salt
		 * @param   string  $hash		previously generated salt token to verify
		 * @return  bool  $flag		true or false
		 */
		public static function verify($password, $hash) {
			return ($hash == self::hash($password));
		}
	}
	
	 /*
	 * To get predifined MIME type by ext
	 *
	 * @param   string  $ext	file ext
	 * @return  string  $type	if ext is include at array, it will return mime type
	 */
	function getFileExtensionfromMediaType($ext) {

        $mime_types = array(

            'text/plain' => 'txt',
            'application/json' => 'json',
            'application/xml' => 'xml',
            'application/x-shockwave-flash' => 'swf',
            'video/x-flv' => 'flv' ,
            'video/mp4' => 'mp4' ,

            // images
            'image/png' => 'png' ,
            'image/jpeg' => 'jpeg' ,
            'image/jpg' => 'jpg' ,
            'image/gif' => 'gif',
            'image/bmp' => 'bmp' ,
            'image/vnd.microsoft.icon' => 'ico' ,
            'image/tiff' => 'tiff' ,
            'image/svg+xml' => 'svg' ,
            
            // archives
            'application/zip' => 'zip' ,
            'application/x-rar-compressed' => 'rar' ,
                        
            // adobe
            'application/pdf' => 'pdf',
            
            // ms office
            'application/msword' => 'doc' ,
            'application/rtf' => 'rtf',
            'application/vnd.ms-excel' => 'xls' ,
            'application/vnd.ms-powerpoint' => 'ppt',
        );

        //$ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        return "";
    }
	
	 /*
     * To generate attach file list as thumbnail list
     *
     * @param   string  $subfoldername		sub folder name not include / and ..
     * @param   string  $uploadfiletype		uploadfiletype defined at config
     * @param   string  $width				width for thumbnail image
     * @param   string  $height				height for thumbnail image
     * @return  string  $show_img1			thumbnail file list string
     */
	function get_attachments_list($subfoldername, $uploadfiletype, $width = "1110" ,$height = "400" )
	{
		if(strpos($subfoldername,"..") !== false || strpos($subfoldername,"/") !== false) {
			die("UnAuthorized Access");
		}
		
		global $g_upload_path;
		global $g_upload_path_type; 
		$base_g_upload_path = "";
		if (array_key_exists($uploadfiletype, $g_upload_path_type)) {
			// file is okay continue
			$base_g_upload_path = $g_upload_path_type[$uploadfiletype];
		} else {
			 die("Invalid type $uploadfiletype , error code 0");
		} 
		$show_img1 = '';
		$oldfile_folderpath =  $g_upload_path.$base_g_upload_path.$subfoldername;
		$filelist = getfilenamelist($oldfile_folderpath); 
		foreach ($filelist as $filepathobj) {
			$oldfilename = clean($filepathobj['FileName']); 
			$old_image_path = $oldfile_folderpath. "/" . $oldfilename; 
			if(file_exists($old_image_path))
			{ 
				$phpthumb_path =  $oldfilename;
				$image_path1 = phpThumbURL("src=$phpthumb_path&w=$width&h=$height&dpi=117&uploadtype=$uploadfiletype&tmpfoldername=$subfoldername");
				if($show_img1 == '')
				 $show_img1 = $image_path1;
				else 
					$show_img1 .= ','.$image_path1;
			}
		}
		return $show_img1;
	}	
	
	 /*
     * To generate download file list
     *
     * @param   string  $subfoldername		sub folder name not include / and ..
     * @param   string  $uploadfiletype		uploadfiletype defined at config
     * @return  string  $show_img1			show_data_content.php list as file list string
     */
	function get_download_attachments_list($subfoldername, $uploadfiletype='attach')
	{
		if(strpos($subfoldername,"..") !== false || strpos($subfoldername,"/") !== false) {
			die("UnAuthorized Access");
		}
		global $g_upload_path;
		global $g_upload_path_type; 
		$base_g_upload_path = "";
		if (array_key_exists($uploadfiletype, $g_upload_path_type)) {
			// file is okay continue
			$base_g_upload_path = $g_upload_path_type[$uploadfiletype];
		} else {
			 die("Invalid type $uploadfiletype , error code 0");
		} 

		$show_img1 = '';
		$oldfile_folderpath = $g_upload_path.$base_g_upload_path.$subfoldername;;
		$filelist = getfilenamelist($oldfile_folderpath); 
		foreach ($filelist as $filepathobj) {
			$oldfilename = clean($filepathobj['FileName']); 
			$old_image_path = $oldfile_folderpath. "/" . $oldfilename; 
			if(file_exists($old_image_path))
			{ 
				$sno = rand(100, 10000);
				$enc_data = "name=$oldfilename&type=$uploadfiletype&code=$subfoldername&sno=$sno";
				$hash = GWTFixedSaultHashPassword::hash($enc_data);
				$image_path1 = getsiteurl_nobasename()."show_data_content.php?".$enc_data."&token=".$hash;
				if($show_img1 == '')
				 $show_img1 = $image_path1;
				else 
					$show_img1 .= ','.$image_path1;
			}
		}
		return $show_img1;
	}	
	
	/*
     * Get URL not include base name
     *
     * @return  string  $actual_link	URL not include base name. used for download link to create full path
     */
	function getsiteurl_nobasename(){
		try{
			$siteuri = $_SERVER['REQUEST_URI'];
			$sitebasename = basename($_SERVER['REQUEST_URI']);
			$siteurl = str_ireplace($sitebasename,"",$siteuri );
			$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]".$siteurl;
			return $actual_link;
		} catch (Exception $e) {
			return "";
		}
	}
	
	 /*
     * Generate salt token for folder name
     *
     * @param   string  $foldername		foldername
     * @param   string  $uploadfiletype	uploadfiletype defined at config
     * @return  string  $hash			token string
     */
	function getsaltfolder_token($foldername,$uploadfiletype){
		try{
			$enc_data = "data=$foldername&type=$uploadfiletype";
			$hash = GWTFixedSaultHashPassword::hash($enc_data);
			return $hash;
		} catch (Exception $e) {
			return "";
		}
	}
?>