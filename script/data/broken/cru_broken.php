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
                            $("#txt_brokh_date").datepicker(
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
  $brokh_is_canceled='Tidak';
  if ($cru=='u' || $cru=='d')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_broken_header="SELECT brokh_id, brokh_code, brokh_date, 
                                     CASE brokh_status
                                          WHEN '0' THEN 'Tidak Dijual'
                                          WHEN '1' THEN 'Dijual Sebagian'
                                          WHEN '2' THEN 'Dijual Semua'
                                    END brokh_status, 
                                    CASE brokh_is_canceled
                                         WHEN '0' THEN 'Tidak'
                                         WHEN '1' THEN 'Ya'
                                    END brokh_is_canceled, brokh_canceled_date, brokh_canceled_reason, brokh_notes, created_by, created_time, updated_by, updated_time
                              FROM broken_header
                              WHERE branch_id='$branch_id' AND brokh_id='$id'";
	   $exec_get_broken_header=mysqli_query($db_connection, $q_get_broken_header);
	   $total_get_broken_header=mysqli_num_rows($exec_get_broken_header);
	   $field_broken_header=mysqli_fetch_array($exec_get_broken_header);
	   if ($total_get_broken_header==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Transaksi Kerusakan Aset yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $q_get_broken_detail="SELECT brokd_id, broken_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty,                                          brokd_notes, brokd_is_canceled
                                   FROM broken_detail
                                   INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
                                   INNER JOIN item_detail ON item_detail.itemd_id=broken_detail.itemd_id
								   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                   INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                   WHERE broken_header.branch_id='$branch_id' AND broken_detail.brokh_id='$id'";				
			$exec_get_broken_detail=mysqli_query($db_connection, $q_get_broken_detail);
			$brokh_id=$field_broken_header['brokh_id'];
		    $trans_code=$field_broken_header['brokh_code'];
			$trans_code_1=$field_broken_header['brokh_code'];
			$brokh_date=get_date_1($field_broken_header['brokh_date']);
			$brokh_status=$field_broken_header['brokh_status'];
			$brokh_notes=$field_broken_header['brokh_notes'];
			$brokh_is_canceled=$field_broken_header['brokh_is_canceled'];
			$brokh_canceled_date=$field_broken_header['brokh_canceled_date'];
			$brokh_canceled_reason=$field_broken_header['brokh_canceled_reason'];
			$q_get_maker="SELECT users_names FROM users WHERE users_id='".$field_broken_header['created_by']."'";
			$exec_get_maker=mysqli_query($db_connection, $q_get_maker);
			$field_maker=mysqli_fetch_array($exec_get_maker);
			$created_by=$field_maker['users_names']." ".$field_broken_header['created_time'];
			if ($field_broken_header['created_by']!='')
			   {
			     $q_get_updater="SELECT users_names FROM users WHERE users_id='".$field_broken_header['updated_by']."'";
			     $exec_get_updater=mysqli_query($db_connection, $q_get_updater);
			     $field_maker=mysqli_fetch_array($exec_get_updater);
			     $updated_by=$field_maker['users_names']." ".$field_broken_header['updated_time'];
			   }
			else
			   $updated_by='-';
		  }	  
	 }
  else
     {
	   $id="";
       $trans_code=get_format_number($db_connection, 'BRO',$branch_id);
	 }
 // echo $q_get_broken_detail;
  $current_date=date('d-m-Y');
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_broken" id="f_cru_broken" onSubmit="">
  <table width="1280" border="0" cellspacing="1" cellpadding="1" id="tbl_broken">
    <tr>
      <th colspan="6" scope="col" align="right">Kantor Cabang    :
        <select id="s_branch_trans" name="s_branch_trans" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></th>
    </tr>
    <tr>
      <th colspan="6" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH TRANSAKSI KERUSAKAN ASET BARU";
															 }
														  else
														  if ($cru=='u' || $cru=='d')
														     {
															   echo "UPDATE TRANSAKSI KERUSAKAN ASET";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td>No Transaksi  * </td>
      <td>:</td>
      <td><input name="txt_code" type="text" id="txt_code" size="30" maxlength="50" readonly="readonly"   
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
				  echo $brokh_id;
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
      <td width="39%" valign="top"><input name="txt_brokh_date" type="text" id="txt_brokh_date"  readonly="readonly" size="10" 
		 <?php 
			if ($id!='')
				echo "value=".$brokh_date; 
			else 
		        echo "value='$current_date'";
		 ?>></td>
      <td width="18%" valign="top">&nbsp;</td>
      <td width="0%" valign="top">&nbsp;</td>
      <td width="26%" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td>Status</td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "Tidak Dijual";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $brokh_status;
				}	
		 ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Keterangan</td>
      <td valign="top">:</td>
      <td valign="top"><textarea name="txt_brokh_notes" id="txt_brokh_notes" cols="23"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $brokh_notes;
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
				  echo $brokh_is_canceled;
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
				  echo $brokh_canceled_reason;
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
					       echo "value=".mysqli_num_rows($exec_get_broken_detail);	  
				       }	  
			        else
			           {
				         echo "value='0'";
					   }	 
			   ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td colspan="6">
	  <div STYLE=" height: 100%; width: 100%; font-size: 12px; overflow: auto;">
	  <table width="120%" border="0" cellspacing="1" cellpadding="1" id="tbl_detail" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col">
            <input type="checkbox" id="check_all_data" name="check_all_data" value="1" onClick="select_unselect_all()"/></th>
          <th width="13%" scope="col">Kode Aset </th>
          <th width="29%" scope="col">Deskripsi Isi Aset </th>
          <th width="12%" scope="col">Serial No</th>
          <th width="7%" scope="col">Kapasitas</th>
          <th width="12%" scope="col">Kategori</th>
          <th width="6%" scope="col">Qty</th>
          <th width="6%" scope="col">Dibatalkan</th>
          <th width="19%" scope="col">Notes</th>
        </tr>
        <?php  		   
			  if ($id!='')
			     {					
				   $no=0;
				   while ($field_get_broken_detail=mysqli_fetch_array($exec_get_broken_detail)) 
				         {
						   $no++;
						   $brokd_id=$field_get_broken_detail['brokd_id']; 
						   $itemd_id= $field_get_broken_detail['itemd_id']; 
						   $itemd_code=$field_get_broken_detail['itemd_code'];
						   $masti_name=$field_get_broken_detail['masti_name'];
						   $itemd_serial_no=$field_get_broken_detail['itemd_serial_no'];
						   $masti_capacity=$field_get_broken_detail['itemd_capacity']." ".$field_get_broken_detail['uom_name'];
						   $cati_name=$field_get_broken_detail['cati_name'];
						   $itemd_qty=$field_get_broken_detail['itemd_qty']." Cylinder";
						   $brokd_is_canceled=$field_get_broken_detail['brokd_is_canceled'];
					       $brokd_notes=$field_get_broken_detail['brokd_notes'];
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $itemd_id;?>"/></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_brokd_is_canceled_<?php echo $itemd_id;?>" name="cb_brokd_is_canceled_<?php echo $itemd_id;?>" 
					                    value="1" <?php if ($brokd_is_canceled=='1') 
										                   {
														     echo "checked='checked' disabled='disabled'";
														   }  ?> onClick="call_canceled_item('<?php echo $itemd_id;?>','<?php echo $brokd_id;?>')"><?php if ($brokd_is_canceled=='1') 
														                   echo "Ya"; 
																	   else 
																	       echo "Tidak";?></td>
					 <td class="td_lb"><input type="text" id="txt_brokd_notes_<?php echo $itemd_id;?>" name="txt_brokd_notes_<?php echo $itemd_id;?>" 
					                    value="<?php echo $brokd_notes;?>"  <?php if ($brokd_is_canceled=='1') echo "readonly='readonly'"; ?> size="35"></td>
				 </tr>	     	
		    <?php		   
						 }	   
			     }	
			?>	
      </table>
	  </div></td>
    </tr>
    
    <tr>
      <td>Dibuat Oleh</td>
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
      <td align="right"><input name="btn_save" type="button" id="btn_save" value="Simpan" onClick="input_data()" <?php if ($brokh_is_canceled=='Ya' || $cru=='d') echo "disabled='disabled'";?>/>
        <input name="btn_new" type="reset" id="btn_new" value="Baru" <?php if ($cru=='u' || $cru=='d') echo "disabled='disabled'"; ?>/>
        <input name="btn_close" type="button" id="btn_close" value="Tutup" onClick="window.close()"/></td>
    </tr>
    <tr>
      <td>Diupdate Oleh</td>
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
			 var broken_code=document.getElementById('txt_code').value.trim(); 
			 var date_broken=document.getElementById('txt_brokh_date').value.trim();
			 var d=date_broken.substring(0,2);
			 var m=date_broken.substring(3,5);
			 var y=date_broken.substring(6,10);
			 var broken_date=Date.parse(y+'-'+m+'-'+d);
			 var total_detail=document.getElementById('txt_rows').value;
				
			 	
			 if (broken_code=='')
			    {
				  alert('No transaksi harus diisi!');
				  return (false)
				}
		     else
			 if (date_broken=='')		
			    {
				  alert('Tanggal Kerusakan harus diisi!');
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
				       chk_data=document.getElementsByName('cb_data[]');
				       for (i=0; i<document.getElementsByName('cb_data[]').length;i++)
					       {
					         chk_data[i].checked=true;	
							 y='cb_brokd_is_canceled_'+chk_data[i].value;
							 if (x=='u')
							     document.getElementById(y).disabled=false;
						   }	 
				     } 
					 	 
			      f_cru_broken.action='../../data/broken/input_broken.php?b='+branch_id;
				  f_cru_broken.submit(); 
				}  
		   }
		   
  function call_add_data()
           {
		     var w=1500;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
			 var t=(screen.height/2)-(h/2);
			 open_child=window.open("../../data/broken/pick_tube.php", "f_pick_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0,scrollbars=Yes");
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
		     var id_branch=document.getElementById('s_branch_trans').value;
		     var z=document.getElementById('cb_brokd_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_broken.action='../../data/broken/canceled_item.php?id='+y+'&b='+id_branch;
				       f_cru_broken.submit();
					 }
				  else
				     z.checked=false;
				}  
		   }	
		   
  function rounding(num) 
           {    
              return +(Math.round(num + "e+2")  + "e-2");
           }
</script>






