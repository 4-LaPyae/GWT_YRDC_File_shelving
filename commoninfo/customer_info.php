<?php
class customer_info
{
	private $customer_id;
	private $customer_name;
	private $nrc_division_code;
	private $nrc_township_code;
	private $nrc_citizen_type;
	private $nrc_number;
	private $nrc_text;
	private $passport;
	private $father_name;
	private $date_of_birth;
	private $street;
	private $house_no;
	private $division_id;
	private $township_id;
	private $ward_id;
	private $created_by;
	private $created_date;
	private $modified_by;
	private $modified_date;
	
	public function set_customer_id($value)
	{
		$this->customer_id=$value;
	}
	public function get_customer_id()
	{
		return $this->customer_id;
	}
		
	public function set_customer_name($value)
	{
		$this->customer_name=$value;
	}	
	public function get_customer_name()
	{
		return $this->customer_name;
	}
	
	public function set_nrc_division_code($value)
	{
		$this->nrc_division_code = $value;
	}
	public function get_nrc_division_code()
	{
		return $this->nrc_division_code;
	}
	
	public function set_nrc_township_code($value)
	{
		$this->nrc_township_code = $value;
	}
	public function get_nrc_township_code()
	{
		return $this->nrc_township_code;
	}
	
	public function set_nrc_citizen_type($value)
	{
		$this->nrc_citizen_type = $value;
	}
	public function get_nrc_citizen_type()
	{
		return $this->nrc_citizen_type;
	}
	
	public function set_nrc_number($value)
	{
		$this->nrc_number = $value;
	}
	public function get_nrc_number()
	{
		return $this->nrc_number;
	}
	
	public function set_nrc_text($value)
	{
		$this->nrc_text = $value;
	}
	public function get_nrc_text()
	{
		return $this->nrc_text;
	}
	
	public function set_passport($value)
	{
		$this->passport = $value;
	}
	public function get_passport()
	{
		return $this->passport;
	}
	
	public function set_father_name($value)
	{
		$this->father_name=$value;
	}
	public function get_father_name()
	{
		return $this->father_name;
	}
	
	public function set_date_of_birth($value)
	{
		$this->date_of_birth=$value;
	}
	public function get_date_of_birth()
	{
		return $this->date_of_birth;
	}
	
	public function set_street($value)
	{
		$this->street=$value;
	}
	public function get_street()
	{
		return $this->street;
	}
	
	public function set_house_no($value)
	{
		$this->house_no=$value;
	}
	public function get_house_no()
	{
		return $this->house_no;
	}
	
	public function set_division_id($value)
	{
		$this->division_id=$value;
	}	
	public function get_division_id()
	{
		return $this->division_id;
	}
	
	public function set_township_id($value)
	{
		$this->township_id=$value;
	}	
	public function get_township_id()
	{
		return $this->township_id;
	}
	
	public function set_ward_id($value)
	{
		$this->ward_id=$value;
	}	
	public function get_ward_id()
	{
		return $this->ward_id;
	}
	
	public function set_created_by($value)
	{
		$this->created_by = $value;
	}
	public function get_created_by()
	{
		return $this->created_by;
	}
	
	public function set_created_date($value)
	{
		$this->created_date = $value;
	}
	public function get_created_date()
	{
		return $this->created_date;
	}
	
	public function set_modified_by($value)
	{
		$this->modified_by = $value;
	}
	public function get_modified_by()
	{
		return $this->modified_by;
	}
	
	public function set_modified_date($value)
	{
		$this->modified_date = $value;
	}
	public function get_modified_date()
	{
		return $this->modified_date;
	}
}
?>