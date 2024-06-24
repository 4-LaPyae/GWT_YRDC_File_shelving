<?php
class gate_bol
{
	function select_gate_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$gate_dal = new gate_dal();
		return $gate_dal ->select_gate_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function select_rfid_gate_pass_log_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$gate_dal = new gate_dal();
		return $gate_dal ->select_rfid_gate_pass_log_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function check_duplicate_gate_name($gate_code, $gate_name, $location_id, $gate_id = 0)
	{
		$gate_dal = new gate_dal();
		return $gate_dal->check_duplicate_gate_name($gate_code, $gate_name, $location_id, $gate_id);
	}
	
	function save_gate($gate_info) 
	{
		$gate_dal = new gate_dal();
		if($gate_info->get_gate_id())
			$result = $gate_dal->update_gate($gate_info);
		else					
			$result = $gate_dal->insert_gate($gate_info);
		return $result;		
	}
	
	function select_gate_byid($gate_id) 
	{
		$gate_dal = new gate_dal();
		$result = $gate_dal->select_gate_byid ($gate_id);
		return $result->getNext();
	}
	
	function delete_gate($gate_id) 
	{
		$gate_dal = new gate_dal();
		return $gate_dal->delete_gate($gate_id);		
	}
	
	function get_all_gate()
	{
		$gate_dal = new gate_dal();
		return $gate_dal->select_all_gate();
	}
}
?>