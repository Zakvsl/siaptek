<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  
  if ($cru=='u')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_customer_type="SELECT custtp_id, custtp_code, custtp_name, 
	                                CASE custtp_is_active
							             WHEN '0' THEN 'Active'
									     WHEN '1' THEN 'InActive'
							        END custtp_is_active 
                             FROM customer_type
				             WHERE custtp_id='$id'";
	//   echo $q_get_customer;
	   $exec_get_customer_type=mysqli_query($db_connection,$q_get_customer_type);
	   $total_customer_type=mysqli_num_rows($exec_get_customer_type);
	   $field_customer_type=mysqli_fetch_array($exec_get_customer_type);
	   if ($total_customer_type==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Tipe Customer yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $custtp_id=$field_customer_type['custtp_id'];
		    $custtp_code=$field_customer_type['custtp_code'];
			$custtp_code_1=$field_customer_type['custtp_code'];
			$custtp_name=$field_customer_type['custtp_name'];
			$custtp_is_active=$field_customer_type['custtp_is_active'];
		  }	  
	 }
  else
     $id=""; 
?>
<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_cru_customer" id="f_cru_customer">
  <table width="403" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH DATA TIPE CUSTOMER BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA TIPE CUSTOMER";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="33%" nowrap="nowrap">Kode Tipe Customer</td>
      <td width="1%">:</td>
      <td width="66%"><label>
      <input name="txt_code" type="text" id="txt_code" size="30" maxlength="25"   
	     value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custtp_code;
				}		
		 ?>">
      <input name="txt_id" type="hidden" id="txt_id" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custtp_id;
				}		
		 ?>"/>
      <input name="txt_code_1" type="hidden" id="txt_code_1" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custtp_code_1;
				}		
		 ?>"/>
      </label></td>
    </tr>
    

    <tr>
      <td nowrap="nowrap">Nama Tipe Customer</td>
      <td>:</td>
      <td><input name="txt_custtp_name" type="text" id="txt_custtp_name"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custtp_name;
				}	
		 ?>"/></td>
    </tr>
    
    <tr>
      <td valign="top">Status</td>
      <td valign="top">:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "Active";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custtp_is_active;
				}	
		 ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><label>
      <input name="btn_save" type="submit" id="btn_save" value="Save"/>              
      <input name="btn_new" type="reset" id="btn_new" value="New" />
      <input name="btn_close" type="button" id="btn_close" value="Close" onClick="window.close()"/>
      </label></td>
    </tr>
  </table>
</form>

<?php 
  if (isset($_POST['btn_save']))
     {
	   $custtp_id=htmlspecialchars($_POST['txt_id']);
	   $custtp_code=htmlspecialchars($_POST['txt_code']);
	   $custtp_code_1=htmlspecialchars($_POST['txt_code_1']);
	   $custtp_name=htmlspecialchars($_POST['txt_custtp_name']);
	   $custtp_is_active='0';
	   
	   if (trim($custtp_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode Tipe Customer harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($custtp_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Nama Tipe Customer harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if ($custtp_id=='')  //jika tambah data
	      {
		    $q_check_customer_type="select * from customer_type where custtp_code='$custtp_code'";
			$exec_check_customer_type=mysqli_query($db_connection,$q_check_customer_type);
			if (mysqli_num_rows($exec_check_customer_type)>0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Duplikasi Kode Tipe Customer!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {
			      mysqli_autocommit($db_connection, false);
			      $q_input_customer_type="INSERT INTO customer_type (custtp_code, custtp_name, custtp_is_active)
					                           VALUES ('$custtp_code', '$custtp_name', '0')";
				  $exec_input_customer_type=mysqli_query($db_connection,$q_input_customer_type);
				  if ($exec_input_customer_type)
				     {
					   mysqli_commit($db_connection);
					   ?>
                          <script language="javascript">
						    opener.location.reload(true);
					      </script>
				       <?php 
				   	 }
				  else
				     {  
					//   echo $q_input_customer;
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
	   else   // jika update data
	      { 	  
		    $q_check_customer="SELECT * FROM customer WHERE custtp_id='$custtp_id'";
		//	echo $q_check_customer_issuing;
			$exec_check_customer=mysqli_query($db_connection,$q_check_customer);
			if (mysqli_num_rows($exec_check_customer)>0)
			   {
			     ?>
				    <script language="javascript">
					  alert('Tipe Customer tidak dapat diupdate!\nSudah digunakan pada table Pelanggan!');
					</script>
				 <?php
			   }
			else
			   {   
  		         $q_check_customer_type="select * from customer_type where custtp_id='$custtp_id'";
			     $exec_check_customer_type=mysqli_query($db_connection,$q_check_customer_type);
			     if (mysqli_num_rows($exec_check_customer_type)>0)
			        {	
			          if ($custtp_code==$custtp_code_1)
			             {	 
			               mysqli_autocommit($db_connection, false);
				           $q_update_customer_type="UPDATE customer_type SET custtp_name='$custtp_name'
					     	  	               WHERE custtp_id='$custtp_id'";					
				       //    echo $q_update_customer;
						   $exec_update_customer_type=mysqli_query($db_connection,$q_update_customer_type);
				           if ($exec_update_customer_type)
					          {
					            mysqli_commit($db_connection);
					            ?>
                                   <script language="javascript">
						             opener.location.reload(true);
						             window.close();
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
					        $q_check_customer_type="select * from customer_type where custtp_code='$custtp_code'";
					        $exec_check_customer_type=mysqli_query($db_connection,$q_check_customer_type);
					        if (mysqli_num_rows($exec_check_customer_type)==0)
					           {
						         mysqli_autocommit($db_connection, false);
				                 $q_update_customer_type="UPDATE customer_type SET custtp_code='$custtp_code', custtp_name='$custtp_name'
						  	                              WHERE custtp_id='$custtp_id'";				
                               //  echo $q_update_customer;
							     $exec_update_customer_type=mysqli_query($db_connection,$q_update_customer_type);
				                 if ($exec_update_customer_type)
					                {
					                  mysqli_commit($db_connection);
					                  ?>
                                         <script language="javascript">
						                   opener.location.reload(true);
						                   window.close();
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
					              ?>
                                     <script language="javascript">
						               alert('Duplikasi Kode Tipe Customer!');
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
		                 alert('Tipe Customer yang akan diupdate tidak ditemukan!');
				         window.close();
			           </script>
		             <?php  
				  }	 
			   }     
		  }	  
	 }
?>






