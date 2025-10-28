<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $cru=mysqli_real_escape_string($db_connection,$_GET['c']);
  if ($cru=='u')
     $id=mysqli_real_escape_string($db_connection,$_GET['id']);
  else
     $id="";
  
  if ($cru=='u')
     {
	   $q_get_branch="SELECT branch_id, branch_code, branch_name, branch_is_headquarter, branch_address
                      FROM branch
				      WHERE branch_id='$id'";
	//   echo $q_get_branch;
	   $exec_get_branch=mysqli_query($db_connection, $q_get_branch);
	   $total_branch=mysqli_num_rows($exec_get_branch);
	   $field_branch=mysqli_fetch_array($exec_get_branch);
	   if ($total_branch==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Kantor Cabang yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $branch_id=$field_branch['branch_id'];
		    $branch_code=$field_branch['branch_code'];
			$branch_code_1=$field_branch['branch_code'];
			$branch_name=$field_branch['branch_name'];
			$branch_is_headquarter=$field_branch['branch_is_headquarter']; 
			$branch_address=$field_branch['branch_address'];
		  }	  
	 }
?>
<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_cru_branch" id="f_cru_branch">
  <table width="403" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH DATA KANTOR CABANG BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA KANTOR CABANG";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="35%" nowrap="nowrap">Kode Kantor Cabang</td>
      <td width="2%">:</td>
      <td width="63%"><label>
      <input name="txt_code" type="text" id="txt_code" size="30" maxlength="25" 
	     <?php 
		     if ($cru=='i')
			    {
				  echo "value=''";
				}
		     else
			 if ($cru=='u')
			    {
				  echo "value='$branch_code' readonly='readonly'";
				}		
		 ?>>
      <input name="txt_id" type="hidden" id="txt_id" <?php 
		     if ($cru=='i')
			    {
				  echo "value=''";
				}
		     else
			 if ($cru=='u')
			    {
				  echo "value='$branch_id'";
				}		
		 ?>/>
      <input name="txt_code_1" type="hidden" id="txt_code_1" <?php 
		     if ($cru=='i')
			    {
				  echo "value=''";
				}
		     else
			 if ($cru=='u')
			    {
				  echo "value='$branch_code_1'";
				}		
		 ?>/>
      </label></td>
    </tr>
    <tr>
      <td nowrap="nowrap">Nama Kantor Cabang</td>
      <td>:</td>
      <td><input name="txt_branch_name" type="text" id="txt_branch_name"  size="30"
		 <?php 
		     if ($cru=='i')
			    {
				  echo "value=''";
				}
		     else
			 if ($cru=='u')
			    {
				  echo "value='$branch_name'";
				}	
		 ?>/></td>
    </tr>
    
    <tr>
      <td valign="top">Tipe</td>
      <td valign="top">:</td>
      <td><?php
		     if ($cru=='i')
			    {
				  echo "<input id='rb_is_headquarter' name='rb_is_headquarter' type='radio' value='1' checked='checked'> Kantor Cabang";
				  echo "<input id='rb_is_headquarter' name='rb_is_headquarter' type='radio' value='0'> Kantor Pusat</td>";
				}
 			 else
			 if ($cru=='u')
			    {
				  if ($branch_is_headquarter=='0')
				     {
					   echo "<input id='rb_is_headquarter' name='rb_is_headquarter' type='radio' value='1'> Kantor Cabang";
					   echo "<input id='rb_is_headquarter' name='rb_is_headquarter' type='radio' value='0' checked='checked'> Kantor Pusat</td>";
					 }
				  else
				     {
					   echo "<input id='rb_is_headquarter' name='rb_is_headquarter' type='radio' value='1' checked='checked'> Kantor Cabang";
					   echo "<input id='rb_is_headquarter' name='rb_is_headquarter' type='radio' value='0'> Kantor Pusat</td>";
					 }	 
				}  
		  ?></td>
    </tr>
    <tr>
      <td valign="top">Alamat</td>
      <td valign="top">:</td>
      <td><label>
        <textarea name="txt_branch_address" cols="23"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $branch_address;
				}	
		 ?></textarea>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><label>
      <input name="btn_save" type="submit" id="btn_save" value="Simpan"/>              
      <input name="btn_new" type="reset" id="btn_new" value="Baru" />
      <input name="btn_close" type="button" id="btn_close" value="Tutup" onClick="window.close()"/>
      </label></td>
    </tr>
  </table>
</form>

<?php 
  if (isset($_POST['btn_save']))
     {
	   $branch_id=htmlspecialchars($_POST['txt_id']);
	   $branch_code=htmlspecialchars($_POST['txt_code']);
	   $branch_code_1=htmlspecialchars($_POST['txt_code_1']);
	   $branch_name=htmlspecialchars($_POST['txt_branch_name']);
	   $branch_is_headquarter=$_POST['rb_is_headquarter'];
	   $branch_address=htmlspecialchars($_POST['txt_branch_address']);
	   
	   if (trim($branch_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode Kantor Cabang harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($branch_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Nama Kantor Cabang harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($branch_address==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Alamat Kantor Cabang harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else   
	   if ($branch_id=='')  //jika tambah data
	      {
		    $q_check_branch="select * from branch where branch_code='$branch_code'";
			$exec_check_branch=mysqli_query($db_connection, $q_check_branch);
			if (mysqli_num_rows($exec_check_branch)>0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Duplikasi Kode Kantor Cabang!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {    
			      if ($branch_is_headquarter=='0')
				     {
			           $q_check_headquater="SELECT * FROM branch WHERE branch_is_headquarter='0'";
					   $exec_check_headquarter=mysqli_query($db_connection, $q_check_headquater);
					   if (mysqli_num_rows($exec_check_headquarter)>0)
					      {
						    ?>
							   <script language="javascript">
							     alert('Kantor Pusat sudah terdaftar!');
								 history.back(1);
							   </script>
							<?php
							exit;
						  }
					 }  
			      mysqli_query($db_connection, 'begin');
			      $q_input_branch="INSERT INTO branch (branch_code, branch_name, branch_is_headquarter, branch_address)
					                           VALUES ('$branch_code', '$branch_name', '$branch_is_headquarter', '$branch_address')";
				  $q_input_format_number="INSERT INTO format_number (fn_name, branch_id, YEAR, fn_no_inc, fn_notes)
                                                       VALUES('TRANS',(SELECT branch_id FROM branch WHERE branch_code='$branch_code'), YEAR(NOW()), 0, 'TRANSFER TABUNG'),
                                                             ('CYO',(SELECT branch_id FROM branch WHERE branch_code='$branch_code'), YEAR(NOW()), 0, 'PENGELUARAN TABUNG'),
                                                             ('CYI',(SELECT branch_id FROM branch WHERE branch_code='$branch_code'), YEAR(NOW()), 0, 'PENGEMBALIAN TABUNG'),
                                                             ('BRO',(SELECT branch_id FROM branch WHERE branch_code='$branch_code'), YEAR(NOW()), 0, 'KERUSAKAN TABUNG'),
                                                             ('WO',(SELECT branch_id FROM branch WHERE branch_code='$branch_code'), YEAR(NOW()), 0, 'PENGHAPUSAN TABUNG'),
                                                             ('DSP',(SELECT branch_id FROM branch WHERE branch_code='$branch_code'), YEAR(NOW()), 0, 'PENJUALAN TABUNG'),
                                                             ('CHG',(SELECT branch_id FROM branch WHERE branch_code='$branch_code'), YEAR(NOW()), 0, 'PERUBAHAN DESKRIPSI'),
															 ('RTT',(SELECT branch_id FROM branch WHERE branch_code='$branch_code'), YEAR(NOW()), 0, 'PENERIMAAN TRANSFER TABUNG')";
				  //echo $q_input_branch."<br>";
				  //echo $q_input_format_number;
				  $exec_input_branch=mysqli_query($db_connection, $q_input_branch);
				  $exec_input_format_number=mysqli_query($db_connection, $q_input_format_number);
				  if ($exec_input_branch && $exec_input_format_number)
				     {
					   mysqli_query($db_connection, 'commit');
					   ?>
                          <script language="javascript">
						    opener.location.reload(true);
							window.close();
					      </script>
				       <?php 
				   	 }
				  else
				     {  
					//   echo $q_input_branch;
				       mysqli_query($db_connection, 'rollback');
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
  		    $q_check_branch="select * from branch where branch_id='$branch_id'";
			$exec_check_branch=mysqli_query($db_connection, $q_check_branch);
			if (mysqli_num_rows($exec_check_branch)>0)
			   {	
			      if ($branch_code==$branch_code_1)
			         {	 
			           mysqli_query($db_connection, 'begin');
				       $q_update_branch="UPDATE branch SET branch_name='$branch_name', branch_is_headquarter='$branch_is_headquarter', branch_address='$branch_address'
					     	  	         WHERE branch_id='$branch_id'";					
				       //    echo $q_update_branch;
					   $exec_update_branch=mysqli_query($db_connection, $q_update_branch);
				       if ($exec_update_branch)
					      {
					        mysqli_query($db_connection, 'commit');
					        ?>
                               <script language="javascript">
						          opener.location.reload(true);
						          window.close();
					           </script>
				            <?php 
				          }
				       else
				          {
					        mysqli_query($db_connection, 'rollback');
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
					    $q_check_branch="select * from branch where branch_code='$branch_code'";
					    $exec_check_branch=mysqli_query($db_connection, $q_check_branch);
					    if (mysqli_num_rows($exec_check_branch)==0)
					       {
						     mysqli_query($db_connection, 'begin');
				             $q_update_branch="UPDATE branch SET branch_code='$branch_code', branch_name='$branch_name', branch_is_headquarter='$branch_is_headquarter',
						     		                  branch_address='$branch_address'
						  	                   WHERE branch_id='$branch_id'";				
                               //  echo $q_update_branch;
							 $exec_update_branch=mysqli_query($db_connection, $q_update_branch);
				             if ($exec_update_branch)
					            {
					              mysqli_query($db_connection, 'commit');
					              ?>
                                     <script language="javascript">
						               opener.location.reload(true);
						               window.close();
					                 </script>
				                  <?php 
				                }
				             else
				                {
					              mysqli_query($db_connection, 'rollback');
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
						          alert('Duplikasi Kode Kantor Cabang!');
								  opener.location.reload(true);
							      window.close();
					            </script>
				             <?php  
						   }
					 }	
			   } 
		  }	  
	 }
?>






