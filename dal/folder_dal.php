<?php
class folder_dal
{
	function select_folder_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS *, f.folder_id, letter_count_no 
		FROM fss_tbl_folder f 
        LEFT JOIN
        (
        	SELECT folder_id, IFNULL(COUNT(letter_no), 0) as letter_count_no 
            FROM fss_tbl_file GROUP BY folder_id 
        )td on td.folder_id = f.folder_id
		LEFT JOIN fss_tbl_file_type ft ON f.file_type_id = ft.file_type_id 
		LEFT JOIN fss_tbl_security_type st ON f.security_type_id = st.security_type_id 
		LEFT JOIN fss_tbl_shelf sf ON f.shelf_id = sf.shelf_id ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		// echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_folder_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_rfid_no($file_type_id, $rfid_no, $description, $folder_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_folder WHERE file_type_id = :file_type_id AND rfid_no = :rfid_no AND description = :description 
		AND folder_id <> :folder_id";
		$params = array(':file_type_id'=>$file_type_id, ':rfid_no'=>$rfid_no, ':description'=>$description, ':folder_id'=>$folder_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_rfid_no query fail.');
		return $result->rowCount();
	}
	
	function insert_folder($folder_info)
	{
		$rfid_no = $folder_info->get_rfid_no();
		$folder_no = $folder_info->get_folder_no();
		$description = $folder_info->get_description();
		$file_type_id = $folder_info->get_file_type_id();
		$security_type_id = $folder_info->get_security_type_id();
		$shelf_id = $folder_info->get_shelf_id();
		$shelf_row = $folder_info->get_shelf_row();
		$shelf_column = $folder_info->get_shelf_column();
		$created_by = $folder_info->get_created_by();
		
		$query = "INSERT INTO fss_tbl_folder(file_type_id, rfid_no, description, folder_no, security_type_id, shelf_id, shelf_row, shelf_column, created_by, created_date) 
		VALUES (:file_type_id, :rfid_no, :description, :folder_no, :security_type_id, :shelf_id, :shelf_row, :shelf_column, :created_by, NOW());";

		$params = array(':file_type_id'=>$file_type_id, ':rfid_no'=>$rfid_no, ':description'=>$description, ':folder_no'=>$folder_no, 
		':security_type_id'=>$security_type_id, ':shelf_id'=>$shelf_id, ':shelf_row'=>$shelf_row, ':shelf_column'=>$shelf_column, ':created_by'=>$created_by);
		//echo debugPDO($query, $params);exit;
		
		$result = execute_query($query, $params) or die('insert_folder query fail.');
		if( $result )
		{
			$folder_id = last_instert_id();
			$filter = "folder_id=$folder_id";
			$table = 'folder';
			$type = 'Insert';
			$new_field_arr = array('file_type_id' => $file_type_id, 'rfid_no' => $rfid_no, 'description' => $description, 'folder_no' => $folder_no, 
			'security_type_id' => $security_type_id, 'shelf_id' => $shelf_id, 'shelf_row' => $shelf_row, 'shelf_column' => $shelf_column, 'created_by' => $created_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "folder_id=:folder_id", array(':folder_id'=>$folder_id));

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
			$eventloginfo->setencrypt_value($encrypt_value);
			if ( $eventlogbol->save_eventlog($eventloginfo) )
				return $folder_id;
		}
		else
			return FALSE;
	}
	
	function update_folder($folder_info)
	{
		$folder_id = $folder_info->get_folder_id();
		$file_type_id = $folder_info->get_file_type_id();
		$rfid_no = $folder_info->get_rfid_no();
		$folder_no = $folder_info->get_folder_no();
		$description = $folder_info->get_description();
		$security_type_id = $folder_info->get_security_type_id();
		$shelf_id = $folder_info->get_shelf_id();
		$shelf_row = $folder_info->get_shelf_row();
		$shelf_column = $folder_info->get_shelf_column();
		$modified_by = $folder_info->get_modified_by();
		
		$eventlogbol = new eventlogbol();
		$table = 'folder';
		$filter = "folder_id=:folder_id";
		$old_data = $eventlogbol->get_old_data($table, "folder_id=:folder_id", array("folder_id"=>$folder_id) );
		
		$query = "UPDATE fss_tbl_folder SET file_type_id = :file_type_id, rfid_no = :rfid_no, folder_no = :folder_no, description = :description, 
		security_type_id = :security_type_id, shelf_id = :shelf_id, shelf_row = :shelf_row, shelf_column = :shelf_column, modified_by=:modified_by, modified_date=NOW() 
		WHERE folder_id = :folder_id;";
		$params = array(':folder_id'=>$folder_id, ':file_type_id'=>$file_type_id, ':rfid_no'=>$rfid_no, ':folder_no'=>$folder_no, ':description'=>$description, 
		':security_type_id'=>$security_type_id, ':shelf_id'=>$shelf_id, ':shelf_row'=>$shelf_row, ':shelf_column'=>$shelf_column, ':modified_by'=>$modified_by);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_folder query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('folder_id' => $folder_id, 'file_type_id' => $file_type_id, 'rfid_no' => $rfid_no, 'folder_no' => $folder_no, 'description' => $description, 
			'security_type_id' => $security_type_id, 'shelf_id' => $shelf_id, 'shelf_row' => $shelf_row, 'shelf_column' => $shelf_column, 'modified_by' => $modified_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "folder_id=:folder_id", array(':folder_id'=>$folder_id));

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
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function update_folder_destroy($folder_info)
	{
		$folder_id = $folder_info->get_folder_id();
		$destroy_order_employeeid = $folder_info->get_destroy_order_employeeid();
		$destroy_order_employee_name = $folder_info->get_destroy_order_employee_name();
		$destroy_order_designation = $folder_info->get_destroy_order_designation();
		$destroy_order_department = $folder_info->get_destroy_order_department();
		$destroy_duty_employeeid = $folder_info->get_destroy_duty_employeeid();
		$destroy_duty_employee_name = $folder_info->get_destroy_duty_employee_name();
		$destroy_duty_designation = $folder_info->get_destroy_duty_designation();
		$destroy_duty_department = $folder_info->get_destroy_duty_department();
		$destroy_date = $folder_info->get_destroy_date();
		$destroy_order_no = $folder_info->get_destroy_order_no();
		$destroy_remark = $folder_info->get_destroy_remark();
		$modified_by = $folder_info->get_modified_by();
		
		$eventlogbol = new eventlogbol();
		$table = 'folder';
		$filter = "folder_id=:folder_id";
		$old_data = $eventlogbol->get_old_data($table, "folder_id=:folder_id", array("folder_id"=>$folder_id) );
		
		$query = "UPDATE fss_tbl_folder SET destroy_order_employeeid = :destroy_order_employeeid, destroy_order_employee_name = :destroy_order_employee_name, 
		destroy_order_designation = :destroy_order_designation, destroy_order_department = :destroy_order_department, 
		destroy_duty_employeeid = :destroy_duty_employeeid, destroy_duty_employee_name = :destroy_duty_employee_name, 
		destroy_duty_designation = :destroy_duty_designation, destroy_duty_department = :destroy_duty_department, 
		destroy_date=:destroy_date, destroy_order_no=:destroy_order_no, destroy_remark=:destroy_remark, status = 3, 
		modified_by=:modified_by, modified_date=NOW() 
		WHERE folder_id = :folder_id;";
		
		$params = array(':folder_id'=>$folder_id, ':destroy_order_employeeid'=>$destroy_order_employeeid, 
		':destroy_order_employee_name'=>$destroy_order_employee_name, ':destroy_order_designation'=>$destroy_order_designation, 
		':destroy_order_department'=>$destroy_order_department, ':destroy_duty_employeeid'=>$destroy_duty_employeeid, 
		':destroy_duty_employee_name'=>$destroy_duty_employee_name, ':destroy_duty_designation'=>$destroy_duty_designation, 
		':destroy_duty_department'=>$destroy_duty_department, ':destroy_date'=>$destroy_date, ':destroy_order_no'=>$destroy_order_no, 
		':destroy_remark'=>$destroy_remark, ':modified_by'=>$modified_by);
		
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_folder_destroy query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('folder_id'=>$folder_id, 'destroy_order_employeeid'=>$destroy_order_employeeid, 
			'destroy_order_employee_name'=>$destroy_order_employee_name, 'destroy_order_designation'=>$destroy_order_designation, 
			'destroy_order_department'=>$destroy_order_department, 'destroy_duty_employeeid'=>$destroy_duty_employeeid, 
			'destroy_duty_employee_name'=>$destroy_duty_employee_name, 'destroy_duty_designation'=>$destroy_duty_designation, 
			'destroy_duty_department'=>$destroy_duty_department, 'destroy_date'=>$destroy_date, 'destroy_order_no'=>$destroy_order_no, 
			'destroy_remark'=>$destroy_remark, 'modified_by'=>$modified_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "folder_id=:folder_id", array(':folder_id'=>$folder_id));

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
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function select_folder_byid($folder_id)
	{
		$query = "SELECT *  FROM fss_tbl_folder f 
		LEFT JOIN fss_tbl_file_type ft ON f.file_type_id = ft.file_type_id 
		LEFT JOIN fss_tbl_security_type st ON f.security_type_id = st.security_type_id 
		LEFT JOIN fss_tbl_shelf sf ON f.shelf_id = sf.shelf_id
		WHERE folder_id = :folder_id";
		$params = array(':folder_id'=>$folder_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_folder_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_folder($folder_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "folder_id=:folder_id";
		$table = 'folder';
		$old_data = $eventlogbol->get_old_data($table, "folder_id=:folder_id", array("folder_id"=>$folder_id) );
		$encrypt_value = $old_data['encrypt_value'];
		unset($old_data['encrypt_value']);
		
		$query = "DELETE FROM fss_tbl_folder WHERE folder_id = :folder_id";
		if( execute_non_query($query, array(':folder_id' => $folder_id)) )
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
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
	}
	
	function select_all_folder()
	{
		$qry = "SELECT * FROM fss_tbl_folder;";
		$result = execute_query($qry) or die("select_all_folder query fail.");
		return new readonlyresultset($result);
	}
	
	function update_folder_status($folder_id, $status)
	{
		$eventlogbol = new eventlogbol();
		$table = 'folder';
		$filter = "folder_id=:folder_id";
		$old_data = $eventlogbol->get_old_data($table, "folder_id=:folder_id", array("folder_id"=>$folder_id) );
		
		$query = "UPDATE fss_tbl_folder SET status = :status WHERE folder_id = :folder_id;";
		
		$params = array(':folder_id'=>$folder_id, ':status'=>$status);
		
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_folder_status query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('folder_id'=>$folder_id, 'status'=>$status);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "folder_id=:folder_id", array(':folder_id'=>$folder_id));

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
			$eventloginfo->setencrypt_value($encrypt_value);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function select_folder_location_by_shelf_id($shelf_id, $folder_id, $shelf_row, $shelf_column)
	{
		$query = "SELECT f.* , file_type_name 
		FROM fss_tbl_folder f
		LEFT JOIN fss_tbl_file_type ft ON f.file_type_id = ft.file_type_id
		WHERE shelf_id = :shelf_id  AND folder_id = :folder_id  AND shelf_row = :shelf_row AND `shelf_column` = :shelf_column";
		$param =  array(':shelf_id'=>$shelf_id, ':folder_id'=>$folder_id,  ':shelf_row'=>$shelf_row, ':shelf_column'=>$shelf_column);
		$result = execute_query($query, $param);
		return new readonlyresultset($result);
	}
	
	function change_folder_lock($folder_id, $status) 
	{
		$param=array("status"=>$status,"folder_id"=>$folder_id);
		$query = "UPDATE fss_tbl_folder SET is_lock=:status WHERE folder_id=:folder_id ; ";		
		if(execute_query($query,$param))
		{
			$filter = "folder_id=:folder_id";
			$table = 'folder';
			
			$encrypt_value = '';
			$securitybol = new securitybol();
			if( $status != 1 )
				$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "folder_id=:folder_id", array(':folder_id'=>$folder_id));
			
			$user_id = 0;
			if(isset($_SESSION['YRDCFSH_LOGIN_ID']))
				$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
			
			$eventloginfo = new eventloginfo();
			$eventlogbol = new eventlogbol();
			$eventloginfo->setaction_type("Invalid Log");
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription(" is_lock > 0 > $status ");
			$eventloginfo->setencrypt_value($encrypt_value);
			$eventloginfo->setuser_id($user_id);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}
	
	function get_rfid_scanning_log()
	{
		$query = "SELECT rfid_no FROM fss_tbl_rfid_scanning_log";
		//echo debugPDO($query, array() );exit;
		$result = execute_query( $query, array() ) or die("get_rfid_scanning_log query fail.");
		return new readonlyresultset($result);
	}
}
?>