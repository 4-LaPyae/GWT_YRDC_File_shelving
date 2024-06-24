<?php
	session_start();
	$errmsg_arr = array ();
	$movepath= '';
	$autherrflag = false;
	$accessdenied =true;
	$pagesarr = array();
	$pageurlidarr = array();
	$pageenablearr = array();
	$userbol = new userbol();
	$user_type_department_bol = new user_type_department_bol();
	$user_type_application_type_bol = new user_type_application_type_bol();
	$user_type_security_type_bol = new user_type_security_type_bol();
	
    if(isset($_SERVER['SCRIPT_FILENAME'])){
		$currentpage=basename($_SERVER['SCRIPT_FILENAME']);
	if ( isset($_GET['authaction']) )
		$currentpage = $currentpage.'?authaction='.$_GET['authaction'];
        setcookie("url", $currentpage);
    }
    
	if ((! isset ( $_SESSION ['YRDCFSH_LOGIN_ID'] )) || $_SESSION ['YRDCFSH_LOGIN_ID'] == '' ) 
	{
		$accessdenied =false; 
		$autherrflag = false;
	} 
	else 
	{
		$userid = $_SESSION ['YRDCFSH_LOGIN_ID'];
		$usertypeid = $_SESSION ['YRDCFSH_LOGIN_TYPE_ID'];
		if($usertypeid==0)
		{
			$accessdenied =false; 
			$autherrflag = true;
		}
		else
		{
			$result = $userbol->getMenuURL($userid,$usertypeid);
			$pagesarr[] = "index.php";
			$pagesarr[] = "changepassword.php";
			$pageurlidarr["index.php"] = 0;
			$pageurlidarr["changepassword.php"] = 0;
			while($row=$result->getNext())
			{
				$pagesarr[] = $row['url'];
				$pageurlidarr[$row['url']] = $row['menu_id'];
			}
			
			$_SESSION['SESS_CRMLITE_PAGES']=$pagesarr;
			$_SESSION['SESS_CRMLITE_PAGES_URLID']=$pageurlidarr;
			$autherrflag = (array_search($currentpage,$pagesarr,true)>-1);
			
			if ($autherrflag!=false)
			{
				$currentpagemenuid=$pageurlidarr[$currentpage];
				$pageenablearr = $userbol->getMenuEnable($usertypeid,$currentpagemenuid);
				
				//permission by usertype_department
				$department_enables = $user_type_department_bol->select_department_enables($usertypeid);
				// echo $department_enables;exit;
				
				//permission by user_type_application_type
				$application_type_enables = $user_type_application_type_bol->select_application_type_enables($usertypeid);
				// echo $application_type_enables;exit;
				
				//permission by user_type_security_type
				$security_type_enables = $user_type_security_type_bol->select_security_type_enables($usertypeid);
				// echo $security_type_enables;exit;
			}
			
			if( $_SESSION ['YRDCFSH_REQUIRE_CHANGE_PASSWORD'] == 1 && $currentpage != "changepassword.php")
			{
				header ("location: changepassword.php");	
				exit();
			}
		}
	}
if ($autherrflag == false) 
{
	if($accessdenied==true)
		header ("location: access-denied.php");	
	else 
		header ("location: admin_login.php");	
	exit();
}
?>