<?php
class township_bol
{
	function select_township_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$township_dal = new township_dal();
		return $township_dal ->select_township_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_township_name($township_code, $township_name, $division_id, $township_id = 0)
	{
		$township_dal = new township_dal();
		return $township_dal->check_duplicate_township_name($township_code, $township_name, $division_id, $township_id);
	}
	
	function save_township($township_info) 
	{
		$township_dal = new township_dal();
		if($township_info->get_township_id())
			$result = $township_dal->update_township($township_info);
		else					
			$result = $township_dal->insert_township($township_info);
		return $result;		
	}
	
	function select_township_byid($township_id) 
	{
		$township_dal = new township_dal();
		$result = $township_dal->select_township_byid ($township_id);
		return $result->getNext();
	}
	
	function delete_township($township_id) 
	{
		$township_dal = new township_dal();
		return $township_dal->delete_township($township_id);		
	}
	
	function get_all_township()
	{
		$township_dal = new township_dal();
		return $township_dal->select_all_township();
	}
	
	function get_township_by_division_id($division_id)
	{
		$township_dal = new township_dal();
		return $township_dal->select_township_by_division_id($division_id);
	}
}
?>