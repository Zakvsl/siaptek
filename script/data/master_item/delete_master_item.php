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
 $q_delete="DELETE FROM master_item 
            WHERE masti_id IN ($delete) AND masti_id NOT IN (SELECT masti_id FROM item_detail)";
 $q_check="SELECT masti_code
           FROM master_item
           WHERE masti_id IN ($delete) AND masti_id IN (SELECT masti_id FROM item_detail)";
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
				       $data=$field['masti_code'];
				   else
				       $data=$data.', '.$field['masti_code'];
				 }
		   ?>
		      <script language="javascript"> 
			     var data='<?php echo $data;?>';
				 alert('Master Item berikut ini tidak dapat dihapus karena sudah digunakan pada Master Item :\n'+data);
			  </script>
		   <?php
		 } 
	  ?>
	     <script language="javascript">
		   window.location='../../index/index.php?page=master-item';
		 </script>
	  <?php  
	}
 else
    {
	  mysqli_rollback($db_connection);
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       window.location='../../index/index.php?page=master-item';
		 </script>
	  <?php
	} 	 			
?>