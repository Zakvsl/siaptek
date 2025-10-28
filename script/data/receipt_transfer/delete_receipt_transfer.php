<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 
 compare_branch($branch_id, $branch_id_transaction);
 
 $data=$_POST['check_data'];
 $x=count($data);
 $delete='';
 $is_continue=1;
 foreach ($data as $delete_id)
 		 { 
		   if ($delete=='')
		       $delete="'".$delete_id."'"; 
		   else
		       $delete=$delete.", '".$delete_id."'";
		 }

 $q_get_receipt_transfer_header="SELECT receipt_transfer_header.*, MONTH(rth_date) AS month, YEAR(rth_date) AS year  
                                 FROM receipt_transfer_header 
                                 WHERE rth_id IN ($delete) and branch_id='$branch_id'
                                 ORDER BY rth_date, created_time DESC";	
 $exec_get_receipt_transfer_header=mysqli_query($db_connection, $q_get_receipt_transfer_header);
 
 if (mysqli_num_rows($exec_get_receipt_transfer_header)>0)
    {
	  $total_delete_rth=mysqli_num_rows($exec_get_receipt_transfer_header);
	  $total_execute=0;
	  $rth_not_delete=array();
	  
	  
	  while ($field_receipt_transfer_header=mysqli_fetch_array($exec_get_receipt_transfer_header))
	        {
			  $active_period=check_active_period($db_connection, $field_receipt_transfer_header['month'], $field_receipt_transfer_header['year']);
		      if ($active_period!='OK')
		         {
				   $is_continue=0;
		           ?>
			          <script language="javascript">
				          var x='<?php echo $active_period;?>';
						  var y='<?php echo $field_receipt_transfer_header['rth_code'];?>';
				          alert(x+'\n'+y);
				      </script>
			       <?php
				   break;
				   exit;		     
		         }				  
			}
			
	  if ($is_continue==1)
	     {
		   mysqli_data_seek($exec_get_receipt_transfer_header,0);
		   while ($field_receipt_transfer_header=mysqli_fetch_array($exec_get_receipt_transfer_header))
		         {
			       mysqli_autocommit($db_connection, false);
			       $branch_id_from=$field_receipt_transfer_header['branch_id_from'];
			       $tth_id=$field_receipt_transfer_header['tth_id'];
			       $rth_id=$field_receipt_transfer_header['rth_id'];
			       $rth_code=$field_receipt_transfer_header['rth_code'];
			       $rth_date=$field_receipt_transfer_header['rth_date'];
			       $rth_status=$field_receipt_transfer_header['rth_is_canceled'];
			       $rth_created_time=$field_receipt_transfer_header['created_time'];
			       $is_delete='0';
			       $is_error='No';
			       if ($rth_status=='1') //if is canceled
			           $is_delete='1';
			       else
			          {
				        $tube_id="";
				        $ttd_id="";
				        $q_get_tube_id="SELECT itemd_id, ttd_id
                                        FROM receipt_transfer_detail
                                        WHERE rtd_is_canceled='0' AND rth_id='$rth_id'"; 
				        $exec_get_tube_id=mysqli_query($db_connection, $q_get_tube_id);
				        while ($field_tube_id=mysqli_fetch_array($exec_get_tube_id))
				              {
						        if ($tube_id=="")
						            $tube_id="'".$field_tube_id['itemd_id']."'";
						        else
						            $tube_id=$tube_id.",'".$field_tube_id['itemd_id']."'";
							   
						        if ($ttd_id=="")
						            $ttd_id="'".$field_tube_id['ttd_id']."'";
						        else
						            $ttd_id=$ttd_id.",'".$field_tube_id['ttd_id']."'";
						      }		 
				   
				        $action='DELETE';
				        $rth_date_old=$rth_date;
				        $rth_date_new=$rth_date;
				        $rth_created_time=$rth_created_time;
				        $messages=is_there_any_new_trans($db_connection, $action, $rth_id, $tube_id, $rth_date_old, $rth_date_new, $rth_created_time);		
				   
				        if ($messages=='1') // there is no new transaction
					        $is_delete='2'; 
				        else
				           {
					         $is_delete='0';
						     $rth_not_delete[]=$rth_code;
					       }	 
				      }
				 
			       if ($is_delete!='0')
			          {
				        if ($is_delete=='2')
				           {
				             $q_update_transfer_detail="UPDATE transfer_detail SET ttd_status='0' 
                                                        WHERE ttd_id IN ($ttd_id) AND
									   			              tth_id='$tth_id'";
				             $q_update_transfer_header_status="UPDATE transfer_header 
                                                                      SET tth_status=(IF((SELECT COUNT(*) FROM transfer_detail WHERE ttd_status='0' AND 
															                          tth_id='$tth_id')=0,'2',
                                                                                      IF((SELECT COUNT(*) FROM transfer_detail WHERE ttd_status='0' AND 
																				           tth_id='$tth_id')!=(SELECT COUNT(*) FROM transfer_detail 
																						                       WHERE tth_id='$tth_id'),'1','0')))
                                                               WHERE tth_id='$tth_id'";
				             $q_update_item_detail="UPDATE item_detail, receipt_transfer_detail 
				                                           SET branch_id='$branch_id_from', item_detail.whsl_id=whsl_id_old, itemd_position='In Transit'
									                WHERE item_detail.itemd_id=receipt_transfer_detail.itemd_id  AND rth_id='$rth_id' AND rtd_is_canceled='0'"; 
						
						     $exec_update_transfer_detail=mysqli_query($db_connection, $q_update_transfer_detail);
						     $exec_update_transfer_header_status=mysqli_query($db_connection, $q_update_transfer_header_status);
						     $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
						     if (!$exec_update_transfer_detail && !$exec_update_transfer_header_status && !$exec_update_item_detail)
						        {
						          mysqli_rollback($db_connection);
							      ?>
							         <script language="javascript">
								       alert('Terjadi Kesalahan!\nSilahkan hubungi Programmer Anda!');
								       window.location='../../index/index.php?page=receipt-transfer';	
								     </script>
							      <?php
							      break;
						        }	
					       }	  
					  
				        $q_delete_receipt_transfer_header="DELETE FROM receipt_transfer_header WHERE rth_id='$rth_id' AND branch_id='$branch_id'";
				        $q_delete_receipt_transfer_detail="DELETE FROM receipt_transfer_detail WHERE rth_id='$rth_id'";
				        $exec_delete_receipt_transfer_header=mysqli_query($db_connection, $q_delete_receipt_transfer_header);
				        $exec_delete_receipt_transfer_detail=mysqli_query($db_connection, $q_delete_receipt_transfer_detail);
				        if (!$exec_delete_receipt_transfer_header && !$exec_delete_receipt_transfer_detail)
				           {
					         mysqli_rollback($db_connection);
					         ?>
						        <script language="javascript">
						          alert('Terjadi Kesalahan!\nSilahkan hubungi Programmer Anda!');
						          window.location='../../index/index.php?page=receipt-transfer';	
						        </script>
					         <?php
						     break;
					       } 
				        else
				            mysqli_commit($db_connection);
				      }
			       $total_execute++;	
		         }
	  
	       if (count($rth_not_delete)>0)
	          {
		        $rth_code_not_delete=''; 
		        for ($i=0; $i<count($rth_not_delete); $i++)
		            {
			          if ($rth_code_not_delete=='')
				          $rth_code_not_delete=$rth_not_delete[$i];
				      else
				          $rth_code_not_delete=$rth_code_not_delete.",".$rth_not_delete[$i];
			        }
		        ?>
			       <script language="javascript">
			         var x='<?php echo $rth_code_not_delete;?>';
			         alert('Transaksi berikut ini tidak dapat dihapus : '+x+'\nTanggal transaksi terakhir lebih besar!');
				     window.location='../../index/index.php?page=receipt-transfer';
			       </script>
		        <?php  
		      }
	       ?>
	          <script language="javascript">
		          window.location='../../index/index.php?page=receipt-transfer';
		      </script>
	       <?php
		 }
	  else
	     {
	       ?>
	          <script language="javascript">
		          window.location='../../index/index.php?page=receipt-transfer';
		      </script>
	       <?php		   
		 }
	}
 else
    {
	  ?>
	     <script language="javascript">
		   alert('Semua Transaksi yang akan dihapus tidak ditemukan!');
		   history.back();
		 </script>
	  <?php
	}

?>  		
		
		
		
		