<?php
class file_info
{
	private $file_id;
	private $folder_id;
	private $letter_no;
	private $letter_count;
	private $letter_date;
	private $description;
	private $to_do;
	private $remark;
	private $from_department_type;
	private $from_department_id;
	private $to_department_type;
	private $security_type_id;
	private $application_type_id;
	private $application_description;
	private $application_references;
	private $receiver_customer_id;
	private $sender_customer_id;
	private $destroy_date;
	private $destroy_order_no;
	private $destroy_remark;
	private $destroy_order_employeeid;
	private $destroy_order_employee_name;
	private $destroy_order_designation;
	private $destroy_order_department;
	private $destroy_duty_employeeid;
	private $destroy_duty_employee_name;
	private $destroy_duty_designation;
	private $destroy_duty_department;
	private $status;
	private $created_by;
	private $created_date;
	private $modified_by;
	private $modified_date;
	
	public function set_file_id($value)
	{
		$this->file_id=$value;
	}
	public function get_file_id()
	{
		return $this->file_id;
	}
		
	public function set_folder_id($value)
	{
		$this->folder_id=$value;
	}	
	public function get_folder_id()
	{
		return $this->folder_id;
	}
	
	public function set_letter_no($value)
	{
		$this->letter_no=$value;
	}	
	public function get_letter_no()
	{
		return $this->letter_no;
	}	
	
	public function set_letter_count($value)
	{
		$this->letter_count=$value;
	}	
	public function get_letter_count()
	{
		return $this->letter_count;
	}	
	
	public function set_letter_date($value)
	{
		$this->letter_date=$value;
	}
	public function get_letter_date()
	{
		return $this->letter_date;
	}
	
	public function set_description($value)
	{
		$this->description=$value;
	}
	public function get_description()
	{
		return $this->description;
	}
	
	public function set_to_do($value)
	{
		$this->to_do=$value;
	}
	public function get_to_do()
	{
		return $this->to_do;
	}
	
	public function set_remark($value)
	{
		$this->remark=$value;
	}
	public function get_remark()
	{
		return $this->remark;
	}
	
	public function set_from_department_type($value)
	{
		$this->from_department_type=$value;
	}
	public function get_from_department_type()
	{
		return $this->from_department_type;
	}
	
	public function set_from_department_id($value)
	{
		$this->from_department_id=$value;
	}
	public function get_from_department_id()
	{
		return $this->from_department_id;
	}
	
	public function set_to_department_type($value)
	{
		$this->to_department_type=$value;
	}
	public function get_to_department_type()
	{
		return $this->to_department_type;
	}
	
	public function set_security_type_id($value)
	{
		$this->security_type_id=$value;
	}
	public function get_security_type_id()
	{
		return $this->security_type_id;
	}
	
	public function set_application_type_id($value)
	{
		$this->application_type_id=$value;
	}
	public function get_application_type_id()
	{
		return $this->application_type_id;
	}
	
	public function set_application_description($value)
	{
		$this->application_description=$value;
	}
	public function get_application_description()
	{
		return $this->application_description;
	}
	
	public function set_application_references($value)
	{
		$this->application_references=$value;
	}
	public function get_application_references()
	{
		return $this->application_references;
	}
	
	public function set_receiver_customer_id($value)
	{
		$this->receiver_customer_id=$value;
	}
	public function get_receiver_customer_id()
	{
		return $this->receiver_customer_id;
	}
	
	public function set_sender_customer_id($value)
	{
		$this->sender_customer_id=$value;
	}
	public function get_sender_customer_id()
	{
		return $this->sender_customer_id;
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
	
	public function set_status($value)
	{
		$this->status=$value;
	}
	public function get_status()
	{
		return $this->status;
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