<?php
class ward_info
{
	private $ward_id;
	private $township_id;
	private $ward_name;
	
	public function set_ward_id($value)
	{
		$this->ward_id=$value;
	}
	public function get_ward_id()
	{
		return $this->ward_id;
	}
	
	public function set_township_id($value)
	{
		$this->township_id=$value;
	}
	public function get_township_id()
	{
		return $this->township_id;
	}
	
	public function set_ward_name($value)
	{
		$this->ward_name=$value;
	}	
	public function get_ward_name()
	{
		return $this->ward_name;
	}
}
?>