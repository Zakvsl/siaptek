<?php
 include "../../library/db_connection.php";
 $data=$_POST['check_data'];
 $cid=mysqli_real_escape_string($db_connection, $_GET['cid']);
 $x=count($data);
 $delete='';
 foreach ($data as $delete_id)
 		 { 
		   if ($delete=='')
		       $delete="'".$delete_id."'"; 
		   else
		       $delete=$delete.", '".$delete_id."'";
		 }
		 		 	 
 mysqli_autocommit($db_connection, false);
 $q_delete="DELETE FROM customer_pic
            WHERE custp_id IN ($delete) AND cust_id='$cid'";
 $exec_delete=mysqli_query($db_connection,$q_delete);
 if ($exec_delete)
    {
	  mysqli_commit($db_connection);
	  ?>
	     <script language="javascript">
		   var id='<?php echo $cid;?>';
		   window.location="../../data/customer/pic_customer.php?id="+id;
		 </script>
	  <?php  
	}
 else
    {
	  mysqli_rollback($db_connection);
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       history.back();
		 </script>
	  <?php
	} 	 			
?>