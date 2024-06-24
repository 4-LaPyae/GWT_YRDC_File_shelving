<?php
class department_bol
{
	function select_department_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$department_dal = new department_dal();
		return $department_dal ->select_department_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_department_name($department_name, $department_id = 0)
	{
		$department_dal = new department_dal();
		return $department_dal->check_duplicate_department_name($department_name, $department_id);
	}
	
	function save_department($department_info) 
	{
		$department_dal = new department_dal();
		if($department_info->get_department_id())
			$result = $department_dal->update_department($department_info);
		else					
			$result = $department_dal->insert_department($department_info);
		return $result;		
	}
	
	function select_department_byid($department_id) 
	{
		$department_dal = new department_dal();
		$result = $department_dal->select_department_byid ($department_id);
		return $result->getNext();
	}
	
	function delete_department($department_id) 
	{
		$department_dal = new department_dal();
		return $department_dal->delete_department($department_id);		
	}
	
	function get_all_department($department_enables = '')
	{
		$department_dal = new department_dal();
		return $department_dal->select_all_department($department_enables);
	}
	
	function get_department_by_depttype($type)
	{
		$department_dal = new department_dal();
		return $department_dal->get_department_by_depttype($type);
	}
}
?>