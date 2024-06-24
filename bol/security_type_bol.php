<?php
class security_type_bol
{
	function select_security_type_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$security_type_dal = new security_type_dal();
		return $security_type_dal ->select_security_type_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_security_type_name($security_type_code, $security_type_name, $security_type_id = 0)
	{
		$security_type_dal = new security_type_dal();
		return $security_type_dal->check_duplicate_security_type_name($security_type_code, $security_type_name, $security_type_id);
	}
	
	function save_security_type($security_type_info) 
	{
		$security_type_dal = new security_type_dal();
		if($security_type_info->get_security_type_id())
			$result = $security_type_dal->update_security_type($security_type_info);
		else					
			$result = $security_type_dal->insert_security_type($security_type_info);
		return $result;		
	}
	
	function select_security_type_byid($security_type_id) 
	{
		$security_type_dal = new security_type_dal();
		$result = $security_type_dal->select_security_type_byid ($security_type_id);
		return $result->getNext();
	}
	
	function delete_security_type($security_type_id) 
	{
		$security_type_dal = new security_type_dal();
		return $security_type_dal->delete_security_type($security_type_id);		
	}
	
	function get_all_security_type($security_type_enables='')
	{
		$security_type_dal = new security_type_dal();
		return $security_type_dal->select_all_security_type($security_type_enables);
	}
}
?>