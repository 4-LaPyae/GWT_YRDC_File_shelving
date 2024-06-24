<?php
class nrcdal
{
	function select_nrc_division()
	{
		$query = "SELECT * FROM fss_tbl_nrc GROUP BY division_code;";
		$result = execute_query( $query ) or die("select_nrc_division query fail.");
		return new readonlyresultset($result);
	}
	
	function select_nrc_township_by_division_id($division_code)
	{
		if($division_code != '')
			$query = "SELECT * FROM fss_tbl_nrc WHERE division_code = :division_code ORDER BY township_code;";
		else
			$query = "SELECT * FROM fss_tbl_nrc WHERE division_code = '၁' ORDER BY township_code;";
		$param = array(':division_code'=>$division_code);
		//echo debugPDO($query, $param);exit;
		$result = execute_query( $query, $param ) or die("select_nrc_township_by_division_id query fail.");
		return new readonlyresultset($result);
	}

	function get_nrc_division_township_byid($division_code)
	{
		$query = "SELECT division_code AS value, 'division' AS type 
		FROM fss_tbl_nrc 
		GROUP BY division_code 
		UNION ALL
		SELECT township_code AS value, 'township' AS type 
		FROM fss_tbl_nrc 
		WHERE division_code = :division_code 
		ORDER BY value";
		$param = array(':division_code'=>$division_code);
		//echo debugPDO($query, $param);exit;		
		$result = execute_query($query, $param) or die('get_nrc_division_township_byid query fail.');
		return new readonlyresultset($result);
	}

	function check_duplicate_nrc_code($division_code, $township_code, $id = 0)
	{
		$query = "SELECT * FROM fss_tbl_nrc WHERE division_code = :division_code AND township_code = :township_code AND id <> :id";
		$result = execute_query($query, array(':division_code' => $division_code, ':township_code' => $township_code, ':id' => $id)) or die('check_duplicate_nrc_code query fail.');
		$result_obj =  new readonlyresultset($result);
		if( $result_obj->rowCount() >0 )
			return TRUE;
		else
			return FALSE;
	}

	function insert_nrc_code($nrc_info)
	{
		$division_code = $nrc_info->get_division_code();
		$township_code = $nrc_info->get_township_code();

		$query = "INSERT INTO fss_tbl_nrc(division_code, township_code) VALUES (:division_code, :township_code);";
		$param = array(':division_code'=>$division_code, ':township_code'=>$township_code);
		//echo debugPDO($query,$param);exit;
		if( execute_non_query($query, $param) )
		{
			$nrc_id = last_instert_id();
			$filter = "nrc_id=$nrc_id";
			$table = 'nrc';
			$type = 'Insert';
			$new_field_arr = array('division_code'=>$division_code, 'township_code'=> $township_code);
			$eventlogbol = new eventlogbol();
			$description = $eventlogbol->get_event_description($type, $new_field_arr);
			$user_id = 0;
			if( isset($_SESSION['TLR_ADMINISTRATOR_LOGIN_ID']) )
				$user_id = clean($_SESSION['TLR_ADMINISTRATOR_LOGIN_ID']);

			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}

	function update_nrc_code($nrc_info)
	{
		$nrc_id = $nrc_info->get_nrc_id();
		$division_code = $nrc_info->get_division_code();
		$township_code = $nrc_info->get_township_code();	
		
		$eventlogbol = new eventlogbol();
		$table = 'nrc';		
		$filter = "id = $nrc_id";			
		$old_data = $eventlogbol->get_old_data_combine($table, "id=:nrc_id", array("nrc_id"=>$nrc_id) );
		
		$query = "UPDATE fss_tbl_nrc SET division_code = :division_code, township_code = :township_code WHERE id = :nrc_id;";
		$param = array(':division_code'=>$division_code, ':township_code'=>$township_code, ':nrc_id'=>$nrc_id);
		//echo debugPDO($query,$param);exit;
		if( execute_non_query($query, $param) )
		{
			$type = 'Update';
			$eventlogbol = new eventlogbol();
			$new_field_arr = array('division_code'=>$division_code, 'township_code'=> $township_code);			
			$description = $eventlogbol->get_event_description($type, $new_field_arr, $old_data);
			if($description == '')
				return TRUE;
			$user_id = 0;
			if( isset($_SESSION['TLR_ADMINISTRATOR_LOGIN_ID']) )
				$user_id = clean($_SESSION['TLR_ADMINISTRATOR_LOGIN_ID']);
				
			$eventloginfo = new eventloginfo();
			$eventloginfo->setaction_type($type);
			$eventloginfo->setuser_id($user_id);
			$eventloginfo->settable_name($table);
			$eventloginfo->setfilter($filter);
			$eventloginfo->setdescription($description);
			return $eventlogbol->save_eventlog($eventloginfo);
		}
		else
			return FALSE;
	}

	function select_nrc_list($DisplayStart, $DisplayLength, $SortingCols, $cri_arr)
	{
		$cri_str = $cri_arr[0];
		$param = $cri_arr[1];
		
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM fss_tbl_nrc ";
		$query .= $cri_str;
		$query .= $SortingCols;		
		$query .= " LIMIT $DisplayStart, $DisplayLength";
		$result = execute_query($query, $param) or die("select_nrc_list query fail.");
		return new readonlyresultset($result);
	}

	function select_nrc_byid($nrc_id)
	{
		$query = "SELECT * FROM fss_tbl_nrc WHERE id = :nrc_id ";
		$param = array(':nrc_id'=>$nrc_id);
		//echo debugPDO($query,$param);exit;
		$result = execute_query( $query, $param ) or die("select_nrc_byid query fail.");
		return new readonlyresultset($result);
	}
	
	function update_globalsetting($update_arr)
	{
		$cri_str = $update_arr['cri_str'];
		$value = $update_arr['value'];
		$query = "UPDATE com_tbl_globalsetting SET setting_value = $value $cri_str";
		return execute_query($query) ;
	}
	
	function get_nrc_division_township()
	{
		$query ="	SELECT township_code AS value, 'township' AS type 
		FROM fss_tbl_nrc 
		WHERE division_code = '၁' ORDER BY value";
		$result = execute_query($query) or die('get_nrc_division_township query fail.');
		return new readonlyresultset($result);
	}

	function get_nrc_bydivision($division_code)
	{
		$query = "SELECT * FROM fss_tbl_nrc WHERE division_code = :division_code ORDER BY township_code";
		$param = array(':division_code'=>$division_code);
		//echo debugPDO($query, $param);exit;
		$result = execute_query($query, $param) or die('get_nrc_bydivision query fail.');
		return new readonlyresultset($result);
	}
}
?>