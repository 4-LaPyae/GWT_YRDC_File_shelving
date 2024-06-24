<?php
class report_bol
{
	function select_folder_borrow_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$report_dal = new report_dal();
		return $report_dal ->select_folder_borrow_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function select_file_borrow_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$report_dal = new report_dal();
		return $report_dal ->select_file_borrow_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function select_folder_burn_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$report_dal = new report_dal();
		return $report_dal ->select_folder_burn_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	function select_file_burn_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$report_dal = new report_dal();
		return $report_dal ->select_file_burn_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	// စာဖိုင်တွဲ အနှစ်ချုပ်
	function select_folder_summary_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$report_dal = new report_dal();
		return $report_dal ->select_folder_summary_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	// စာဖိုင်တွဲ အနှစ်ချုပ် စုစုပေါင်း
	function get_total_folder_summary_report($cri_arr)
	{
		$report_dal = new report_dal();
		$result = $report_dal->select_total_folder_summary_report($cri_arr);
		return $result->getNext();
	}
	
	// စာဖိုင် အနှစ်ချုပ်
	function select_file_summary_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$report_dal = new report_dal();
		return $report_dal ->select_file_summary_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr);		
	}
	
	// စာဖိုင် အနှစ်ချုပ် စုစုပေါင်း
	function get_total_file_summary_report($cri_arr)
	{
		$report_dal = new report_dal();
		$result = $report_dal->select_total_file_summary_report($cri_arr);
		return $result->getNext();
	}
}
?>