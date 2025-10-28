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
 $q_delete="DELETE FROM uom
            WHERE uom_id IN ($delete) AND uom_id NOT IN (SELECT uom_id_1 FROM master_item) AND uom_id NOT IN (SELECT uom_id_2 FROM master_item)";
 $q_check="SELECT uom_code
           FROM uom
           WHERE uom_id IN ($delete) AND (uom_id IN (SELECT uom_id_1 FROM master_item) OR uom_id IN (SELECT uom_id_2 FROM master_item))";
 $exec_delete=mysqli_query($db_connection, $q_delete);
 $exec_check=mysqli_query($db_connection, $q_check);
 if ($exec_delete)
    {
	  mysqli_commit($db_connection);
	  if (mysqli_num_rows($exec_check)>0)
	     {
		   
		   $data='';
		   while ($field=mysqli_fetch_array($exec_check))
		         {
				   if ($data=='')
				       $data=$field['uom_code'];
				   else
				       $data=$data.', '.$field['uom_code'];
				 }
		   ?>
		      <script language="javascript"> 
			     var data='<?php echo $data;?>';
				 alert('Satuan Item berikut ini tidak dapat dihapus karena sudah digunakan pada transaksi Pengeluaran Tabung :\n'+data);
			  </script>
		   <?php
		 } 
	  ?>
	     <script language="javascript">
		   window.location='../../index/index.php?page=uom';
		 </script>
	  <?php  
	}
 else
    {
	  mysqli_rollback($db_connection);
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       window.location='../../index/index.php?page=vendor';
		 </script>
	  <?php
	} 	 			
?>