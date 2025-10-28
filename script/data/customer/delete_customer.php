<?php
 include "../../library/db_connection.php";
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $data=$_POST['check_data'];
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
 $q_delete="DELETE FROM customer 
            WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id IN ($delete) AND cust_id NOT IN (SELECT cust_id FROM issuing_header WHERE issuingh_type='1' AND 
			      branch_id='$branch_id' )";
 $q_check="SELECT cust_code
           FROM customer
           WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id IN ($delete) AND cust_id IN (SELECT cust_id FROM issuing_header WHERE issuingh_type='1' AND 
		         branch_id='$branch_id')";
 $exec_delete=mysqli_query($db_connection,$q_delete);
 $exec_check=mysqli_query($db_connection,$q_check);
 if ($exec_delete)
    {
	  mysqli_commit($db_connection);
	  if (mysqli_num_rows($exec_check)>0)
	     {
		   
		   $data='';
		   while ($field=mysqli_fetch_array($exec_check))
		         {
				   if ($data=='')
				       $data=$field['cust_code'];
				   else
				       $data=$data.', '.$field['cust_code'];
				 }
		   ?>
		      <script language="javascript"> 
			     var data='<?php echo $data;?>';
				 alert('Customer berikut ini tidak dapat dihapus karena sudah digunakan pada transaksi Pengeluaran Aset :\n'+data);
			  </script>
		   <?php
		 } 
	  ?>
	     <script language="javascript">
		   window.location='../../index/index.php?page=customer';
		 </script>
	  <?php  
	}
 else
    {
	  mysqli_rollback($db_connection);
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       window.location='../../index/index.php?page=customer';
		 </script>
	  <?php
	} 	 			
?>