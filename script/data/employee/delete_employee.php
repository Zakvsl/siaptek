<?php
 include "../../library/db_connection.php";
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
 $q_delete="DELETE FROM employee 
            WHERE emp_id IN ($delete) AND 
                  (emp_id NOT IN (SELECT emp_id_sender FROM transfer_header) OR
                   emp_id NOT IN (SELECT emp_id_sender FROM transfer_header) OR
                   emp_id NOT IN (SELECT emp_id FROM issuing_header) OR
                   emp_id NOT IN (SELECT emp_id_receiver FROM return_header) OR
                   emp_id NOT IN (SELECT emp_id_dispossed_by FROM dispossal_header))";
 $q_check="SELECT * FROM employee 
           WHERE emp_id IN ($delete) AND 
                  emp_id IN (SELECT emp_id_sender FROM transfer_header) OR
                  emp_id IN (SELECT emp_id_sender FROM transfer_header) OR
                  emp_id IN (SELECT emp_id FROM issuing_header) OR
                  emp_id IN (SELECT emp_id_receiver FROM return_header) OR
                  emp_id IN (SELECT emp_id_dispossed_by FROM dispossal_header)";
 echo $q_delete;
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
				       $data=$field['emp_code'];
				   else
				       $data=$data.', '.$field['emp_code'];
				 }
		   ?>
		      <script language="javascript"> 
			     var data='<?php echo $data;?>';
				 alert('Karyawan berikut ini tidak dapat dihapus karena sudah digunakan pada salah satu transaksi Pengeluaran/Pengembalian/Perpindahan/Penjualan :\n'+data);
			  </script>
		   <?php
		 } 
	  ?>
	     <script language="javascript">
		   window.location='../../index/index.php?page=employee';
		 </script>
	  <?php  
	}
 else
    {
	  mysqli_rollback($db_connection);
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       window.location='../../index/index.php?page=employee';
		 </script>
	  <?php
	} 	 			
?>