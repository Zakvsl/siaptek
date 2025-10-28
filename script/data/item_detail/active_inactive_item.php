<?php
 include "../../library/check_session.php"; 
 include "../../library/db_connection.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $data=$_POST['check_data'];
 $x=count($data);
 $update='';
 foreach ($data as $update_id)
 		 { 
		   if ($update=='')
		       $update="'".$update_id."'"; 
		   else
		       $update=$update.", '".$update_id."'";
		 }
		 		 	 
 mysqli_autocommit($db_connection, false);
 $q_inactive_item="UPDATE item_detail
 						  SET itemd_status=IF(itemd_status='0','1','0')
                   WHERE itemd_id IN ($update) AND branch_id='$branch_id' AND itemd_position='Internal' AND itemd_is_broken='0' AND itemd_is_wo='0' AND 
				   itemd_is_dispossed='0'";
 echo $q_inactive_item;				   
 $exec_inactive_item=mysqli_query($db_connection, $q_inactive_item);
 if ($exec_inactive_item)
    {
	  mysqli_commit($db_connection); 
	  ?>
	     <script language="javascript">
		   window.location='../../index/index.php?page=item-detail';
		 </script>
	  <?php  
	}
 else
    {
	  mysqli_rollback($db_connection);
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       //window.location='../../index/index.php?page=item-detail';
		 </script>
	  <?php
	} 	 			
?>