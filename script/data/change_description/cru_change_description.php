<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
?>
 <link type="text/css" rel="stylesheet" href="../../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../../library/development-bundle/ui/ui.core.js"></script>
    <script src="../../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_cidh_date").datepicker(
							 {
                               dateFormat : "dd-mm-yy",
                               changeMonth : true,
                               changeYear : true
                             }
							                          );				  
                          }
						  );
    </script>
<?php
  include "../../library/db_connection.php";	
  include "../../library/library_function.php";
  include "../../library/style.css";
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  $cidh_is_canceled="Tidak";
  
  if ($cru=='u' || $cru=='d')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_change_description="SELECT cidh_id, cidh_code, cidh_date, 
                                    CASE cidh_is_canceled
                                         WHEN '0' THEN 'Tidak'
                                         WHEN '1' THEN 'Ya'
                                    END cidh_is_canceled, cidh_canceled_date, cidh_canceled_reason, cidh_notes, created_by, created_time, updated_by, updated_time
                              FROM change_item_description_header
                              WHERE branch_id='$branch_id' AND cidh_id='$id'";
	   $exec_get_change_description=mysqli_query($db_connection, $q_get_change_description);
	   $total_get_change_description=mysqli_num_rows($exec_get_change_description);
	   $field_change_description=mysqli_fetch_array($exec_get_change_description);
	   if ($total_get_change_description==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Transaksi Perubahan Deskripsi Aset yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $q_get_change_description_detail="SELECT cidd_id, change_item_description_detail.itemd_id, itemd_code, masti_id_old, masti_id_new, uom_id_1,uom_id_2,
                                                     CONCAT('Deskripsi Isi Aset : ',masti_name,' | Isi : ',masti_capacity,' ', 
													        (SELECT uom_name FROM uom WHERE uom_id=uom_id_1),' | Kategori : ',cati_name) AS masti_name_1, itemd_serial_no, 
													 cidd_is_canceled, cidd_notes, masti_name
                                              FROM change_item_description_detail 
                                              INNER JOIN change_item_description_header ON change_item_description_header.cidh_id=change_item_description_detail.cidh_id 
                                              INNER JOIN item_detail ON item_detail.itemd_id=change_item_description_detail.itemd_id 
                                              INNER JOIN master_item ON master_item.masti_id=change_item_description_detail.masti_id_old
                                              INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                              WHERE change_item_description_header.branch_id='$branch_id' AND change_item_description_detail.cidh_id='$id'
											  ORDER BY masti_name ASC";			
			$exec_get_change_description_detail=mysqli_query($db_connection, $q_get_change_description_detail);
			$cidh_id=$field_change_description['cidh_id'];
		    $trans_code=$field_change_description['cidh_code'];
			$trans_code_1=$field_change_description['cidh_code'];
			$cidh_date=get_date_1($field_change_description['cidh_date']);
			$cidh_notes=$field_change_description['cidh_notes'];
			$cidh_is_canceled=$field_change_description['cidh_is_canceled'];
			$cidh_canceled_date=$field_change_description['cidh_canceled_date'];
			$cidh_canceled_reason=$field_change_description['cidh_canceled_reason'];
			$q_get_maker="SELECT users_names FROM users WHERE users_id='".$field_change_description['created_by']."'";
			$exec_get_maker=mysqli_query($db_connection, $q_get_maker);
			$field_maker=mysqli_fetch_array($exec_get_maker);
			$created_by=$field_maker['users_names']." ".$field_change_description['created_time'];
			if ($field_change_description['created_by']!='')
			   {
			     $q_get_updater="SELECT users_names FROM users WHERE users_id='".$field_change_description['updated_by']."'";
			     $exec_get_updater=mysqli_query($db_connection, $q_get_updater);
			     $field_maker=mysqli_fetch_array($exec_get_updater);
			     $updated_by=$field_maker['users_names']." ".$field_change_description['updated_time'];
			   }
			else
			   $updated_by='-';
		  }	  
	 }
  else
     {
	   $id="";
       $trans_code=get_format_number($db_connection, 'CHG',$branch_id);
	 }
 // echo $q_get_change_description_detail;
  $current_date=date('d-m-Y');
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_change_description" id="f_cru_change_description" onSubmit="">
  <table width="1270" border="0" cellspacing="1" cellpadding="1" id="tbl_broken">
    <tr>
      <th colspan="6" scope="col" align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch">
          <option value="<?php echo $field_branch['branch_id'];?>" style="width:300px"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></th>
    </tr>
    <tr>
      <th colspan="6" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH TRANSAKSI PERUBAHAN DESKRIPSI ASET BARU";
															 }
														  else
														  if ($cru=='u' || $cru=='d')
														     {
															   echo "UPDATE TRANSAKSI PERUBAHAN DESKRIPSI ASET";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td>No Transaksi * </td>
      <td>:</td>
      <td><input name="txt_code" type="text" id="txt_code" size="30" maxlength="50"   
	     value="<?php 
		     if ($cru=='i')
			    {
				  echo $trans_code;
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $trans_code;
				}		
		 ?>">
        <input name="txt_id" type="hidden" id="txt_id" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $cidh_id;
				}		
		 ?>"/>
        <input name="txt_code_1" type="hidden" id="txt_code_1" <?php 
		     if ($cru=='i')
			    {
				  echo "value=''";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo "value='$trans_code_1'";
				}		
		 ?>/></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    <td>    </tr>
    <tr>
      <td width="16%" valign="top">Tanggal * </td>
      <td width="0%" valign="top">:</td>
      <td width="39%" valign="top"><input name="txt_cidh_date" type="text" id="txt_cidh_date"  readonly="readonly" size="10" 
		 <?php 
			if ($id!='')
				echo "value=".$cidh_date; 
			else 
		        echo "value='$current_date'";
		 ?>></td>
      <td width="18%" valign="top">&nbsp;</td>
      <td width="0%" valign="top">&nbsp;</td>
      <td width="26%" valign="top">&nbsp;</td>
    </tr>
    
    <tr>
      <td valign="top">Keterangan</td>
      <td valign="top">:</td>
      <td valign="top"><textarea name="txt_cidh_notes" id="txt_cidh_notes" cols="35"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $cidh_notes;
				}
		 ?></textarea></td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Dibatalkan</td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "Tidak";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $cidh_is_canceled;
				}	
		 ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Alasan Pembatalan</td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "-";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $cidh_canceled_reason;
				}	
		 ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td></td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top"></td>
    </tr>
    <tr>
      <td><input type="button" id="btn_add" name="btn_add" value="Tambah" onClick="call_add_data()">
	      <input type="button" id="btn_delete" name="btn_delete" value="Hapus" onClick="call_delete_data()">
	      <input type="hidden" name="txt_rows" id="txt_rows" <?php if ($id!='')
			           {
					       echo "value=".mysqli_num_rows($exec_get_change_description_detail);	  
				       }	  
			        else
			           {
				         echo "value='0'";
					   }	 
			   ?>>      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td colspan="6">
	  <div STYLE=" height: 100%; width: 100%; font-size: 12px; overflow: auto;">
	  <table width="100%" border="0" cellspacing="1" cellpadding="1" id="tbl_detail" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col">
            <input type="checkbox" id="check_all_data" name="check_all_data" value="1" onClick="select_unselect_all()"/></th>
          <th width="13%" scope="col">Kode Aset </th>
          <th width="29%" scope="col">Deskripsi Isi Aset  Lama</th>
          <th width="29%" scope="col">Deskripsi Isi Aset Baru</th>
          <th width="12%" scope="col">Serial No</th>
          <th width="6%" scope="col">Dibatalkan</th>
          <th width="19%" scope="col">Notes</th>
        </tr>
        <?php  		   
			  if ($id!='')
			     {					
				   $no=0;
				   while ($field_get_change_description_detail=mysqli_fetch_array($exec_get_change_description_detail)) 
				         {
						   $no++;
						   $cidd_id=$field_get_change_description_detail['cidd_id']; 
						   $itemd_id= $field_get_change_description_detail['itemd_id']; 
						   $itemd_code=$field_get_change_description_detail['itemd_code'];
						   $masti_id_old=$field_get_change_description_detail['masti_id_old'];
						   $masti_name=$field_get_change_description_detail['masti_name'];
						   $itemd_serial_no=$field_get_change_description_detail['itemd_serial_no'];
						   $masti_id_new=$field_get_change_description_detail['masti_id_new'];
						   $cidd_is_canceled=$field_get_change_description_detail['cidd_is_canceled'];
					       $cidd_notes=$field_get_change_description_detail['cidd_notes'];
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $itemd_id;?>"/></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><select id="s_old_item_name_<?php echo $itemd_id;?>" name="s_old_item_name_<?php echo $itemd_id;?>" style="width:300px">
                                               <option value="<?php echo $masti_id_old;?>"><?php echo $masti_name;?></option>
									   </select></td>
                     <td class="td_lb"><select id="s_new_item_name_<?php echo $itemd_id;?>" name="s_new_item_name_<?php echo $itemd_id;?>" 
					                    <?php if ($cidd_is_canceled=='1') echo "disabled='disabled'";?> style="width:300px">
					                     <?php
										   $q_get_master_item='SELECT masti_id, masti_code, masti_name, CONCAT("Deskripsi Isi Aset : ", masti_name," | Isi : ",
														              masti_capacity," ",(SELECT uom_name FROM uom WHERE uom_id=uom_id_1)," | Kategori : ",cati_name) AS 
																	  masti_name_1
                                                               FROM master_item
                                                               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
															   ORDER BY masti_name ASC';
										   $exec_get_master_item=mysqli_query($db_connection, $q_get_master_item);
										   echo "<option value='0'>-Pilih Nama item-</option>";
										   if (mysqli_num_rows($exec_get_master_item)>0)
										      {
											    while ($field_data_master_item=mysqli_fetch_array($exec_get_master_item))
												      {
													    if ($masti_id_new==$field_data_master_item['masti_id'])
														    $selected="selected='selected'";
													    else
														    $selected='';
														echo "<option value='".$field_data_master_item['masti_id']."' $selected>".$field_data_master_item['masti_name_1']."</option>";		
													  }
											  }
										 ?>
					                   </select></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_cidd_is_canceled_<?php echo $itemd_id;?>" name="cb_cidd_is_canceled_<?php echo $itemd_id;?>" 
					                    value="1" <?php if ($cidd_is_canceled=='1') 
										                   {
														     echo "checked='checked' disabled='disabled'";
														   }  ?> onClick="call_canceled_item('<?php echo $itemd_id;?>','<?php echo $cidd_id;?>')"><?php if ($cidd_is_canceled=='1') 
														                   echo "Ya"; 
																	   else 
																	       echo "Tidak";?></td>
					 <td class="td_lb"><input type="text" id="cidd_notes_<?php echo $itemd_id;?>" name="cidd_notes_<?php echo $itemd_id;?>" 
					                    value="<?php echo $cidd_notes;?>"  <?php if ($cidd_is_canceled=='1') echo "readonly='readonly'"; ?> size="35"></td>
				 </tr>	     	
		    <?php		   
						 }	   
			     }	
			?>	
      </table>
	  </div></td>
    </tr>
    <tr>
      <td>Dibuat Oleh </td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $created_by;
				}		
		 ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right"><input name="btn_save" type="button" id="btn_save" value="Simpan" onClick="input_data()" <?php if ($cidh_is_canceled=='Ya' || $cru=='d') echo "disabled='disabled'";?>/>
        <input name="btn_new" type="reset" id="btn_new" value="Baru" <?php if ($cru=='u' || $cru=='d') echo "disabled='disabled'"; ?>/>
        <input name="btn_close" type="button" id="btn_close" value="Tutup" onClick="window.close()"/></td>
    </tr>
    <tr>
      <td>Diupdate Oleh </td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $updated_by;
				}		
		 ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right">&nbsp;</td>
    </tr>
  </table>
</form>

<script language="javascript">
  function input_data()
           {
		     var branch_id='<?php echo $branch_id;?>';
			 var cidh_code=document.getElementById('txt_code').value.trim(); 
			 var date_cidh=document.getElementById('txt_cidh_date').value.trim();
			 var d=date_cidh.substring(0,2);
			 var m=date_cidh.substring(3,5);
			 var y=date_cidh.substring(6,10);
			 var cidh_date=Date.parse(y+'-'+m+'-'+d);
			 var total_detail=document.getElementById('txt_rows').value;
				
			 	
			 if (cidh_code=='')
			    {
				  alert('No Transaksi harus diisi!');
				  return (false)
				}
		     else
			 if (date_cidh=='')		
			    {
				  alert('Tanggal Perubahan Deskripsi Aset harus diisi!');
				  return (false)
				}
			 else
			 if (total_detail<=0) 
			    {
				  alert('Detail Data harus diisi!');
				  return (false);
				}
			 else	
			    { 
				  if (total_detail>0)
			         { 
					   var x='<?php echo $cru;?>';
					   var k=0;
				       chk_data=document.getElementsByName('cb_data[]');
				       for (i=0; i<document.getElementsByName('cb_data[]').length;i++)
					       {
					         chk_data[i].checked=true;	
							 y='cb_cidd_is_canceled_'+chk_data[i].value;
							 z='s_new_item_name_'+chk_data[i].value;
							 if (x=='u')
							    {
							      document.getElementById(y).disabled=false;
								  document.getElementById(z).disabled=false;
								}  
							 if (document.getElementById(z).value=='0')
							     k++;
						   }	 
				     }  
				  if (k>0)
				     {
					   alert('Nama Item Baru harus dipilih!');
					 }
				  else
				     {	 
			           f_cru_change_description.action='../../data/change_description/input_change_description.php?b='+branch_id;
				       f_cru_change_description.submit(); 
					 }
				}  
		   }
		   
  function call_add_data()
           {
		     var w=1500;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
			 var t=(screen.height/2)-(h/2);
			 open_child=window.open("../../data/change_description/pick_tube.php", "f_pick_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0,scrollbars=Yes");
		   }
		   
  function call_delete_data()
           {
		     var objTable = document.getElementById('tbl_detail');
			 var objTR, objTD, intIndex;
			 var chks = document.getElementsByName('cb_data[]');
			 if (typeof(chks) == 'object') 
			    {
				  for (i = chks.length - 1; i >= 0; i--) 
				      {
					    if (chks[i].checked) 
					       {
						     x=chks[i].value;
						     objTable.deleteRow(i + 1);
						     document.getElementById("txt_rows").value = parseInt(document.getElementById("txt_rows").value) - 1;
					       }
				      }
			    } 
		   }		  
			
  function select_unselect_all()
           {
		     var check_select=document.getElementsByName('check_all_data');
		     var select_unselect_all_data = document.getElementsByName('cb_data[]');
			 
             for (i = 0; i<select_unselect_all_data.length; i++)
			     {
				   if (check_select[0].checked==true)
				      {
                        select_unselect_all_data[i].checked = true ;
					  }
				   else
				      {
					    select_unselect_all_data[i].checked = false ;
					  }	  	
				 }   
		   }
		   	    
		   
  function call_validation_number(number)
           {
			  if (!/^[0-9.]+$/.test(number.value))
	             {
	               number.value = number.value.substring(0,number.value.length-1000);
	             }
		   }		

  function call_canceled_item(x,y)
           {
		     var z=document.getElementById('cb_cidd_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_change_description.action='../../data/change_description/canceled_item.php?id='+y;
				       f_cru_change_description.submit();
					 }
				  else
				     z.checked==false;
				}  
		   }	
		   
  function rounding(num) 
           {    
              return +(Math.round(num + "e+2")  + "e-2");
           }
</script>






