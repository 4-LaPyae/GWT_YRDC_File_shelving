<?php
class file_type_info
{
	private $file_type_id;
	private $file_type_code;
	private $file_type_name;
	
	public function set_file_type_id($value)
	{
		$this->file_type_id=$value;
	}
	public function get_file_type_id()
	{
		return $this->file_type_id;
	}
	
	public function set_file_type_code($value)
	{
		$this->file_type_code=$value;
	}	
	public function get_file_type_code()
	{
		return $this->file_type_code;
	}
	
	public function set_file_type_name($value)
	{
		$this->file_type_name=$value;
	}	
	public function get_file_type_name()
	{
		return $this->file_type_name;
	}
}
?>