<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  
  if ($cru=='u')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_uom="SELECT uom_id, uom_code, uom_name
                      FROM uom
				      WHERE uom_id='$id'";
	//   echo $q_get_uom;
	   $exec_get_uom=mysqli_query($db_connection, $q_get_uom);
	   $total_uom=mysqli_num_rows($exec_get_uom);
	   $field_uom=mysqli_fetch_array($exec_get_uom);
	   if ($total_uom==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('uom yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $uom_id=$field_uom['uom_id'];
		    $uom_code=$field_uom['uom_code'];
			$uom_code_1=$field_uom['uom_code'];
			$uom_name=$field_uom['uom_name'];
		  }	  
	 }
  else
     $id="";
?>
<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_cru_uom" id="f_cru_uom">
  <table width="403" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH DATA SATUAN ITEM BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA SATUAN ITEM";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="33%">Kode Satuan</td>
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
				  echo $uom_code;
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
				  echo $uom_id;
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
				  echo $uom_code_1;
				}		
		 ?>"/>
      </label></td>
    </tr>
    

    <tr>
      <td>Nama Satuan </td>
      <td>:</td>
      <td><input name="txt_uom_name" type="text" id="txt_uom_name"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $uom_name;
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
	   $uom_id=htmlspecialchars($_POST['txt_id']);
	   $uom_code=htmlspecialchars($_POST['txt_code']);
	   $uom_name=htmlspecialchars($_POST['txt_uom_name']);
	   if (trim($uom_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode Satuan harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($uom_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Nama Satuan harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if ($uom_id=='')  //jika tambah data
	      {
		    $q_check_uom="select * from uom where uom_code='$uom_code'";
			$exec_check_uom=mysqli_query($db_connection, $q_check_uom);
			if (mysqli_num_rows($exec_check_uom)>0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Duplikasi Kode Satuan!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {
			      mysqli_autocommit($db_connection, false);
			      $q_input_uom="INSERT INTO uom (uom_code, uom_name) VALUES ('$uom_code', '$uom_name')";
				  $exec_input_uom=mysqli_query($db_connection, $q_input_uom);
				  if ($exec_input_uom)
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
					   echo $q_input_uom;
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
		    $q_check_uom_master_item="SELECT * FROM master_item WHERE uom_id_1='$uom_id' OR uom_id_2='$uom_id'";
		//	echo $q_check_uom_issuing;
			$exec_check_uom_master_item=mysqli_query($db_connection, $q_check_uom_master_item);
			if (mysqli_num_rows($exec_check_uom_issuing)>0)
			   {
			     ?>
				    <script language="javascript">
					  alert('Satuan Item tidak dapat diupdate!\nSudah digunakan pada transaksi Master Item!');
					</script>
				 <?php
			   }
			else
			   {   
  		         $q_check_uom="select * from uom where uom_id='$uom_id'";
			     $exec_check_uom=mysqli_query($db_connection, $q_check_uom);
			     if (mysqli_num_rows($exec_check_uom)>0)
			        {	
			          if ($uom_code==$uom_code_1)
			             {	 
			               mysqli_autocommit($db_connection, false);
				           $q_update_uom="UPDATE uom SET uom_name='$uom_name'
					     	  	          WHERE uom_id='$uom_id'";					
				       //    echo $q_update_uom;
						   $exec_update_uom=mysqli_query($db_connection, $q_update_uom);
				           if ($exec_update_uom)
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
					        $q_check_uom="select * from uom where uom_code='$uom_code'";
					        $exec_check_uom=mysqli_query($db_connection, $q_check_uom);
					        if (mysqli_num_rows($exec_check_uom)==0)
					           {
						         mysqli_autocommit($db_connection, false);
				                 $q_update_uom="UPDATE uom SET uom_code='$uom_code', uom_name='$uom_name'
						  	                    WHERE uom_id='$uom_id'";				
                               //  echo $q_update_uom;
							     $exec_update_uom=mysqli_query($db_connection, $q_update_uom);
				                 if ($exec_update_uom)
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
						               alert('Duplikasi Kode Satuan!');
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
		                 alert('Satuan Item yang akan diupdate tidak ditemukan!');
				         window.close();
			           </script>
		             <?php  
				  }	 
			   }     
		  }	  
	 }
?>






