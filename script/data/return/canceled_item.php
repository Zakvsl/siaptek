<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);
 
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 
 compare_branch($branch_id, $branch_id_transaction);
 
 $current_date=date('Y-m-d');
 $q_get_return_detail="SELECT return_header.reth_id, return_header.issuingh_id, issuingh_type, issuing_detail.issuingd_id, reth_code, 
                              MONTH(reth_date) AS month, YEAR(reth_date) AS year, reth_is_canceled, 
                              retd_is_canceled, itemd_id, reth_date, return_header.created_time
                       FROM return_header 
					   INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                       INNER JOIN return_detail ON return_detail.reth_id=return_header.reth_id
					   INNER JOIN issuing_detail ON issuing_detail.issuingd_id=return_detail.issuingd_id
                       WHERE return_header.branch_id='$branch_id' AND return_detail.retd_id='$id'";
// echo $q_get_return_detail;
 $exec_get_return_detail=mysqli_query($db_connection, $q_get_return_detail);
 $total_get_return_detail=mysqli_num_rows($exec_get_return_detail);
 if ($total_get_return_detail>0)
    {
	  $field_get_return_detail=mysqli_fetch_array($exec_get_return_detail);
      $active_period=check_active_period($db_connection, $field_get_return_detail['month'], $field_get_return_detail['year']);
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
	  if ($field_get_return_detail['reth_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status Transaksi Pengembalian sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	  if ($field_get_return_detail['retd_is_canceled']=='1')
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
		   $reth_id=$field_get_return_detail['reth_id'];
		   $itemd_id=$field_get_return_detail['itemd_id'];
		   $issuingh_id=$field_get_return_detail['issuingh_id'];
		   $issuingd_id=$field_get_return_detail['issuingd_id'];
		   $issuingh_type=$field_get_return_detail['issuingh_type'];
		   $reth_date=$field_get_return_detail['reth_date'];
		   $reth_created_time=$field_get_return_detail['created_time'];
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
				 	 alert('Proses pembatalan tidak dapat dilakukan!\nSilahkan cek status aset!');
					 window.location.href='javascript:history.back(1)';
				   </script>
			    <?php  
			  }
		   else
		   if ($field_itemd_position['itemd_status']=='1')
		      {
			    ?>
                   <script language="javascript">
				 	 alert('Proses pembatalan tidak dapat dilakukan!\nSilahkaan cek status aset!');
					 window.location.href='javascript:history.back(1)';
				   </script>
			    <?php  
			  }
		   else
		      {
			    $action="EDIT";
				$reth_date_old=$reth_date;
				$reth_date_new=$reth_date;
				$reth_created_time=$reth_created_time;
				$messages=is_there_any_new_trans($db_connection, $action, $reth_id, $itemd_id, $reth_date_old, $reth_date_new, $reth_created_time);
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
			         mysqli_autocommit($db_connection, false);
			         $q_update_item="UPDATE return_detail SET retd_is_canceled='1' WHERE retd_id='$id'";
				     if ($issuingh_type=='0') 
				         $q_update_item_detail="UPDATE item_detail, return_detail SET itemd_position='Customer', item_detail.whsl_id=whsl_id_first 
									            WHERE itemd_id='$itemd_id' AND retd_id='$id'";
						          
			         else
				         $q_update_item_detail="UPDATE item_detail, return_detail SET itemd_position='Vendor', item_detail.whsl_id=whsl_id_first 
									            WHERE itemd_id='$itemd_id' AND retd_id='$id'";
												
					 $q_update_issuing_detail="UPDATE issuing_detail SET issuingd_is_return='0' WHERE issuingd_id='$issuingd_id'";
					 $q_update_issuing_header="UPDATE issuing_header 
                                               SET issuingh_status=IF(
                                                                      (SELECT COUNT(*) FROM issuing_detail WHERE issuingh_id='$issuingh_id' AND issuingd_is_return='0' AND 
																	                        issuingd_is_canceled='0')=0,'2',
                                                                       IF(
                                                                          (SELECT COUNT(*) FROM issuing_detail WHERE issuingh_id='$issuingh_id' AND issuingd_is_return='1' AND 
																		                        issuingd_is_canceled='0')=0,'0','1'
                                                                         )
                                                                      )
                                               WHERE issuingh_id='$issuingh_id'";
				     $q_get_item_status="SELECT * FROM return_detail WHERE reth_id='$reth_id' AND retd_is_canceled='0'";
				     $exec_update_item=mysqli_query($db_connection, $q_update_item);
				     $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
					 $exec_update_issuing_detail=mysqli_query($db_connection, $q_update_issuing_detail);
					 $exec_update_issuing_header=mysqli_query($db_connection, $q_update_issuing_header);
				     $exec_get_item_status=mysqli_query($db_connection, $q_get_item_status);
			     	 if (mysqli_num_rows($exec_get_item_status)==0)
				        { 
					      $q_update_status_header="UPDATE return_header SET reth_is_canceled='1', reth_canceled_date='$current_date', 
					                                      reth_canceled_reason='Semua Aset dibatalkan' 
					                               WHERE reth_id='$reth_id'";
						  $exec_update_status_header=mysqli_query($db_connection, $q_update_status_header);
					      if ($exec_update_item && $exec_update_item_detail && $exec_update_issuing_detail && $exec_update_issuing_header && $exec_update_status_header)
				             {
				               mysqli_commit($db_connection);
					          ?>
                                  <script language="javascript">
						              var x='<?php echo $reth_id;?>';
						              window.location="../../data/return/cru_return.php?c=u&id="+x;
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
				          if ($exec_update_item)
				            {
				               mysqli_commit($db_connection);
					          ?>
                                  <script language="javascript">
						              var x='<?php echo $reth_id;?>';
						              window.location="../../data/return/cru_return.php?c=u&id="+x;
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
		   alert('Transaksi tidak ditemukan!');
		   window.location.href='javascript:history.back(1)';
		 </script>
	  <?php
	} 						
?>