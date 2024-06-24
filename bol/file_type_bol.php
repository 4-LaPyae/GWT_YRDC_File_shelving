<?php
class file_type_bol
{
	function select_file_type_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$file_type_dal = new file_type_dal();
		return $file_type_dal ->select_file_type_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_file_type_name($file_type_code, $file_type_name, $file_type_id = 0)
	{
		$file_type_dal = new file_type_dal();
		return $file_type_dal->check_duplicate_file_type_name($file_type_code, $file_type_name, $file_type_id);
	}
	
	function save_file_type($file_type_info) 
	{
		$file_type_dal = new file_type_dal();
		if($file_type_info->get_file_type_id())
			$result = $file_type_dal->update_file_type($file_type_info);
		else					
			$result = $file_type_dal->insert_file_type($file_type_info);
		return $result;		
	}
	
	function select_file_type_byid($file_type_id) 
	{
		$file_type_dal = new file_type_dal();
		$result = $file_type_dal->select_file_type_byid ($file_type_id);
		return $result->getNext();
	}
	
	function delete_file_type($file_type_id) 
	{
		$file_type_dal = new file_type_dal();
		return $file_type_dal->delete_file_type($file_type_id);		
	}
	
	function get_all_file_type()
	{
		$file_type_dal = new file_type_dal();
		return $file_type_dal->select_all_file_type();
	}
}
?>