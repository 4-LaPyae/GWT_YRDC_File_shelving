<?php
class transaction_dal
{
	function select_folder_transaction_list($offset, $rpage, $sorting, $cri_arr)
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$qry = "SELECT SQL_CALC_FOUND_ROWS *, 
		DATE_FORMAT(date(taken_date), '%d-%m-%Y') AS now_taken_date,  
		DATE_FORMAT(date(given_date), '%d-%m-%Y') AS now_given_date 
		FROM fss_tbl_folder_transaction ";
		$qry .= $cri_str;
		$qry .= $sorting;
		if ($rpage != 0)
			$qry .= " LIMIT $offset, $rpage";
		$result = execute_query($qry, $param) or die ("select_folder_transaction_list query fail.");	
		return new readonlyresultset ($result);
	}
	
	function check_duplicate_transaction_name($taken_employeeid, $taken_employee_name, $taken_designation, $taken_department, $transaction_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_folder_transaction WHERE taken_employeeid = :taken_employeeid AND taken_employee_name = :taken_employee_name 
		AND taken_designation = :taken_designation AND taken_department = :taken_department AND transaction_id <> :transaction_id";
		
		$params = array(':taken_employeeid'=>$taken_employeeid, ':taken_employee_name'=>$taken_employee_name, 
		':taken_designation'=>$taken_designation, ':taken_department'=>$taken_department, ':transaction_id'=>$transaction_id);
		
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_transaction_name query fail.');
		return $result->rowCount();
	}
	
	function check_given_date($folder_id)
	{
		$qry = "SELECT given_date FROM fss_tbl_folder_transaction WHERE folder_id=:folder_id ORDER BY transaction_id DESC LIMIT 0,1";		
		$result = execute_query ($qry, array(':folder_id' => $folder_id)) or die("check_given_date query fail.");
		return new readonlyresultset($result);
	}
	
	function insert_transaction($transaction_info) 
	{
		$folder_id = $transaction_info->get_folder_id();
		$taken_date = $transaction_info->get_taken_date();
		$taken_employeeid = $transaction_info->get_taken_employeeid();
		$taken_employee_name = $transaction_info->get_taken_employee_name();
		$taken_designation = $transaction_info->get_taken_designation();		
		$taken_department = $transaction_info->get_taken_department();
		$remark = $transaction_info->get_remark();
		$created_by = $transaction_info->get_created_by();
		
		$query = "INSERT INTO fss_tbl_folder_transaction(folder_id, taken_date, taken_employeeid, taken_employee_name, taken_designation, taken_department, remark, created_by, created_date) 
		VALUES(:folder_id, :taken_date, :taken_employeeid,  :taken_employee_name,  :taken_designation, :taken_department, :remark, :created_by, NOW())";
		
		$param = array(":folder_id"=>$folder_id, ":taken_date"=>$taken_date, ":taken_employeeid"=>$taken_employeeid,  ":taken_employee_name"=>$taken_employee_name, ":taken_designation"=>$taken_designation, ":taken_department"=>$taken_department, ":remark"=>$remark, ":created_by"=>$created_by);
		
		if ( execute_non_query($query, $param))
		{
			$transaction_id = last_instert_id();			
			$filter = "transaction_id=:transaction_id";
			$table = 'folder_transaction';
			$type = 'Insert';
			$new_field_arr = array('folder_id'=>$folder_id, 'taken_date'=>$taken_date, 'taken_employeeid'=>$taken_employeeid, 'taken_employee_name'=>$taken_employee_name, 'taken_designation'=>$taken_designation, 'taken_department'=>$taken_department, 'remark'=>$remark, 'created_by'=>$created_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "transaction_id=:transaction_id", array(':transaction_id'=>$transaction_id));

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
			if ( $eventlogbol->save_eventlog($eventloginfo) )
				return $transaction_id;
		}
		else
			return FALSE;
	}
	
	function select_transaction_byid($transaction_id)
	{
		$query = "SELECT * FROM fss_tbl_folder_transaction WHERE transaction_id = :transaction_id";
		$params = array(':transaction_id'=>$transaction_id);
		//echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_transaction_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function update_transaction($transaction_info)
	{
		$transaction_id = $transaction_info->get_transaction_id();
		$folder_id = $transaction_info->get_folder_id();
		$taken_date = $transaction_info->get_taken_date();
		$taken_employeeid = $transaction_info->get_taken_employeeid();
		$taken_employee_name = $transaction_info->get_taken_employee_name();
		$taken_designation = $transaction_info->get_taken_designation();		
		$taken_department = $transaction_info->get_taken_department();
		$remark = $transaction_info->get_remark();
		$modified_by = $transaction_info->get_modified_by();
		
		$eventlogbol = new eventlogbol();
		$table = 'folder_transaction';
		$filter = "transaction_id=:transaction_id";
		$old_data = $eventlogbol->get_old_data($table, "transaction_id=:transaction_id", array("transaction_id"=>$transaction_id) );
		
		$query = "UPDATE fss_tbl_folder_transaction SET folder_id = :folder_id, taken_date = :taken_date, taken_employeeid = :taken_employeeid, taken_employee_name = :taken_employee_name, 
		taken_designation = :taken_designation, taken_department = :taken_department, remark = :remark, modified_by=:modified_by, modified_date=NOW() 
		WHERE transaction_id = :transaction_id;";
		
		$params = array(':transaction_id'=>$transaction_id, ':folder_id'=>$folder_id, ':taken_date'=>$taken_date, ':taken_employeeid'=>$taken_employeeid, ':taken_employee_name'=>$taken_employee_name, 
		':taken_designation'=>$taken_designation, ':taken_department'=>$taken_department, ':remark'=>$remark, ':modified_by'=>$modified_by);
		
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_transaction query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('transaction_id'=>$transaction_id, 'folder_id'=>$folder_id, 'taken_date'=>$taken_date, 'taken_employeeid'=>$taken_employeeid, 'taken_employee_name'=>$taken_employee_name, 'taken_designation'=>$taken_designation, 'taken_department'=>$taken_department, 'remark'=>$remark, 'modified_by'=>$modified_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "transaction_id=:transaction_id", array(':transaction_id'=>$transaction_id));

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
	
	function delete_folder_transaction($transaction_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "transaction_id=:transaction_id";
		$table = 'folder_transaction';
		$old_data = $eventlogbol->get_old_data($table, "transaction_id=:transaction_id", array("transaction_id"=>$transaction_id) );
		$encrypt_value = $old_data['encrypt_value'];
		unset($old_data['encrypt_value']);
		
		$query = "DELETE FROM fss_tbl_folder_transaction WHERE transaction_id = :transaction_id";
		if( execute_non_query($query, array(':transaction_id' => $transaction_id)) )
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
	
	function save_given_date($given_date, $transaction_id)
	{
		$eventlogbol = new eventlogbol();
		$table = 'folder_transaction';		
		$filter = "transaction_id=:transaction_id";
		$eventlog_param = array(':transaction_id'=>$transaction_id);
		$old_data = $eventlogbol->get_old_data($table, $filter, $eventlog_param);		
		
		$query = "UPDATE fss_tbl_folder_transaction SET given_date = :given_date WHERE transaction_id = :transaction_id";
		$param = array(':given_date'=>$given_date, ':transaction_id'=>$transaction_id);
		if( execute_query($query, $param) )
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('given_date' => $given_date, 'transaction_id' => $transaction_id);			
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "transaction_id=:transaction_id", array(':transaction_id'=>$transaction_id));

			$description = $eventlogbol->get_event_description($type, $new_field_arr, $old_data);
			if($description == '')
				return TRUE;
			
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
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}	
}
?>