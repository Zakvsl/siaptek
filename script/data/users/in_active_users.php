<?php
 include "../../library/check_session.php";
 include "../../library/db_connection.php";
 $data=$_POST['check_data'];
 $users_id='';
 foreach ($data as $field_data)
         {
		   if ($users_id=='')
		       $users_id="'$field_data'";
		   else
		       $users_id=$users_id.", '$field_data'";
		 }
 $q_check_user="SELECT * FROM users WHERE users_id IN ($users_id) AND users_level='0'";		 
 $exec_check_user=mysqli_query($db_connection, $q_check_user);
 if (mysqli_num_rows($exec_check_user)>0)
    {
	  while ($field=mysqli_fetch_array($exec_check_user))
	        {
			  if ($user_name=='')
	              $user_name=$field['user_name'];
			  else
			      $user_name=$user_name.", ".$field['user_name'];
			}  
	  ?>
	     <script language="javascript">
		   var x='<?php echo $user_name;?>';
		   alert('User dengan level Super Administrator tidak dapat diubah statusnya menjadi InActive!');
		 </script>
	  <?php 
	  echo "<meta http-equiv='refresh' content='0; url=../../index/index.php?page=users'>";
	}
 else
    {
      mysqli_autocommit($db_connection, false);
      $q_update_users="UPDATE users SET users_status=IF(users_status='0','1','0') WHERE users_id IN($users_id)";
      $exec_update_users=mysqli_query($db_connection, $q_update_users); 
      if ($exec_update_users)
         {
	       mysqli_commit($db_connection);
	       echo "<meta http-equiv='refresh' content='0; url=../../index/index.php?page=users'>";
   	     }
      else
         {
	       mysqli_rollback($db_connection);
           ?>
             <script language="javascript">
	           alert('Terjadi Kesalahan!\nSilahkan hubungi programer anda!');
		       opener.location.reload(true);
	         </script>
           <?php
		 }  
	}
?>