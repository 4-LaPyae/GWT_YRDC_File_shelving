<?php
class township_info
{
	private $township_id;
	private $division_id;
	private $township_code;
	private $township_name;
	
	public function set_township_id($value)
	{
		$this->township_id=$value;
	}
	public function get_township_id()
	{
		return $this->township_id;
	}
	
	public function set_division_id($value)
	{
		$this->division_id=$value;
	}
	public function get_division_id()
	{
		return $this->division_id;
	}
	
	public function set_township_code($value)
	{
		$this->township_code=$value;
	}	
	public function get_township_code()
	{
		return $this->township_code;
	}
	
	public function set_township_name($value)
	{
		$this->township_name=$value;
	}	
	public function get_township_name()
	{
		return $this->township_name;
	}
}
?>