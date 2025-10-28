<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 compare_branch($branch_id, $branch_id_transaction);
 
      $current_date=date('Y-m-d');
      $q_get_transfer_detail="SELECT branch_id, transfer_header.tth_id, tth_code, MONTH(tth_date) AS month, YEAR(tth_date) AS year, 
	                                 tth_is_canceled, ttd_is_canceled, itemd_id, branch_id_to, tth_date, whsl_id_from, ttd_status
                              FROM transfer_header
                              INNER JOIN transfer_detail ON transfer_detail.tth_id=transfer_header.tth_id
                              WHERE branch_id='$branch_id' AND ttd_id='$id'";
      $exec_get_transfer_detail=mysqli_query($db_connection, $q_get_transfer_detail);
      $total_get_transfer_detail=mysqli_num_rows($exec_get_transfer_detail);
      if ($total_get_transfer_detail>0)
         {
	       $field_get_transfer_detail=mysqli_fetch_array($exec_get_transfer_detail);
		   $active_period=check_active_period($db_connection, $field_get_transfer_detail['month'], $field_get_transfer_detail['year']);
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
	       if ($field_get_transfer_detail['tth_is_canceled']=='1')
	          {
		        ?>
	               <script language="javascript">
		             alert('Status transaksi sudah dibatalkan!');
		             window.location.href='javascript:history.back(1)';
		           </script>
	            <?php
		      }
	       else
	       if ($field_get_transfer_detail['ttd_status']=='1')
	          {
		        ?>
	               <script language="javascript">
		             alert('Aset tidak dapat dibatalkan!\nAset sudah diteruskan ke Transaksi Penerimaan Transfer!');
		             window.location.href='javascript:history.back(1)';
		           </script>
	            <?php
		      }
	       else
	       if ($field_get_transfer_detail['ttd_is_canceled']=='1')
	          {
		        ?>
	               <script language="javascript">
		             alert('Status Aset sudah dibatalkan!');
		             window.location.href='javascript:history.back(1)';
		           </script>
	            <?php
		      }
	       else
	          {
		        mysqli_autocommit($db_connection, false);
		        $q_update_item="UPDATE transfer_detail SET ttd_is_canceled='1' WHERE ttd_id='$id'";
		        $q_update_item_detail="UPDATE item_detail SET itemd_position='Internal' WHERE itemd_id='".$field_get_transfer_detail['itemd_id']."'";
		        $q_get_item_status="SELECT * FROM transfer_detail WHERE tth_id='".$field_get_transfer_detail['tth_id']."' AND ttd_is_canceled='0'";
		        $exec_update_item=mysqli_query($db_connection, $q_update_item);
		        $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
		        $exec_get_item_status=mysqli_query($db_connection, $q_get_item_status);
		        if (mysqli_num_rows($exec_get_item_status)==0)
		           { 
			         $q_update_status_header="UPDATE transfer_header SET tth_is_canceled='1', tth_canceled_date='$current_date', 
					                                 tth_canceled_reason='Semua Aset dibatalkan' 
					                          WHERE tth_id='".$field_get_transfer_detail['tth_id']."'";
				     //echo $q_update_status_header;
			         $exec_update_status_header=mysqli_query($db_connection, $q_update_status_header);
			         if ($exec_update_item && $exec_update_item_detail && $exec_update_status_header)
				        {
				          mysqli_commit($db_connection);
					      ?>
                             <script language="javascript">
						        var x='<?php echo $field_get_transfer_detail['tth_id'];?>';
								var b='<?php echo $branch_id;?>';
						        window.location="../../data/transfer/cru_transfer.php?c=u&id="+x+"&b="+b;
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
			         if ($exec_update_item && $exec_update_item_detail)
				        {
				          mysqli_commit($db_connection);
					      ?>
                             <script language="javascript">
						        var x='<?php echo $field_get_transfer_detail['tth_id'];?>';
								var b='<?php echo $branch_id;?>';
						        window.location="../../data/transfer/cru_transfer.php?c=u&id="+x+"&b="+b;
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
  //	} 					
?>