<?php
class employee_bol
{
	function get_all_employee()
	{
		$employee_dal = new employee_dal();
		return $employee_dal->select_all_employee();
	}
}
?>