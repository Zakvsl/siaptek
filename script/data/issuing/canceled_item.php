<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);
 $current_date=date('Y-m-d');
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 compare_branch($branch_id, $branch_id_transaction); 
 
 $q_get_issuing_detail="SELECT issuing_header.issuingh_id, issuingh_code, MONTH(issuingh_date) AS month, YEAR(issuingh_date) AS year, 
                               issuingd_is_canceled, itemd_id, issuingd_is_return
                        FROM issuing_header 
                        INNER JOIN issuing_detail ON issuing_detail.issuingh_id=issuing_header.issuingh_id
                        WHERE branch_id='$branch_id' AND issuingd_id='$id'";
 $exec_get_issuing_detail=mysqli_query($db_connection,$q_get_issuing_detail);
 $total_get_issuing_detail=mysqli_num_rows($exec_get_issuing_detail);
 if ($total_get_issuing_detail>0)
    {
	  $field_get_issuing_detail=mysqli_fetch_array($exec_get_issuing_detail);
	  $active_period=check_active_period($db_connection, $field_get_issuing_detail['month'], $field_get_issuing_detail['year']);
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
	  if ($field_get_issuing_detail['issuingd_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status aset sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	  if ($field_get_issuing_detail['issuingd_is_return']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status aset sudah dikembalikan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	     {
		   $issuingh_id=$field_get_issuing_detail['issuingh_id'];
		   $itemd_id=$field_get_issuing_detail['itemd_id'];
		   $q_check_return="SELECT * FROM return_detail WHERE issuingd_id='$id' AND retd_is_canceled='0' ";
		   $exec_check_return=mysqli_query($db_connection,$q_check_return);
		   if (mysqli_num_rows($exec_check_return)==0)
		      {
			    mysqli_autocommit($db_connection, false);
			    $q_update_item="UPDATE issuing_detail SET issuingd_is_canceled='1' WHERE issuingd_id='$id'";
				$q_update_item_detail="UPDATE item_detail SET itemd_position='Internal' WHERE itemd_id='$itemd_id'";
				$q_get_item_status="SELECT * FROM issuing_detail WHERE issuingh_id='$issuingh_id' AND issuingd_is_canceled='0'";
				$exec_update_item=mysqli_query($db_connection,$q_update_item);
				$exec_update_item_detail=mysqli_query($db_connection,$q_update_item_detail);
				$exec_get_item_status=mysqli_query($db_connection,$q_get_item_status);
				if ($q_update_item && $q_update_item_detail)
				   {
				     if (mysqli_num_rows($exec_get_item_status)==0)
				        { 
					      $q_update_status_header="UPDATE issuing_header SET issuingh_is_canceled='1', issuingh_canceled_date='$current_date', 
					                                      issuingh_canceled_reason='Semua Item dibatalkan' 
					                               WHERE issuingh_id='$issuingh_id'";
				          $exec_update_status_header=mysqli_query($db_connection,$q_update_status_header);
					      if ($exec_update_status_header)
				             {
				               mysqli_commit($db_connection);
					           ?>
                                  <script language="javascript">
						             var x='<?php echo $issuingh_id;?>';
						             window.location="../../data/issuing/cru_issuing.php?c=u&id="+x;
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
						         var x='<?php echo $issuingh_id;?>';
						         window.location="../../data/issuing/cru_issuing.php?c=u&id="+x;
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
		   else
		      {
			    ?>
	              <script language="javascript">
		            alert('Status Aset sudah dikembalikan!');
		            window.location.href='javascript:history.back(1)';
		          </script>
	             <?php 
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