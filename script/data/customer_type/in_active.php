<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $maker=$_SESSION['ses_user_id'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);
 $q_check_status="SELECT * FROM customer_type WHERE custtp_id='$id'";
 $exec_check_status=mysqli_query($db_connection,$q_check_status);
 if (mysqli_num_rows($exec_check_status)==0)
    {
	  ?>
	     <script language="javascript">
		   alert('Tipe Customer yang akan di InActivekan tidak ditemukan!');
		   javascript.back();
		 </script>
	  <?php
	}
 else
    {
	  $field=mysqli_fetch_array($exec_check_status);
	  $status=$field['custtp_is_active'];
	  if ($status=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Tipe Customer sudah di InActivekan sebelumnya!');
		        window.location='../../index/index.php?page=customer-type';
		      </script>
	       <?php
		 }
	  else
	     {
		   mysqli_autocommit($db_connection, false);
		   $update_data="UPDATE customer_type SET custtp_is_active='1' WHERE custtp_id='$id'";
		   $exec_update_data=mysqli_query($db_connection,$update_data);
		   if (!$exec_update_data)
		      {
			    mysqli_rollback($db_connection);
				?>
	               <script language="javascript">
		             alert('Terjadi Kesalahan!\nSilahkan hubungi programmer anda!');
		             window.location='../../index/index.php?page=customer-type';
		           </script>
	            <?php
			  }
		   else
		      {
			    mysqli_commit($db_connection);
				?>
                   <script language="javascript">
					 window.location='../../index/index.php?page=customer-type';
				   </script>
				 <?php
			  }
		 }
	}
?>