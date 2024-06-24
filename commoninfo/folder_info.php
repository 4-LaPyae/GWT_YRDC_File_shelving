<?php
class folder_info
{
	private $folder_id;
	private $file_type_id;
	private $rfid_no;
	private $description;
	private $folder_no;
	private $security_type_id;
	private $shelf_id;
	private $shelf_row;
	private $shelf_column;
	private $destroy_order_employeeid;
	private $destroy_order_employee_name;
	private $destroy_order_designation;
	private $destroy_order_department;
	private $destroy_duty_employeeid;
	private $destroy_duty_employee_name;
	private $destroy_duty_designation;
	private $destroy_duty_department;
	private $destroy_date;
	private $destroy_order_no;
	private $destroy_remark;
	private $created_by;
	private $created_date;
	private $modified_by;
	private $modified_date;
	
	public function set_folder_id($value)
	{
		$this->folder_id=$value;
	}
	public function get_folder_id()
	{
		return $this->folder_id;
	}
		
	public function set_file_type_id($value)
	{
		$this->file_type_id=$value;
	}	
	public function get_file_type_id()
	{
		return $this->file_type_id;
	}
	
	public function set_rfid_no($value)
	{
		$this->rfid_no=$value;
	}	
	public function get_rfid_no()
	{
		return $this->rfid_no;
	}
	
	public function set_description($value)
	{
		$this->description=$value;
	}
	public function get_description()
	{
		return $this->description;
	}
	
	public function set_folder_no($value)
	{
		$this->folder_no=$value;
	}
	public function get_folder_no()
	{
		return $this->folder_no;
	}
	
	public function set_security_type_id($value)
	{
		$this->security_type_id=$value;
	}
	public function get_security_type_id()
	{
		return $this->security_type_id;
	}
	
	public function set_shelf_id($value)
	{
		$this->shelf_id=$value;
	}
	public function get_shelf_id()
	{
		return $this->shelf_id;
	}
	
	public function set_shelf_row($value)
	{
		$this->shelf_row=$value;
	}	
	public function get_shelf_row()
	{
		return $this->shelf_row;
	}
	
	public function set_shelf_column($value)
	{
		$this->shelf_column=$value;
	}	
	public function get_shelf_column()
	{
		return $this->shelf_column;
	}
	
	public function set_destroy_order_employeeid($value)
	{
		$this->destroy_order_employeeid=$value;
	}	
	public function get_destroy_order_employeeid()
	{
		return $this->destroy_order_employeeid;
	}
	
	public function set_destroy_order_employee_name($value)
	{
		$this->destroy_order_employee_name=$value;
	}	
	public function get_destroy_order_employee_name()
	{
		return $this->destroy_order_employee_name;
	}
	
	public function set_destroy_order_designation($value)
	{
		$this->destroy_order_designation=$value;
	}	
	public function get_destroy_order_designation()
	{
		return $this->destroy_order_designation;
	}
	
	public function set_destroy_order_department($value)
	{
		$this->destroy_order_department=$value;
	}	
	public function get_destroy_order_department()
	{
		return $this->destroy_order_department;
	}
	
	public function set_destroy_duty_employeeid($value)
	{
		$this->destroy_duty_employeeid=$value;
	}	
	public function get_destroy_duty_employeeid()
	{
		return $this->destroy_duty_employeeid;
	}
	
	public function set_destroy_duty_employee_name($value)
	{
		$this->destroy_duty_employee_name=$value;
	}	
	public function get_destroy_duty_employee_name()
	{
		return $this->destroy_duty_employee_name;
	}
	
	public function set_destroy_duty_designation($value)
	{
		$this->destroy_duty_designation=$value;
	}	
	public function get_destroy_duty_designation()
	{
		return $this->destroy_duty_designation;
	}
	
	public function set_destroy_duty_department($value)
	{
		$this->destroy_duty_department=$value;
	}	
	public function get_destroy_duty_department()
	{
		return $this->destroy_duty_department;
	}
	
	public function set_destroy_date($value)
	{
		$this->destroy_date=$value;
	}	
	public function get_destroy_date()
	{
		return $this->destroy_date;
	}
	
	public function set_destroy_order_no($value)
	{
		$this->destroy_order_no=$value;
	}	
	public function get_destroy_order_no()
	{
		return $this->destroy_order_no;
	}
	
	public function set_destroy_remark($value)
	{
		$this->destroy_remark=$value;
	}
	public function get_destroy_remark()
	{
		return $this->destroy_remark;
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