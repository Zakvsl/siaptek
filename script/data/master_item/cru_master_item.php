<script language="javascript">
   function validAngka(a)
           {
	         if (!/^[0-9.]+$/.test(a.value))
	            {
	              a.value = a.value.substring(0,a.value.length-1000);
	            }
           }
</script>

<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  if ($cru=="u")
      $id=mysqli_real_escape_string($db_connection, $_GET['id']);
  else
      $id="";
  
  if ($cru=='u')
     {
	   $q_get_master_item="SELECT masti_id, masti_code, masti_name, masti_capacity, uom_id_1, cati_id
                           FROM master_item
				           WHERE masti_id='$id'";
	   //echo $q_get_master_item;
	   $exec_get_master_item=mysqli_query($db_connection, $q_get_master_item);
	   $total_master_item=mysqli_num_rows($exec_get_master_item);
	   $field_master_item=mysqli_fetch_array($exec_get_master_item);
	   if ($total_master_item==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Master Item yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $masti_id=$field_master_item['masti_id'];
		    $masti_code=$field_master_item['masti_code'];
			$masti_code_1=$field_master_item['masti_code'];
			$masti_name=$field_master_item['masti_name'];
			$masti_capacity=$field_master_item['masti_capacity']; 
			$uom_id_1=$field_master_item['uom_id_1'];
			//$uom_id_2=$field_master_item['uom_id_2'];
			$cati_id=$field_master_item['cati_id'];
		  }	  
	 }
?>
<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_master_item" id="f_cru_master_item">
  <table width="403" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH DATA DESKRIPSI ISI ASET BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA DESKRIPSI ISI ASET";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="33%" nowrap="nowrap">Kode Isi Aset </td>
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
				  echo $masti_code;
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
				  echo $masti_id;
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
				  echo $masti_code_1;
				}		
		 ?>"/>
      </label></td>
    </tr>
    

    <tr>
      <td nowrap="nowrap">Deskripsi Isi Aset </td>
      <td>:</td>
      <td><input name="txt_masti_name" type="text" id="txt_masti_name"  size="40"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $masti_name;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td valign="top">Isi</td>
      <td valign="top">:</td>
      <td><input name="txt_masti_capacity" type="text" id="txt_masti_capacicty"  size="15" onKeyUp="validAngka(this)"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $masti_capacity;
				}	
		 ?>"/>
        <select id="s_uom_1" name="s_uom_1">
          <?php
		      $q_get_uom_1="SELECT uom_id, uom_code, uom_name FROM uom";
			  $exec_get_uom_1=mysqli_query($db_connection, $q_get_uom_1);
			  $total_get_uom_1=mysqli_num_rows($exec_get_uom_1);
	          if ($cru=='i')
			     {
			       echo "<option value='0'>-Pilih Satuan-</option>";
				   if ($total_get_uom_1>0)
				      {
					    while ($field_uom_1=mysqli_fetch_array($exec_get_uom_1))
					           echo "<option value='".$field_uom_1['uom_id']."'>".$field_uom_1['uom_code']." - ".$field_uom_1['uom_name']."</option>";
					  }
			     }
		      else
			  if ($cru=='u')
			     {
			       echo "<option value='0'>-Pilih Satuan-</option>";
			       if ($total_get_uom_1>0)
				      {
					    while ($field_uom_1=mysqli_fetch_array($exec_get_uom_1))
					          {
							    if ($uom_id_1!='')
							       {
								     if ($uom_id_1==$field_uom_1['uom_id'])
								         $selected="selected='selected'";
								     else
								         $selected="";
								   }  
					            echo "<option value='".$field_uom_1['uom_id']."' $selected>".$field_uom_1['uom_code']." - ".$field_uom_1['uom_name']."</option>";
							  } 
					  }	
			     }
			?>
        </select></td>
    </tr>
    
    <tr>
      <td valign="top">Kategori</td>
      <td valign="top">:</td>
      <td><select id="s_category" name="s_category" style="size:300px">
        <?php
		      $q_get_cati="SELECT cati_id, cati_code, cati_name FROM category_item";
			  $exec_get_cati=mysqli_query($db_connection, $q_get_cati);
			  $total_get_cati=mysqli_num_rows($exec_get_cati);
	          if ($cru=='i')
			     {
			       echo "<option value='0'>-Pilih Kategori-</option>";
				   if ($total_get_cati>0)
				      {
					    while ($field_cati=mysqli_fetch_array($exec_get_cati))
					           echo "<option value='".$field_cati['cati_id']."'>".$field_cati['cati_code']." - ".$field_cati['cati_name']."</option>";
					  }
			     }
		      else
			  if ($cru=='u')
			     {
			       echo "<option value='0'>-Pilih Satuan-</option>";
			       if ($total_get_cati>0)
				      {
					    while ($field_cati=mysqli_fetch_array($exec_get_cati))
					          {
							    if ($cati_id!='')
							       {
								     if ($cati_id==$field_cati['cati_id'])
								         $selected="selected='selected'";
								     else
								         $selected="";
								   }  
					            echo "<option value='".$field_cati['cati_id']."' $selected>".$field_cati['cati_code']." - ".$field_cati['cati_name']."</option>";
							  } 
					  }	
			     }
			?>
      </select></td>
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
	   $masti_id=htmlspecialchars($_POST['txt_id']);
	   $masti_code=htmlspecialchars($_POST['txt_code']);
	   $masti_code_1=htmlspecialchars($_POST['txt_code_1']);
	   $masti_name=htmlspecialchars($_POST['txt_masti_name']);
	   $masti_capacity=htmlspecialchars($_POST['txt_masti_capacity']);
	   $uom_id_1=$_POST['s_uom_1'];
	  // $uom_id_2=$_POST['s_uom_2'];
	   $cati_id=$_POST['s_category'];
	   $q_get_uom_1="SELECT * FROM uom WHERE uom_id='$uom_id_1'";
	   $q_get_category="SELECT * FROM category_item WHERE cati_id='$cati_id'";
	   $exec_get_uom_1=mysqli_query($db_connection, $q_get_uom_1);
	   $exec_get_category=mysqli_query($db_connection, $q_get_category);
	   $total_uom_1=mysqli_num_rows($exec_get_uom_1);
	   $total_category=mysqli_num_rows($exec_get_category);
	   
	   if (trim($masti_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode isi aset harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($masti_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Deskripsi isi aset harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else  
	   if ($masti_capacity<=0 || $masti_capacity=="")
	      {
		    ?>
			  <script language="javascript">
			    alert('Isi harus lebih besar atau sama dengan 0!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if ($total_uom_1==0)
	      {
		    ?>
			  <script language="javascript">
			    alert('Satuan tidak ditemukan!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else	 
	   if ($total_category==0)
	      {
		    ?>
			  <script language="javascript">
			    alert('Kategori tidak ditemukan!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else	
	   if ($masti_id=='')  //jika tambah data
	      {
		    $q_check_master_item="select * from master_item where masti_code='$masti_code'";
			$exec_check_master_item=mysqli_query($db_connection, $q_check_master_item);
			if (mysqli_num_rows($exec_check_master_item)>0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Duplikasi kode isi aset!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {
			      mysqli_autocommit($db_connection, false);
			      $q_input_master_item="INSERT INTO master_item (masti_code, masti_name, masti_capacity, uom_id_1,  cati_id)
					                                     VALUES ('$masti_code', '$masti_name', '$masti_capacity','$uom_id_1', '$cati_id')";
				  $exec_input_master_item=mysqli_query($db_connection, $q_input_master_item);
				  //echo $q_input_master_item;
				  if ($exec_input_master_item)
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
				//	   echo $q_input_master_item;
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
		    $q_check_master_item_item_detail="SELECT * FROM item_detail WHERE masti_id='$masti_id'";
		//	echo $q_check_master_item_issuing;
			$exec_check_master_item_item_detail=mysqli_query($db_connection, $q_check_master_item_item_detail);
			if (mysqli_num_rows($exec_check_master_item_item_detail)>0)
			   {
			     ?>
				    <script language="javascript">
					  alert('Deskripsi isi aset tidak dapat diupdate!\nSudah digunakan pada Aset!');
					</script>
				 <?php
			   }
			else
			   {   
  		         $q_check_master_item="select * from master_item where masti_id='$masti_id'";
			     $exec_check_master_item=mysqli_query($db_connection, $q_check_master_item);
			     if (mysqli_num_rows($exec_check_master_item)>0)
			        {	
			          if ($masti_code==$masti_code_1)
			             {	 
			               mysqli_autocommit($db_connection, false);
				           $q_update_master_item="UPDATE master_item SET masti_name='$masti_name', masti_capacity='$masti_capacity', 
								                  uom_id_1='$uom_id_1', cati_id='$cati_id'
						  	                      WHERE masti_id='$masti_id'";					
				       //    echo $q_update_master_item;
						   $exec_update_master_item=mysqli_query($db_connection, $q_update_master_item);
				           if ($exec_update_master_item)
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
					        $q_check_master_item="select * from master_item where masti_code='$masti_code'";
					        $exec_check_master_item=mysqli_query($db_connection, $q_check_master_item);
					        if (mysqli_num_rows($exec_check_master_item)==0)
					           {
						         mysqli_autocommit($db_connection, false);
				                 $q_update_master_item="UPDATE master_item SET masti_code='$masti_code', masti_name='$masti_name', masti_capacity='$masti_capacity', 
								                        uom_id_1='$uom_id_1', cati_id='$cati_id'
						  	                            WHERE masti_id='$masti_id'";				
                               //  echo $q_update_master_item;
							     $exec_update_master_item=mysqli_query($db_connection, $q_update_master_item);
				                 if ($exec_update_master_item)
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
						               alert('Duplikasi isi aset!');
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
		                 alert('Deskripsi aset yang akan diupdate tidak ditemukan!');
				         window.close();
			           </script>
		             <?php  
				  }	 
			   }     
		  }	  
	 }
?>






