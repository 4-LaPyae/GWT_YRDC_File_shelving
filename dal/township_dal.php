<?php
class township_dal
{
	function select_township_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM fss_tbl_township ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_township_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_township_name($township_code, $township_name, $division_id, $township_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_township WHERE township_code = :township_code AND township_name = :township_name AND division_id = :division_id 
		AND township_id <> :township_id";
		$params = array(':township_code'=>$township_code, ':township_name'=>$township_name, ':division_id'=>$division_id, ':township_id'=>$township_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_township_name query fail.');
		return $result->rowCount();
	}
	
	function insert_township($township_info)
	{
		$division_id = $township_info->get_division_id();
		$township_code = $township_info->get_township_code();
		$township_name = $township_info->get_township_name();
		$query = "INSERT INTO fss_tbl_township(division_id, township_code, township_name) VALUES (:division_id, :township_code, :township_name);";

		$params = array(':division_id'=>$division_id, ':township_code'=>$township_code, ':township_name'=>$township_name);
		$result = execute_query($query, $params) or die('insert_township query fail.');
		if( $result )
		{
			$township_id = last_instert_id();
			$filter = "township_id=$township_id";
			$table = 'township';
			$type = 'Insert';
			$new_field_arr = array('division_id' => $division_id, 'township_code' => $township_code, 'township_name' => $township_name);
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
				return $township_id;
		}
		else
			return FALSE;
	}
	
	function update_township($township_info)
	{
		$township_id = $township_info->get_township_id();
		$division_id = $township_info->get_division_id();
		$township_code = $township_info->get_township_code();
		$township_name = $township_info->get_township_name();
		
		$eventlogbol = new eventlogbol();
		$table = 'township';
		$filter = "township_id=:township_id";
		$old_data = $eventlogbol->get_old_data($table, "township_id=:township_id", array("township_id"=>$township_id) );
		
		$query = "UPDATE fss_tbl_township SET division_id = :division_id, township_code = :township_code, township_name = :township_name WHERE township_id = :township_id;";
		$params = array(':division_id'=>$division_id, ':township_id'=>$township_id, ':township_code'=>$township_code, ':township_name'=>$township_name);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_township query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('division_id' => $division_id, 'township_id' => $township_id, 'township_code' => $township_code, 'township_name' => $township_name);
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
	
	function select_township_byid($township_id)
	{
		$query = "SELECT * FROM fss_tbl_township WHERE township_id = :township_id";
		$params = array(':township_id'=>$township_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_township_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_township($township_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "township_id=:township_id";
		$table = 'township';
		$old_data = $eventlogbol->get_old_data($table, "township_id=:township_id", array("township_id"=>$township_id) );
		
		$query = "DELETE FROM fss_tbl_township WHERE township_id = :township_id";
		if( execute_non_query($query, array(':township_id' => $township_id)) )
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
	
	function select_all_township()
	{
		$qry = "SELECT * FROM fss_tbl_township;";
		$result = execute_query($qry) or die("select_all_township query fail.");
		return new readonlyresultset($result);
	}
	
	function select_township_by_division_id($division_id)
	{
		$cri_str = " WHERE 1=1 ";
		$param = array();
		if( $division_id != '' )
		{
			$cri_str .= " AND division_id = :division_id ";
			$param[':division_id'] = $division_id;
		}
		
		$qry = "SELECT * FROM fss_tbl_township $cri_str ;";
		$result = execute_query($qry, $param) or die("select_township_by_division_id query fail.");
		return new readonlyresultset($result);
	}
}
?>