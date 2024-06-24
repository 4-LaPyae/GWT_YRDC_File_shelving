<?php
class employee_dal
{
	function select_all_employee()
	{
		$qry = "SELECT * FROM fss_tbl_employee;";
		$result = execute_query($qry) or die("select_all_employee query fail.");
		return new readonlyresultset($result);
	}
}
?>