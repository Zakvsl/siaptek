<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  $id=mysqli_real_escape_string($db_connection, $_GET['id']);
  
  mysqli_autocommit($db_connection, false);
  $q_delete="DELETE FROM users_branch WHERE users_id='".$id."'";
  $q_delete_authorization="DELETE FROM users_authorization WHERE users_id='$id'";			
  $exec_delete=mysqli_query($db_connection, $q_delete);
  $exec_delete_authorization=mysqli_query($db_connection, $q_delete_authorization);
  if ($exec_delete && $exec_delete_authorization)
	 {
	   mysqli_commit($db_connection);
	   ?>
		  <script language="javascript">
			 opener.location.reload(true);
			 window.close();
		  </script>
	    <?php 
	 }
  else
	 {
	   mysqli_rollback($db_connection);
	   ?>
		  <script language="javascript">
		    alert('Terjadi kesalahan\nSilahkan hubungi programer anda!');
		    window.location.href='javascript:history.back(1)';
	      </script>
		<?php 	
	 } 
?>