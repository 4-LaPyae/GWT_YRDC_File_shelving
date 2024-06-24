<?php
	$movepath = '';
	require_once('autoload.php');
	require_once($movepath.'library/reference.php');
	
	$division_bol = new division_bol();
	$township_bol = new township_bol();
	$ward_bol = new ward_bol();
	$department_bol = new department_bol();
	
	$json_retrun_arr['sessionexpire'] = 0;
	
	// select nrc township by district id
	if(isset($_POST['nrc_division_code']))
	{
		$str = '';
		$township_code = '';
		if( isset($_POST['nrc_township_code']) )
			$township_code = $_POST['nrc_township_code'];
		$nrcbol = new nrcbol();
		$result = $nrcbol->get_nrc_bydivision(clean($_POST['nrc_division_code']));
		if( $result->rowCount() > 0 )
		{
			while( $row = $result->getNext() )
			{
				$str .= "<option value='" . $row['township_code'] ."' ";
				if( $township_code == $row['township_code'] && $township_code != '' )
					$str .= " selected";
				$str .= ">" . $row['township_code'] . "</option>";
			}
		}
		echo $str;
	}
	
	/* Call from general.js -- get_township_by_division_id() */
	if(isset($_POST['division_id']))
	{
		$division_id = $_POST['division_id'];
		$prefix = $_POST['prefix'];
		if($prefix == 'cri_')
			$str = "<select id='cri_seltownship' name='cri_seltownship' class='form-control' onchange=get_division_ward_by_township_id(this.value,'cri_')><option value=''> မြို့နယ်အားလုံး </option>";
		else
			$str = "<select id='sel_township_name' name='sel_township_name' class='form-control' onchange=get_division_ward_by_township_id(this.value,'')><option value=''> ရွေးရန် </option>";
			
		$result = $township_bol->get_township_by_division_id($division_id);
		if($result->rowCount() > 0)
		{
			while($row = $result->getNext())
			{	
				$str .= "<option value='" . $row['township_id'] . "'>" . htmlspecialchars($row['township_name']) . "</option>";
			}
		}
		$str .= "</select>";
		echo $str;
	}
	
	/* Call from general.js -- get_division_ward_by_township_id() */
	if(isset($_POST['township_id']))
	{
		$json_retrun_arr['sessionexpire'] = 0;
		$township_id = $_POST['township_id'];
		$prefix = $_POST['prefix'];
		if($prefix == 'cri_')
		{
			$division_str = "<select id='cri_seldivision' name='cri_seldivision' class='form-control' onchange=get_township_by_division_id(this.value,'cri_')><option value = ''> တိုင်း/ပြည်နယ်အားလုံး </option>";
			$township_str = "<select id='cri_seltownship' name='cri_seltownship' class='form-control' onchange=get_division_ward_by_township_id(this.value,'cri_')><option value = ''> မြို့နယ်အားလုံး </option>";
			$ward_str = "<select id='cri_selward' name='cri_selward' class='form-control'><option value=''> ရပ်ကွက်အားလုံး </option>";
		}
		else
		{
			$division_str = "<select id='sel_division_name' name='sel_division_name' class='form-control' onchange=get_township_by_division_id(this.value,'')><option value = ''> ရွေးရန် </option>";
			$township_str = "<select id='sel_township_name' name='sel_township_name' class='form-control' onchange=get_division_ward_by_township_id(this.value,'')><option value = ''> ရွေးရန် </option>";
			$ward_str = "<select id='sel_ward_name' name='sel_ward_name' class='form-control'><option value = ''> ရွေးရန် </option>";
		}
		
		$township_result = $township_bol->select_township_byid($township_id);
		$division_id = $township_result['division_id'];
		$division_result = $division_bol->get_all_division();
		if($division_result->rowCount() > 0)
		{
			while($division_row = $division_result->getNext())
			{
				if($division_row['division_id'] == $division_id)
					$division_str .= "<option value='" . $division_row['division_id'] . "' selected>" . htmlspecialchars($division_row['division_name']) . "</option>";
				else
					$division_str .= "<option value='" . $division_row['division_id'] . "'>" . htmlspecialchars($division_row['division_name']) . "</option>";
			}
		}
		
		$township_result = $township_bol->get_township_by_division_id($division_id);
		if($township_result->rowCount() > 0)
		{
			while($township_row = $township_result->getNext())
			{
				if($township_row['township_id'] == $township_id)
					$township_str .= "<option value='" . $township_row['township_id'] . "' selected>" . htmlspecialchars($township_row['township_name']) . "</option>";
				else
					$township_str .= "<option value='" . $township_row['township_id'] . "'>" . htmlspecialchars($township_row['township_name']) . "</option>";
			}
		}
		
		$ward_result = $ward_bol->get_ward_by_township_id($township_id);
		if($ward_result->rowCount() > 0)
		{
			while($ward_row = $ward_result->getNext())
			{
				$ward_str .= "<option value='" . $ward_row['ward_id'] . "'>" . htmlspecialchars($ward_row['ward_name']) . "</option>";
			}
		}
		
		$division_str .= '</select>';
		$township_str .= '</select>';
		$ward_str .= '</select>';
		
		$json_retrun_arr ['division'] = $division_str;
		$json_retrun_arr ['township'] = $township_str;
		$json_retrun_arr ['ward'] = $ward_str;
		echo json_encode($json_retrun_arr);
		exit();
	}
	
	/* Customer Edit */
	if( isset($_POST['get_township_division_id']) && isset($_POST['inputid']) )
	{
		$township_id = $_POST['get_township_division_id'];
		$id = $_POST['inputid'];
		$division_bol = new division_bol();
		$result = $division_bol->get_division_by_townshipid($township_id);
		$return_str = '<input type="hidden" class="form-control" id="txtdivisionid'.$id.'" name="txtdivisionid'.$id.'" value="'.$result['division_id'].'" readonly>
								<input type="text" class="form-control" id="division'.$id.'" name="division'.$id.'" value="'. htmlspecialchars($result['division_name']) .'" readonly>';
		echo $return_str; exit();
	}
	
	if( isset($_POST['get_township_id']) && isset($_POST['inputid']) )
	{
		$township_id = $_POST['get_township_id'];
		$id = $_POST['inputid'];
		$readonly = '';		
		$ward_result = $ward_bol->get_ward_by_township_id($township_id);
		
		if( $township_id == 0 )
			$readonly = " readonly ";
		
		$selward = '<select id="selward'.$id.'" name="selward'.$id.'" class="form-control" '. $readonly .' >
							<option value="">ရွေးရန်</option>';
		if( $ward_result->rowCount() > 0 )
		{
			while($ward_row = $ward_result->getNext())
			{
				$selward .= "<option value='". $ward_row['ward_id'] ."'>". htmlspecialchars($ward_row['ward_name']) ."</option>";
			}
		}
		$selward .= '</select>' ;
		$json_retrun_arr['selward'] = $selward;
		echo json_encode($json_retrun_arr); exit();
	}
	
	if(isset($_POST['department_types']))
	{
		$from_department_type = $_POST['department_types'];
		$str = "";
		$result = $department_bol->get_department_by_depttype($from_department_type);
		if($result->rowCount() > 0)
		{
			while($row = $result->getNext())
			{	
				$str .= "<option value='" . $row['department_id'] . "'>" . htmlspecialchars($row['department_name']) . "</option>";
			}
		}
		echo $str;exit();
	}
	
	/* Select RFID Card No */
	if( isset($_POST['rfid_card_no']) )
	{
		$folder_bol = new folder_bol();
		$aRow = $folder_bol->get_rfid_scanning_log();
		$rfid_card_no = htmlspecialchars($aRow['rfid_no']);
		echo $rfid_card_no; exit();
	}
?>