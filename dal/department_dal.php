<?php
class department_dal
{
	function select_department_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM fss_tbl_department ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param) or die("select_department_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_department_name($department_name, $department_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_department WHERE department_name = :department_name 
		AND department_id <> :department_id";
		$params = array(':department_name'=>$department_name, ':department_id'=>$department_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_department_name query fail.');
		return $result->rowCount();
	}
	
	function insert_department($department_info)
	{
		$department_name = $department_info->get_department_name();
		$query = "INSERT INTO fss_tbl_department(department_name) VALUES (:department_name);";

		$params = array(':department_name'=>$department_name);
		$result = execute_query($query, $params) or die('insert_department query fail.');
		if( $result )
		{
			$department_id = last_instert_id();
			$filter = "department_id=$department_id";
			$table = 'department';
			$type = 'Insert';
			$new_field_arr = array('department_name' => $department_name);
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
				return $department_id;
		}
		else
			return FALSE;
	}
	
	function update_department($department_info)
	{
		$department_id = $department_info->get_department_id();
		$department_name = $department_info->get_department_name();
		
		$eventlogbol = new eventlogbol();
		$table = 'department';
		$filter = "department_id=:department_id";
		$old_data = $eventlogbol->get_old_data($table, "department_id=:department_id", array("department_id"=>$department_id) );
		
		$query = "UPDATE fss_tbl_department SET department_name = :department_name WHERE department_id = :department_id;";
		$params = array(':department_id'=>$department_id, ':department_name'=>$department_name);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_department query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('department_id' => $department_id, 'department_name' => $department_name);
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
	
	function select_department_byid($department_id)
	{
		$query = "SELECT * FROM fss_tbl_department WHERE department_id = :department_id";
		$params = array(':department_id'=>$department_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_department_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_department($department_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "department_id=$department_id";
		$table = 'department';
		$old_data = $eventlogbol->get_old_data($table, "department_id=:department_id", array("department_id"=>$department_id) );
		
		$query = "DELETE FROM fss_tbl_department WHERE department_id = :department_id";
		if( execute_non_query($query, array(':department_id' => $department_id)) )
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
	
	function select_all_department($department_enables)
	{
		$qry = "SELECT * FROM fss_tbl_department $department_enables";
		$result = execute_query($qry, array()) or die("select_all_department query fail.");
		return new readonlyresultset($result);
	}
	
	function get_department_by_depttype($type)
	{
		$qry = "SELECT * FROM fss_tbl_department WHERE is_external = :type;";
		$param = array(':type'=>$type);
		// echo debugPDO($qry, $param);exit;
		$result = execute_query($qry, $param) or die("get_department_by_depttype query fail.");
		return new readonlyresultset($result);
	}
}
?>