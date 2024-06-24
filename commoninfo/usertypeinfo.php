<?php 
	class usertypeinfo
	{
		private $usertype_id;
		private $usertype_name;
		private $is_root_admin;
		private $menulist;
		private $departmentlist;
		private $applicationlist;
		private $securitylist;
		
		public function set_usertype_id($value)
		{
			$this->usertype_id = $value;
		}
		public function get_usertype_id()
		{
			return $this->usertype_id;
		}
		
		public function set_is_root_admin($value)
		{
			$this->is_root_admin = $value;
		}
		public function get_is_root_admin()
		{
			return $this->is_root_admin;
		}
		
		public function set_usertype_name($value)
		{
			$this->usertype_name = $value;
		}
		public function get_usertype_name()
		{
			return $this->usertype_name;
		}
		
		public function set_menulist($value)
		{
			$this->menulist = $value;
		}
		public function get_menulist()
		{
			return $this->menulist;
		}
		
		public function set_departmentlist($value)
		{
			$this->departmentlist = $value;
		}
		public function get_departmentlist()
		{
			return $this->departmentlist;
		}
		
		public function set_applicationlist($value)
		{
			$this->applicationlist = $value;
		}
		public function get_applicationlist()
		{
			return $this->applicationlist;
		}
		
		public function set_securitylist($value)
		{
			$this->securitylist = $value;
		}
		public function get_securitylist()
		{
			return $this->securitylist;
		}
	}
?>