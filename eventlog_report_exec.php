<?php
	$movepath = '';
	require_once ("autoload.php");
	require_once($movepath . 'library/reference.php');
	$eventlogbol= new eventlogbol();
	if(isset($_POST['detail_id']))
	{
		$id =$_POST['detail_id'];
		$result=$eventlogbol->select_description_by_id($id);
		$row=$result->getNext();
		$description=$row['description'];
		$description=str_replace(",","<br>",$description);
		//$description=substr($description, 0, 100) ;
		
		$return_str = '<div class="modal-body">
					<form id="frm_description" name="frm_description" class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="">
						'.$description.'
						<div id="divprogress" class="text-center"></div>
					</form>
				</div>';

		$json_return_arr['popupdata'] = $return_str;
		echo json_encode($json_return_arr);
	}
?>