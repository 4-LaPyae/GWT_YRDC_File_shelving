<?php
class nrcbol
{
	/* Call from globalfunction.php -- get_nrc_choice_option() */
	function get_nrc_division()
	{
		$nrcdal = new nrcdal();
		return $nrcdal->select_nrc_division();
	}
	
	/* Call from globalfunction.php -- get_nrc_choice_option() | form_exec.php */
	function get_nrc_township_by_division_id($division_code)
	{	
		$nrcdal = new nrcdal();
		return $nrcdal->select_nrc_township_by_division_id($division_code);
	}
	
	function get_nrc_division_township()
	{
		$nrcdal = new nrcdal();
		return $nrcdal->get_nrc_division_township();
	}

	function get_nrc_bydivision($division_code)
	{
		$nrcdal = new nrcdal();
		return $nrcdal->get_nrc_bydivision($division_code);
	}
	
	function get_nrc_by_division($division_code)
	{
		$nrcdal = new nrcdal();
		return $nrcdal->get_nrc_bydivision($division_code);
	}
	
	function get_nrc_division_township_byid($division_code)
	{
		$nrcdal = new nrcdal();
		return $nrcdal->get_nrc_division_township_byid($division_code);
	}

	function check_duplicate_nrc_code($division_code, $township_code, $id = 0)
	{
		$nrcdal = new nrcdal();
		return $nrcdal->check_duplicate_nrc_code($division_code, $township_code, $id);
	}

	function save_nrc_code($nrc_info)
	{
		$nrcdal = new nrcdal();
		if( $nrc_info->get_nrc_id() )
			$result = $nrcdal->update_nrc_code($nrc_info);
		else
			$result = $nrcdal->insert_nrc_code($nrc_info);
		return $result;
	}

	function select_nrc_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$nrcdal = new nrcdal();
		return $nrcdal->select_nrc_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}

	function select_nrc_byid($nrc_id)
	{
		$nrcdal = new nrcdal();
		$result = $nrcdal->select_nrc_byid($nrc_id);	
		if( $result )
			return $result->getNext();
		else
			return FALSE;
	}
	
	function update_globalsetting($update_arr)
	{
		$nrcdal = new nrcdal();
		return $nrcdal->update_globalsetting($update_arr);
	}
}
?>