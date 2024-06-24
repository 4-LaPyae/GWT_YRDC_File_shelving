<?php
class division_dal
{
	function select_division_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM fss_tbl_division ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_division_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_division_name($division_code, $division_name, $division_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_division WHERE division_code = :division_code AND division_name = :division_name 
		AND division_id <> :division_id";
		$params = array(':division_code'=>$division_code, ':division_name'=>$division_name, ':division_id'=>$division_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_division_name query fail.');
		return $result->rowCount();
	}
	
	function insert_division($division_info)
	{
		$division_code = $division_info->get_division_code();
		$division_name = $division_info->get_division_name();
		$query = "INSERT INTO fss_tbl_division(division_code, division_name) VALUES (:division_code, :division_name);";

		$params = array(':division_code'=>$division_code, ':division_name'=>$division_name);
		$result = execute_query($query, $params) or die('insert_division query fail.');
		if( $result )
		{
			$division_id = last_instert_id();
			$filter = "division_id=$division_id";
			$table = 'division';
			$type = 'Insert';
			$new_field_arr = array('division_code' => $division_code, 'division_name' => $division_name);
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
				return $division_id;
		}
		else
			return FALSE;
	}
	
	function update_division($division_info)
	{
		$division_id = $division_info->get_division_id();
		$division_code = $division_info->get_division_code();
		$division_name = $division_info->get_division_name();
		
		$eventlogbol = new eventlogbol();
		$table = 'division';
		$filter = "division_id=$division_id";
		$old_data = $eventlogbol->get_old_data($table, "division_id=:division_id", array("division_id"=>$division_id) );
		
		$query = "UPDATE fss_tbl_division SET division_code = :division_code, division_name = :division_name WHERE division_id = :division_id;";
		$params = array(':division_id'=>$division_id, ':division_code'=>$division_code, ':division_name'=>$division_name);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_division query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('division_id' => $division_id, 'division_code' => $division_code, 'division_name' => $division_name);
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
	
	function select_division_byid($division_id)
	{
		$query = "SELECT * FROM fss_tbl_division WHERE division_id = :division_id";
		$params = array(':division_id'=>$division_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_division_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_division($division_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "division_id=$division_id";
		$table = 'division';
		$old_data = $eventlogbol->get_old_data($table, "division_id=:division_id", array("division_id"=>$division_id) );
		
		$query = "DELETE FROM fss_tbl_division WHERE division_id = :division_id";
		if( execute_non_query($query, array(':division_id' => $division_id)) )
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
	
	function select_all_division()
	{
		$qry = "SELECT * FROM fss_tbl_division;";
		$result = execute_query($qry) or die("select_all_division query fail.");
		return new readonlyresultset($result);
	}
	
	function get_division_by_townshipid($township_id)
	{
		$query = "SELECT d.division_id, d.division_name  
		FROM fss_tbl_township t 
		LEFT JOIN fss_tbl_division d ON t.division_id = d.division_id 
		WHERE township_id = :township_id";

		$result = execute_query($query, array(':township_id' => $township_id));
		return new readonlyresultset($result);
	}
}
?>