<?php
class shelf_info
{
	private $shelf_id;
	private $shelf_code;
	private $shelf_name;
	private $location_id;
	private $department_id;
	private $no_of_row;
	private $no_of_column;
	
	public function set_shelf_id($value)
	{
		$this->shelf_id=$value;
	}
	public function get_shelf_id()
	{
		return $this->shelf_id;
	}
		
	public function set_shelf_code($value)
	{
		$this->shelf_code=$value;
	}	
	public function get_shelf_code()
	{
		return $this->shelf_code;
	}
	
	public function set_shelf_name($value)
	{
		$this->shelf_name=$value;
	}	
	public function get_shelf_name()
	{
		return $this->shelf_name;
	}
	
	public function set_location_id($value)
	{
		$this->location_id=$value;
	}
	public function get_location_id()
	{
		return $this->location_id;
	}
	
	public function set_department_id($value)
	{
		$this->department_id=$value;
	}
	public function get_department_id()
	{
		return $this->department_id;
	}
	
	public function set_no_of_row($value)
	{
		$this->no_of_row=$value;
	}
	public function get_no_of_row()
	{
		return $this->no_of_row;
	}
	
	public function set_no_of_column($value)
	{
		$this->no_of_column=$value;
	}
	public function get_no_of_column()
	{
		return $this->no_of_column;
	}
}
?>