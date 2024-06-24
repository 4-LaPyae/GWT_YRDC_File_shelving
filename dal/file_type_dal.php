<?php
class file_type_dal
{
	function select_file_type_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM fss_tbl_file_type ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_file_type_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_file_type_name($file_type_code, $file_type_name, $file_type_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_file_type WHERE file_type_code = :file_type_code AND file_type_name = :file_type_name 
		AND file_type_id <> :file_type_id";
		$params = array(':file_type_code'=>$file_type_code, ':file_type_name'=>$file_type_name, ':file_type_id'=>$file_type_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_file_type_name query fail.');
		return $result->rowCount();
	}
	
	function insert_file_type($file_type_info)
	{
		$file_type_code = $file_type_info->get_file_type_code();
		$file_type_name = $file_type_info->get_file_type_name();
		$query = "INSERT INTO fss_tbl_file_type(file_type_code, file_type_name) VALUES (:file_type_code, :file_type_name);";

		$params = array(':file_type_code'=>$file_type_code, ':file_type_name'=>$file_type_name);
		$result = execute_query($query, $params) or die('insert_file_type query fail.');
		if( $result )
		{
			$file_type_id = last_instert_id();
			$filter = "file_type_id=$file_type_id";
			$table = 'file_type';
			$type = 'Insert';
			$new_field_arr = array('file_type_code' => $file_type_code, 'file_type_name' => $file_type_name);
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
				return $file_type_id;
		}
		else
			return FALSE;
	}
	
	function update_file_type($file_type_info)
	{
		$file_type_id = $file_type_info->get_file_type_id();
		$file_type_code = $file_type_info->get_file_type_code();
		$file_type_name = $file_type_info->get_file_type_name();
		
		$eventlogbol = new eventlogbol();
		$table = 'file_type';
		$filter = "file_type_id=:file_type_id";
		$old_data = $eventlogbol->get_old_data($table, "file_type_id=:file_type_id", array("file_type_id"=>$file_type_id) );
		
		$query = "UPDATE fss_tbl_file_type SET file_type_code = :file_type_code, file_type_name = :file_type_name WHERE file_type_id = :file_type_id;";
		$params = array(':file_type_id'=>$file_type_id, ':file_type_code'=>$file_type_code, ':file_type_name'=>$file_type_name);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_file_type query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('file_type_id' => $file_type_id, 'file_type_code' => $file_type_code, 'file_type_name' => $file_type_name);
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
	
	function select_file_type_byid($file_type_id)
	{
		$query = "SELECT * FROM fss_tbl_file_type WHERE file_type_id = :file_type_id";
		$params = array(':file_type_id'=>$file_type_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_file_type_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_file_type($file_type_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "file_type_id=$file_type_id";
		$table = 'file_type';
		$old_data = $eventlogbol->get_old_data($table, "file_type_id=:file_type_id", array("file_type_id"=>$file_type_id) );
		
		$query = "DELETE FROM fss_tbl_file_type WHERE file_type_id = :file_type_id";
		if( execute_non_query($query, array(':file_type_id' => $file_type_id)) )
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
	
	function select_all_file_type()
	{
		$qry = "SELECT * FROM fss_tbl_file_type;";
		$result = execute_query($qry) or die("select_all_file_type query fail.");
		return new readonlyresultset($result);
	}
}
?>