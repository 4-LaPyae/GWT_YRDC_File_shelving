<?php
class shelf_bol
{
	function select_shelf_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$shelf_dal = new shelf_dal();
		return $shelf_dal ->select_shelf_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_shelf_name($shelf_code, $shelf_name, $location_id, $shelf_id = 0)
	{
		$shelf_dal = new shelf_dal();
		return $shelf_dal->check_duplicate_shelf_name($shelf_code, $shelf_name, $location_id, $shelf_id);
	}
	
	function save_shelf($shelf_info) 
	{
		$shelf_dal = new shelf_dal();
		if($shelf_info->get_shelf_id())
			$result = $shelf_dal->update_shelf($shelf_info);
		else					
			$result = $shelf_dal->insert_shelf($shelf_info);
		return $result;		
	}
	
	function select_shelf_byid($shelf_id) 
	{
		$shelf_dal = new shelf_dal();
		$result = $shelf_dal->select_shelf_byid ($shelf_id);
		return $result->getNext();
	}
	
	function delete_shelf($shelf_id) 
	{
		$shelf_dal = new shelf_dal();
		return $shelf_dal->delete_shelf($shelf_id);		
	}
	
	function get_all_shelf($department_enables = '')
	{
		$shelf_dal = new shelf_dal();
		return $shelf_dal->select_all_shelf($department_enables);
	}
	
	function get_shelf_data_by_id($shelf_id)
	{
		$shelf_dal = new shelf_dal();
		return $shelf_dal->select_shelf_data_by_id($shelf_id);
	}
}
?>