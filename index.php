<?php
	$movepath = '';
	$currentPg = '';
	require_once('autoload.php');
	require_once($movepath . 'library/reference.php');
	
	require_once('adminauth.php');
	require_once('admin_header.php');
	
	if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0 )
	{
		$errors = $_SESSION['ERRMSG_ARR'];
		echo "<div class='align-center alert-success'>";
		echo implode('<br>',$errors);
		echo "</div>";
		unset($_SESSION['ERRMSG_ARR']);
	}
	session_write_close();
?>

<h2 class="text-center my-5">
	<span class="text-serif">Welcome to <br class="hidden-sm-up">File Shelving System Dashboard</span>
</h2>
<?php
	include 'admin_footer.php';
?>