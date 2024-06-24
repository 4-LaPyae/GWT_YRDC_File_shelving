<?php
class report_dal
{
	function select_folder_borrow_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SQL_CALC_FOUND_ROWS fd.folder_id, taken_employeeid, taken_employee_name, 
		taken_department, DATE_FORMAT(date(taken_date), '%d-%m-%Y') AS now_taken_date, rfid_no, folder_no, 
		description, fd.file_type_id, file_type_name, fd.security_type_id, 
		st.security_type_name 
		FROM fss_tbl_folder fd 
		LEFT JOIN fss_tbl_folder_transaction fot ON fot.folder_id = fd.folder_id
		LEFT JOIN fss_tbl_file_type ft ON fd.file_type_id = ft.file_type_id 
		LEFT JOIN fss_tbl_security_type st ON fd.security_type_id = st.security_type_id 
		LEFT JOIN fss_tbl_shelf sf ON fd.shelf_id = sf.shelf_id ";
		$query .= $cri_str;
		$query .= $SortingCols;	
		if ($DisplayLength > 0)
			$query .= " LIMIT $DisplayStart, $DisplayLength";
		// echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_folder_borrow_report_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function select_file_borrow_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SQL_CALC_FOUND_ROWS f.file_id, taken_employeeid, taken_employee_name, 
		taken_department, DATE_FORMAT(date(taken_date), '%d-%m-%Y') AS now_taken_date, 
		DATE_FORMAT(date(letter_date), '%d-%m-%Y') AS now_letter_date, 
		letter_no, description, f.security_type_id, security_type_name, f.application_type_id, application_type_name  
		FROM fss_tbl_file f 
		LEFT JOIN (
			SELECT  folder_id, fd.shelf_id, sf.department_id 
			FROM fss_tbl_folder fd 
			LEFT JOIN fss_tbl_shelf sf ON fd.shelf_id = sf.shelf_id
		) td ON td.folder_id = f.folder_id
		LEFT JOIN fss_tbl_file_transaction flt ON flt.file_id = f.file_id 
		LEFT JOIN fss_tbl_security_type s ON s.security_type_id = f.security_type_id 
		LEFT JOIN fss_tbl_application_type a ON a.application_type_id = f.application_type_id  ";
		$query .= $cri_str;
		$query .= $SortingCols;	
		if ($DisplayLength > 0)
			$query .= " LIMIT $DisplayStart, $DisplayLength";
		// echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_file_borrow_report_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function select_folder_burn_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SQL_CALC_FOUND_ROWS fd.folder_id, destroy_order_no, destroy_order_employeeid, destroy_order_employee_name, 
		destroy_order_department, destroy_duty_employeeid, destroy_duty_employee_name, destroy_duty_department, 
		DATE_FORMAT(date(destroy_date), '%d-%m-%Y') AS now_destroy_date, rfid_no, folder_no, 
		description, fd.file_type_id, file_type_name, fd.security_type_id, 
		st.security_type_name 
		FROM fss_tbl_folder fd 
		LEFT JOIN fss_tbl_file_type ft ON fd.file_type_id = ft.file_type_id 
		LEFT JOIN fss_tbl_security_type st ON fd.security_type_id = st.security_type_id 
		LEFT JOIN fss_tbl_shelf sf ON fd.shelf_id = sf.shelf_id ";
		$query .= $cri_str;
		$query .= $SortingCols;	
		if ($DisplayLength > 0)
			$query .= " LIMIT $DisplayStart, $DisplayLength";
		// echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_folder_burn_report_list query fail."); 
		return new readonlyresultset($result);
	}
	
	function select_file_burn_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SQL_CALC_FOUND_ROWS f.file_id, destroy_order_no, destroy_order_employeeid, destroy_order_employee_name, 
		destroy_order_department, destroy_duty_employeeid, destroy_duty_employee_name, destroy_duty_department, 
		DATE_FORMAT(date(destroy_date), '%d-%m-%Y') AS now_destroy_date, 
		DATE_FORMAT(date(letter_date), '%d-%m-%Y') AS now_letter_date, 
		letter_no, description, f.security_type_id, security_type_name, f.application_type_id, application_type_name  
		FROM fss_tbl_file f 
		LEFT JOIN (
			SELECT  folder_id, fd.shelf_id, sf.department_id 
			FROM fss_tbl_folder fd 
			LEFT JOIN fss_tbl_shelf sf ON fd.shelf_id = sf.shelf_id
		) td ON td.folder_id = f.folder_id
		LEFT JOIN fss_tbl_security_type s ON s.security_type_id = f.security_type_id 
		LEFT JOIN fss_tbl_application_type a ON a.application_type_id = f.application_type_id  ";
		$query .= $cri_str;
		$query .= $SortingCols;	
		if ($DisplayLength > 0)
			$query .= " LIMIT $DisplayStart, $DisplayLength";
		// echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_file_burn_report_list query fail."); 
		return new readonlyresultset($result);
	}
	
	// စာဖိုင်တွဲ အနှစ်ချုပ်
	function select_folder_summary_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SQL_CALC_FOUND_ROWS s.department_id, department_name, 
		IFNULL(COUNT(f.folder_id), 0) AS folder_count, IFNULL(COUNT(td.folder_id), 0) AS borrow_count, 
		IFNULL(COUNT(fs.folder_id), 0) AS destroy_count 
		/* IFNULL(
		(
			SELECT COUNT(folder_id) FROM fss_tbl_folder WHERE status = 3 AND folder_id = f.folder_id AND folder_id IS NOT NULL
			GROUP BY folder_id 
		), 0) AS destroy_count */
		FROM fss_tbl_folder f 
		LEFT JOIN fss_tbl_shelf s ON s.shelf_id = f.shelf_id 
		LEFT JOIN 
		(
			SELECT folder_id 
			FROM fss_tbl_folder_transaction 
			WHERE given_date IS NULL 
		) td ON td.folder_id = f.folder_id 
		LEFT JOIN 
		(
			SELECT folder_id
				FROM fss_tbl_folder 
				WHERE status = 3 
		) fs ON fs.folder_id = f.folder_id
		LEFT JOIN fss_tbl_department d ON d.department_id = s.department_id  ";
		$query .= $cri_str.' GROUP BY s.department_id, department_name';
		$query .= $SortingCols;
		if ($DisplayLength > 0)
			$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_folder_summary_report_list query fail."); 
		return new readonlyresultset($result);
	}
	
	// စာဖိုင်တွဲ အနှစ်ချုပ် စုစုပေါင်း
	function select_total_folder_summary_report($cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT 
		SUM(folder_count) AS sum_folder_count, SUM(borrow_count) AS sum_borrow_count, SUM(destroy_count) AS sum_destroy_count
		FROM(
			SELECT s.department_id, department_name, 
			IFNULL(COUNT(f.folder_id), 0) AS folder_count, IFNULL(COUNT(td.folder_id), 0) AS borrow_count, 
			IFNULL(COUNT(fs.folder_id), 0) AS destroy_count 
			/* IFNULL(
			(
				SELECT COUNT(folder_id) FROM fss_tbl_folder WHERE status = 3 AND folder_id = f.folder_id AND folder_id IS NOT NULL
				GROUP BY folder_id 
			), 0) AS destroy_count */
			FROM fss_tbl_folder f 
			LEFT JOIN fss_tbl_shelf s ON s.shelf_id = f.shelf_id 
			LEFT JOIN 
			(
				SELECT folder_id 
				FROM fss_tbl_folder_transaction 
				WHERE given_date IS NULL 
			) td ON td.folder_id = f.folder_id 
			LEFT JOIN 
			(
				SELECT folder_id
					FROM fss_tbl_folder 
					WHERE status = 3 
			) fs ON fs.folder_id = f.folder_id 
			LEFT JOIN fss_tbl_department d ON d.department_id = s.department_id $cri_str 
			GROUP BY s.department_id, department_name 
			ORDER BY s.department_id ASC
		)td ";
		//echo debugPDO($query, $param);exit();
		$result = execute_query($query, $param) or die('select_total_folder_summary_report query fail.');;
		return new readonlyresultset($result);
	}
	
	// စာဖိုင် အနှစ်ချုပ်
	function select_file_summary_report_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		/* $query = "SELECT SQL_CALC_FOUND_ROWS td.department_id, td.department_name, 
		COUNT(f.file_id) AS file_count, 
		SUM((
			SELECT COUNT(fl.file_id) 
			FROM fss_tbl_file fl 
			LEFT JOIN fss_tbl_file_transaction flt ON flt.file_id = fl.file_id 
			WHERE status = 2 AND given_date IS NULL AND fl.file_id = f.file_id 
		)) AS borrow_count, 
		SUM((
			SELECT COUNT(file_id) 
			FROM fss_tbl_file WHERE status = 3 AND file_id = f.file_id 
		)) AS destroy_count 
		FROM fss_tbl_file f 
		LEFT JOIN (
			SELECT fd.folder_id, s.department_id, department_name 
			FROM fss_tbl_folder fd 
			LEFT JOIN fss_tbl_shelf s ON s.shelf_id = fd.shelf_id 
			LEFT JOIN fss_tbl_department d ON d.department_id = s.department_id     
			GROUP BY fd.folder_id , s.department_id, department_name
		) td ON td.folder_id = f.folder_id  ";
		$query .= $cri_str.' GROUP BY td.department_id, td.department_name '; */
		
		$query = "SELECT SQL_CALC_FOUND_ROWS s.department_id, department_name, 
		COUNT(f.file_id) AS file_count, IFNULL(COUNT(td.file_id), 0) AS borrow_count, 
			IFNULL(COUNT(fs.file_id), 0) AS destroy_count 
		/* SUM((
			SELECT COUNT(fl.file_id) 
			FROM fss_tbl_file fl 
			LEFT JOIN fss_tbl_file_transaction flt ON flt.file_id = fl.file_id 
			WHERE status = 2 AND given_date IS NULL AND fl.file_id = f.file_id 
		)) AS borrow_count, 
		SUM((
			SELECT COUNT(file_id) 
			FROM fss_tbl_file WHERE status = 3 AND file_id = f.file_id 
		)) AS destroy_count  */
		FROM fss_tbl_file f 
		LEFT JOIN 
			(
				SELECT file_id 
				FROM fss_tbl_file_transaction 
				WHERE given_date IS NULL 
			) td ON td.file_id = f.file_id 
			LEFT JOIN 
			(
				SELECT file_id
					FROM fss_tbl_file 
					WHERE status = 3 
			) fs ON fs.file_id = f.file_id  
		LEFT JOIN fss_tbl_folder fo ON f.folder_id = fo.folder_id 
		LEFT JOIN fss_tbl_shelf s ON s.shelf_id = fo.shelf_id 
		LEFT JOIN fss_tbl_department d ON d.department_id = s.department_id  	
		";
		$query .= $cri_str.' GROUP BY s.department_id, department_name ';		
		$query .= $SortingCols;
		if ($DisplayLength > 0)
			$query .= " LIMIT $DisplayStart, $DisplayLength";
		//echo debugPDO($query, $param);exit;
		$result = execute_query ($query, $param)  or die("select_file_summary_report_list query fail."); 
		return new readonlyresultset($result);
	}
	
	// စာဖိုင် အနှစ်ချုပ် စုစုပေါင်း
	function select_total_file_summary_report($cri_arr) 
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SUM(file_count) AS sum_file_count, SUM(borrow_count) AS sum_borrow_count, 
		SUM(destroy_count) AS sum_destroy_count
		FROM
		(
			SELECT s.department_id, department_name, 
			IFNULL(COUNT(f.file_id), 0) AS file_count, IFNULL(COUNT(td.file_id), 0) AS borrow_count, 
			IFNULL(COUNT(fs.file_id), 0) AS destroy_count 
			/* SUM((
				SELECT COUNT(fl.file_id) 
				FROM fss_tbl_file fl 
				LEFT JOIN fss_tbl_file_transaction flt ON flt.file_id = fl.file_id 
				WHERE status = 2 AND given_date IS NULL AND fl.file_id = f.file_id 
			)) AS borrow_count, 
			SUM((
				SELECT COUNT(file_id) 
				FROM fss_tbl_file WHERE status = 3 AND file_id = f.file_id 
			)) AS destroy_count  */
			FROM fss_tbl_file f 
			/* LEFT JOIN (
				SELECT fd.folder_id, s.department_id, department_name 
				FROM fss_tbl_folder fd 
				LEFT JOIN fss_tbl_shelf s ON s.shelf_id = fd.shelf_id 
				LEFT JOIN fss_tbl_department d ON d.department_id = s.department_id     
				GROUP BY fd.folder_id 
			) td ON td.folder_id = f.folder_id $cri_str  */
			LEFT JOIN 
			(
				SELECT file_id 
				FROM fss_tbl_file_transaction 
				WHERE given_date IS NULL 
			) td ON td.file_id = f.file_id 
			LEFT JOIN 
			(
				SELECT file_id
					FROM fss_tbl_file 
					WHERE status = 3 
			) fs ON fs.file_id = f.file_id  
			LEFT JOIN fss_tbl_folder fo ON f.folder_id = fo.folder_id 
			LEFT JOIN fss_tbl_shelf s ON s.shelf_id = fo.shelf_id 
			LEFT JOIN fss_tbl_department d ON d.department_id = s.department_id  
			GROUP BY s.department_id, department_name 
		) th";
		//echo debugPDO($query, $param);exit();
		$result = execute_query($query, $param) or die('select_total_file_summary_report query fail.');;
		return new readonlyresultset($result);
	}
}
?>