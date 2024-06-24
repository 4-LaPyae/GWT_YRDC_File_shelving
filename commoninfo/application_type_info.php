<?php
class application_type_info
{
	private $application_type_id;
	private $application_type_code;
	private $application_type_name;
	
	public function set_application_type_id($value)
	{
		$this->application_type_id=$value;
	}
	public function get_application_type_id()
	{
		return $this->application_type_id;
	}
	
	public function set_application_type_code($value)
	{
		$this->application_type_code=$value;
	}	
	public function get_application_type_code()
	{
		return $this->application_type_code;
	}
	
	public function set_application_type_name($value)
	{
		$this->application_type_name=$value;
	}	
	public function get_application_type_name()
	{
		return $this->application_type_name;
	}
}
?>