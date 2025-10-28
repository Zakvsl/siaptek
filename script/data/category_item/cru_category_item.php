<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  if ($cru=='u')
      $id=mysqli_real_escape_string($db_connection, $_GET['id']);
  else
      $id="";
  
  if ($cru=='u')
     {
	   $q_get_category_item="SELECT cati_id, cati_code, cati_name, cati_notes 
                             FROM category_item
				             WHERE cati_id='$id'";
	//   echo $q_get_category_item;
	   $exec_get_category_item=mysqli_query($db_connection, $q_get_category_item);
	   $total_category_item=mysqli_num_rows($exec_get_category_item);
	   $field_category_item=mysqli_fetch_array($exec_get_category_item);
	   if ($total_category_item==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Kategori Aset yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $cati_id=$field_category_item['cati_id'];
		    $cati_code=$field_category_item['cati_code'];
			$cati_code_1=$field_category_item['cati_code'];
			$cati_name=$field_category_item['cati_name'];
			$cati_notes=$field_category_item['cati_notes']; 
		  }	  
	 }
?>
<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_category_item" id="f_cru_category_item">
  <table width="364" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH DATA KATEGORI ASET BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA KATEGORI ASET";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="33%">Kode Kategori</td>
      <td width="1%">:</td>
      <td width="66%"><label>
      <input name="txt_code" type="text" id="txt_code" size="30" maxlength="50"   
	     value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cati_code;
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
				  echo $cati_id;
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
				  echo $cati_code_1;
				}		
		 ?>"/>
      </label></td>
    </tr>
    

    <tr>
      <td nowrap="nowrap">Nama Kategori</td>
      <td>:</td>
      <td><input name="txt_cati_name" type="text" id="txt_cati_name"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cati_name;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td valign="top">Keterangan</td>
      <td valign="top">:</td>
      <td><label>
        <textarea id="txt_cati_notes" name="txt_cati_notes" cols="30"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $cati_notes;
				}	
		 ?></textarea>
      </label></td>
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
	   $cati_id=htmlspecialchars($_POST['txt_id']);
	   $cati_code=trim(htmlspecialchars($_POST['txt_code']));
	   $cati_code_1=trim(htmlspecialchars($_POST['txt_code_1']));
	   $cati_name=htmlspecialchars($_POST['txt_cati_name']);
	   $cati_notes=htmlspecialchars($_POST['txt_cati_notes']);
	   
	   if (trim($cati_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode Kategori harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($cati_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Nama Kategori harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else  
	   if ($cati_id=='')  //jika tambah data
	      {
		    $q_check_category_item="select * from category_item where cati_code='$cati_code'";
			$exec_check_category_item=mysqli_query($db_connection, $q_check_category_item);
			if (mysqli_num_rows($exec_check_category_item)>0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Duplikasi Kode Kategori!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {
			      mysqli_autocommit($db_connection, false);
			      $q_input_category_item="INSERT INTO category_item (cati_code, cati_name, cati_notes)
					                                         VALUES ('$cati_code', '$cati_name', '$cati_notes')";
				  $exec_input_category_item=mysqli_query($db_connection, $q_input_category_item);
				  if ($exec_input_category_item)
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
				//	   echo $q_input_category_item;
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
		    $q_check_category_item_master_item="SELECT * FROM master_item WHERE cati_id='$cati_id'";
		//	echo $q_check_category_item_issuing;
			$exec_check_category_item_master_item=mysqli_query($db_connection, $q_check_category_item_master_item);
			if (mysqli_num_rows($exec_check_category_item_master_item)>0)
			   {
			     ?>
				    <script language="javascript">
					  alert('Kategori Aset tidak dapat diupdate!\nSudah digunakan pada Master Item!');
					</script>
				 <?php
			   }
			else
			   {   
  		         $q_check_category_item="select * from category_item where cati_id='$cati_id'";
			     $exec_check_category_item=mysqli_query($db_connection, $q_check_category_item);
			     if (mysqli_num_rows($exec_check_category_item)>0)
			        {	
			          if ($cati_code==$cati_code_1)
			             {	 
			               mysqli_autocommit($db_connection, false);
				           $q_update_category_item="UPDATE category_item SET  cati_name='$cati_name', cati_notes='$cati_notes'
					     	  	                    WHERE cati_id='$cati_id'";					
				       //    echo $q_update_category_item;
						   $exec_update_category_item=mysqli_query($db_connection, $q_update_category_item);
				           if ($exec_update_category_item)
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
					        $q_check_category_item="select * from category_item where cati_code='$cati_code'";
					        $exec_check_category_item=mysqli_query($db_connection, $q_check_category_item);
					        if (mysqli_num_rows($exec_check_category_item)==0)
					           {
						         mysqli_autocommit($db_connection, false);
				                 $q_update_category_item="UPDATE category_item SET cati_code='$cati_code', cati_name='$cati_name', cati_notes='$cati_notes'
						  	                              WHERE cati_id='$cati_id'";				
                               //  echo $q_update_category_item;
							     $exec_update_category_item=mysqli_query($db_connection, $q_update_category_item);
				                 if ($exec_update_category_item)
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
						               alert('Duplikasi Kode Kategori!');
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
		                 alert('Kategori yang akan diupdate tidak ditemukan!');
				         window.close();
			           </script>
		             <?php  
				  }	 
			   }     
		  }	  
	 }
?>






