<?php
class customer_dal
{
	function select_customer_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		$query = "SELECT SQL_CALC_FOUND_ROWS *, 
		DATE_FORMAT(date(date_of_birth), '%d-%m-%Y') AS birth_date, 
		IF(nrc_number <> '', CONCAT(nrc_division_code, '/', nrc_township_code, '(' , nrc_citizen_type, ')', nrc_number), IF(nrc_text = '', passport, nrc_text)) AS nrc 
		FROM fss_tbl_customer c 
		LEFT JOIN fss_tbl_division d ON d.division_id = c.division_id 
		LEFT JOIN fss_tbl_township t ON t.township_id = c.township_id 
		LEFT JOIN fss_tbl_ward w ON w.ward_id = c.ward_id ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		// echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_customer_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function check_duplicate_customer_name($date_of_birth, $customer_name, $customer_id = 0)
	{
		$query = "SELECT * FROM fss_tbl_customer 
		WHERE customer_name = :customer_name AND date_of_birth = :date_of_birth AND customer_id <> :customer_id";
		$params = array(':customer_name'=>$customer_name, ':date_of_birth'=>$date_of_birth, ':customer_id'=>$customer_id);
		//echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('check_duplicate_customer_name query fail.');
		return $result->rowCount();
	}
	
	function insert_customer($customer_info)
	{
		$customer_name = $customer_info->get_customer_name();
		$nrc_division_code = $customer_info->get_nrc_division_code();
		$nrc_township_code = $customer_info->get_nrc_township_code();
		$nrc_citizen_type = $customer_info->get_nrc_citizen_type();
		$nrc_number = $customer_info->get_nrc_number();
		$nrc_text = $customer_info->get_nrc_text();
		$passport = $customer_info->get_passport();
		$father_name = $customer_info->get_father_name();
		$date_of_birth = $customer_info->get_date_of_birth();
		$street = $customer_info->get_street();
		$house_no = $customer_info->get_house_no();
		$division_id = $customer_info->get_division_id();
		if($division_id == '')
			$division_id = NULL;
		$township_id = $customer_info->get_township_id();
		if($township_id == '')
			$township_id = NULL;
		$ward_id = $customer_info->get_ward_id();
		if($ward_id == '' || $ward_id == 0)
			$ward_id = NULL;
		$created_by = $customer_info->get_created_by();
		
		$query = "INSERT INTO fss_tbl_customer(customer_name, nrc_division_code, nrc_township_code, nrc_citizen_type, nrc_number, nrc_text, passport, father_name, date_of_birth, street, house_no, division_id, township_id, ward_id, created_by, created_date) 
		VALUES (:customer_name, :nrc_division_code, :nrc_township_code, :nrc_citizen_type, :nrc_number, :nrc_text, :passport, :father_name, :date_of_birth, :street, :house_no, :division_id, :township_id, :ward_id, :created_by, NOW());";

		$params = array(':customer_name'=>$customer_name, ':nrc_division_code'=>$nrc_division_code, ':nrc_township_code'=>$nrc_township_code, ':nrc_citizen_type'=>$nrc_citizen_type, ':nrc_number'=>$nrc_number, ':nrc_text'=>$nrc_text, ':passport'=>$passport, 
		':father_name'=>$father_name, ':date_of_birth'=>$date_of_birth, ':street'=>$street, ':house_no'=>$house_no, ':division_id'=>$division_id, ':township_id'=>$township_id, ':ward_id'=>$ward_id, ':created_by'=>$created_by);
		//echo debugPDO($query, $params);exit;
		
		$result = execute_query($query, $params) or die('insert_customer query fail.');
		if( $result )
		{
			$customer_id = last_instert_id();
			$filter = "customer_id=$customer_id";
			$table = 'customer';
			$type = 'Insert';
			$new_field_arr = array('customer_name' => $customer_name, 'nrc_division_code' => $nrc_division_code, 'nrc_township_code' => $nrc_township_code, 'nrc_citizen_type' => $nrc_citizen_type, 'nrc_number' => $nrc_number, 'nrc_text' => $nrc_text, 'passport' => $passport, 
			'father_name' => $father_name, 'date_of_birth' => $date_of_birth, 'street' => $street, 'house_no' => $house_no, 'division_id' => $division_id, 'township_id' => $township_id, 'ward_id' => $ward_id, 'created_by' => $created_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "customer_id=:customer_id", array(':customer_id'=>$customer_id));

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
				return $customer_id;
		}
		else
			return FALSE;
	}
	
	function update_customer($customer_info)
	{
		$customer_id = $customer_info->get_customer_id();
		$customer_name = $customer_info->get_customer_name();
		$nrc_division_code = $customer_info->get_nrc_division_code();
		$nrc_township_code = $customer_info->get_nrc_township_code();
		$nrc_citizen_type = $customer_info->get_nrc_citizen_type();
		$nrc_number = $customer_info->get_nrc_number();
		$nrc_text = $customer_info->get_nrc_text();
		$passport = $customer_info->get_passport();
		$father_name = $customer_info->get_father_name();
		$date_of_birth = $customer_info->get_date_of_birth();
		$street = $customer_info->get_street();
		$house_no = $customer_info->get_house_no();
		$division_id = $customer_info->get_division_id();
		if($division_id == '')
			$division_id = NULL;
		$township_id = $customer_info->get_township_id();
		if($township_id == '')
			$township_id = NULL;
		$ward_id = $customer_info->get_ward_id();
		if($ward_id == '' || $ward_id == 0)
			$ward_id = NULL;
		$modified_by = $customer_info->get_modified_by();
		
		$eventlogbol = new eventlogbol();
		$table = 'customer';
		$filter = "customer_id=:customer_id";
		$old_data = $eventlogbol->get_old_data($table, "customer_id=:customer_id", array("customer_id"=>$customer_id) );
		
		$query = "UPDATE fss_tbl_customer SET customer_name = :customer_name, nrc_division_code = :nrc_division_code, nrc_township_code = :nrc_township_code, nrc_citizen_type = :nrc_citizen_type, nrc_number = :nrc_number, nrc_text = :nrc_text, passport = :passport, 
		father_name = :father_name, date_of_birth = :date_of_birth, street = :street, house_no = :house_no, division_id = :division_id, township_id = :township_id, ward_id = :ward_id, modified_by=:modified_by, modified_date=NOW() 
		WHERE customer_id = :customer_id;";
		
		$params = array(':customer_id'=>$customer_id, ':customer_name'=>$customer_name, ':nrc_division_code'=>$nrc_division_code, ':nrc_township_code'=>$nrc_township_code, ':nrc_citizen_type'=>$nrc_citizen_type, ':nrc_number'=>$nrc_number, ':nrc_text'=>$nrc_text, ':passport'=>$passport, 
		':father_name'=>$father_name, ':date_of_birth'=>$date_of_birth, ':street'=>$street, ':house_no'=>$house_no, ':division_id'=>$division_id, ':township_id'=>$township_id, ':ward_id'=>$ward_id, ':modified_by'=>$modified_by);
		
		// echo debugPDO($query, $params);exit;
		$result = execute_query($query, $params) or die('update_customer query fail.');
		if($result)
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('customer_id' => $customer_id, 'customer_name' => $customer_name, 'nrc_division_code' => $nrc_division_code, 'nrc_township_code' => $nrc_township_code, 'nrc_citizen_type' => $nrc_citizen_type, 'nrc_number' => $nrc_number, 'nrc_text' => $nrc_text, 'passport' => $passport, 
			'father_name' => $father_name, 'date_of_birth' => $date_of_birth, 'street' => $street, 'house_no' => $house_no, 'division_id' => $division_id, 'township_id' => $township_id, 'ward_id' => $ward_id, 'modified_by' => $modified_by);
			
			$securitybol = new securitybol();			
			$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "customer_id=:customer_id", array(':customer_id'=>$customer_id));

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
	
	function select_customer_byid($customer_id)
	{
		$query = "SELECT c.*, division_name, township_name, ward_name 
		FROM fss_tbl_customer c 
		LEFT JOIN fss_tbl_division d ON c.division_id = d.division_id 
		LEFT JOIN fss_tbl_township t ON c.township_id = t.township_id 
		LEFT JOIN fss_tbl_ward w ON c.ward_id = w.ward_id
		WHERE customer_id = :customer_id";
		$params = array(':customer_id'=>$customer_id);
		// echo debugPDO($query, $params );exit;
		$result = execute_query( $query, $params ) or die("select_customer_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function delete_customer($customer_id)
	{
		$eventlogbol = new eventlogbol();
		$filter = "customer_id=:customer_id";
		$table = 'customer';
		$old_data = $eventlogbol->get_old_data($table, "customer_id=:customer_id", array("customer_id"=>$customer_id) );
		$encrypt_value = $old_data['encrypt_value'];
		unset($old_data['encrypt_value']);
		
		$query = "DELETE FROM fss_tbl_customer WHERE customer_id = :customer_id";
		if( execute_non_query($query, array(':customer_id' => $customer_id)) )
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
	
	function select_all_customer()
	{
		$qry = "SELECT * FROM fss_tbl_customer;";
		$result = execute_query($qry) or die("select_all_customer query fail.");
		return new readonlyresultset($result);
	}
	
	function change_customer_lock($customer_id, $status) 
	{
		$param=array("status"=>$status,"customer_id"=>$customer_id);
		$query = "UPDATE fss_tbl_customer SET is_lock=:status WHERE customer_id=:customer_id ; ";		
		if(execute_query($query,$param))
		{
			$filter = "customer_id=:customer_id";
			$table = 'customer';
			
			$encrypt_value = '';
			$securitybol = new securitybol();
			if( $status != 1 )
				$encrypt_value = $securitybol->update_and_return_encryptvalue_in_table($table, "customer_id=:customer_id", array(':customer_id'=>$customer_id));
			
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
}
?>