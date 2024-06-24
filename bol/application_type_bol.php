<?php
class application_type_bol
{
	function select_application_type_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$application_type_dal = new application_type_dal();
		return $application_type_dal ->select_application_type_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_application_type_name($application_type_code, $application_type_name, $application_type_id = 0)
	{
		$application_type_dal = new application_type_dal();
		return $application_type_dal->check_duplicate_application_type_name($application_type_code, $application_type_name, $application_type_id);
	}
	
	function save_application_type($application_type_info) 
	{
		$application_type_dal = new application_type_dal();
		if($application_type_info->get_application_type_id())
			$result = $application_type_dal->update_application_type($application_type_info);
		else					
			$result = $application_type_dal->insert_application_type($application_type_info);
		return $result;		
	}
	
	function select_application_type_byid($application_type_id) 
	{
		$application_type_dal = new application_type_dal();
		$result = $application_type_dal->select_application_type_byid ($application_type_id);
		return $result->getNext();
	}
	
	function delete_application_type($application_type_id) 
	{
		$application_type_dal = new application_type_dal();
		return $application_type_dal->delete_application_type($application_type_id);		
	}
	
	function get_all_application_type($application_type_enables='')
	{
		$application_type_dal = new application_type_dal();
		return $application_type_dal->select_all_application_type($application_type_enables);
	}
}
?>