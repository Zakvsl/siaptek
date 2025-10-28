<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);
 $current_date=date('Y-m-d');
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 compare_branch($branch_id, $branch_id_transaction); 
 
 $q_get_write_off_detail="SELECT write_off_detail.woh_id, woh_code, MONTH(woh_date) AS month, YEAR(woh_date) AS year,  
                                 woh_is_canceled, wod_is_canceled, itemd_id, write_off_header.woh_id, woh_sources, brokh_id,
                                 woh_date, created_time
                          FROM write_off_detail
                          INNER JOIN write_off_header ON write_off_detail.woh_id=write_off_header.woh_id
                          WHERE write_off_header.branch_id='$branch_id' AND write_off_detail.wod_id='$id'";
// echo $q_get_write_off_detail;
 $exec_get_write_off_detail=mysqli_query($db_connection, $q_get_write_off_detail);
 $total_get_write_off_detail=mysqli_num_rows($exec_get_write_off_detail);
 if ($total_get_write_off_detail>0)
    {
	  $field_get_write_off_detail=mysqli_fetch_array($exec_get_write_off_detail);
      $active_period=check_active_period($db_connection, $field_get_write_off_detail['month'], $field_get_write_off_detail['year']);
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
	  if ($field_get_write_off_detail['woh_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status Transaksi Penjualan sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	  if ($field_get_write_off_detail['wod_is_canceled']=='1')
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
		   $woh_id=$field_get_write_off_detail['woh_id'];
		   $itemd_id=$field_get_write_off_detail['itemd_id'];
		   $woh_date=$field_get_write_off_detail['woh_date'];
		   $woh_created_time=$field_get_write_off_detail['created_time'];

		        mysqli_autocommit($db_connection, false);
				$continue='1'; 
		        $q_update_item="UPDATE item_detail SET itemd_position='Internal', itemd_is_wo='0', itemd_status='0' 
				                WHERE itemd_id='".$field_get_write_off_detail['itemd_id']."'";
		        $q_update_write_off_detail="UPDATE write_off_detail SET wod_is_canceled='1' WHERE wod_id='$id'";
		        $q_check_write_off_header="SELECT *
		                                   FROM write_off_detail 
									       INNER JOIN write_off_header ON write_off_header.woh_id=write_off_detail.woh_id
									       WHERE write_off_header.woh_id='".$field_get_write_off_detail['woh_id']."' AND wod_is_canceled='0' AND
		                                   branch_id='$branch_id'";	  
		        $exec_update_item=mysqli_query($db_connection, $q_update_item);
		        $exec_update_write_off_detail=mysqli_query($db_connection, $q_update_write_off_detail);
		        $exec_check_write_off_header=mysqli_query($db_connection, $q_check_write_off_header);
                $total_check_write_off_header=mysqli_num_rows($exec_check_write_off_header); 
		        if ($field_get_write_off_detail['woh_sources']=='1')
		           {
			         $brokh_id=$field_get_write_off_detail['brokh_id'];
				     $q_update_broken_detail="UPDATE broken_detail, write_off_detail SET brokd_is_wo='0' 
		                                      WHERE broken_detail.brokd_id=write_off_detail.brokd_id AND wod_id='$id'";
				     $q_check_broken="SELECT brokh_id AS brokh_id_1, 
                                            (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0') AS total,
                                            (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0' AND brokd_is_wo='1') AS total_wo,
                                            (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0' AND brokd_is_dispossed='1') AS 
										     total_dispossed
                                      FROM broken_header
                                      WHERE brokh_id IN ('$brokh_id') AND brokh_is_canceled='0'";
			     //	echo $q_check_broken;
				     $exec_update_broken_detail=mysqli_query($db_connection, $q_update_broken_detail);
				     $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
				     $total_check_broken=mysqli_num_rows($exec_check_broken);
				     if (!$exec_update_broken_detail)
				        {
						  $continue='0';
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
						       $exec_update_broken_header_status=mysqli_query($db_connection, $q_update_broken_header_status);
						       if (!$exec_update_broken_header_status)
							      {
								    $continue='0';
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
							   $continue='0';
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
		   
		   		if ($continue=='1')
				   {   
		             if ($exec_update_item && $exec_update_write_off_detail)
			            { 
			              if ($total_check_write_off_header==0)
				             {
			                   $q_update_write_off_header="UPDATE write_off_header SET woh_canceled_reason='Semua Aset Dibatalkan', woh_canceled_date=NOW(),
                                                                  woh_is_canceled='1'
                                                           WHERE woh_id='$woh_id'";
				               $exec_update_write_off_header=mysqli_query($db_connection, $q_update_write_off_header);
					           if ($exec_update_write_off_header)
					              {
						            mysqli_commit($db_connection);
				                    ?>
                                      <script language="javascript">
					                    var x='<?php echo $woh_id;?>';
					                    window.location="../../data/write_off/cru_write_off.php?c=u&id="+x;
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
					                var x='<?php echo $woh_id;?>';
					                window.location="../../data/write_off/cru_write_off.php?c=u&id="+x;
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