<?php
class ward_bol
{
	function select_ward_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$ward_dal = new ward_dal();
		return $ward_dal ->select_ward_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_ward_name($ward_name, $township_id, $ward_id = 0)
	{
		$ward_dal = new ward_dal();
		return $ward_dal->check_duplicate_ward_name($ward_name, $township_id, $ward_id);
	}
	
	function save_ward($ward_info) 
	{
		$ward_dal = new ward_dal();
		if($ward_info->get_ward_id())
			$result = $ward_dal->update_ward($ward_info);
		else					
			$result = $ward_dal->insert_ward($ward_info);
		return $result;		
	}
	
	function select_ward_byid($ward_id) 
	{
		$ward_dal = new ward_dal();
		$result = $ward_dal->select_ward_byid ($ward_id);
		return $result->getNext();
	}
	
	function delete_ward($ward_id) 
	{
		$ward_dal = new ward_dal();
		return $ward_dal->delete_ward($ward_id);		
	}
	
	function get_all_ward()
	{
		$ward_dal = new ward_dal();
		return $ward_dal->select_all_ward();
	}
	
	function get_ward_by_township_id($township_id)
	{
		$ward_dal = new ward_dal();
		return $ward_dal->select_ward_by_township_id($township_id);
	}
}
?>