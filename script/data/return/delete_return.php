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
		 
 $q_get_return="SELECT return_header.*, MONTH(reth_date) AS month, YEAR(reth_date) AS year   
                FROM return_header WHERE reth_id IN ($delete) AND branch_id='$branch_id'
                ORDER BY reth_date, created_time DESC";	
 $exec_get_return=mysqli_query($db_connection, $q_get_return);
 if (mysqli_num_rows($exec_get_return)>0)
    {
	  $total_delete_rth=mysqli_num_rows($exec_get_return);
	  $total_execute=0;
	  $reth_not_delete="";
	  
	  while ($field_return=mysqli_fetch_array($exec_get_return))
	        {
			  $active_period=check_active_period($db_connection, $field_return['month'], $field_return['year']);
		      if ($active_period!='OK')
		         {
				   $is_continue=0;
		           ?>
			          <script language="javascript">
				          var x='<?php echo $active_period;?>';
						  var y='<?php echo $field_return['reth_code'];?>';
				          alert(x+'\n'+y);
						  alert('123456789');
				      </script>
			       <?php
				   break;
				   exit;		     
		         }				  
			}	  
	  
	  if ($is_continue==1)
	     { 
		   mysqli_data_seek($exec_get_return,0);
	       while ($field_return=mysqli_fetch_array($exec_get_return))
	             {
				   mysqli_autocommit($db_connection, false);
			       $issuingh_id=$field_return['issuingh_id'];
			       $reth_id=$field_return['reth_id'];
			       $reth_code=$field_return['reth_code'];
			       $reth_date=$field_return['reth_date'];
			       $reth_status=$field_return['reth_is_canceled'];
			       $reth_created_time=$field_return['created_time'];
			  
			       $is_delete='0';
			       $is_error='No';
			       if ($reth_status=='1') //if is canceled
			           $is_delete='1';
			       else
			          {
				        $tube_id="";
				        $issuingd_id="";
				        $q_get_tube_id="SELECT itemd_id, return_detail.issuingd_id
                                        FROM return_detail
								        INNER JOIN issuing_detail ON issuing_detail.issuingd_id=return_detail.issuingd_id
                                        WHERE retd_is_canceled='0' AND reth_id='$reth_id'"; 
				        $exec_get_tube_id=mysqli_query($db_connection, $q_get_tube_id);
				        while ($field_tube_id=mysqli_fetch_array($exec_get_tube_id))
				              {
						        if ($tube_id=="")
						            $tube_id="'".$field_tube_id['itemd_id']."'";
						        else
						            $tube_id=$tube_id.",'".$field_tube_id['itemd_id']."'";
							   
						        if ($issuingd_id=="")
						            $issuingd_id="'".$field_tube_id['issuingd_id']."'";
						        else
						            $issuingd_id=$issuingd_id.",'".$field_tube_id['issuingd_id']."'";
						      }		 
				        $action='DELETE';
				        $reth_date_old=$reth_date;
				        $reth_date_new=$reth_date;
				        $reth_created_time=$reth_created_time;
				        $messages=is_there_any_new_trans($db_connection, $action, $reth_id, $tube_id, $reth_date_old, $reth_date_new, $reth_created_time);		
				   
				        if ($messages=='1') // there is no new transaction
					        $is_delete='2'; 
				        else
				           {
					         $is_delete='0';
						     if ($reth_not_delete=="")
						         $reth_not_delete=$reth_code;
					         else
						         $reth_not_delete=$reth_not_delete.",".$reth_code;
					       }	 
			          }
			  
			       if ($is_delete!='0')
			          {
				        if ($is_delete=='2')
				           {
				             $q_update_issuing_detail="UPDATE issuing_detail SET issuingd_is_return='0' 
						                               WHERE issuingh_id='$issuingh_id' AND issuingd_is_canceled='0' AND issuingd_id IN ($issuingd_id)";
				             $q_update_issuing_header="UPDATE issuing_header SET issuingh_status=IF(
                                                                                 (SELECT COUNT(*) FROM issuing_detail WHERE issuingh_id='$issuingh_id' AND 
																		          issuingd_is_return='0' AND issuingd_is_canceled='0')=0,'2',
                                                                                  IF(
                                                                                     (SELECT COUNT(*) FROM issuing_detail WHERE issuingh_id='$issuingh_id' AND 
																			          issuingd_is_return='1' AND issuingd_is_canceled='0')=0,'0','1')
                                                                                    )
                                                     WHERE issuingh_id='$issuingh_id'";
				             $q_update_item="UPDATE item_detail, issuing_header, issuing_detail, return_header, return_detail 
							                        SET itemd_position=IF(issuingh_type='0','Customer','Vendor'), 
                                                    item_detail.whsl_id=whsl_id_first
		                                WHERE item_detail.itemd_id=issuing_detail.itemd_id AND issuing_header.issuingh_id=issuing_detail.issuingh_id AND 
							                  return_header.reth_id=return_detail.reth_id AND issuing_detail.issuingd_id=return_detail.issuingd_id AND retd_is_canceled='0' AND 
									          reth_is_canceled='0' AND return_header.reth_id IN ('$reth_id')";
						
						     $exec_update_issuing_detail=mysqli_query($db_connection, $q_update_issuing_detail);
						     $exec_update_issuing_header=mysqli_query($db_connection, $q_update_issuing_header);
						     $exec_update_item=mysqli_query($db_connection, $q_update_item);
						     if (!$exec_update_issuing_detail && !$exec_update_issuing_header && !$exec_update_item)
						        {
						          mysqli_rollback($db_connection);
							      ?>
							         <script language="javascript">
								       alert('Terjadi Kesalahan!\nSilahkan hubungi Programmer Anda!');
								       window.location='../../index/index.php?page=return';	
								     </script>
							      <?php
							      break;
						        }
					       }	  
					  
				        $q_delete_header="DELETE FROM return_header WHERE reth_id IN ('$reth_id') AND branch_id='$branch_id'"; 
				        $q_delete_detail="DELETE FROM return_detail WHERE reth_id IN ('$reth_id')";
				        $exec_delete_header=mysqli_query($db_connection, $q_delete_header);
				        $exec_delete_detail=mysqli_query($db_connection, $q_delete_detail);
				        if (!$exec_delete_header && !$exec_delete_detail)
				           {
					         mysqli_rollback($db_connection);
					         ?>
						        <script language="javascript">
						          alert('Terjadi Kesalahan!\nSilahkan hubungi Programmer Anda!');
						          window.location='../../index/index.php?page=return';	
						        </script>
					         <?php
						     break;
					       } 
				        else
				           mysqli_commit($db_connection);
			          } 	
			       $total_execute++;
                 }
		  
	       if ($reth_not_delete!='')
	          {
		        ?>
			       <script language="javascript">
			         var x='<?php echo $reth_not_delete;?>';
			         alert('Transaksi berikut ini tidak dapat dihapus : '+x+'\nTanggal transaksi terakhir lebih besar!');
			       </script>
		        <?php  
		      }
	       ?>
	          <script language="javascript">
		          window.location='../../index/index.php?page=return';
		      </script>
	       <?php
		 }
	  else
	     {
	       ?>
	          <script language="javascript">
		          window.location='../../index/index.php?page=return';
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