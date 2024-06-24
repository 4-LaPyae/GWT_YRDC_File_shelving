<?php
class file_transaction_bol
{
	function select_file_transaction_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$file_transaction_dal = new file_transaction_dal();
		return $file_transaction_dal->select_file_transaction_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_file_transaction_name($taken_employeeid, $taken_employee_name, $taken_designation, $taken_department, $file_transaction_id = 0)
	{
		$file_transaction_dal = new file_transaction_dal();
		return $file_transaction_dal->check_duplicate_file_transaction_name($taken_employeeid, $taken_employee_name, $taken_designation, $taken_department, $file_transaction_id);
	}
	
	function check_given_date($file_id) 
	{
		$file_transaction_dal = new file_transaction_dal();
		return $file_transaction_dal->check_given_date($file_id);
	}
	
	function save_given_date($given_date, $file_transaction_id)
	{
		$file_transaction_dal = new file_transaction_dal();
		$result = $file_transaction_dal->save_given_date($given_date, $file_transaction_id);
		return $result;
	}
	
	function save_file_transaction($file_transaction_info) 
	{
		$file_transaction_dal = new file_transaction_dal();
		if($file_transaction_info->get_file_transaction_id())
			$result = $file_transaction_dal->update_file_transaction($file_transaction_info);
		else					
			$result = $file_transaction_dal->insert_file_transaction($file_transaction_info);
		return $result;		
	}
	
	function select_file_transaction_byid($file_transaction_id) 
	{
		$file_transaction_dal = new file_transaction_dal();
		$result = $file_transaction_dal->select_file_transaction_byid($file_transaction_id);
		return $result->getNext();
	}
	
	function delete_file_transaction($file_transaction_id) 
	{
		$file_transaction_dal = new file_transaction_dal();
		return $file_transaction_dal->delete_file_transaction($file_transaction_id);		
	}
}
?>