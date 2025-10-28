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
		 		 	 
 mysql_query('begin');
 $q_delete="DELETE FROM vendor 
            WHERE vend_id IN ($delete) AND vend_id NOT IN (SELECT cust_vend_id FROM issuing_header WHERE issuingh_type='1')";
 $q_check="SELECT vend_code
           FROM vendor
           WHERE vend_id IN ($delete) AND vend_id IN (SELECT cust_vend_id FROM issuingh_header WHERE issuingh_type='1')";
 $exec_delete=mysql_query($q_delete, $db_connection);
 $exec_check=mysql_query($q_check, $db_connection);
 if ($exec_delete)
    {
	  mysql_query('commit');
	  if (mysql_num_rows($exec_check)>0)
	     {
		   
		   $data='';
		   while ($field=mysql_fetch_array($exec_check))
		         {
				   if ($data=='')
				       $data=$field['vend_code'];
				   else
				       $data=$data.', '.$field['vend_code'];
				 }
		   ?>
		      <script language="javascript"> 
			     var data='<?php echo $data;?>';
				 alert('Vendor berikut ini tidak dapat dihapus karena sudah digunakan pada transaksi Pengeluaran Aset :\n'+data);
			  </script>
		   <?php
		 } 
	  ?>
	     <script language="javascript">
		   window.location='../../index/index.php?page=vendor';
		 </script>
	  <?php  
	}
 else
    {
	  mysql_query('rollback');
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       window.location='../../index/index.php?page=vendor';
		 </script>
	  <?php
	} 	 			
?>