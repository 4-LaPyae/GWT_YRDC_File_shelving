<?php
class division_info
{
	private $division_id;
	private $division_code;
	private $division_name;
	
	public function set_division_id($value)
	{
		$this->division_id=$value;
	}
	public function get_division_id()
	{
		return $this->division_id;
	}
	
	public function set_division_code($value)
	{
		$this->division_code=$value;
	}	
	public function get_division_code()
	{
		return $this->division_code;
	}
	
	public function set_division_name($value)
	{
		$this->division_name=$value;
	}	
	public function get_division_name()
	{
		return $this->division_name;
	}
}
?>