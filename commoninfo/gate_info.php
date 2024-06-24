<?php
class gate_info
{
	private $gate_id;
	private $gate_code;
	private $gate_name;
	private $location_id;
	
	public function set_gate_id($value)
	{
		$this->gate_id=$value;
	}
	public function get_gate_id()
	{
		return $this->gate_id;
	}
		
	public function set_gate_code($value)
	{
		$this->gate_code=$value;
	}	
	public function get_gate_code()
	{
		return $this->gate_code;
	}
	
	public function set_gate_name($value)
	{
		$this->gate_name=$value;
	}	
	public function get_gate_name()
	{
		return $this->gate_name;
	}	
	
	public function set_location_id($value)
	{
		$this->location_id=$value;
	}
	public function get_location_id()
	{
		return $this->location_id;
	}
}
?>