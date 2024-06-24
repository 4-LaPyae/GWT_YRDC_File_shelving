<?php
class securitybol
{
	function get_encrypt_column($table_name)
	{
		$field_str = $primary_key = '';
		switch($table_name)
		{
			case 'user':
				$field_str = "user_id, user_name, user_email, password, user_type_id, is_active, require_changepassword, IFNULL(modified_date, '') ";
				$primary_key = 'user_id';
				break;
				
			case 'customer':
				$field_str = "customer_id, customer_name, IFNULL(nrc_division_code, '') , 
				IFNULL(nrc_township_code, '') , IFNULL(nrc_citizen_type, '') , IFNULL(nrc_number, '') , IFNULL(nrc_text, '') , IFNULL(passport, '') , 
				father_name, IFNULL(date_of_birth, '') , street, house_no, IFNULL(division_id, '') , IFNULL(township_id, '') , IFNULL(ward_id, '') , 
				created_by, created_date, IFNULL(modified_by, '') , IFNULL(modified_date, '') , is_lock";
				$primary_key = 'customer_id';
				break;
			
			case 'folder':
				$field_str = "folder_id, file_type_id, IFNULL(rfid_no, '') , IFNULL(description, '') , folder_no, 
				security_type_id, IFNULL(shelf_id, '') , shelf_row, shelf_column, IFNULL(destroy_date, '') , IFNULL(destroy_order_no, '') , 
				IFNULL(destroy_remark, '') , IFNULL(destroy_order_employeeid, '') , IFNULL(destroy_order_employee_name, '') , 
				IFNULL(destroy_order_designation, '') , IFNULL(destroy_order_department, '') , IFNULL(destroy_duty_employeeid, '') , 
				IFNULL(destroy_duty_employee_name, '') , IFNULL(destroy_duty_designation, '') , IFNULL(destroy_duty_department, '') , status, 
				created_by, created_date, IFNULL(modified_by, '') , IFNULL(modified_date, '') , is_lock";
				$primary_key = 'folder_id';
				break;
			
			case 'folder_transaction':
				$field_str = "transaction_id, folder_id, taken_date, IFNULL(given_date, '') , taken_employeeid, 
				taken_employee_name, taken_designation, taken_department, IFNULL(given_employeeid, '') , IFNULL(given_employee_name, '') , 
				IFNULL(given_designation, '') , IFNULL(given_department, '') , IFNULL(remark, '') , created_by, created_date, 
				IFNULL(modified_by, '') , IFNULL(modified_date, '')";
				$primary_key = 'transaction_id';
				break;
			
			case 'file':
				$field_str = "file_id, folder_id, IFNULL(letter_no, '') , letter_count, letter_date, 
				IFNULL(description, '') , IFNULL(to_do, '') , IFNULL(remark, '') , from_department_type, IFNULL(from_department_id, '') , 
				to_department_type, IFNULL(security_type_id, '') , IFNULL(application_type_id, '') , IFNULL(application_description, '') , 
				IFNULL(application_references, '') , IFNULL(receiver_customer_id, '') , IFNULL(sender_customer_id, '') , IFNULL(destroy_date, '') , 
				IFNULL(destroy_order_no, '') , IFNULL(destroy_remark, '') , IFNULL(destroy_order_employeeid, '') , 
				IFNULL(destroy_order_employee_name, '') , IFNULL(destroy_order_designation, '') , IFNULL(destroy_order_department, '') , 
				IFNULL(destroy_duty_employeeid, '') , IFNULL(destroy_duty_employee_name, '') , IFNULL(destroy_duty_designation, '') , 
				IFNULL(destroy_duty_department, '') , status, created_by, created_date, IFNULL(modified_by, '') , 
				IFNULL(modified_date, '')";
				$primary_key = 'file_id';
				break;
				
			case 'file_transaction':
				$field_str = "file_transaction_id, IFNULL(folder_transaction_id, '') , file_id, taken_date, 
				IFNULL(given_date, '') , taken_employeeid, taken_employee_name, taken_designation, taken_department, 
				IFNULL(given_employeeid, '') , IFNULL(given_employee_name, '') , IFNULL(given_designation, '') , IFNULL(given_department, '') , 
				IFNULL(remark, '') , created_by, created_date, IFNULL(modified_by, '') , IFNULL(modified_date, '')";
				$primary_key = 'file_transaction_id';
				break;
		}
		return array($field_str, $primary_key);
	}
	
	function get_mysql_encryptvalue($table, $field_str, $cristr, $param)
	{
		$securitydal = new securitydal();
		$result_arr = $securitydal->get_mysql_encryptvalue($table, $field_str, $cristr, $param);
		return $result_arr['encrypt_value'];
	}
	
	//Check exist record or not in invalid_log table
	function is_exist_invalid_log($table_name, $field_name, $record_id)
	{
		$securitydal = new securitydal();
		if($securitydal->is_exist_invalid_log($table_name, $field_name, $record_id) != 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function check_and_change_invalid_record($table_name, $cri_arr, $url)
	{
		$event_page = basename($url);
		$change_result_value = FALSE;
		$adminid = 0;
		$usertype = 2;
		$field_str='';
		if(isset($_SESSION['YRDCFSH_LOGIN_ID']))
		{
			$adminid = $_SESSION['YRDCFSH_LOGIN_ID'];
			$usertype = 1;
		}
		$securitydal = new securitydal();
		$userdal = new userdal();
		$customer_dal = new customer_dal();
		$folder_dal = new folder_dal();
		
		$encrypt_array = $this->get_encrypt_column($table_name);
		$field_str = $encrypt_array[0];
		$primary_key = $encrypt_array[1];
		
		$change_result = $securitydal->select_change_encryptvalue($table_name, $field_str, $cri_arr);
		if($change_result->rowCount())
		{
			while($result_arr = $change_result->getNext())
			{
				$decrypt_value_arr = explode(', ', array_pop($result_arr));
				
				//Handle encrypt_value is null or blank
				if(count($decrypt_value_arr) == 1)
				{
					$decrypt_value_arr = array();
					foreach($result_arr as $val)
						$decrypt_value_arr[] = "";
				}
				
				$i = 0;
				foreach($result_arr as $key=>$val)
				{
					$column_name = str_replace("IFNULL(", "", $key);
					$column_name = str_replace(", '')", "", $column_name);
					// echo $val .'==' .$decrypt_value_arr[$i];exit;
					if($val != $decrypt_value_arr[$i])
					{
						if(! $this->is_exist_invalid_log($table_name, $column_name, $result_arr[$primary_key]))
						{
							// Save in Invalid Log
							$securitydal->save_invalid_log($adminid, $usertype, $url, $table_name, $result_arr[$primary_key], $column_name, $decrypt_value_arr[$i], $val, 1);		
						}
						
						//Change Status
						if( $table_name == 'user' )
							$change_result_value = $userdal->change_user_status($result_arr[$primary_key], 2);
						elseif($table_name == 'customer')
							$change_result_value = $customer_dal->change_customer_lock($result_arr[$primary_key], 1);
						elseif($table_name == 'folder' || $table_name == 'folder_transaction' || $table_name == 'file' || $table_name == 'file_transaction')
						{
							if($table_name == 'file_transaction')
								$change_result_value = $folder_dal->change_folder_lock($result_arr[$primary_key], 1);
							else
								$change_result_value = $folder_dal->change_folder_lock($result_arr['folder_id'], 1);
						}
						else
							$change_result_value = TRUE;
					}
					$i++;
				}
			}
		}		
		return $change_result_value;
	}
	
	function update_and_return_encryptvalue_in_table($table_name, $filter, $param = array())
	{
		$encrypt_array = $this->get_encrypt_column($table_name);
		$field_str = $encrypt_array[0];
		$cri_str = " WHERE $filter ";
		
		$encrypt_value = $this->get_mysql_encryptvalue($table_name, $field_str, $cri_str, $param);
		$securitydal = new securitydal();
		return $securitydal->update_encrypt_value($table_name, $encrypt_value, $cri_str, $param);		
	}
	
	function check_invalid_log()
	{
		$securitydal = new securitydal();
		return  $securitydal->check_invalid_log();
	}
}
?>