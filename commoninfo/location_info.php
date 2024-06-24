<?php
class location_info
{
	private $location_id;
	private $location_code;
	private $location_name;
	
	public function set_location_id($value)
	{
		$this->location_id=$value;
	}
	public function get_location_id()
	{
		return $this->location_id;
	}
	
	public function set_location_code($value)
	{
		$this->location_code=$value;
	}	
	public function get_location_code()
	{
		return $this->location_code;
	}
	
	public function set_location_name($value)
	{
		$this->location_name=$value;
	}	
	public function get_location_name()
	{
		return $this->location_name;
	}
}
?>