<?php
class security_type_info
{
	private $security_type_id;
	private $security_type_code;
	private $security_type_name;
	
	public function set_security_type_id($value)
	{
		$this->security_type_id=$value;
	}
	public function get_security_type_id()
	{
		return $this->security_type_id;
	}
	
	public function set_security_type_code($value)
	{
		$this->security_type_code=$value;
	}	
	public function get_security_type_code()
	{
		return $this->security_type_code;
	}
	
	public function set_security_type_name($value)
	{
		$this->security_type_name=$value;
	}	
	public function get_security_type_name()
	{
		return $this->security_type_name;
	}
}
?>