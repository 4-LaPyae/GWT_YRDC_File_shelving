<?php
class file_transaction_info
{
	private $file_transaction_id;
	private $folder_transaction_id;
	private $file_id;
	private $taken_date;
	private $taken_employeeid;
	private $taken_employee_name;
	private $taken_designation;
	private $taken_department;
	private $remark;
	private $created_by;
	private $created_date;
	private $modified_by;
	private $modified_date;
	
	public function set_file_transaction_id($value)
	{
		$this->file_transaction_id = $value;
	}
	public function get_file_transaction_id()
	{
		return $this->file_transaction_id;
	}
	
	public function set_folder_transaction_id($value)
	{
		$this->folder_transaction_id = $value;
	}
	public function get_folder_transaction_id()
	{
		return $this->folder_transaction_id;
	}
	
	public function set_file_id($value)
	{
		$this->file_id = $value;
	}
	public function get_file_id()
	{
		return $this->file_id;
	}
	
	public function set_taken_date($value)
	{
		$this->taken_date = $value;
	}
	public function get_taken_date()
	{
		return $this->taken_date;
	}
	
	public function set_taken_employeeid($value)
	{
		$this->taken_employeeid = $value;
	}
	public function get_taken_employeeid()
	{
		return $this->taken_employeeid;
	}
	
	public function set_taken_employee_name($value)
	{
		$this->taken_employee_name = $value;
	}
	public function get_taken_employee_name()
	{
		return $this->taken_employee_name;
	}
	
	public function set_taken_designation($value)
	{
		$this->taken_designation = $value;
	}
	public function get_taken_designation()
	{
		return $this->taken_designation;
	}
		
	public function set_taken_department($value)
	{
		$this->taken_department = $value;
	}
	public function get_taken_department()
	{
		return $this->taken_department;
	}
	
	public function set_remark($value)
	{
		$this->remark = $value;
	}
	public function get_remark()
	{
		return $this->remark;
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