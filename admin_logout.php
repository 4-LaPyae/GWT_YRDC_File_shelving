<?php
	$movepath= '';
	include ($movepath . "library/reference.php");
	session_start();
	session_destroy();
	session_write_close();
	unsetcookie("url");
?>
<script src="<?php echo $movepath; ?>js/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		localStorage.clear();
		window.location="admin_login.php";
	});
</script>