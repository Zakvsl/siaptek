<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);
 $current_date=date('Y-m-d');
 $q_get_change_item_description_detail="SELECT change_item_description_header.cidh_id, cidh_code, cidh_is_canceled, cidd_is_canceled, itemd_id, cidh_date
                                        FROM change_item_description_header 
                                        INNER JOIN change_item_description_detail ON change_item_description_detail.cidh_id=change_item_description_header.cidh_id
                                        WHERE branch_id='$branch_id' AND cidd_id='$id'";
 $exec_get_change_item_description_detail=mysqli_query($db_connection, $q_get_change_item_description_detail);
 $total_get_change_item_description_detail=mysqli_num_rows($exec_get_change_item_description_detail);
 if ($total_get_change_item_description_detail>0)
    {
	  $field_get_change_item_description_detail=mysqli_fetch_array($exec_get_change_item_description_detail);
	  $cidh_date=$field_get_change_item_description_detail['cidh_date'];
	  $itemd_id=$field_get_change_item_description_detail['itemd_id'];
	  if ($field_get_change_item_description_detail['cidh_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status Transaksi sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	  if ($field_get_change_item_description_detail['cidd_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status item sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	     {
		   $cidh_id=$field_get_change_item_description_detail['cidh_id'];
		   $itemd_id=$field_get_change_item_description_detail['itemd_id'];
		   
		   $q_check_issuing="SELECT *
		                     FROM issuing_detail 
							 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
							 WHERE itemd_id='$itemd_id' AND issuingd_is_canceled='0' AND issuingh_is_canceled='0' AND issuingh_date>='$cidh_date'";
		   $q_check_broken="SELECT * 
		                    FROM broken_detail 
							INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
						    WHERE itemd_id='$itemd_id' AND brokd_is_canceled='0' AND brokh_is_canceled='0' AND brokh_date>='$cidh_date'";
		   $q_check_dispossal="SELECT *
		                       FROM dispossal_detail 
							   INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
						       WHERE itemd_id='$itemd_id' AND dispd_is_canceled='0' AND disph_is_canceled='0' AND disph_date>='$cidh_date'";
		   $q_check_transfer="SELECT *
		                      FROM transfer_detail 
							  INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
						      WHERE itemd_id='$itemd_id' AND ttd_is_canceled='0' AND tth_is_canceled='0' AND tth_date>='$cidh_date'";
		   $q_check_change_description="SELECT *
		                                FROM change_item_description_detail 
							            INNER JOIN change_item_description_header ON change_item_description_header.cidh_id=change_item_description_detail.cidh_id
						                WHERE itemd_id='$itemd_id' AND cidd_is_canceled='0' AND cidh_is_canceled='0' AND 
										      change_item_description_detail.cidh_id!='$cidh_id' AND cidh_date>='$cidh_date' AND cidd_id>$id";
		//   echo $q_check_change_description; 
		   $exec_check_issuing=mysqli_query($db_connection, $q_check_issuing);
		   $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
		   $exec_check_dispossal=mysqli_query($db_connection, $q_check_dispossal);
		   $exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
		   $exec_check_change_description=mysqli_query($db_connection, $q_check_change_description);
		   if (mysqli_num_rows($exec_check_issuing)>0 || mysqli_num_rows($exec_check_broken)>0 || mysqli_num_rows($exec_check_dispossal)>0 || 
		       mysqli_num_rows($exec_check_transfer)>0 || mysqli_num_rows($exec_check_change_description)>0)
			   {
			     if (mysqli_num_rows($exec_check_issuing)>0)
				     $issuing='Ya';
				 else
				     $issuing='Tidak';
				 if (mysqli_num_rows($exec_check_broken)>0)
				     $broken='Ya';
				 else
				     $broken='Tidak';
				 if (mysqli_num_rows($exec_check_dispossal)>0)
				     $dispossal='Ya';
				 else
				     $dispossal='Tidak';
		         if (mysqli_num_rows($exec_check_transfer)>0) 
				     $transfer='Ya';
				 else
				     $transfer='Tidak';
			     if (mysqli_num_rows($exec_check_change_description)>0)
				     $change='Ya';
				 else
				     $change='Tidak';
				 ?>
				    <script language="javascript">
					  var issuing='<?php echo $issuing;?>';
					  var broken='<?php echo $broken;?>';
					  var dispossal='<?php echo $dispossal;?>';
					  var transfer='<?php echo $transfer;?>';
					  var change='<?php echo $change;?>';
					  alert('Item tidak dapat dibatalkan. Sudah digunakan pada transaksi berikut ini : \n1. Pengeluaran Aset : '+issuing+'\n2. Kerusakan : '+broken+'\n3. Penjualan : '+dispossal+'\n4. Perpindahan Aset : '+transfer+'\n5. Perubahan Deskripsi : '+change);
					  history.back();
					</script>
				 <?php	 
			   }  
		   else
		      {
			    mysqli_autocommit($db_connection, 'false');
			    $q_update_item="UPDATE change_item_description_detail SET cidd_is_canceled='1' WHERE cidd_id='$id'";
				$q_update_item_detail="UPDATE item_detail, change_item_description_detail SET masti_id=masti_id_old
                                       WHERE item_detail.itemd_id=change_item_description_detail.itemd_id AND item_detail.itemd_id='$itemd_id' AND cidd_id='$id'";
				$exec_update_item=mysqli_query($db_connection, $q_update_item);
				$exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
				if ($exec_update_item && $exec_update_item_detail)
				   {
				     $q_check_change_description_header="SELECT COUNT(*) AS total
                                                         FROM change_item_description_detail 
                                                         WHERE cidd_is_canceled='0' AND cidh_id='$cidh_id'";
                     $exec_check_change_description_header=mysqli_query($db_connection, $q_check_change_description_header);
					 $field_change_change_description=mysqli_fetch_array($exec_check_change_description_header);
					 if ($field_change_change_description['total']=='0')
					    {
						  $q_update_header_status="UPDATE change_item_description_header SET cidh_is_canceled='1', cidh_canceled_date='$current_date', 
						                           cidh_canceled_reason='Semua Item Dibatalkan' WHERE cidh_id='$cidh_id'";
						  $exec_update_header_status=mysqli_query($db_connection, $q_update_header_status);
						  if ($exec_update_header_status)
						     {
							   mysqli_commit($db_connection);
					           ?>
                                  <script language="javascript">
						            var x='<?php echo $cidh_id;?>';
						            window.location="../../data/change_description/cru_change_description.php?c=u&id="+x;
					              </script>
				                <?php
							 }
						  else
						     {
							   mysqli_rollback($db_connection);
							   ?>
                                  <script language="javascript">
						            alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                            window.location.href='javascript:history.back(1)';
					              </script>
				                <?php
							 }	 
						}
					 else
					    {
				          mysqli_commit($db_connection);
					      ?>
                             <script language="javascript">
						       var x='<?php echo $cidh_id;?>';
						       window.location="../../data/change_description/cru_change_description.php?c=u&id="+x;
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
		                  window.location.href='javascript:history.back(1)';
		                </script>
	                 <?php
				   }
			  }
		 }
	}
 else
    {
	  ?>
	     <script language="javascript">
		   alert('Item tidak ditemukan!');
		   window.location.href='javascript:history.back(1)';
		 </script>
	  <?php
	} 						
?>