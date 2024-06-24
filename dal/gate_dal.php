<?php
class gate_dal
{
	function select_gate_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM fss_tbl_gate g 
		LEFT JOIN fss_tbl_location l ON g.location_id = l.location_id";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_gate_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function select_rfid_gate_pass_log_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT gl.rfid_no AS rfid_card_no, folder_no, description, gate_name, log_time, 
		DATE_FORMAT(log_time, '%d-%m-%Y : %H:%i:%s') AS now_date
		FROM fss_tbl_gate_log gl 
		LEFT JOIN fss_tbl_folder f ON f.folder_id = gl.folder_id 
		LEFT JOIN fss_tbl_gate g ON g.gate_id = gl.gate_id ";
		$query .= $cri_str;
		$query .= $SortingCols;
		if ($DisplayLength > 0)
			$query .= " LIMIT $DisplayStart, $DisplayLength";
		// echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_rfid_gate_pass_log_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_gate_name($gate_code, $gate_name, $location_id, $gate_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_gate WHERE gate_code = :gate_code AND gate_name = :gate_name AND location_id = :location_id 
		AND gate_id <> :gate_id";
		$params = array(':gate_code'=>$gate_code, ':gate_name'=>$gate_name, ':location_id'=>$location_id, ':gate_id'=>$gate_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_gate_name query fail.');
		return $result->rowCount();
	}
	
	function insert_gate($gate_info)
	{
		$gate_code = $gate_info->get_gate_code();
		$gate_name = $gate_info->get_gate_name();
		$location_id = $gate_info->get_location_id();
		$query = "INSERT INTO fss_tbl_gate(gate_code, gate_name, location_id) VALUES (:gate_code, :gate_name, :location_id);";

		$params = array(':gate_code'=>$gate_code, ':gate_name'=>$gate_name, ':location_id'=>$location_id);
		$result = execute_query($query, $params) or die('insert_gate query fail.');
		if( $result )
		{
			$gate_id = last_instert_id();
			$filter = "gate_id=$gate_id";
			$table = 'gate';
			$type = 'Insert';
			$new_field_arr = array('gate_code' => $gate_code, 'gate_name' => $gate_name, 'location_id' => $location_id);
			$eventlogbol = new eventlogbol();
			$description = $eventlogbol->get_event_description($type, $new_field_arr);
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = clean($_SESSION['YRDCFSH_LOGIN_ID']);
			
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter(clean($filter));
			$eventloginfo->setdescription(clean($description));
			if ( $eventlogbol->save_eventlog($eventloginfo) )
				return $gate_id;
		}
		else
			return FALSE;
	}
	
	function update_gate($gate_info)
	{
		$gate_id = $gate_info->get_gate_id();
		$gate_code = $gate_info->get_gate_code();
		$gate_name = $gate_info->get_gate_name();
		$location_id = $gate_info->get_location_id();
		
		$eventlogbol = new eventlogbol();
		$table = 'gate';
		$filter = "gate_id=:gate_id";
		$old_data = $eventlogbol->get_old_data($table, "gate_id=:gate_id", array("gate_id"=>$gate_id) );
		
		$query = "UPDATE fss_tbl_gate SET gate_code = :gate_code, gate_name = :gate_name, location_id = :location_id WHERE gate_id = :gate_id;";
		$params = array(':gate_id'=>$gate_id, ':gate_code'=>$gate_code, ':gate_name'=>$gate_name, ':location_id'=>$location_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_gate query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('gate_id' => $gate_id, 'gate_code' => $gate_code, 'gate_name' => $gate_name, 'location_id' => $location_id);
			$description = $eventlogbol->get_event_description($type, $new_field_arr, $old_data);
			if($description == '')
				return TRUE;
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = clean($_SESSION['YRDCFSH_LOGIN_ID']);
			
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter(clean($filter));
			$eventloginfo->setdescription(clean($description));
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function select_gate_byid($gate_id)
	{
		$query = "SELECT * FROM fss_tbl_gate WHERE gate_id = :gate_id";
		$params = array(':gate_id'=>$gate_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_gate_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_gate($gate_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "gate_id=$gate_id";
		$table = 'gate';
		$old_data = $eventlogbol->get_old_data($table, "gate_id=:gate_id", array("gate_id"=>$gate_id) );
		
		$query = "DELETE FROM fss_tbl_gate WHERE gate_id = :gate_id";
		if( execute_non_query($query, array(':gate_id' => $gate_id)) )
		{
			$type = 'Delete';
			$eventlogbol = new eventlogbol();
			$description = $eventlogbol->get_event_description($type, $old_data);
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = clean($_SESSION['YRDCFSH_LOGIN_ID']);
			
			$eventloginfo = new eventloginfo();				
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter(clean($filter));
			$eventloginfo->setdescription(clean($description));
			return $eventlogbol->save_eventlog($eventloginfo);
		}
	}
	
	function select_all_gate()
	{
		$qry = "SELECT * FROM fss_tbl_gate;";
		$result = execute_query($qry) or die("select_all_gate query fail.");
		return new readonlyresultset($result);
	}
}
?>