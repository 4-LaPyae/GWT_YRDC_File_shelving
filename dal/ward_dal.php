<?php
class ward_dal
{
	function select_ward_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM fss_tbl_ward ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_ward_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_ward_name($ward_name, $township_id, $ward_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_ward WHERE ward_name = :ward_name AND township_id = :township_id 
		AND ward_id <> :ward_id";
		$params = array(':ward_name'=>$ward_name, ':township_id'=>$township_id, ':ward_id'=>$ward_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_ward_name query fail.');
		return $result->rowCount();
	}
	
	function insert_ward($ward_info)
	{
		$township_id = $ward_info->get_township_id();
		$ward_name = $ward_info->get_ward_name();
		$query = "INSERT INTO fss_tbl_ward(township_id, ward_name) VALUES (:township_id, :ward_name);";

		$params = array(':township_id'=>$township_id, ':ward_name'=>$ward_name);
		$result = execute_query($query, $params) or die('insert_ward query fail.');
		if( $result )
		{
			$ward_id = last_instert_id();
			$filter = "ward_id=$ward_id";
			$table = 'ward';
			$type = 'Insert';
			$new_field_arr = array('township_id' => $township_id, 'ward_name' => $ward_name);
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
				return $ward_id;
		}
		else
			return FALSE;
	}
	
	function update_ward($ward_info)
	{
		$ward_id = $ward_info->get_ward_id();
		$township_id = $ward_info->get_township_id();
		$ward_name = $ward_info->get_ward_name();
		
		$eventlogbol = new eventlogbol();
		$table = 'ward';
		$filter = "ward_id=:ward_id";
		$old_data = $eventlogbol->get_old_data($table, "ward_id=:ward_id", array("ward_id"=>$ward_id) );
		
		$query = "UPDATE fss_tbl_ward SET township_id = :township_id, ward_name = :ward_name WHERE ward_id = :ward_id;";
		$params = array(':township_id'=>$township_id, ':ward_id'=>$ward_id, ':ward_name'=>$ward_name);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_ward query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('township_id' => $township_id, 'ward_id' => $ward_id, 'ward_name' => $ward_name);
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
	
	function select_ward_byid($ward_id)
	{
		$query = "SELECT * FROM fss_tbl_ward WHERE ward_id = :ward_id";
		$params = array(':ward_id'=>$ward_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_ward_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_ward($ward_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "ward_id=:ward_id";
		$table = 'ward';
		$old_data = $eventlogbol->get_old_data($table, "ward_id=:ward_id", array("ward_id"=>$ward_id) );
		
		$query = "DELETE FROM fss_tbl_ward WHERE ward_id = :ward_id";
		if( execute_non_query($query, array(':ward_id' => $ward_id)) )
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
	
	function select_all_ward()
	{
		$qry = "SELECT * FROM fss_tbl_ward;";
		$result = execute_query($qry) or die("select_all_ward query fail.");
		return new readonlyresultset($result);
	}
	
	function select_ward_by_township_id($township_id)
	{
		$cri_str = " WHERE 1=1 ";
		$param = array();
		if( $township_id != '' )
		{
			$cri_str .= " AND township_id = :township_id ";
			$param[':township_id'] = $township_id;
		}
		
		$qry = "SELECT * FROM fss_tbl_ward $cri_str ;";
		$result = execute_query($qry, $param) or die("select_ward_by_township_id query fail.");
		return new readonlyresultset($result);
	}
}
?>