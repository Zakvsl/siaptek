<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $data=$_POST['check_data'];
 $x=count($data);
 $delete='';
 $is_continue=1;
 
 compare_branch($branch_id, $branch_id_transaction);
 
 foreach ($data as $delete_id)
 		 { 
		   if ($delete=='')
		       $delete="'".$delete_id."'"; 
		   else
		       $delete=$delete.", '".$delete_id."'";
		 }

 $q_check_write_off="SELECT write_off_header.*, MONTH(woh_date) AS month, YEAR(woh_date) AS year  
                     FROM write_off_header WHERE woh_id IN ($delete) AND branch_id='$branch_id' ORDER BY woh_date, created_time DESC";
 $exec_check_write_off=mysqli_query($db_connection, $q_check_write_off);

 if (mysqli_num_rows($exec_check_write_off)>0)
    {
	  $brokh_id=array();
	  $delete_id_wo='';
	  $delete_id_broken='';
	  $delete_id_direct='';
	  $not_delete_code='';
	  while ($field_data=mysqli_fetch_array($exec_check_write_off))
	        {
			  $active_period=check_active_period($db_connection, $field_data['month'], $field_data['year']);
		      if ($active_period!='OK')
		         {
				   $is_continue=0;
		           ?>
			          <script language="javascript">
				          var x='<?php echo $active_period;?>';
						  var y='<?php echo $field_data['woh_code'];?>';
				          alert(x+'\n'+y);
				      </script>
			       <?php
				   break;
				   exit;		     
		         }				
			
			  if ($field_data['woh_is_canceled']=='0')
			     {
				   if ($field_data['woh_sources']=='0')
				      {
				        if ($delete_id_wo=='')
				            $delete_id_wo="'".$field_data['woh_id']."'";
				        else
				            $delete_id_wo=$delete_id_wo.", '".$field_data['woh_id']."'"; 
					  }
				   else
				      {
					    $brokh_id[]=$field_data['brokh_id'];
						if ($delete_id_broken=='')
				            $delete_id_broken="'".$field_data['woh_id']."'";
				        else
				            $delete_id_broken=$delete_id_broken.", '".$field_data['woh_id']."'"; 
					  }
				 }
			  else
			     {
				   if ($delete_id_direct=='')
				       $delete_id_direct="'".$field_data['woh_id']."'";
				   else
				       $delete_id_direct=$delete_id_direct.", '".$field_data['woh_id']."'";
				 } 
			}      
	}


 if ($is_continue=='1')
    {
      if ($delete_id_direct!='')
         {
	       mysqli_autocommit($db_connection, false);
           $q_delete_wo_detail_direct="DELETE FROM write_off_detail WHERE woh_id IN ($delete_id_direct)";	
           $q_delete_wo_header_direct="DELETE FROM write_off_header WHERE woh_id IN ($delete_id_direct)";
           $exec_delete_wo_detail_direct=mysqli_query($db_connection, $q_delete_wo_detail_direct);
           $exec_delete_wo_header_direct=mysqli_query($db_connection, $q_delete_wo_header_direct);
           if ($exec_delete_wo_detail_direct && $exec_delete_wo_header_direct)
               mysqli_commit($db_connection);
           else
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
	}
	
 if ($is_continue=='1')
    {
      mysqli_autocommit($db_connection, false);
      if ($delete_id_broken!='')
         {
		   $q_update_item="UPDATE item_detail, write_off_detail, write_off_header 
                                  SET itemd_status='0', itemd_is_wo='0', itemd_position='Internal' 
                           WHERE item_detail.itemd_id=write_off_detail.itemd_id AND write_off_detail.woh_id=write_off_header.woh_id AND
                                 item_detail.branch_id='$branch_id' AND write_off_header.branch_id='$branch_id' AND write_off_detail.woh_id IN ($delete_id_broken) AND 
			                     wod_is_canceled='0'";
           $q_update_broken="UPDATE broken_detail, write_off_detail SET brokd_is_wo='0'
			                 WHERE broken_detail.brokd_id=write_off_detail.brokd_id AND woh_id IN ($delete_id_broken) AND wod_is_canceled='0'";	 
           $q_delete_wo_detail="DELETE write_off_detail FROM write_off_detail, write_off_header
                                WHERE write_off_detail.woh_id=write_off_header.woh_id AND branch_id='$branch_id' AND write_off_detail.woh_id IN ($delete_id_broken)";
           $q_delete_wo_header="DELETE FROM write_off_header
                                WHERE woh_id IN ($delete_id_broken) AND branch_id='$branch_id'";
	       $exec_update_item=mysqli_query($db_connection, $q_update_item);
		   $exec_update_broken=mysqli_query($db_connection, $q_update_broken);
	       $exec_delete_wo_detail=mysqli_query($db_connection, $q_delete_wo_detail);
	       $exec_delete_wo_header=mysqli_query($db_connection, $q_delete_wo_header);
	       if (!$exec_update_item || !$exec_update_broken || !$exec_delete_wo_detail && !$exec_delete_wo_header)
	          {
		        $is_continue='0';
		        mysqli_rollback($db_connection);
	            ?>
                  <script language="javascript">
		             alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		             history.back();
	              </script>
	            <?php 		   
		      } 
	       else
	          {
		        foreach($brokh_id as $id_brokh)
		               {
				         $q_get_broken_1="SELECT COUNT(*) AS total FROM broken_detail WHERE brokh_id='".$id_brokh."' AND brokd_is_canceled='0' AND brokd_is_wo='0' AND 
				                                 brokd_is_dispossed='0'";	 
				         $q_get_broken_2="SELECT COUNT(*) AS total_wo FROM broken_detail WHERE brokh_id='".$id_brokh."' AND brokd_is_canceled='0' AND brokd_is_wo='1'";
				         $q_get_broken_3="SELECT COUNT(*) AS total_dispossed FROM broken_detail WHERE brokh_id='".$id_brokh."' AND brokd_is_canceled='0' AND 
				                                 brokd_is_dispossed='1'";  
				         $exec_get_broken_1=mysqli_query($db_connection, $q_get_broken_1);
				         $exec_get_broken_2=mysqli_query($db_connection, $q_get_broken_2);
				         $exec_get_broken_3=mysqli_query($db_connection, $q_get_broken_3);
					
				         $field_broken_1=mysqli_fetch_array($exec_get_broken_1);
				         $field_broken_2=mysqli_fetch_array($exec_get_broken_2);
				         $field_broken_3=mysqli_fetch_array($exec_get_broken_3);
				         $total_broken=$field_broken_1['total'];
				         $total_wo=$field_broken_2['total_wo'];
			             $total_dispossed=$field_broken_3['total_dispossed'];   
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
                                                         WHERE brokh_id='".$id_brokh."'";
					     //	echo $q_update_broken_header_status;
			             $exec_update_broken_header_status=mysqli_query($db_connection, $q_update_broken_header_status);
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
		      }
	     }
 
      if ($is_continue=='1')
         {
		   if ($delete_id_wo!='')
		      {
                $q_update_item="UPDATE item_detail, write_off_detail, write_off_header 
                                       SET itemd_status='0', itemd_is_wo='0', itemd_position='Internal' 
                                WHERE item_detail.itemd_id=write_off_detail.itemd_id AND write_off_detail.woh_id=write_off_header.woh_id AND
                                      item_detail.branch_id='$branch_id' AND write_off_header.branch_id='$branch_id' AND write_off_detail.woh_id IN ($delete_id_wo) AND 
			                          wod_is_canceled='0'";
                $q_delete_wo_detail="DELETE write_off_detail FROM write_off_detail, write_off_header
                                     WHERE write_off_detail.woh_id=write_off_header.woh_id AND branch_id='$branch_id' AND write_off_detail.woh_id IN ($delete_id_wo)";
                $q_delete_wo_header="DELETE FROM write_off_header
                                     WHERE woh_id IN ($delete_id_wo) AND branch_id='$branch_id'";	
		        $exec_update_item=mysqli_query($db_connection, $q_update_item);
	            $exec_delete_wo_detail=mysqli_query($db_connection, $q_delete_wo_detail);
	            $exec_delete_wo_header=mysqli_query($db_connection, $q_delete_wo_header);
	            if ($q_update_item && $exec_delete_wo_detail && $exec_delete_wo_header)
	               {
				     mysqli_commit($db_connection);
	                 ?>
	                    <script language="javascript">
		                  window.location='../../index/index.php?page=write-off';
		                </script>
	                 <?php
			       }	 
		        else
		           {
		             mysqli_rollback($db_connection);
	                 ?>
                       <script language="javascript">
		                  alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                  history.back();
	                   </script>
	                 <?php 		
				   }		    
			  } 
		   else
		      {
			    mysqli_commit($db_connection);
	            ?>
	                <script language="javascript">
		              window.location='../../index/index.php?page=write-off';
		            </script>
	            <?php				    
			  } 
		 } 				   
 	}
 else
    {
	  ?>
	    <script language="javascript">
		   window.location='../../index/index.php?page=write-off';
		</script>
	  <?php		  
	}
?>