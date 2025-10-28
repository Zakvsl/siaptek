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
		 		 	 
 mysqli_autocommit($db_connection, 'false');
 $q_delete="DELETE FROM category_item 
            WHERE cati_id IN ($delete) AND cati_id NOT IN (SELECT cati_id FROM master_item)";
 $q_check="SELECT cati_code
           FROM category_item
           WHERE cati_id IN ($delete) AND cati_id IN (SELECT cati_id FROM master_item)";
 //echo $q_delete."<br>";
 //echo $q_check;
 $exec_delete=mysqli_query($db_connection, $q_delete);
 $exec_check=mysqli_query($db_connection, $q_check);
 if ($exec_delete)
    {
	//  mysqli_rollback($db_connection);
	  mysqli_commit($db_connection);
	  $data='';
	  if (mysqli_num_rows($exec_check)>0)
	     {
		   while ($field=mysqli_fetch_array($exec_check))
		         {
				   if ($data=='')
				       $data=$field['cati_code'];
				   else
				       $data=$data.', '.$field['cati_code'];
				 }
		 } 
	  ?>
	     <script language="javascript">
		   var data='<?php echo $data;?>';
		   if (data!='')
		       alert('Kategori Aset berikut ini tidak dapat dihapus karena sudah digunakan pada Master Item :\n'+data);
		   window.location='../../index/index.php?page=category-item';
		 </script>
	  <?php  
	}
 else
    {
	  mysqli_rollback($db_connection);
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       window.location='../../index/index.php?page=category-item';
		 </script>
	  <?php
	} 	 			
?>