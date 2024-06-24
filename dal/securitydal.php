<?php
class securitydal
{
	function get_mysql_encryptvalue($table, $field_str, $cristr, $param)
	{
		global $encrypt_key;
		$qry = "SELECT aes_encrypt(CONCAT_WS(', ',  $field_str), '$encrypt_key') AS encrypt_value
					FROM fss_tbl_$table $cristr ";
		// echo debugPDO($qry, $param);exit;
		$result = execute_query($qry, $param);
		return $result->fetch(PDO::FETCH_ASSOC);
	}
	
	function select_change_encryptvalue($table_name, $field_str, $cri_arr)
	{
		$cristr = $cri_arr[0];
		$param = $cri_arr[1];
		global $encrypt_key;
	 	$qry = "SELECT  SQL_CALC_FOUND_ROWS $field_str, aes_decrypt(encrypt_value, '$encrypt_key') AS encrypt_value 
					FROM fss_tbl_$table_name 
					WHERE encrypt_value != aes_encrypt(CONCAT_WS(', ', $field_str), '$encrypt_key')
					$cristr ; ";
		// echo debugPDO($qry, $param);echo '<br/>';exit;
		$result = execute_query($qry, $param);
		return new readonlyresultset($result);		
	}
	
	function is_exist_invalid_log($table_name, $field_name, $record_id)
	{
		$qry = "SELECT COUNT(1) FROM fss_tbl_invalid_log 
					WHERE table_name = :table_name 
					AND field_name = :field_name AND record_id = :record_id AND status = :status; ";
		$param = array( ':table_name'=>"fss_tbl_$table_name", ':field_name'=>$field_name, ':record_id'=>$record_id, ':status'=>1);
		return execute_scalar_query($qry, $param);
	}
	
	function save_invalid_log($userid, $usertype, $page_name, $table_name, $record_id, $field_name, $org_value, $change_value, $status)
	{		
		$qry = "INSERT INTO fss_tbl_invalid_log (user_id, user_type, action_datetime, page_name, table_name, record_id, field_name, org_value, change_value, status)
		VALUES(:userid, :usertype, NOW(), :page_name, :table_name, :record_id, :field_name, :org_value, :change_value, :status); ";
		
		$param = array(':userid'=>$userid, ':usertype'=>$usertype, ':page_name'=>$page_name, ':table_name'=>"fss_tbl_$table_name", 
		':record_id'=>$record_id, ':field_name'=>$field_name, ':org_value'=>$org_value, ':change_value'=>$change_value, 
		':status'=>$status);
		// echo debugPDO($qry, $param);exit;
		$result = execute_non_query($qry,$param) or die("save_invalid_log query fail.");
		return $result;
	}
	
	function update_encrypt_value($table_name, $encrypt_value, $cri_str, $param)
	{
		$encrypt_qry = "UPDATE fss_tbl_$table_name SET encrypt_value =:encrypt_value $cri_str; ";
		$param[':encrypt_value'] = $encrypt_value;
		// echo debugPDO($encrypt_qry, $param);exit;
		$result = execute_query($encrypt_qry, $param) or die( 'update_encrypt_value query fail.');
		return $encrypt_value;
	}
	
	function select_pending_invalidrecord_summary()
	{
		$qry = "SELECT table_name, COUNT(DISTINCT record_id) AS invalid_count 
		FROM fss_tbl_invalid_log WHERE status = :status1 OR  status = :status2 GROUP BY table_name; ";		
		$result = execute_query($qry, array(':status1'=>1, ':status2'=>4));
		return new readonlyresultset($result);
	}
	
	function check_invalid_log()
	{
		$qry = "SELECT COUNT(*) AS status_count FROM fss_tbl_invalid_log WHERE status = :status ";
		// echo debugPDO($qry, array(':status'=>1));exit;
		return  execute_scalar_query($qry, array(':status'=>1));
	}
}
?>