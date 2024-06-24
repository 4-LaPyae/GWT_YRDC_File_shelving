<?php
class location_bol
{
	function select_location_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$location_dal = new location_dal();
		return $location_dal ->select_location_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_location_name($location_code, $location_name, $location_id = 0)
	{
		$location_dal = new location_dal();
		return $location_dal->check_duplicate_location_name($location_code, $location_name, $location_id);
	}
	
	function save_location($location_info) 
	{
		$location_dal = new location_dal();
		if($location_info->get_location_id())
			$result = $location_dal->update_location($location_info);
		else					
			$result = $location_dal->insert_location($location_info);
		return $result;		
	}
	
	function select_location_byid($location_id) 
	{
		$location_dal = new location_dal();
		$result = $location_dal->select_location_byid ($location_id);
		return $result->getNext();
	}
	
	function delete_location($location_id) 
	{
		$location_dal = new location_dal();
		return $location_dal->delete_location($location_id);		
	}
	
	function get_all_location()
	{
		$location_dal = new location_dal();
		return $location_dal->select_all_location();
	}
}
?>