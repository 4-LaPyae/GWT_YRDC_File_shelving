<?php
class shelf_dal
{
	function select_shelf_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS * 
		FROM fss_tbl_shelf s 
		LEFT JOIN fss_tbl_location l ON s.location_id = l.location_id 
		LEFT JOIN fss_tbl_department d ON s.department_id = d.department_id";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_shelf_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_shelf_name($shelf_code, $shelf_name, $location_id, $shelf_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_shelf WHERE shelf_code = :shelf_code AND shelf_name = :shelf_name AND location_id = :location_id 
		AND shelf_id <> :shelf_id";
		$params = array(':shelf_code'=>$shelf_code, ':shelf_name'=>$shelf_name, ':location_id'=>$location_id, ':shelf_id'=>$shelf_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_shelf_name query fail.');
		return $result->rowCount();
	}
	
	function insert_shelf($shelf_info)
	{
		$shelf_code = $shelf_info->get_shelf_code();
		$shelf_name = $shelf_info->get_shelf_name();
		$location_id = $shelf_info->get_location_id();
		$department_id = $shelf_info->get_department_id();
		$no_of_row = $shelf_info->get_no_of_row();
		$no_of_column = $shelf_info->get_no_of_column();
		$query = "INSERT INTO fss_tbl_shelf(shelf_code, shelf_name, location_id, department_id, no_of_row, no_of_column) VALUES (:shelf_code, :shelf_name, :location_id, :department_id, :no_of_row, :no_of_column);";

		$params = array(':shelf_code'=>$shelf_code, ':shelf_name'=>$shelf_name, ':location_id'=>$location_id, ':department_id'=>$department_id, ':no_of_row'=>$no_of_row, ':no_of_column'=>$no_of_column);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('insert_shelf query fail.');
		if( $result )
		{
			$shelf_id = last_instert_id();
			$filter = "shelf_id=$shelf_id";
			$table = 'shelf';
			$type = 'Insert';
			$new_field_arr = array('shelf_code' => $shelf_code, 'shelf_name' => $shelf_name, 'location_id' => $location_id, 'department_id' => $department_id, 'no_of_row' => $no_of_row, 'no_of_column' => $no_of_column);
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
				return $shelf_id;
		}
		else
			return FALSE;
	}
	
	function update_shelf($shelf_info)
	{
		$shelf_id = $shelf_info->get_shelf_id();
		$shelf_code = $shelf_info->get_shelf_code();
		$shelf_name = $shelf_info->get_shelf_name();
		$location_id = $shelf_info->get_location_id();
		$department_id = $shelf_info->get_department_id();
		$no_of_row = $shelf_info->get_no_of_row();
		$no_of_column = $shelf_info->get_no_of_column();
		
		$eventlogbol = new eventlogbol();
		$table = 'shelf';
		$filter = "shelf_id=:shelf_id";
		$old_data = $eventlogbol->get_old_data($table, "shelf_id=:shelf_id", array("shelf_id"=>$shelf_id) );
		
		$query = "UPDATE fss_tbl_shelf SET shelf_code = :shelf_code, shelf_name = :shelf_name, location_id = :location_id, department_id = :department_id, no_of_row = :no_of_row, no_of_column = :no_of_column WHERE shelf_id = :shelf_id;";
		$params = array(':shelf_id'=>$shelf_id, ':shelf_code'=>$shelf_code, ':shelf_name'=>$shelf_name, ':location_id'=>$location_id, ':department_id'=>$department_id, ':no_of_row'=>$no_of_row, ':no_of_column'=>$no_of_column);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_shelf query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('shelf_id' => $shelf_id, 'shelf_code' => $shelf_code, 'shelf_name' => $shelf_name, 'location_id' => $location_id, 'department_id' => $department_id, 'no_of_row' => $no_of_row, 'no_of_column' => $no_of_column);
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
	
	function select_shelf_byid($shelf_id)
	{
		$query = "SELECT *  FROM fss_tbl_shelf WHERE shelf_id = :shelf_id";
		$params = array(':shelf_id'=>$shelf_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_shelf_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_shelf($shelf_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "shelf_id=$shelf_id";
		$table = 'shelf';
		$old_data = $eventlogbol->get_old_data($table, "shelf_id=:shelf_id", array("shelf_id"=>$shelf_id) );
		
		$query = "DELETE FROM fss_tbl_shelf WHERE shelf_id = :shelf_id";
		if( execute_non_query($query, array(':shelf_id' => $shelf_id)) )
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
	
	function select_all_shelf($department_enables)
	{
		$qry = "SELECT * FROM fss_tbl_shelf $department_enables;";
		$result = execute_query($qry) or die("select_all_shelf query fail.");
		return new readonlyresultset($result);
	}
	
	function select_shelf_data_by_id($shelf_id)
	{
		$qry = "SELECT s.*, s.no_of_row AS shelf_row, s.no_of_column AS shelf_column 
		FROM fss_tbl_shelf s 
		WHERE shelf_id = :shelf_id";
		$param =  array(':shelf_id'=>$shelf_id);
		$result = execute_query($qry, $param);		
		return new readonlyresultset($result);
	}
}
?>