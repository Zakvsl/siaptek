<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
  
 compare_branch($branch_id, $branch_id_transaction);
 
 $data=$_POST['check_data'];
 $x=count($data);
 $delete='';
 $continue=1;
 foreach ($data as $delete_id)
 		 { 
		   if ($delete=='')
		       $delete="'".$delete_id."'"; 
		   else
		       $delete=$delete.", '".$delete_id."'";
		 }
 $q_check_issuing="SELECT issuing_header.*, MONTH(issuingh_date) AS month, YEAR(issuingh_date) AS year  
                   FROM issuing_header WHERE issuingh_id IN ($delete) AND branch_id='$branch_id' ORDER BY issuingh_date, created_time DESC";
 $exec_check_issuing=mysqli_query($db_connection,$q_check_issuing);
 if (mysqli_num_rows($exec_check_issuing)>0)
    {	
	  $delete_id='';
	  $not_delete_code='';
	  while ($field_data=mysqli_fetch_array($exec_check_issuing))
	        {
              $active_period=check_active_period($db_connection, $field_data['month'], $field_data['year']);
		      if ($active_period!='OK')
		         {
				   $continue=0;
		           ?>
			          <script language="javascript">
				          var x='<?php echo $active_period;?>';
						  var y='<?php echo $field_data['issuingh_code'];?>';
				          alert(x+'\n'+y);
				      </script>
			       <?php
				   break;
				   exit;		     
		         }
				 	
			  if ($field_data['issuingh_status']=='0')
			     {
				   $itemd_id="";
				   $messages='0';
				   $q_check_return_issuing="SELECT * FROM return_header WHERE issuingh_id='".$field_data['issuingh_id']."'";
				   $exec_check_return_issuing=mysqli_query($db_connection,$q_check_return_issuing);
				   if (mysqli_num_rows($exec_check_return_issuing)>0)
				      {
				        if ($not_delete_code=='')
				            $not_delete_code=$field_data['issuingh_code'];
				        else
				            $not_delete_code=$not_delete_code.", ".$field_data['issuingh_code'];  
				      }
				   else
				      {
					     if ($delete_id=='')
				             $delete_id="'".$field_data['issuingh_id']."'";
				         else
				             $delete_id=$delete_id.", '".$field_data['issuingh_id']."'"; 
					   }
				 }
			  else
			     {
				   if ($not_delete_code=='')
				       $not_delete_code=$field_data['issuingh_code'];
				   else
				       $not_delete_code=$not_delete_code.", ".$field_data['issuingh_code'];  
				 }
			}
		
	  if ($continue=="1") 
	     { 			
	       if ($delete_id!='')
	          {
		        $tube_id_update="";
		        $q_get_tube_update="SELECT * FROM issuing_detail 
				                    INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
				                    WHERE branch_id='$branch_id' AND issuing_detail.issuingh_id IN ($delete_id) AND issuingd_is_canceled='0' AND issuingh_is_canceled='0'
						            ORDER BY issuingh_date, created_time DESC ";
		        //echo $q_get_tube_update."<br>";
		        $exec_get_tube_update=mysqli_query($db_connection,$q_get_tube_update);
		        if (mysqli_num_rows($exec_get_tube_update)>0)
			       {
			         while ($field_update_tube_id=mysqli_fetch_array($exec_get_tube_update))
				           {
					         if ($tube_id_update=="")
					             $tube_id_update="'".$field_update_tube_id['itemd_id']."'";
					         else
						         $tube_id_update=$tube_id_update.",'".$field_update_tube_id['itemd_id']."'";
					       }
			       }	  
		   
		        mysqli_autocommit($db_connection, false);
		        $continue=0;
		        if ($tube_id_update!="")
		           {
		             $q_update_item_detail_status="UPDATE item_detail SET itemd_position='Internal' WHERE itemd_id IN ($tube_id_update)";
				     //echo $q_update_item_detail_status;
		             $exec_update_item_detail_status=mysqli_query($db_connection,$q_update_item_detail_status);
				     if ($exec_update_item_detail_status)
					     $continue=1;
				     else
				        {
					      $continue=0;
					      mysqli_rollback($db_connection);
	                      ?>
	                          <script language="javascript">
		                          alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
                                  window.location='../../index/index.php?page=issuing';
		                      </script>
	                      <?php 
				        }
			       }
		        else
			       $continue=1;
					   
		        if ($continue=="1")
		           {
			         $q_delete_header="DELETE FROM issuing_detail WHERE issuingh_id IN ($delete_id)"; 
			         $exec_delete_header=mysqli_query($db_connection,$q_delete_header);
				     if ($exec_delete_header)
				        {
				          $q_delete_detail="DELETE FROM issuing_header
                                            WHERE issuingh_id IN ($delete_id)";
		                  $exec_delete_detail=mysqli_query($db_connection,$q_delete_detail);
					      if ($exec_delete_detail)
					         {
						       mysqli_commit($db_connection);
	            		       ?>
	              		          <script language="javascript">
				    		         var x='<?php echo $not_delete_code;?>';
							         if (x!='')
					    		         alert('Transaksi berikut ini tidak dapat dihapus, karena sudah diteruskan ke transaksi Pengembalian :\n'+x);
                    		         window.location='../../index/index.php?page=issuing';
		          		          </script>
	            		       <?php 
						     }
				          else
					         {
					           mysqli_rollback($db_connection);
	                           ?>
	                              <script language="javascript">
		                             alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
                                     window.location='../../index/index.php?page=issuing';
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
                               window.location='../../index/index.php?page=issuing';
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
                           window.location='../../index/index.php?page=issuing';
		                </script>
	                 <?php 
			       }
		      }
	       else
		   if ($not_delete_code!='')
	          {
		        ?>
	               <script language="javascript">
				      var x='<?php echo $not_delete_code;?>';
			          alert('Transaksi berikut ini tidak dapat dihapus, karena sudah diteruskan ke transaksi Pengembalian Aset :\n'+x);
                      window.location='../../index/index.php?page=issuing';
		           </script>
	            <?php 
		      }
	       else 
	          {
		        ?>
	                <script language="javascript">
                        window.location='../../index/index.php?page=issuing';
		            </script>
	            <?php 
		      }
		 }
	  else
	     {
	       ?>
	          <script language="javascript">
                 window.location='../../index/index.php?page=issuing';
		      </script>
	       <?php 		  
		 }	  
	}
 else
    {
	  ?>
	      <script language="javascript">
             window.location='../../index/index.php?page=issuing';
		  </script>
	  <?php 
    }  			
?>