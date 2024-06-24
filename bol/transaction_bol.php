<?php
class transaction_bol
{
	function select_folder_transaction_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$transaction_dal = new transaction_dal();
		return $transaction_dal->select_folder_transaction_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_transaction_name($taken_employeeid, $taken_employee_name, $taken_designation, $taken_department, $transaction_id = 0)
	{
		$transaction_dal = new transaction_dal();
		return $transaction_dal->check_duplicate_transaction_name($taken_employeeid, $taken_employee_name, $taken_designation, $taken_department, $transaction_id);
	}
	
	function check_given_date($application_id) 
	{
		$transaction_dal = new transaction_dal();
		return $transaction_dal->check_given_date($application_id);
	}
	
	function save_transaction($transaction_info) 
	{
		$transaction_dal = new transaction_dal();
		if($transaction_info->get_transaction_id())
			$result = $transaction_dal->update_transaction($transaction_info);
		else					
			$result = $transaction_dal->insert_transaction($transaction_info);
		return $result;		
	}
	
	function select_transaction_byid($transaction_id) 
	{
		$transaction_dal = new transaction_dal();
		$result = $transaction_dal->select_transaction_byid($transaction_id);
		return $result->getNext();
	}
	
	function delete_folder_transaction($transaction_id) 
	{
		$transaction_dal = new transaction_dal();
		return $transaction_dal->delete_folder_transaction($transaction_id);		
	}
		
	function save_given_date($given_date, $transaction_id)
	{
		$transaction_dal = new transaction_dal();		
		$result = $transaction_dal->save_given_date($given_date, $transaction_id);
		return $result;
	}
}
?>