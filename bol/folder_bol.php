<?php
class folder_bol
{
	function select_folder_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$folder_dal = new folder_dal();
		return $folder_dal ->select_folder_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_folder_name($folder_code, $folder_name, $location_id, $folder_id = 0)
	{
		$folder_dal = new folder_dal();
		return $folder_dal->check_duplicate_folder_name($folder_code, $folder_name, $location_id, $folder_id);
	}
	
	function save_folder($folder_info) 
	{
		$folder_dal = new folder_dal();
		if($folder_info->get_folder_id())
			$result = $folder_dal->update_folder($folder_info);
		else					
			$result = $folder_dal->insert_folder($folder_info);
		return $result;		
	}
	
	function update_folder_destroy($folder_info) 
	{
		$folder_dal = new folder_dal();
		return $folder_dal->update_folder_destroy($folder_info);		
	}
	
	function select_folder_byid($folder_id) 
	{
		$folder_dal = new folder_dal();
		$result = $folder_dal->select_folder_byid ($folder_id);
		return $result->getNext();
	}
	
	function delete_folder($folder_id) 
	{
		$folder_dal = new folder_dal();
		return $folder_dal->delete_folder($folder_id);		
	}
	
	function get_all_folder()
	{
		$folder_dal = new folder_dal();
		return $folder_dal->select_all_folder();
	}
	
	function update_folder_status($folder_id, $status)
	{
		$folder_dal = new folder_dal();
		return  $folder_dal->update_folder_status($folder_id, $status);
	}
	
	function get_folder_location_by_shelf_id($shelf_id, $folder_id, $i, $j)
	{
		$folder_dal = new folder_dal();
		return $folder_dal->select_folder_location_by_shelf_id($shelf_id, $folder_id, $i, $j);
	}
	
	function get_rfid_scanning_log() 
	{
		$folder_dal = new folder_dal();
		$result = $folder_dal->get_rfid_scanning_log();
		return $result->getNext();
	}
}
?>