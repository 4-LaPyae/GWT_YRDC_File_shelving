<?php
class location_dal
{
	function select_location_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM fss_tbl_location ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_location_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_location_name($location_code, $location_name, $location_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_location WHERE location_code = :location_code AND location_name = :location_name 
		AND location_id <> :location_id";
		$params = array(':location_code'=>$location_code, ':location_name'=>$location_name, ':location_id'=>$location_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_location_name query fail.');
		return $result->rowCount();
	}
	
	function insert_location($location_info)
	{
		$location_code = $location_info->get_location_code();
		$location_name = $location_info->get_location_name();
		$query = "INSERT INTO fss_tbl_location(location_code, location_name) VALUES (:location_code, :location_name);";

		$params = array(':location_code'=>$location_code, ':location_name'=>$location_name);
		$result = execute_query($query, $params) or die('insert_location query fail.');
		if( $result )
		{
			$location_id = last_instert_id();
			$filter = "location_id=$location_id";
			$table = 'location';
			$type = 'Insert';
			$new_field_arr = array('location_code' => $location_code, 'location_name' => $location_name);
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
				return $location_id;
		}
		else
			return FALSE;
	}
	
	function update_location($location_info)
	{
		$location_id = $location_info->get_location_id();
		$location_code = $location_info->get_location_code();
		$location_name = $location_info->get_location_name();
		
		$eventlogbol = new eventlogbol();
		$table = 'location';
		$filter = "location_id=:location_id";
		$old_data = $eventlogbol->get_old_data($table, "location_id=:location_id", array("location_id"=>$location_id) );
		
		$query = "UPDATE fss_tbl_location SET location_code = :location_code, location_name = :location_name WHERE location_id = :location_id;";
		$params = array(':location_id'=>$location_id, ':location_code'=>$location_code, ':location_name'=>$location_name);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_location query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('location_id' => $location_id, 'location_code' => $location_code, 'location_name' => $location_name);
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
	
	function select_location_byid($location_id)
	{
		$query = "SELECT * FROM fss_tbl_location WHERE location_id = :location_id";
		$params = array(':location_id'=>$location_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_location_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_location($location_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "location_id=$location_id";
		$table = 'location';
		$old_data = $eventlogbol->get_old_data($table, "location_id=:location_id", array("location_id"=>$location_id) );
		
		$query = "DELETE FROM fss_tbl_location WHERE location_id = :location_id";
		if( execute_non_query($query, array(':location_id' => $location_id)) )
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
	
	function select_all_location()
	{
		$qry = "SELECT * FROM fss_tbl_location;";
		$result = execute_query($qry) or die("select_all_location query fail.");
		return new readonlyresultset($result);
	}
}
?>