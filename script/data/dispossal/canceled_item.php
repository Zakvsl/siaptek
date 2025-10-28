<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);
 $current_date=date('Y-m-d');

 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 compare_branch($branch_id, $branch_id_transaction); 
 
 $q_get_dispossal_detail="SELECT dispossal_detail.disph_id, disph_code, disph_date,  MONTH(disph_date) AS month, YEAR(disph_date) AS year,
                                 disph_is_canceled, dispd_is_canceled, itemd_id, dispossal_header.disph_id, 
                                 disph_sources, brokh_id, created_time
                          FROM dispossal_detail
                          INNER JOIN dispossal_header ON dispossal_detail.disph_id=dispossal_header.disph_id
                          WHERE dispossal_header.branch_id='$branch_id' AND dispossal_detail.dispd_id='$id'";
// echo $q_get_dispossal_detail;
 $exec_get_dispossal_detail=mysqli_query($db_connection,$q_get_dispossal_detail);
 $total_get_dispossal_detail=mysqli_num_rows($exec_get_dispossal_detail);
 if ($total_get_dispossal_detail>0)
    {
	  $field_get_dispossal_detail=mysqli_fetch_array($exec_get_dispossal_detail);
      $active_period=check_active_period($db_connection, $field_get_dispossal_detail['month'], $field_get_dispossal_detail['year']);
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
	  if ($field_get_dispossal_detail['disph_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status Transaksi Penjualan Aset sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	  if ($field_get_dispossal_detail['dispd_is_canceled']=='1')
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
		   $disph_id=$field_get_dispossal_detail['disph_id'];
		   $itemd_id=$field_get_dispossal_detail['itemd_id'];

			    $is_continue='1';
		        mysqli_autocommit($db_connection, false);
		        $q_update_item="UPDATE item_detail SET itemd_position='Internal', itemd_is_dispossed='0', itemd_status='0' 
				                WHERE itemd_id='".$field_get_dispossal_detail['itemd_id']."'";
		        $q_update_dispossal_detail="UPDATE dispossal_detail SET dispd_is_canceled='1' WHERE dispd_id='$id'";
		        $q_check_dispossal_header="SELECT * FROM dispossal_detail 
									       INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
									       WHERE dispossal_header.disph_id='".$field_get_dispossal_detail['disph_id']."' AND dispd_is_canceled='0' AND
		                                   branch_id='$branch_id'";	  
		        $exec_update_item=mysqli_query($db_connection,$q_update_item);
		        $exec_update_dispossal_detail=mysqli_query($db_connection,$q_update_dispossal_detail);
		        $exec_check_dispossal_header=mysqli_query($db_connection,$q_check_dispossal_header);
                $total_check_dispossal_header=mysqli_num_rows($exec_check_dispossal_header); 
		        if ($field_get_dispossal_detail['disph_sources']=='1')
		           {
			         $brokh_id=$field_get_dispossal_detail['brokh_id'];
				     $q_update_broken_detail="UPDATE broken_detail, dispossal_detail SET brokd_is_dispossed='0' 
		                                      WHERE broken_detail.brokd_id=dispossal_detail.brokd_id AND dispd_id='$id'";
				     $q_check_broken="SELECT brokh_id AS brokh_id_1, 
                                            (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0') AS total,
                                            (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0' AND brokd_is_wo='1') AS total_wo,
                                            (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0' AND brokd_is_dispossed='1') AS 
										     total_dispossed
                                      FROM broken_header
                                      WHERE brokh_id IN ('$brokh_id') AND brokh_is_canceled='0'";
				     $exec_update_broken_detail=mysqli_query($db_connection,$q_update_broken_detail);
				     $exec_check_broken=mysqli_query($db_connection,$q_check_broken);
				     $total_check_broken=mysqli_num_rows($exec_check_broken);
				     if (!$exec_update_broken_detail)
				        {
						  $is_continue='0';
                          mysqli_rollback($db_connection);
				          ?>
	                          <script language="javascript">
		                        alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                        window.location.href='javascript:history.back(1)';
		                      </script>
	                      <?php 
				        }
				     else
				        {
				          if ($total_check_broken>0)
				             {													 
					           $field_broken=mysqli_fetch_array($exec_check_broken);
					           $total_broken=$field_broken['total'];
					           $total_wo=$field_broken['total_wo'];
					           $total_dispossed=$field_broken['total_dispossed'];
					           if ($total_wo==0 && $total_dispossed==0)
					               $status_broken='0';
					           else
					           if ($total_wo>0 && $total_wo<$total_broken && $total_dispossed==0)
					               $status_broken='1';
					           else
					           if ($total_wo==$total_broken && $total_dispossed==0)
					               $status_broken='2';
					           else
					           if ($total_dispossed>0 && $total_dispossed<$total_broken && $total_wo==0)
					               $status_broken='3';
					           else
					           if ($total_dispossed==$total_dispossed && $total_wo==0)
					               $status_broken='4';
						       else
					           if ($total_dispossed+$total_wo==$total_broken)
					               $status_broken='6';
					           else
					           if ($total_dispossed>0 && $total_dispossed<$total_broken && $total_wo>0 && $total_wo<$total_broken)
					               $status_broken='5';
					      
											 
						       $q_update_broken_header_status="UPDATE broken_header
                                                                      SET brokh_status='$status_broken'
                                                               WHERE brokh_id='$brokh_id'";
						       $exec_update_broken_header_status=mysqli_query($db_connection,$q_update_broken_header_status);
						       if (!$exec_update_broken_header_status)
							      {
								     $is_continue='0';
							         mysqli_rollback($db_connection);
					                 ?>
                                        <script language="javascript">
						                   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
							               history.back();
					                    </script>
				                     <?php 
								     exit;
							      }
						      }  
					       else
						      {
							    $is_continue='0';
						        mysqli_rollback($db_connection);
					            ?>
                                   <script language="javascript">
						             alert('No Transaksi Kerusakan tidak ditemukan!');
						             window.location.href='javascript:history.back(1)';
					               </script>
				                <?php 
					            exit; 
						      }
				        }
			       }
		   
		   		if ($is_continue=='1')
				   {   
		             if ($exec_update_item && $exec_update_dispossal_detail)
			            { 
			              if ($total_check_dispossal_header==0)
				             {
			                   $q_update_dispossal_header="UPDATE dispossal_header SET disph_canceled_reason='Semua Item Dibatalkan', disph_canceled_date=NOW(),
                                                                  disph_is_canceled=IF((SELECT COUNT(*) FROM dispossal_detail 
															                            WHERE disph_id='$disph_id' AND dispd_is_canceled='0')=0,'1','0')
                                                           WHERE disph_id='$disph_id'";
				               $exec_update_dispossal_header=mysqli_query($db_connection,$q_update_dispossal_header);
					           if ($exec_update_dispossal_header)
					              {
						            mysqli_commit($db_connection);
				                    ?>
                                      <script language="javascript">
					                    var x='<?php echo $disph_id;?>';
					                    window.location="../../data/dispossal/cru_dispossal.php?c=u&id="+x;
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
					                var x='<?php echo $disph_id;?>';
					                window.location="../../data/dispossal/cru_dispossal.php?c=u&id="+x;
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
		   alert('Data tidak ditemukan!');
		   window.location.href='javascript:history.back(1)';
		 </script>
	  <?php
	} 						
?>