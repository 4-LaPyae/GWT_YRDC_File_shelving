<?php
class customer_bol
{
	function select_customer_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$customer_dal = new customer_dal();
		return $customer_dal ->select_customer_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_customer_name($date_of_birth, $customer_name, $customer_id = 0)
	{
		$customer_dal = new customer_dal();
		return $customer_dal->check_duplicate_customer_name($date_of_birth, $customer_name, $customer_id);
	}
	
	function save_customer($customer_info) 
	{
		$customer_dal = new customer_dal();
		if($customer_info->get_customer_id())
			$result = $customer_dal->update_customer($customer_info);
		else					
			$result = $customer_dal->insert_customer($customer_info);
		return $result;		
	}
	
	function select_customer_byid($customer_id) 
	{
		$customer_dal = new customer_dal();
		$result = $customer_dal->select_customer_byid ($customer_id);
		return $result->getNext();
	}
	
	function delete_customer($customer_id) 
	{
		$customer_dal = new customer_dal();
		return $customer_dal->delete_customer($customer_id);		
	}
	
	function get_all_customer()
	{
		$customer_dal = new customer_dal();
		return $customer_dal->select_all_customer();
	}
}
?>