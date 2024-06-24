<?php
class nrc_info
{
	private $nrc_id;
	private $division_code;
	private $township_code;
	
	public function set_nrc_id($value)
	{
		$this->nrc_id = $value;
	}
	public function get_nrc_id()
	{
		return $this->nrc_id;
	}
	
	public function set_division_code($value)
	{
		$this->division_code = $value;
	}
	public function get_division_code()
	{
		return $this->division_code;
	}
	
	public function set_township_code($value)
	{
		$this->township_code = $value;
	}
	public function get_township_code()
	{
		return $this->township_code;
	}
}
?>