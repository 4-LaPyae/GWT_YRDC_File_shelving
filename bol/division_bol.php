<?php
class division_bol
{
	function select_division_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$division_dal = new division_dal();
		return $division_dal ->select_division_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_division_name($division_code, $division_name, $division_id = 0)
	{
		$division_dal = new division_dal();
		return $division_dal->check_duplicate_division_name($division_code, $division_name, $division_id);
	}
	
	function save_division($division_info) 
	{
		$division_dal = new division_dal();
		if($division_info->get_division_id())
			$result = $division_dal->update_division($division_info);
		else					
			$result = $division_dal->insert_division($division_info);
		return $result;		
	}
	
	function select_division_byid($division_id) 
	{
		$division_dal = new division_dal();
		$result = $division_dal->select_division_byid ($division_id);
		return $result->getNext();
	}
	
	function delete_division($division_id) 
	{
		$division_dal = new division_dal();
		return $division_dal->delete_division($division_id);		
	}
	
	function get_all_division()
	{
		$division_dal = new division_dal();
		return $division_dal->select_all_division();
	}
	
	function get_division_by_townshipid($township_id)
	{
		$division_dal = new division_dal();
		$result=  $division_dal->get_division_by_townshipid($township_id);
		return $result->getNext();
	}
}
?>