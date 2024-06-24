<?php
class file_dal
{
	function select_file_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS *, department_name AS from_department_name, rc.customer_name AS receiver_customer_name, sc.customer_name AS sender_customer_name, 
		DATE_FORMAT(date(letter_date), '%d-%m-%Y') AS now_date 		
		FROM fss_tbl_file f 
		LEFT JOIN fss_tbl_department d ON d.department_id = f.from_department_id 
		LEFT JOIN fss_tbl_security_type s ON s.security_type_id = f.security_type_id 
		LEFT JOIN fss_tbl_application_type a ON a.application_type_id = f.application_type_id 
		LEFT JOIN fss_tbl_customer rc ON rc.customer_id = f.receiver_customer_id 
		LEFT JOIN fss_tbl_customer sc ON sc.customer_id = f.sender_customer_id ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_file_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_file_name($letter_date, $letter_no, $file_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_file WHERE letter_date = :letter_date AND letter_no = :letter_no 
		AND file_id <> :file_id";
		$params = array(':letter_date'=>$letter_date, ':letter_no'=>$letter_no, ':file_id'=>$file_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_file_name query fail.');
		return $result->rowCount();
	}
	
	function insert_file($file_info)
	{
		$folder_id = $file_info->get_folder_id();
		$letter_no = $file_info->get_letter_no();
		$letter_date = $file_info->get_letter_date();
		$from_department_type = $file_info->get_from_department_type();
		$from_department_id = $file_info->get_from_department_id();
		$to_department_type = $file_info->get_to_department_type();
		$sender_customer_id = $file_info->get_sender_customer_id();
		$receiver_customer_id = $file_info->get_receiver_customer_id();
		$description = $file_info->get_description();
		$security_type_id = $file_info->get_security_type_id();
		$application_type_id = $file_info->get_application_type_id();
		$application_description = $file_info->get_application_description();
		$application_references = $file_info->get_application_references();
		$letter_count = $file_info->get_letter_count();
		$to_do = $file_info->get_to_do();
		$remark = $file_info->get_remark();
		$created_by = $file_info->get_created_by();
		
		if($from_department_type == '')
			$from_department_type = NULL;
		
		if($to_department_type == '')
			$to_department_type = NULL;
		
		$query = "INSERT INTO fss_tbl_file(folder_id, letter_no, letter_count, letter_date, description, to_do, remark, from_department_type, from_department_id, to_department_type, security_type_id, application_type_id, application_description, application_references, receiver_customer_id, sender_customer_id, status, created_by, created_date) 
		VALUES (:folder_id, :letter_no, :letter_count, :letter_date, :description, :to_do, :remark, :from_department_type, :from_department_id, :to_department_type, :security_type_id, :application_type_id, :application_description, :application_references, :receiver_customer_id, :sender_customer_id, 1, :created_by, NOW());";

		$params = array(':folder_id'=>$folder_id, ':letter_no'=>$letter_no, ':letter_count'=>$letter_count, ':letter_date'=>$letter_date, 
		':description'=>$description, ':to_do'=>$to_do, ':remark'=>$remark, ':from_department_type'=>$from_department_type, ':from_department_id'=>$from_department_id, ':to_department_type'=>$to_department_type, 
		':security_type_id'=>$security_type_id, ':application_type_id'=>$application_type_id, 
		':application_description'=>$application_description, ':application_references'=>$application_references, 
		':receiver_customer_id'=>$receiver_customer_id, ':sender_customer_id'=>$sender_customer_id, ':created_by'=>$created_by);
		
		//echo debugPDO($query, $params);exit;
		
		$result = execute_query($query, $params) or die('insert_file query fail.');
		if( $result )
		{
			$file_id = last_instert_id();
			$filter = "file_id=$file_id";
			$table = 'file';
			$type = 'Insert';
			$new_field_arr = array('folder_id'=>$folder_id, 'letter_no'=>$letter_no, 'letter_count'=>$letter_count, 'letter_date'=>$letter_date, 
			'description'=>$description, 'to_do'=>$to_do, 'remark'=>$remark, 'from_department_type'=>$from_department_type, 'from_department_id'=>$from_department_id, 'to_department_type'=>$to_department_type, 
			'security_type_id'=>$security_type_id, 'application_type_id'=>$application_type_id, 
			'application_description'=>$application_description, 'application_references'=>$application_references, 
			'receiver_customer_id'=>$receiver_customer_id, 'sender_customer_id'=>$sender_customer_id, 'created_by'=>$created_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "file_id=:file_id", array(':file_id'=>$file_id));

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
				return $file_id;
		}
		else
			return FALSE;
	}
	
	function update_file($file_info)
	{
		$file_id = $file_info->get_file_id();
		$folder_id = $file_info->get_folder_id();
		$letter_no = $file_info->get_letter_no();
		$letter_date = $file_info->get_letter_date();
		$from_department_type = $file_info->get_from_department_type();
		$from_department_id = $file_info->get_from_department_id();
		$to_department_type = $file_info->get_to_department_type();
		$sender_customer_id = $file_info->get_sender_customer_id();
		$receiver_customer_id = $file_info->get_receiver_customer_id();
		$description = $file_info->get_description();
		$security_type_id = $file_info->get_security_type_id();
		$application_type_id = $file_info->get_application_type_id();
		$application_description = $file_info->get_application_description();
		$application_references = $file_info->get_application_references();
		$letter_count = $file_info->get_letter_count();
		$to_do = $file_info->get_to_do();
		$remark = $file_info->get_remark();
		$modified_by = $file_info->get_modified_by();
		
		if($from_department_type == '')
			$from_department_type = NULL;
		
		if($to_department_type == '')
			$to_department_type = NULL;
		
		$eventlogbol = new eventlogbol();
		$table = 'file';
		$filter = "file_id=:file_id";
		$old_data = $eventlogbol->get_old_data($table, "file_id=:file_id", array("file_id"=>$file_id) );
		
		$query = "UPDATE fss_tbl_file SET folder_id = :folder_id, letter_no = :letter_no, letter_count = :letter_count, 
		letter_date = :letter_date, description = :description, to_do = :to_do, remark = :remark, from_department_type = :from_department_type, 
		from_department_id = :from_department_id, to_department_type = :to_department_type, 
		security_type_id = :security_type_id, application_type_id = :application_type_id, application_description = :application_description, 
		application_references = :application_references, receiver_customer_id = :receiver_customer_id, sender_customer_id = :sender_customer_id, 
		modified_by = :modified_by, modified_date = NOW() 		
		WHERE file_id = :file_id;";
		
		$params = array(':file_id'=>$file_id, ':folder_id'=>$folder_id, ':letter_no'=>$letter_no, ':letter_count'=>$letter_count, ':letter_date'=>$letter_date, 
		':description'=>$description, ':to_do'=>$to_do, ':remark'=>$remark, ':from_department_type'=>$from_department_type, ':from_department_id'=>$from_department_id, ':to_department_type'=>$to_department_type, 
		':security_type_id'=>$security_type_id, ':application_type_id'=>$application_type_id, 
		':application_description'=>$application_description, ':application_references'=>$application_references, 
		':receiver_customer_id'=>$receiver_customer_id, ':sender_customer_id'=>$sender_customer_id, ':modified_by'=>$modified_by);
		
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_file query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('file_id'=>$file_id, 'folder_id'=>$folder_id, 'letter_no'=>$letter_no, 'letter_count'=>$letter_count, 'letter_date'=>$letter_date, 
			'description'=>$description, 'to_do'=>$to_do, 'remark'=>$remark, 'from_department_type'=>$from_department_type, 'from_department_id'=>$from_department_id, 'to_department_type'=>$to_department_type, 
			'security_type_id'=>$security_type_id, 'application_type_id'=>$application_type_id, 
			'application_description'=>$application_description, 'application_references'=>$application_references, 
			'receiver_customer_id'=>$receiver_customer_id, 'sender_customer_id'=>$sender_customer_id, 'modified_by'=>$modified_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "file_id=:file_id", array(':file_id'=>$file_id));

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
	
	function update_file_destroy($file_info)
	{
		$file_id = $file_info->get_file_id();
		$destroy_order_employeeid = $file_info->get_destroy_order_employeeid();
		$destroy_order_employee_name = $file_info->get_destroy_order_employee_name();
		$destroy_order_designation = $file_info->get_destroy_order_designation();
		$destroy_order_department = $file_info->get_destroy_order_department();
		$destroy_duty_employeeid = $file_info->get_destroy_duty_employeeid();
		$destroy_duty_employee_name = $file_info->get_destroy_duty_employee_name();
		$destroy_duty_designation = $file_info->get_destroy_duty_designation();
		$destroy_duty_department = $file_info->get_destroy_duty_department();
		$destroy_date = $file_info->get_destroy_date();
		$destroy_order_no = $file_info->get_destroy_order_no();
		$destroy_remark = $file_info->get_destroy_remark();
		$modified_by = $file_info->get_modified_by();
		
		$eventlogbol = new eventlogbol();
		$table = 'file';
		$filter = "file_id=:file_id";
		$old_data = $eventlogbol->get_old_data($table, "file_id=:file_id", array("file_id"=>$file_id) );
		
		$query = "UPDATE fss_tbl_file SET destroy_order_employeeid = :destroy_order_employeeid, destroy_order_employee_name = :destroy_order_employee_name, 
		destroy_order_designation = :destroy_order_designation, destroy_order_department = :destroy_order_department, 
		destroy_duty_employeeid = :destroy_duty_employeeid, destroy_duty_employee_name = :destroy_duty_employee_name, 
		destroy_duty_designation = :destroy_duty_designation, destroy_duty_department = :destroy_duty_department, 
		destroy_date=:destroy_date, destroy_order_no=:destroy_order_no, destroy_remark=:destroy_remark, status = 3, 
		modified_by=:modified_by, modified_date=NOW() 
		WHERE file_id = :file_id;";
		
		$params = array(':file_id'=>$file_id, ':destroy_order_employeeid'=>$destroy_order_employeeid, 
		':destroy_order_employee_name'=>$destroy_order_employee_name, ':destroy_order_designation'=>$destroy_order_designation, 
		':destroy_order_department'=>$destroy_order_department, ':destroy_duty_employeeid'=>$destroy_duty_employeeid, 
		':destroy_duty_employee_name'=>$destroy_duty_employee_name, ':destroy_duty_designation'=>$destroy_duty_designation, 
		':destroy_duty_department'=>$destroy_duty_department, ':destroy_date'=>$destroy_date, ':destroy_order_no'=>$destroy_order_no, 
		':destroy_remark'=>$destroy_remark, ':modified_by'=>$modified_by);
		
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_file_destroy query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('file_id'=>$file_id, 'destroy_order_employeeid'=>$destroy_order_employeeid, 
			'destroy_order_employee_name'=>$destroy_order_employee_name, 'destroy_order_designation'=>$destroy_order_designation, 
			'destroy_order_department'=>$destroy_order_department, 'destroy_duty_employeeid'=>$destroy_duty_employeeid, 
			'destroy_duty_employee_name'=>$destroy_duty_employee_name, 'destroy_duty_designation'=>$destroy_duty_designation, 
			'destroy_duty_department'=>$destroy_duty_department, 'destroy_date'=>$destroy_date, 'destroy_order_no'=>$destroy_order_no, 
			'destroy_remark'=>$destroy_remark, 'modified_by'=>$modified_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "file_id=:file_id", array(':file_id'=>$file_id));

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
	
	function save_file_to_department($file_id, $to_department_arr)
	{
		$query = "INSERT INTO fss_tbl_file_to_dept(file_id, to_department_id) VALUE ";
		foreach($to_department_arr as $to_department_id)
		{
			$query .= "('$file_id', '$to_department_id'), ";
		}
		$query = substr_replace($query, ";", -2 );
		
		return execute_query($query, array());
	}
	
	function update_file_to_department($file_id, $to_department_arr)
	{
		$eventlogbol = new eventlogbol();
		$filter = "file_id=:file_id";
		$table = 'file_to_dept';
		$old_data = $eventlogbol->get_old_data($table, "file_id=:file_id", array("file_id"=>$file_id) );
		$encrypt_value = $old_data['encrypt_value'];
		unset($old_data['encrypt_value']);
		
		$query = "DELETE FROM fss_tbl_file_to_dept WHERE file_id = :file_id";		
		if(execute_non_query($query, array('file_id'=>$file_id)))
		{				
			$type = 'Delete';				
			$eventlogbol = new eventlogbol();
			$description = $eventlogbol->get_event_description($type, $old_data);
			
			$user_id = 0;
			if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
				$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
			
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			$eventlogbol->save_eventlog($eventloginfo);
		}
		foreach ($to_department_arr as $value)
		{			
			$new_field_arr = array('to_department_id' => $value);
			
			$query="INSERT INTO fss_tbl_file_to_dept (file_id, to_department_id) 
			VALUES ( :file_id, :to_department_id)";
			$param=array(':file_id'=>$file_id, ':to_department_id'=>$value);
			
			if( execute_query($query,$param) )
			{
				$filter = "file_id=$file_id";
				$table = 'file_to_dept';
				$type = 'Insert';	
				
				$eventlogbol = new eventlogbol();
				$description = $eventlogbol->get_event_description($type, $new_field_arr);
				
				$user_id = 0;
				if( isset($_SESSION['YRDCFSH_LOGIN_ID']) )
					$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
				
				$eventloginfo = new eventloginfo();
				$eventloginfo->setaction_type($type);
				$eventloginfo->setuser_id($user_id);
				$eventloginfo->settable_name($table);
				$eventloginfo->setfilter($filter);
				$eventloginfo->setdescription($description);
				$eventloginfo->setencrypt_value($encrypt_value);
			}			
		}
	}
	
	function select_file_byid($file_id)
	{
		$query = "SELECT *, sc.customer_name AS sender_customer_name, rc.customer_name AS receiver_customer_name  
		FROM fss_tbl_file f 
		LEFT JOIN fss_tbl_department d ON d.department_id = f.from_department_id 
		LEFT JOIN fss_tbl_security_type s ON s.security_type_id = f.security_type_id 
		LEFT JOIN fss_tbl_application_type a ON a.application_type_id = f.application_type_id 
		LEFT JOIN fss_tbl_customer rc ON rc.customer_id = f.receiver_customer_id 
		LEFT JOIN fss_tbl_customer sc ON sc.customer_id = f.sender_customer_id 		
		WHERE f.file_id = :file_id";
		$params = array(':file_id'=>$file_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_file_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function select_file_to_department($file_id)
	{
		$query = "SELECT *, department_name AS to_department_name  
		FROM  fss_tbl_file_to_dept  ftd 
		LEFT JOIN fss_tbl_department d ON d.department_id = ftd.to_department_id 
		WHERE ftd.file_id=:file_id";		
		// echo debugPDO($query, array('file_id'=>$file_id));exit;
		$result = execute_query($query, array('file_id'=>$file_id)) or die('select_file_to_department query fail.');
		return new readonlyresultset($result);
	}
	
	function delete_file($file_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "file_id=:file_id";
		$table = 'file';
		$old_data = $eventlogbol->get_old_data($table, "file_id=:file_id", array("file_id"=>$file_id) );
		$encrypt_value = $old_data['encrypt_value'];
		unset($old_data['encrypt_value']);
		
		$query = "DELETE FROM fss_tbl_file WHERE file_id = :file_id";
		if( execute_non_query($query, array(':file_id' => $file_id)) )
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
	
	function select_all_file()
	{
		$qry = "SELECT * FROM fss_tbl_file;";
		$result = execute_query($qry) or die("select_all_file query fail.");
		return new readonlyresultset($result);
	}
	
	function select_file_byfolderid($folder_id)
	{
		$query = "SELECT f.*, department_name AS from_department_name, rc.customer_name AS receiver_customer_name, 
		sc.customer_name AS sender_customer_name, security_type_name, application_type_name,
		DATE_FORMAT(date(letter_date), '%d-%m-%Y') AS now_date 		
		FROM fss_tbl_file f 
		LEFT JOIN fss_tbl_department d ON d.department_id = f.from_department_id 
		LEFT JOIN fss_tbl_security_type s ON s.security_type_id = f.security_type_id 
		LEFT JOIN fss_tbl_application_type a ON a.application_type_id = f.application_type_id 
		LEFT JOIN fss_tbl_customer rc ON rc.customer_id = f.receiver_customer_id 
		LEFT JOIN fss_tbl_customer sc ON sc.customer_id = f.sender_customer_id
		WHERE folder_id = :folder_id";
		//echo debugPDO($query, array(':folder_id' => $folder_id));exit;
		$result = execute_query($query, array(':folder_id' => $folder_id)) or die('select_file_byfolderid query fail');
		return new readonlyresultset($result);
	}
	
	function select_folder_file_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SQL_CALC_FOUND_ROWS td.department_id, td.rfid_no, td.folder_no, td.folder_description, 
		td.file_type_id, td.security_type_id, td.shelf_id, td.file_type_name, td.security_type_name AS folder_security_type_name, td.shelf_name, td.shelf_row, td.shelf_column, 
		letter_no, DATE_FORMAT(date(letter_date), '%d-%m-%Y') AS now_date, department_name, sc.customer_name AS sender_customer_name, 
		rc.customer_name AS receiver_customer_name, description AS file_description, s.security_type_name AS file_security_type_name, 
		application_type_name, letter_count, to_do, remark
		FROM fss_tbl_file f 
		LEFT JOIN (
			SELECT  folder_id, rfid_no, folder_no, description AS folder_description, fd.file_type_id, file_type_name, fd.security_type_id, 
			st.security_type_name, fd.shelf_id, sf.department_id, shelf_name, shelf_row, shelf_column
			FROM fss_tbl_folder fd 
			LEFT JOIN fss_tbl_file_type ft ON fd.file_type_id = ft.file_type_id 
			LEFT JOIN fss_tbl_security_type st ON fd.security_type_id = st.security_type_id 
			LEFT JOIN fss_tbl_shelf sf ON fd.shelf_id = sf.shelf_id
		) td ON td.folder_id = f.folder_id
		LEFT JOIN fss_tbl_department d ON d.department_id = f.from_department_id 
		LEFT JOIN fss_tbl_application_type a ON a.application_type_id = f.application_type_id 
		LEFT JOIN fss_tbl_security_type s ON s.security_type_id = f.security_type_id
		LEFT JOIN fss_tbl_customer sc ON sc.customer_id = f.sender_customer_id 
		LEFT JOIN fss_tbl_customer rc ON rc.customer_id = f.receiver_customer_id ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		// echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_file_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function update_file_status($file_id, $status)
	{
		$eventlogbol = new eventlogbol();
		$table = 'file';
		$filter = "file_id=:file_id";
		$old_data = $eventlogbol->get_old_data($table, "file_id=:file_id", array("file_id"=>$file_id) );
		
		$query = "UPDATE fss_tbl_file SET status = :status WHERE file_id = :file_id;";
		
		$params = array(':file_id'=>$file_id, ':status'=>$status);
		
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_file_status query fail.');
		if($result)
		{
			$type = 'Update';			
			$new_field_arr = array('file_id'=>$file_id, 'status'=>$status);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "file_id=:file_id", array(':file_id'=>$file_id));

			$eventlogbol = new eventlogbol();
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
}
?>