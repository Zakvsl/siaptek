<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  $branch_id="";
  if ($cru=='u')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_employee="SELECT emp_id, emp_code, emp_name, emp_sex, emp_phone, emp_phone, emp_email, branch_id 
                      FROM employee
				      WHERE emp_id='$id'";
	//   echo $q_get_employee;
	   $exec_get_employee=mysqli_query($db_connection,$q_get_employee);
	   $total_employee=mysqli_num_rows($exec_get_employee);
	   $field_employee=mysqli_fetch_array($exec_get_employee);
	   if ($total_employee==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('employee yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $emp_id=$field_employee['emp_id'];
		    $emp_code=$field_employee['emp_code'];
			$emp_code_1=$field_employee['emp_code'];
			$emp_name=$field_employee['emp_name'];
			$emp_sex=$field_employee['emp_sex'];
			$emp_phone=$field_employee['emp_phone'];
			$emp_email=$field_employee['emp_email'];
			$branch_id=$field_employee['branch_id'];
		  }	  
	 }
  else
     $id="";
?>
<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_cru_employee" id="f_cru_employee">
  <table width="403" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH DATA KARYAWAN BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA KARYAWAN";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="33%" nowrap="nowrap">Kode Karyawan</td>
      <td width="1%">:</td>
      <td width="66%"><label>
      <input name="txt_code" type="text" id="txt_code" size="45" maxlength="25"   
	     value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $emp_code;
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
				  echo $emp_id;
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
				  echo $emp_code_1;
				}		
		 ?>"/>
      </label></td>
    </tr>
    

    <tr>
      <td nowrap="nowrap">Nama Karyawan</td>
      <td>:</td>
      <td><input name="txt_emp_name" type="text" id="txt_emp_name"  size="45"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $emp_name;
				}	
		 ?>"/></td>
    </tr>
    
    <tr>
      <td valign="top">Jenis Kelamin</td>
      <td valign="top">:</td>
      <td><?php
		     if ($cru=='i')
			    {
				  echo "<input id='rb_sex' name='rb_sex' type='radio' value='1' checked='checked'> Laki-Laki";
                  echo "<input id='rb_sex' name='rb_sex' type='radio' value='0'> Perempuan</td>";
				}
 			 else
			 if ($cru=='u')
			    {
				  if ($emp_sex=='1')
				     {
					   echo "<input id='rb_sex' name='rb_sex' type='radio' value='1' checked='checked'> Laki-Laki";
                       echo "<input id='rb_sex' name='rb_sex' type='radio' value='0'> Perempuan</td>";
					 }
				  else
				     {
					   echo "<input id='rb_sex' name='rb_sex' type='radio' value='1'> Laki-Laki";
                       echo "<input id='rb_sex' name='rb_sex' type='radio' value='0' checked='checked'> Perempuan</td>";
					 }	 
				}  
		  ?></td>
    </tr>
    
    
    <tr>
      <td>No Telpon</td>
      <td>:</td>
      <td><input name="txt_emp_phone" type="text" id="txt_emp_phone"  size="45"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $emp_phone;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td>Alamat Email</td>
      <td>:</td>
      <td><input name="txt_emp_email" type="text" id="txt_emp_email"  size="45"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $emp_email;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td nowrap="nowrap">Kantor Cabang</td>
      <td>:</td>
      <td><?php
	         $q_get_branch="SELECT branch_id, branch_code, branch_name FROM branch";
			 $exec_get_branch=mysqli_query($db_connection,$q_get_branch);
			 $total_branch=mysqli_num_rows($exec_get_branch);
	      ?> 
        <select id="s_branch" name="s_branch" style="width:300px">
          <?php
		    if ($total_branch=='0') 
			   {
			     echo "<option value='0'>--Pilih Kantor Cabang--</option>";
			   } 
			else
			   {
			     echo "<option value='0'>--Pilih Kantor Cabang--</option>";
			     while ($field_branch=mysqli_fetch_array($exec_get_branch))
				       {
					     if ($branch_id==$field_branch['branch_id'])
						     $selected="selected='selected'";
						 else
						     $selected='';
					     echo "<option value='".$field_branch['branch_id']."' $selected>".$field_branch['branch_code']." - ".$field_branch['branch_name']."</option>"; 
					   }
			   } 
		  ?>
	    </select>
      </td>
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
	   $emp_id=htmlspecialchars($_POST['txt_id']);
	   $emp_code=htmlspecialchars($_POST['txt_code']);
	   $emp_code_1=htmlspecialchars($_POST['txt_code_1']);
	   $emp_name=htmlspecialchars($_POST['txt_emp_name']);
	   $emp_sex=$_POST['rb_sex'];
	   $emp_phone=htmlspecialchars($_POST['txt_emp_phone']);
	   $emp_email=htmlspecialchars($_POST['txt_emp_email']);
	   $branch_id=$_POST['s_branch'];
	   $q_get_branch_office="SELECT * FROM branch WHERE branch_id='$branch_id'";
	   $exec_get_branch_office=mysqli_query($db_connection,$q_get_branch_office);
	   $total_branch_officer=mysqli_num_rows($exec_get_branch_office);
	   
	   if (trim($emp_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode Employee harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($emp_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Nama Employee harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else 
	   if ($branch_id=='0')
	      {
		    ?>
			  <script language="javascript">
			    alert('Kantor Cabang harus dipilih!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php
		  }
	   else	  
	   if ($emp_id=='')  //jika tambah data
	      {
		    $q_check_employee="select * from employee where emp_code='$emp_code' and branch_id='$branch_id'";
			//echo $q_check_employee;
			$exec_check_employee=mysqli_query($db_connection,$q_check_employee);
			if (mysqli_num_rows($exec_check_employee)>0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Duplikasi Kode Karyawan!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			if ($total_branch_officer==0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Kantor Cabang tidak ditemukan!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {
			      mysqli_autocommit($db_connection, false);
			      $q_input_employee="INSERT INTO employee (branch_id, emp_code, emp_name, emp_sex, emp_phone, emp_email)
					                           VALUES ('$branch_id', '$emp_code', '$emp_name', '$emp_sex', '$emp_phone', '$emp_email')";
				  $exec_input_employee=mysqli_query($db_connection,$q_input_employee);
				  if ($exec_input_employee)
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
					//   echo $q_input_employee;
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
		    $q_check_employee_dispossal="SELECT * FROM dispossal_header WHERE emp_id_dissposed_by='$emp_id'";
			$q_check_employee_issuing="SELECT * FROM issuing_header WHERE emp_id='$emp_id'";
			$q_check_employee_return="SELECT * FROM return_header WHERE emp_id_receiver_issuing='$emp_id'";
		//	echo $q_check_employee_issuing;
			$exec_check_employee_dispossal=mysqli_query($db_connection,$q_check_employee_dispossal);
			$exec_check_employee_issuing=mysqli_query($db_connection,$q_check_employee_issuing);
			$exec_check_employee_return=mysqli_query($db_connection,$q_check_employee_return);
			if ((mysqli_num_rows($exec_check_employee_dispossal)>0) && (mysqli_num_rows($exec_check_employee_issuing)>0) && (mysqli_num_rows($exec_check_employee_return)>0)) 
			   {
			     ?>
				    <script language="javascript">
					  alert('Karyawan tidak dapat diupdate!\nSudah digunakan pada transaksi Pengeluaran Aset, Pengembalian dan Penjualan Aset!');
					</script>
				 <?php
			   }
			else
			if ((mysqli_num_rows($exec_check_employee_dispossal)>0) && (mysqli_num_rows($exec_check_employee_issuing)>0) && (mysqli_num_rows($exec_check_employee_return)==0)) 
			   {
			     ?>
				    <script language="javascript">
					  alert('Karyawan tidak dapat diupdate!\nSudah digunakan pada transaksi Pengeluaran Aset dan Penjualan Aset!');
					</script>
				 <?php
			   }
			else
			if ((mysqli_num_rows($exec_check_employee_dispossal)>0) && (mysqli_num_rows($exec_check_employee_issuing)==0) && (mysqli_num_rows($exec_check_employee_return)>0)) 
			   {
			     ?>
				    <script language="javascript">
					  alert('Karyawan tidak dapat diupdate!\nSudah digunakan pada transaksi Pengembalian dan Penjualan Aset!');
					</script>
				 <?php
			   }
			else
			if ((mysqli_num_rows($exec_check_employee_dispossal)==0) && (mysqli_num_rows($exec_check_employee_issuing)>0) && (mysqli_num_rows($exec_check_employee_return)>0)) 
			   {
			     ?>
				    <script language="javascript">
					  alert('Karyawan tidak dapat diupdate!\nSudah digunakan pada transaksi Pengeluaran Aset dan Pengembalian!');
					</script>
				 <?php
			   }
			else
			if ((mysqli_num_rows($exec_check_employee_dispossal)>0) && (mysqli_num_rows($exec_check_employee_issuing)==0) && (mysqli_num_rows($exec_check_employee_return)==0)) 
			   {
			     ?>
				    <script language="javascript">
					  alert('Karyawan tidak dapat diupdate!\nSudah digunakan pada transaksi Penjualan Aset!');
					</script>
				 <?php
			   }
			else
			if ((mysqli_num_rows($exec_check_employee_dispossal)==00) && (mysqli_num_rows($exec_check_employee_issuing)>0) && (mysqli_num_rows($exec_check_employee_return)==0)) 
			   {
			     ?>
				    <script language="javascript">
					  alert('Karyawan tidak dapat diupdate!\nSudah digunakan pada transaksi Pengeluaran Aset!');
					</script>
				 <?php
			   }
			else
			if ((mysqli_num_rows($exec_check_employee_dispossal)==0) && (mysqli_num_rows($exec_check_employee_issuing)==0) && (mysqli_num_rows($exec_check_employee_return)>0)) 
			   {
			     ?>
				    <script language="javascript">
					  alert('Karyawan tidak dapat diupdate!\nSudah digunakan pada transaksi Pengembalian Aset!');
					</script>
				 <?php
			   }
			else
			if ($total_branch_officer==0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Kantor Cabang tidak ditemukan!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {   
  		         $q_check_employee="select * from employee where emp_id='$emp_id'";
			     $exec_check_employee=mysqli_query($db_connection,$q_check_employee);
			     if (mysqli_num_rows($exec_check_employee)>0)
			        {	
			          if ($emp_code==$emp_code_1)
			             {	 
			               mysqli_autocommit($db_connection, false);
				           $q_update_employee="UPDATE employee SET emp_name='$emp_name', emp_sex='$emp_sex', emp_phone='$emp_phone', emp_email='$emp_email', 
						                              branch_id='$branch_id'
						                       WHERE emp_id='$emp_id'";					
				        //   echo $q_update_employee;
						   $exec_update_employee=mysqli_query($db_connection,$q_update_employee);
				           if ($exec_update_employee)
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
					        $q_check_employee="select * from employee where emp_code='$emp_code'";
					        $exec_check_employee=mysqli_query($db_connection,$q_check_employee);
					        if (mysqli_num_rows($exec_check_employee)==0)
					           {
						         mysqli_autocommit($db_connection, false);
				                 $q_update_employee="UPDATE employee SET emp_code='$emp_code', emp_name='$emp_name', emp_sex='$emp_sex', emp_phone='$emp_phone', 
								                            emp_email='$emp_email', branch_id='$branch_id'
						                             WHERE emp_id='$emp_id'";				
                               //  echo $q_update_employee;
							     $exec_update_employee=mysqli_query($db_connection,$q_update_employee);
				                 if ($exec_update_employee)
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
						               alert('Duplikasi Kode Karyawan!');
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
		                 alert('Karyawan yang akan diupdate tidak ditemukan!');
				         window.close();
			           </script>
		             <?php  
				  }	 
			   }     
		  }	  
	 }
?>






