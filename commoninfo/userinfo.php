<?php
class userinfo
{
	private $user_id;
	private $user_name;
	private $user_email;
	private $password;
	private $user_type_id;
	
	public function set_user_id($value)
	{
		$this->user_id=$value;
	}
	public function get_user_id()
	{
		return $this->user_id;
	}
	
	public function set_user_type_id($value)
	{
		$this->user_type_id=$value;
	}
	public function get_user_type_id()
	{
		return $this->user_type_id;
	}
	
	public function set_user_name($value)
	{
		$this->user_name=$value;
	}	
	public function get_user_name()
	{
		return $this->user_name;
	}
	
	public function set_user_email($value)
	{
		$this->user_email=$value;
	}
	public function get_user_email()
	{
		return $this->user_email;
	}
	
	public function set_password($value)
	{
		$this->password=$value;
	}
	public function get_password()
	{
		return $this->password;
	}
		
}
?>