<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 
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
 $delete_cidh_success='';
 $delete_cidh_unsuccess='';
 $q_get_cid="SELECT cidh_id, cidh_code, cidh_date, created_time 
             FROM change_item_description_header 
	 		 WHERE cidh_id IN ($delete) ORDER BY cidh_id DESC";
 $exec_get_cid=mysqli_query($db_connection, $q_get_cid);
 while ($field_cid=mysqli_fetch_array($exec_get_cid))	
       {
	     $cidh_id=$field_cid['cidh_id'];
		 $cidh_code=$field_cid['cidh_code'];
		 $cidh_date=$field_cid['cidh_date'];
		 $created_time=$field_cid['created_time'];
		 $q_check_new_cid_internal="SELECT cidd_id
                                    FROM change_item_description_detail cidd
                                    WHERE cidh_id='$cidh_id' AND cidd_id>(SELECT cidd_id
                                                                          FROM change_item_description_detail 
                                                                          INNER JOIN change_item_description_header ON 
														                             change_item_description_header.cidh_id=change_item_description_detail.cidh_id
                                                                          WHERE branch_id='$branch_id' AND cidd_id=cidd.cidd_id)";
		 //echo $q_check_new_cid_internal."<br>";
		 $q_check_new_cid_external="SELECT cidd_id
                                    FROM change_item_description_detail cidd 
                                    WHERE cidh_id='$cidh_id' AND cidd_id>(SELECT cidd_id
                                                                          FROM change_item_description_detail 
                                                                          INNER JOIN change_item_description_header ON 
														                             change_item_description_header.cidh_id=change_item_description_detail.cidh_id
                                                                          WHERE branch_id!='$branch_id' AND cidd_id=cidd.cidd_id)";
		 
		 //echo $q_check_new_cid_external."<br>";
		 $q_check_issuing="SELECT *
                           FROM issuing_detail 
                           INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                           INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
                           INNER JOIN change_item_description_detail ON change_item_description_detail.itemd_id=item_detail.itemd_id 
                           WHERE issuingd_is_canceled='0' AND issuingh_is_canceled='0' AND issuingh_date>='$cidh_date' AND created_time>='$created_time' AND cidh_id='$cidh_id'";
		 //echo $q_check_issuing."<br>";
		 $q_check_broken="SELECT * 
		                  FROM broken_detail 
					      INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
						  INNER JOIN item_detail ON item_detail.itemd_id=broken_detail.itemd_id
                          INNER JOIN change_item_description_detail ON change_item_description_detail.itemd_id=item_detail.itemd_id 
						  WHERE brokd_is_canceled='0' AND brokh_is_canceled='0' AND brokh_date>='$cidh_date' AND created_time>='$created_time' AND cidh_id='$cidh_id'";
		 //echo $q_check_broken."<br>";
		 $q_check_write_off="SELECT * 
		                     FROM write_off_detail 
					         INNER JOIN write_off_header ON write_off_header.woh_id=write_off_detail.woh_id
						     INNER JOIN item_detail ON item_detail.itemd_id=write_off_detail.itemd_id
                             INNER JOIN change_item_description_detail ON change_item_description_detail.itemd_id=item_detail.itemd_id 
						     WHERE wod_is_canceled='0' AND woh_is_canceled='0' AND woh_date>='$cidh_date' AND created_time>='$created_time' AND cidh_id='$cidh_id'";
		 //echo $q_check_write_off."<br>";
		 $q_check_dispossal="SELECT *
		                     FROM dispossal_detail 
							 INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
							 INNER JOIN item_detail ON item_detail.itemd_id=dispossal_detail.itemd_id
                             INNER JOIN change_item_description_detail ON change_item_description_detail.itemd_id=item_detail.itemd_id
						     WHERE dispd_is_canceled='0' AND disph_is_canceled='0' AND disph_date>='$cidh_date' AND created_time>='$created_time' AND cidh_id='$cidh_id'";
		 //echo $q_check_dispossal."<br>";
		 $q_check_transfer="SELECT *
		                    FROM transfer_detail 
							INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
							INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id
                            INNER JOIN change_item_description_detail ON change_item_description_detail.itemd_id=item_detail.itemd_id
						    WHERE ttd_is_canceled='0' AND tth_is_canceled='0' AND tth_date>='$cidh_date' AND created_time>='$created_time' AND cidh_id='$cidh_id'";
		 //echo $q_check_transfer."<br>";
		 $q_check_change_description="SELECT *
		                              FROM change_item_description_detail 
							          INNER JOIN change_item_description_header ON change_item_description_header.cidh_id=change_item_description_detail.cidh_id
						              WHERE cidd_is_canceled='0' AND cidh_is_canceled='0' AND change_item_description_detail.cidh_id!='$cidh_id' AND cidh_date>='$cidh_date' AND 
									        created_time>='$created_time'";
		 //echo $q_check_change_description."<br>"; 
		 $exec_check_new_cid_internal=mysqli_query($db_connection, $q_check_new_cid_internal);
		 $exec_check_new_cid_external=mysqli_query($db_connection, $q_check_new_cid_external);
		 $exec_check_issuing=mysqli_query($db_connection, $q_check_issuing);
		 $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
		 $exec_check_write_off=mysqli_query($db_connection, $q_check_write_off);
		 $exec_check_dispossal=mysqli_query($db_connection, $q_check_dispossal);
		 $exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
		 $exec_check_change_description=mysqli_query($db_connection, $q_check_change_description);
		 if (mysqli_num_rows($exec_check_new_cid_internal)==0 && mysqli_num_rows($exec_check_new_cid_external)==0 && 
		     mysqli_num_rows($exec_check_issuing)==0 && mysqli_num_rows($exec_check_broken)==0 && mysqli_num_rows($exec_check_write_off)==0  && 
			 mysqli_num_rows($exec_check_dispossal)==0 && mysqli_num_rows($exec_check_transfer)==0  && mysqli_num_rows($exec_check_change_description)==0)
		    {
			  mysqli_autocommit($db_connection, 'false');
			  $q_update_item_detail="UPDATE item_detail, change_item_description_detail SET masti_id=masti_id_old
                                     WHERE item_detail.itemd_id=change_item_description_detail.itemd_id AND cidh_id='$cidh_id' AND cidd_is_canceled='0'";
			  $q_delete_cidd="DELETE FROM change_item_description_detail WHERE cidh_id='$cidh_id'";
			  $q_delete_cidh="DELETE FROM change_item_description_header WHERE cidh_id='$cidh_id'"; 
			  $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
			  $exec_delete_cidd=mysqli_query($db_connection, $q_delete_cidd);
			  $exec_delete_cidh=mysqli_query($db_connection, $q_delete_cidh);
			  if ($exec_update_item_detail && $exec_delete_cidd && $exec_delete_cidh)
			     {
				   mysqli_commit($db_connection);
				   if ($delete_cidh_success=='')
				       $delete_cidh_success=$cidh_code;
				   else
				       $delete_cidh_success=$delete_cidh_success.", ".$cidh_code;
				   
				 }
			  else
			     {
				   mysqli_rollback($db_connection);
				   if ($delete_cidh_success!='')
				      {
					    mysqli_rollback($db_connection);
	                    ?>
	                       <script language="javascript">
						     var cidh_code='<?php echo $delete_cidh_success;?>';
		                     alert('Terjadi kesalahan! Silahkan hubungi programmer anda!\nBerikut adalah nomor transaksi yang sudah terhapus : '+cidh_code);
                             window.location='../../index/index.php?page=change-description';
		                   </script>
	                    <?php
					  }
				   else
				      {
					    mysqli_rollback($db_connection);
	                    ?>
	                       <script language="javascript">
						     var cidh_code='<?php echo $delete_cidh_success;?>';
		                     alert('Terjadi kesalahan! Silahkan hubungi programmer anda!');
                             window.location='../../index/index.php?page=change-description';
		                   </script>
	                    <?php 
					  }
				 }
			}
		 else
		    {
			  if ($delete_cidh_unsuccess=='')
			      $delete_cidh_unsuccess=$cidh_code;
			  else
			      $delete_cidh_unsuccess=$delete_cidh_unsuccess.", ".$cidh_code;
			}
	   }
 ?>
	 <script language="javascript">
	    var cidh_undelete='<?php echo $delete_cidh_unsuccess;?>';
		if (cidh_undelete!='')
		    alert('Kode transaksi berikut ini tidak dapat dihapus : '+cidh_undelete+'\nSilahkan dicek pada transaksi berikut ini : Pengeluaran/Kerusakan/Penghapusan/Penjualan/Perpindahan/Perubahan Deskripsi Aset!')
		window.location='../../index/index.php?page=change-description';
     </script>
 <?php	