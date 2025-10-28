<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 
 compare_branch($branch_id, $branch_id_transaction);
 
 $current_date=date('Y-m-d');
 $q_get_receipt_transfer_detail="SELECT receipt_transfer_header.rth_id, receipt_transfer_header.tth_id, transfer_detail.ttd_id, rth_code, 
                                        MONTH(rth_date) AS month, YEAR(rth_date) AS year, rth_is_canceled, rtd_is_canceled, 
                                        receipt_transfer_detail.itemd_id, branch_id_from, whsl_id_old, rth_date, receipt_transfer_header.created_time
                                 FROM receipt_transfer_header 
					             INNER JOIN transfer_header ON transfer_header.tth_id=receipt_transfer_header.tth_id
                                 INNER JOIN receipt_transfer_detail ON receipt_transfer_detail.rth_id=receipt_transfer_header.rth_id
					             INNER JOIN transfer_detail ON transfer_detail.ttd_id=receipt_transfer_detail.ttd_id
                                 WHERE receipt_transfer_header.branch_id='$branch_id' AND receipt_transfer_detail.rtd_id='$id'";
 //echo $q_get_receipt_transfer_detail;
 $exec_get_receipt_transfer_detail=mysqli_query($db_connection, $q_get_receipt_transfer_detail);
 $total_get_receipt_transfer_detail=mysqli_num_rows($exec_get_receipt_transfer_detail);
 if ($total_get_receipt_transfer_detail>0)
    {
	  $field_get_receipt_transfer_detail=mysqli_fetch_array($exec_get_receipt_transfer_detail);
	  $active_period=check_active_period($db_connection, $field_get_receipt_transfer_detail['month'], $field_get_receipt_transfer_detail['year']);
      if ($active_period!='OK')
		 {
		   ?>
			  <script language="javascript">
				 var x='<?php echo $active_period;?>';
				 alert(x);
				 window.location.href='javascript:history.back(1)';
		      </script>
		   <?php		     
		 }
	  else	
	  if ($field_get_receipt_transfer_detail['rth_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status Transaksi Penerimaan Transfer sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	  if ($field_get_receipt_transfer_detail['rtd_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status aset sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	     {
		   $branch_id_from=$field_get_receipt_transfer_detail['branch_id_from'];
		   $whsl_id_old=$field_get_receipt_transfer_detail['whsl_id_old'];
		   $rth_id=$field_get_receipt_transfer_detail['rth_id'];
		   $rth_date=$field_get_receipt_transfer_detail['rth_date'];
		   $rth_created_time=$field_get_receipt_transfer_detail['created_time'];
		   $itemd_id=$field_get_receipt_transfer_detail['itemd_id'];
		   $tth_id=$field_get_receipt_transfer_detail['tth_id'];
		   $ttd_id=$field_get_receipt_transfer_detail['ttd_id'];
		   $q_check_itemd_position="SELECT itemd_position, itemd_status FROM item_detail WHERE itemd_id='$itemd_id' AND branch_id='$branch_id'";
		   $exec_check_itemd_position=mysqli_query($db_connection, $q_check_itemd_position);
		   $field_itemd_position=mysqli_fetch_array($exec_check_itemd_position);
		   if (mysqli_num_rows($exec_check_itemd_position)==0)
		      {
			    ?>
                   <script language="javascript">
				 	 alert('Proses pembatalan tidak dapat dilakukan!\nAset sudah tidak ditemukan!');
					 window.location.href='javascript:history.back(1)';
				   </script>
			    <?php   
			  }
		   else
		   if ($field_itemd_position['itemd_position']!='Internal')
		      {
			    ?>
                   <script language="javascript">
				 	 alert('Proses pembatalan tidak dapat dilakukan!\nPosisi Aset tidak diinternal!');
					 window.location.href='javascript:history.back(1)';
				   </script>
			    <?php  
			  }
		   else
		   if ($field_itemd_position['itemd_status']=='1')
		      {
			    ?>
                   <script language="javascript">
				 	 alert('Proses pembatalan tidak dapat dilakukan!\nSilahkaan cek status Aset!');
					 window.location.href='javascript:history.back(1)';
				   </script>
			    <?php  
			  }
		   else
		      {
			    $action="EDIT";
				$rth_date_old=$rth_date;
				$rth_date_new=$rth_date;
				$rth_created_time=$rth_created_time;
				$messages=is_there_any_new_trans($db_connection, $action, $rth_id, $itemd_id, $rth_date_old, $rth_date_new, $rth_created_time);
				if ($messages!='1')
				   {
					 mysqli_rollback($db_connection);
					 ?>
					    <script language="javascript">
					       var message='<?php echo $messages; ?>';
					       alert(message); 
					       history.back(1);
					    </script>
					 <?php							   
				   }
				else
				   { 
				     mysqli_autocommit($db_connection, 'false');
			         $q_update_item="UPDATE receipt_transfer_detail SET rtd_is_canceled='1' WHERE rtd_id='$id'";
				     $q_update_item_detail="UPDATE item_detail, receipt_transfer_detail 
					                               SET item_detail.whsl_id='$whsl_id_old', branch_id='$branch_id_from', itemd_position='In Transit' 
									        WHERE item_detail.itemd_id='$itemd_id'";
					 $q_update_transfer_detail="UPDATE transfer_detail SET ttd_status='0' WHERE ttd_id='$ttd_id'";
					 $q_update_transfer_header="UPDATE transfer_header 
                                                       SET tth_status=IF((SELECT COUNT(*) FROM transfer_detail WHERE tth_id='$tth_id' AND ttd_status='0' AND 
																	      ttd_is_canceled='0')=0,'2',
                                                                      IF((SELECT COUNT(*) FROM transfer_detail WHERE tth_id='$tth_id' AND ttd_status='1' AND 
																		  ttd_is_canceled='0')=0,'0','1'))
                                                WHERE tth_id='$tth_id'";
				     $q_get_item_status="SELECT * FROM receipt_transfer_detail WHERE rth_id='".$field_get_receipt_transfer_detail['rth_id']."' AND rtd_is_canceled='0'";
				     $exec_update_item=mysqli_query($db_connection, $q_update_item);
				     $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
					 $exec_update_transfer_detail=mysqli_query($db_connection, $q_update_transfer_detail);
					 $exec_update_transfer_header=mysqli_query($db_connection, $q_update_transfer_header);
				     $exec_get_item_status=mysqli_query($db_connection, $q_get_item_status);
			     	 if (mysqli_num_rows($exec_get_item_status)==0)
				        { 
					      $q_update_status_header="UPDATE receipt_transfer_header SET rth_is_canceled='1', rth_canceled_date='$current_date', 
					                                      rth_canceled_reason='Semua Item dibatalkan' 
					                               WHERE rth_id='$rth_id'";
						  $exec_update_status_header=mysqli_query($db_connection, $q_update_status_header);
					      if ($exec_update_item && $exec_update_item_detail && $exec_update_transfer_detail && $exec_update_transfer_header && $exec_update_status_header)
				             {
				               mysqli_commit($db_connection);
					           ?>
                                  <script language="javascript">
						              var x='<?php echo $rth_id;?>';
									  var b='<?php echo $branch_id;?>';
						              window.location="../../data/receipt_transfer/cru_receipt_transfer.php?c=u&id="+x+"&b="+b;
					              </script>
				               <?php
				             }
				          else
				             {
				               mysqli_rollback($db_connection);
				               ?>
	                             <script language="javascript">
		                            alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda 1!');
		                            window.location.href='javascript:history.back(1)';
		                         </script>
	                           <?php
				             }
				        }
				     else
				        {   	 
				          if ($exec_update_item && $exec_update_item_detail && $exec_update_transfer_detail && $exec_update_transfer_header)
				             {
				               mysqli_commit($db_connection);
					           ?>
                                  <script language="javascript">
						              var x='<?php echo $rth_id;?>';
						              var b='<?php echo $branch_id;?>';
						              window.location="../../data/receipt_transfer/cru_receipt_transfer.php?c=u&id="+x+"&b="+b;
					               </script>
				               <?php
				             }
				          else
				             {
				               mysqli_rollback($db_connection);
				               ?>
	                             <script language="javascript">
		                           alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda 2!');
		                           window.location.href='javascript:history.back(1)';
		                         </script>
	                           <?php
						     }
				        }
				   }   
		      }
		 }
	}
 else
    {
	  ?>
	     <script language="javascript">
		   alert('Aset tidak ditemukan!');
		   window.location.href='javascript:history.back(1)';
		 </script>
	  <?php
	} 						
?>