<?php
class department_info
{
	private $department_id;
	private $department_name;
	
	public function set_department_id($value)
	{
		$this->department_id=$value;
	}
	public function get_department_id()
	{
		return $this->department_id;
	}
	
	public function set_department_name($value)
	{
		$this->department_name=$value;
	}	
	public function get_department_name()
	{
		return $this->department_name;
	}
}
?>