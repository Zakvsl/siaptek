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
 $q_delete="DELETE item_detail FROM item_detail, transfer_detail, issuing_detail, broken_detail, write_off_detail, dispossal_detail 
            WHERE item_detail.itemd_id IN ($delete) AND 
			      item_detail.itemd_id!=transfer_detail.itemd_id AND
				  item_detail.itemd_id!=issuing_detail.itemd_id AND
				  item_detail.itemd_id!=broken_detail.itemd_id AND
				  item_detail.itemd_id!=write_off_detail.itemd_id AND
				  item_detail.itemd_id!=dispossal_detail.itemd_id";
 $q_check="SELECT distinct(itemd_code)
           FROM item_detail
		   LEFT JOIN transfer_detail ON item_detail.itemd_id=transfer_detail.itemd_id
		   LEFT JOIN issuing_detail ON item_detail.itemd_id=issuing_detail.itemd_id
		   LEFT JOIN broken_detail ON item_detail.itemd_id=broken_detail.itemd_id
		   LEFT JOIN write_off_detail ON item_detail.itemd_id=write_off_detail.itemd_id
		   LEFT JOIN dispossal_detail ON item_detail.itemd_id=dispossal_detail.itemd_id
           WHERE item_detail.itemd_id IN ($delete)";
 
 //echo $q_delete."<br>";
 //echo $q_check;
 $exec_delete=mysqli_query($db_connection, $q_delete);
 $exec_check=mysqli_query($db_connection, $q_check);
 if ($exec_delete)
    {
	  mysqli_commit($db_connection);
	  if (mysqli_num_rows($exec_check)>0)
	     {
		   $tube_id='';
		   while ($field_data=mysqli_fetch_array($exec_check))
		         {
				   if ($tube_id=='')
				      { 
				        $tube_id=$field_data['itemd_code'];
					  }
				   else
				      {
				        $tube_id=$tube_id.', '.$field_data['itemd_code'];
					  } 
				 }
		   ?>
		      <script language="javascript"> 
			     var tube_id='<?php echo $tube_id;?>';
				 alert('Item berikut ini tidak dapat dihapus karena sudah digunakan pada Transaksi\nTransfer/Pengeluaran/Pengembalian/Kerusakan/Penghapusan/Penjualan/Perubahan Deskripsi :\n'+tube_id);
				 window.location='../../index/index.php?page=item-detail';
			  </script>
		   <?php
		 }
	  else
	     {	  
	       ?>
	         <script language="javascript">
		       window.location='../../index/index.php?page=item-detail';
		     </script>
	       <?php  
		 }
	}
 else
    {
	  mysqli_rollback($db_connection);
	  ?>
	     <script language="javascript">
		   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
	       window.location='../../index/index.php?page=item-detail';
		 </script>
	  <?php
	} 	 			
?>