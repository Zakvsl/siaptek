<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $branch_id=$_SESSION['ses_id_branch'];
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  
  if ($cru=='u')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_customer="SELECT cust_id, cust_code, cust_name, cust_npwp, cust_address, cust_status, cust_phone, cust_fax, cust_web_address, cust_email, custtp_id, cust_type
                        FROM customer
				        WHERE cust_type='1' AND cust_id='$id' AND branch_id='$branch_id'";
	   //echo $q_get_customer;
	   $exec_get_customer=mysqli_query($db_connection, $q_get_customer);
	   $total_customer=mysqli_num_rows($exec_get_customer);
	   $field_customer=mysqli_fetch_array($exec_get_customer);
	   if ($total_customer==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Vendor yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $cust_id=$field_customer['cust_id'];
		    $cust_code=$field_customer['cust_code'];
			$cust_code_1=$field_customer['cust_code'];
			$cust_name=$field_customer['cust_name'];
			$cust_npwp=$field_customer['cust_npwp']; 
			$cust_address=$field_customer['cust_address'];
			$cust_status=$field_customer['cust_status'];
			$cust_phone=$field_customer['cust_phone'];
			$cust_fax=$field_customer['cust_fax'];
			$cust_web=$field_customer['cust_web_address'];
			$cust_email=$field_customer['cust_email'];
			$custtp_id=$field_customer['custtp_id'];
			$cust_type=$field_customer['cust_type'];
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
															   echo "TAMBAH DATA VENDOR BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA VENDOR";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="33%">Kode Vendor</td>
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
				  echo $cust_code;
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
				  echo $cust_id;
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
				  echo $cust_code_1;
				}		
		 ?>"/>
      </label></td>
    </tr>
    

    <tr>
      <td>Nama Vendor </td>
      <td>:</td>
      <td><input name="txt_cust_name" type="text" id="txt_cust_name"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cust_name;
				}	
		 ?>"/></td>
    </tr>
    
    <tr>
      <td valign="top">NPWP No</td>
      <td valign="top">:</td>
      <td><input name="txt_cust_npwp" type="text" id="txt_cust_npwp"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cust_npwp;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td valign="top">Alamat</td>
      <td valign="top">:</td>
      <td><label>
        <textarea name="txt_cust_address" cols="23"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cust_address;
				}	
		 ?></textarea>
      </label></td>
    </tr>
    
    
    <tr>
      <td>Status</td>
      <td>:</td>
      <td>
	      <?php
		     if ($cru=='i')
			    {
				  echo "<input id='rb_status' name='rb_status' type='radio' value='0' checked='checked'> Aktif";
                  echo "<input id='rb_status' name='rb_status' type='radio' value='1'> Tidak Aktif</td>";
				}
 			 else
			 if ($cru=='u')
			    {
				  if ($cust_status=='0')
				     {
					   echo "<input id='rb_status' name='rb_status' type='radio' value='0' checked='checked'> Aktif";
                       echo "<input id='rb_status' name='rb_status' type='radio' value='1'> Tidak Aktif</td>";
					 }
				  else
				     {
					   echo "<input id='rb_status' name='rb_status' type='radio' value='0'> Aktif";
                       echo "<input id='rb_status' name='rb_status' type='radio' value='1' checked='checked'> Tidak Aktif</td>";
					 }	 
				}  
		  ?>
        
    </tr>
    
    
    <tr>
      <td>No Telpon</td>
      <td>:</td>
      <td><input name="txt_cust_phone" type="text" id="txt_cust_phone"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cust_phone;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td>Fax No</td>
      <td>:</td>
      <td><input name="txt_cust_fax" type="text" id="txt_cust_fax"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cust_fax;
				}	
		 ?>"/></td>
    </tr>
    
    <tr>
      <td>Alamat Web</td>
      <td>:</td>
      <td><input name="txt_cust_web" type="text" id="txt_cust_web"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cust_web;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td>Alamat Email</td>
      <td>:</td>
      <td><input name="txt_cust_email" type="text" id="txt_cust_email"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cust_email;
				}	
		 ?>"/></td>
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
	   $cust_id=htmlspecialchars($_POST['txt_id']);
	   $cust_code=htmlspecialchars($_POST['txt_code']);
	   $cust_code_1=htmlspecialchars($_POST['txt_code_1']);
	   $cust_name=htmlspecialchars($_POST['txt_cust_name']);
	   $cust_npwp=htmlspecialchars($_POST['txt_cust_npwp']);
	   $cust_address=htmlspecialchars($_POST['txt_cust_address']);
	   $cust_status=$_POST['rb_status'];
	   $cust_phone=htmlspecialchars($_POST['txt_cust_phone']);
	   $cust_fax=htmlspecialchars($_POST['txt_cust_fax']);
	   $cust_web=htmlspecialchars($_POST['txt_cust_web']);
	   $cust_email=htmlspecialchars($_POST['txt_cust_email']);
	   
	   if (trim($cust_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode Vendor harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($cust_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Nama Vendor harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($cust_address==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Alamat Vendor harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else   
	   if ($cust_id=='')  //jika tambah data
	      {
		    $q_check_customer="select * from customer where cust_code='$cust_code' AND cust_type='1' AND branch_id='$branch_id'";
			$exec_check_customer=mysqli_query($db_connection, $q_check_customer);
			if (mysqli_num_rows($exec_check_customer)>0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Duplikasi Kode Vendor!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {
			      mysqli_autocommit($db_connection, false);
			      $q_input_customer="INSERT INTO customer (branch_id, cust_code, cust_name, cust_npwp, cust_address, cust_status, cust_phone, cust_fax, cust_web_address, 
				                                           cust_email, cust_type)
					                               VALUES ('$branch_id', '$cust_code', '$cust_name', '$cust_npwp', '$cust_address', $cust_status,'$cust_phone', '$cust_fax', 
											               '$cust_web', '$cust_email','1')";
				  $exec_input_customer=mysqli_query($db_connection, $q_input_customer);
				  if ($exec_input_customer)
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
		    $q_check_customer_issuing="SELECT * FROM issuing_header WHERE issuingh_type='1' and cust_id='$cust_id' branch_id='$branch_id'";
		//	echo $q_check_customer_issuing;
			$exec_check_customer_issuing=mysqli_query($db_connection, $q_check_customer_issuing);
			if (mysqli_num_rows($exec_check_customer_issuing)>0)
			   {
			     ?>
				    <script language="javascript">
					  alert('Vendor tidak dapat diupdate!\nSudah digunakan pada transaksi Pengeluaran Aset!');
					</script>
				 <?php
			   }
			else
			   {   
  		         $q_check_customer="select * from customer where cust_id='$cust_id' and cust_type='1' and branch_id='$branch_id'";
			     $exec_check_customer=mysqli_query($db_connection, $q_check_customer);
			     if (mysqli_num_rows($exec_check_customer)>0)
			        {	
			          if ($cust_code==$cust_code_1)
			             {	 
			               mysqli_autocommit($db_connection, false);
				           $q_update_customer="UPDATE customer SET cust_name='$cust_name', cust_npwp='$cust_npwp', cust_address='$cust_address', cust_status='$cust_status',
					     	                          cust_web_address='$cust_web', cust_email='$cust_email', cust_phone='$cust_phone', cust_fax='$cust_fax'
					     	  	               WHERE cust_id='$cust_id' AND cust_type='1' AND branch_id='$branch_id'";					
				       //    echo $q_update_customer;
						   $exec_update_customer=mysqli_query($db_connection, $q_update_customer);
				           if ($exec_update_customer)
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
					        $q_check_customer="select * from customer where cust_type='1' and cust_code='$cust_code' AND branch_id='$branch_id'";
					        $exec_check_customer=mysqli_query($db_connection, $q_check_customer);
					        if (mysqli_num_rows($exec_check_customer)==0)
					           {
						         mysqli_autocommit($db_connection, false);
				                 $q_update_customer="UPDATE customer SET cust_code='$cust_code', cust_name='$cust_name', cust_npwp='$cust_npwp', cust_status='$cust_status',
						     		                        cust_address='$cust_address',cust_web_address='$cust_web', cust_email='$cust_email', cust_phone='$cust_phone',
 									     	                cust_fax='$cust_fax'
						  	                         WHERE cust_id='$cust_id' AND cust_type='1' AND branch_id='$branch_id'";		
                               //  echo $q_update_customer;
							     $exec_update_customer=mysqli_query($db_connection, $q_update_customer);
				                 if ($exec_update_customer)
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
						               alert('Duplikasi Kode Vendor!');
								       opener.location.reload(true);
							           window.close();
					                 </script>
				                  <?php  
						        }
						   }	
			       }  
			    else     
			       {
			         ?>
			           <script language="javascript">
		                 alert('Vendor yang akan diupdate tidak ditemukan!');
				         window.close();
			           </script>
		             <?php  
				  }	 
			   }     
		  }	  
	 }
?>






