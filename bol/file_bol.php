<?php
class file_bol
{
	function select_file_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$file_dal = new file_dal();
		return $file_dal ->select_file_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_file_name($letter_date, $letter_no, $file_id = 0)
	{
		$file_dal = new file_dal();
		return $file_dal->check_duplicate_file_name($letter_date, $letter_no, $file_id);
	}
	
	function save_file($file_info) 
	{
		$file_dal = new file_dal();
		if($file_info->get_file_id())
			$result = $file_dal->update_file($file_info);
		else					
			$result = $file_dal->insert_file($file_info);
		return $result;		
	}
	
	function update_file_destroy($file_info) 
	{
		$file_dal = new file_dal();
		return $file_dal->update_file_destroy($file_info);		
	}
	
	function save_file_to_department($file_id, $to_department_arr)
	{
		$file_dal = new file_dal();
		return $file_dal->save_file_to_department($file_id, $to_department_arr);
	}
	
	function update_file_to_department($file_id, $to_department_arr)
	{
		$file_dal = new file_dal();
		return $file_dal->update_file_to_department($file_id, $to_department_arr);
	}
	
	function select_file_byid($file_id) 
	{
		$file_dal = new file_dal();
		$result = $file_dal->select_file_byid ($file_id);
		return $result->getNext();
	}
	
	function select_file_to_department($file_id)
	{
		$file_dal = new file_dal();
		return $result = $file_dal->select_file_to_department($file_id);
	}
	
	function delete_file($file_id) 
	{
		$file_dal = new file_dal();
		return $file_dal->delete_file($file_id);		
	}
	
	function get_all_file()
	{
		$file_dal = new file_dal();
		return $file_dal->select_all_file();
	}
	
	function select_file_byfolderid($folder_id)
	{
		$file_dal = new file_dal();
		return $file_dal->select_file_byfolderid($folder_id);
	}
	
	function select_folder_file_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$file_dal = new file_dal();
		return $file_dal ->select_folder_file_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function update_file_status($file_id, $status)
	{
		$file_dal = new file_dal();
		return  $file_dal->update_file_status($file_id, $status);
	}
}
?>