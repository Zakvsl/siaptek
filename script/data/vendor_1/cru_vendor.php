<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $cru=mysql_real_escape_string($_GET['c']);
  $id=mysql_real_escape_string($_GET['id']);
  
  if ($cru=='u')
     {
	   $q_get_vendor="SELECT vend_id, vend_code, vend_name, vend_npwp, vend_address, vend_status, vend_phone, vend_fax, vend_web_address, vend_email 
                      FROM vendor
				      WHERE vend_id='$id'";
	//   echo $q_get_vendor;
	   $exec_get_vendor=mysql_query($q_get_vendor, $db_connection);
	   $total_vendor=mysql_num_rows($exec_get_vendor);
	   $field_vendor=mysql_fetch_array($exec_get_vendor);
	   if ($total_vendor==0)
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
		    $vend_id=$field_vendor['vend_id'];
		    $vend_code=$field_vendor['vend_code'];
			$vend_code_1=$field_vendor['vend_code'];
			$type_vendor_id=$field_vendor['type_vendor_id'];
			$vend_name=$field_vendor['vend_name'];
			$vend_npwp=$field_vendor['vend_npwp']; 
			$vend_address=$field_vendor['vend_address'];
			$vend_status=$field_vendor['vend_status'];
			$vend_phone=$field_vendor['vend_phone'];
			$vend_fax=$field_vendor['vend_fax'];
			$vend_web=$field_vendor['vend_web_address'];
			$vend_email=$field_vendor['vend_email'];
		  }	  
	 }
?>
<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_cru_vendor" id="f_cru_vendor">
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
				  echo $vend_code;
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
				  echo $vend_id;
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
				  echo $vend_code_1;
				}		
		 ?>"/>
      </label></td>
    </tr>
    

    <tr>
      <td>Nama Vendor</td>
      <td>:</td>
      <td><input name="txt_vend_name" type="text" id="txt_vend_name"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $vend_name;
				}	
		 ?>"/></td>
    </tr>
    
    <tr>
      <td valign="top">NPWP No</td>
      <td valign="top">:</td>
      <td><input name="txt_vend_npwp" type="text" id="txt_vend_npwp"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $vend_npwp;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td valign="top">Alamat</td>
      <td valign="top">:</td>
      <td><label>
        <textarea name="txt_vend_address" cols="23"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $vend_address;
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
				  if ($vend_status=='0')
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
      <td><input name="txt_vend_phone" type="text" id="txt_vend_phone"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $vend_phone;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td>Fax No</td>
      <td>:</td>
      <td><input name="txt_vend_fax" type="text" id="txt_vend_fax"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $vend_fax;
				}	
		 ?>"/></td>
    </tr>
    
    <tr>
      <td>Alamat Web</td>
      <td>:</td>
      <td><input name="txt_vend_web" type="text" id="txt_vend_web"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $vend_web;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td>Alamat Email</td>
      <td>:</td>
      <td><input name="txt_vend_email" type="text" id="txt_vend_email"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $vend_email;
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
	   $vend_id=htmlspecialchars($_POST['txt_id']);
	   $vend_code=htmlspecialchars($_POST['txt_code']);
	   $vend_code_1=htmlspecialchars($_POST['txt_code_1']);
	   $vend_name=htmlspecialchars($_POST['txt_vend_name']);
	   $vend_npwp=htmlspecialchars($_POST['txt_vend_npwp']);
	   $vend_address=htmlspecialchars($_POST['txt_vend_address']);
	   $vend_status=$_POST['rb_status'];
	   $vend_phone=htmlspecialchars($_POST['txt_vend_phone']);
	   $vend_fax=htmlspecialchars($_POST['txt_vend_fax']);
	   $vend_web=htmlspecialchars($_POST['txt_vend_web']);
	   $vend_email=htmlspecialchars($_POST['txt_vend_email']);
	   
	   if (trim($vend_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode Vendor harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($vend_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Nama Vendor harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($vend_address==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Alamat Vendor harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else   
	   if ($vend_id=='')  //jika tambah data
	      {
		    $q_check_vendor="select * from vendor where vend_code='$vend_code'";
			$exec_check_vendor=mysql_query($q_check_vendor, $db_connection);
			if (mysql_num_rows($exec_check_vendor)>0)
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
			      mysql_query('begin');
			      $q_input_vendor="INSERT INTO vendor (vend_code, vend_name, vend_npwp, vend_address, vend_status, vend_phone, vend_fax, vend_web_address, vend_email)
					                           VALUES ('$vend_code', '$vend_name', '$vend_npwp', '$vend_address', $vend_status,'$vend_phone', '$vend_fax', '$vend_web', 
											           '$vend_email')";
				  $exec_input_vendor=mysql_query($q_input_vendor, $db_connection);
				  if ($exec_input_vendor)
				     {
					   mysql_query('commit');
					   ?>
                          <script language="javascript">
						    opener.location.reload(true);
					      </script>
				       <?php 
				   	 }
				  else
				     {  
					   echo $q_input_vendor;
				       mysql_query('rollback');
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
		    $q_check_vendor_issuing="SELECT * FROM issuing_header WHERE issuingh_type='1' and cust_vend_id='$vend_id'";
		//	echo $q_check_vendor_issuing;
			$exec_check_vendor_issuing=mysql_query($q_check_vendor_issuing, $db_connection);
			if (mysql_num_rows($exec_check_vendor_issuing)>0)
			   {
			     ?>
				    <script language="javascript">
					  alert('Vendor tidak dapat diupdate!\nSudah digunakan pada transaksi Pengeluaran Aset!');
					</script>
				 <?php
			   }
			else
			   {   
  		         $q_check_vendor="select * from vendor where vend_id='$vend_id'";
			     $exec_check_vendor=mysql_query($q_check_vendor, $db_connection);
			     if (mysql_num_rows($exec_check_vendor)>0)
			        {	
			          if ($vend_code==$vend_code_1)
			             {	 
			               mysql_query('begin');
				           $q_update_vendor="UPDATE vendor SET vend_name='$vend_name', vend_npwp='$vend_npwp', vend_address='$vend_address', vend_status='$vend_status',
					     	                vend_web_address='$vend_web', vend_email='$vend_email', vend_phone='$vend_phone', vend_fax='$vend_fax'
					     	  	            WHERE vend_id='$vend_id'";					
				       //    echo $q_update_vendor;
						   $exec_update_vendor=mysql_query($q_update_vendor, $db_connection);
				           if ($exec_update_vendor)
					          {
					            mysql_query('commit');
					            ?>
                                   <script language="javascript">
						             opener.location.reload(true);
						             window.close();
					               </script>
				                <?php 
				              }
				           else
				              {
					            mysql_query('rollback');
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
					        $q_check_vendor="select * from vendor where vend_code='$vend_code'";
					        $exec_check_vendor=mysql_query($q_check_vendor, $db_connection);
					        if (mysql_num_rows($exec_check_vendor)==0)
					           {
						         mysql_query('begin');
				                 $q_update_vendor="UPDATE vendor SET vend_code='$vend_code', vend_name='$vend_name', vend_npwp='$vend_npwp', vend_status='$vend_status',
						     		               vend_address='$vend_address',vend_web_address='$vend_web', vend_email='$vend_email', vend_phone='$vend_phone',
 									     	       vend_fax='$vend_fax'
						  	                       WHERE vend_id='$vend_id'";				
                               //  echo $q_update_vendor;
							     $exec_update_vendor=mysql_query($q_update_vendor, $db_connection);
				                 if ($exec_update_vendor)
					                {
					                  mysql_query('commit');
					                  ?>
                                         <script language="javascript">
						                   opener.location.reload(true);
						                   window.close();
					                     </script>
				                      <?php 
				                    }
				                 else
				                    {
					                  mysql_query('rollback');
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






