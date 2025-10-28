<?php
 include "../../library/check_session.php";
 include "../../library/library_function.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 
 compare_branch($branch_id, $branch_id_transaction);
 
 /*if ($branch_id!=$branch_id_transaction)
    {
	  ?>
        <script language="javascript">
           alert('Ada perbedaan akses kantor cabang!\nSilahkan refresh terlebih dahulu!');
		   window.close();
		</script>
	  <?php
	} */
?>
 <link type="text/css" rel="stylesheet" href="../../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../../library/development-bundle/ui/ui.core.js"></script>
    <script src="../../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_tth_date").datepicker(
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
  include "../../library/style.css";
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  if ($cru=='u' || $cru=='d')
      $id=mysqli_real_escape_string($db_connection, $_GET['id']);
  else
      $id="";
  $branch_id_to="";
  $tth_is_canceled='0';
  if ($cru=='u' || $cru=='d')
     {
	   $q_get_transfer_header="SELECT tth_id, tth_code, tth_date, branch_id_to, emp_id_sender, tth_ba_no, tth_po_no,  
									 CASE tth_is_canceled
									      WHEN '0' THEN 'Tidak'
										  WHEN '1' THEN 'Ya'
									 END tth_is_canceled, tth_canceled_date, tth_canceled_reason, tth_notes, created_by, created_time, updated_by, updated_time
                               FROM transfer_header
                               WHERE branch_id='$branch_id' AND tth_id='$id'";
	   $exec_get_transfer_header=mysqli_query($db_connection, $q_get_transfer_header);
	   $total_get_transfer_header=mysqli_num_rows($exec_get_transfer_header);
	   $field_transfer_header=mysqli_fetch_array($exec_get_transfer_header);
	  // echo $q_get_transfer_header;
	   if ($total_get_transfer_header==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Transaksi Perpindahan Aset yang akan diupdate tidak ditemukan!');
				 ''window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $q_get_transfer_detail="SELECT ttd_id, transfer_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, 
			                        ttd_is_canceled, ttd_notes, whsl_id_from, (SELECT whsl_name FROM warehouse_location WHERE whsl_id=whsl_id_from) AS whsl_id_from
                                    FROM transfer_detail
                                    INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
                                    INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id
									INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                    INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                    INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                    WHERE transfer_header.branch_id='$branch_id' AND transfer_detail.tth_id='$id'";				
			//echo $q_get_transfer_detail;
			$exec_get_transfer_detail=mysqli_query($db_connection, $q_get_transfer_detail);
			$tth_id=$field_transfer_header['tth_id'];
		    $trans_code=$field_transfer_header['tth_code'];
			$trans_code_1=$field_transfer_header['tth_code'];
			$tth_date=get_date_1($field_transfer_header['tth_date']);
			$branch_id_to=$field_transfer_header['branch_id_to'];
			$emp_id_sender=$field_transfer_header['emp_id_sender'];
			$tth_ba_no=$field_transfer_header['tth_ba_no'];
			$tth_po_no=$field_transfer_header['tth_po_no'];
			$tth_notes=$field_transfer_header['tth_notes'];
			$tth_is_canceled=$field_transfer_header['tth_is_canceled'];
			$tth_canceled_date=$field_transfer_header['tth_canceled_date'];
			$tth_canceled_reason=$field_transfer_header['tth_canceled_reason'];
			$q_get_maker="SELECT users_names FROM users WHERE users_id='".$field_transfer_header['created_by']."'";
			$exec_get_maker=mysqli_query($db_connection, $q_get_maker);
			$field_maker=mysqli_fetch_array($exec_get_maker);
			$created_by=$field_maker['users_names']." ".$field_transfer_header['created_time'];
			if ($field_transfer_header['created_by']!='')
			   {
			     $q_get_updater="SELECT users_names FROM users WHERE users_id='".$field_transfer_header['updated_by']."'";
			     $exec_get_updater=mysqli_query($db_connection, $q_get_updater);
			     $field_maker=mysqli_fetch_array($exec_get_updater);
			     $updated_by=$field_maker['users_names']." ".$field_transfer_header['updated_time'];
			   }
			else
			   $updated_by='-';
		  }	  
	 }
  else
     {
       $trans_code=get_format_number($db_connection, 'TRANS',$branch_id);
	 }
  //echo $q_get_transfer_detail;
  $current_date=date('d-m-Y');
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_transfer" id="f_cru_transfer" onSubmit="">
  <table width="1280" border="0" cellspacing="1" cellpadding="1" id="tbl_transfer">
    <tr>
      <th colspan="6" scope="col" align="right">Kantor Cabang    :
        <select id="s_branch_trans" name="s_branch_trans" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></th>
    </tr>
    <tr>
      <th colspan="6" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH TRANSAKSI PERPINDAHAN ASET BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UPDATE TRANSAKSI PERPINDAHAN ASET";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td>No Transaksi * </td>
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
				  echo $tth_id;
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
      <td width="39%" valign="top"><input name="txt_tth_date" type="text" id="txt_tth_date"  readonly="readonly" size="10" 
		 <?php 
			if ($id!='')
				echo "value=".$tth_date; 
			else 
		        echo "value='$current_date'";
		 ?>></td>
      <td width="18%" valign="top">Keterangan</td>
      <td width="0%" valign="top">:</td>
      <td width="26%" valign="top"><textarea name="txt_tth_notes" id="txt_tthh_notes" cols="35"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $tth_notes;
				}
		 ?></textarea></td>
    </tr>
    
    <tr>
      <td valign="top">Kantor Cabang Tujuan * </td>
      <td valign="top">:</td>
      <td valign="top"><select id="s_branch_to" name="s_branch_to" onChange="submit()" style="width:300px">
        <?php 
		     if (isset($_POST['s_branch_to']))
			     $branch_id_to=$_POST['s_branch_to'];
				 
		     $q_get_branch="SELECT branch_id, branch_code, branch_name
                            FROM branch
							WHERE branch_id!='$branch_id'
                            ORDER BY branch_is_headquarter, branch_name ASC";
			 $exec_get_branch=mysqli_query($db_connection, $q_get_branch);
			 echo "<option value='0'>-Pilih Kantor Cabang-</option>";
		     while ($field=mysqli_fetch_array($exec_get_branch))
			       { 
				     if ($branch_id_to==$field['branch_id'])
						 $selected="selected='selected'";
				     else
						 $selected="";
							  
					 if ($cru=='i')
					     echo "<option value='".$field['branch_id']."' $selected>".$field['branch_name']." - [".$field['branch_code']."]</option>";
					 else
					 if ($cru=='u' || $cru=='d')	
						 echo "<option value='".$field['branch_id']."' $selected>".$field['branch_name']." - [".$field['branch_code']."]</option>";	  	 
				   }		
		  ?>
      </select></td>
      <td valign="top">Dibatalkan</td>
      <td valign="top">:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "Tidak";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $tth_is_canceled;
				}	
		 ?></td>
    </tr>
    <tr>
      <td>Dikirim Oleh * </td>
      <td>:</td>
      <td><select name="s_employee_sender" id="s_employee_sender" style="width:300px">
        <?php
		    $q_get_employee_sender="SELECT emp_id, emp_code, emp_name
                                    FROM employee
                                    WHERE branch_id='$branch_id'";
		 	$exec_get_employee_sender=mysqli_query($db_connection, $q_get_employee_sender);
			echo "<option value='0'>-Pilih Pengirim-</option>";
			while ($field_employee_sender=mysqli_fetch_array($exec_get_employee_sender))
			      {    		 
		                 if ($cru=='i')
			                 echo "<option value='".$field_employee_sender['emp_id']."'>".$field_employee_sender['emp_name']." - [".$field_employee_sender['emp_code']."]</option>";
			             else
			             if ($cru=='u' || $cru=='d')
					        {
					          if ($emp_id_sender==$field_employee_sender['emp_id'])
						          $selected="selected='selected'";
						      else
						          $selected="";
							 
					          echo "<option value='".$field_employee_sender['emp_id']."' $selected>".$field_employee_sender['emp_name']." - [".$field_employee_sender['emp_code']."]</option>";
					        }
			      }
		  ?>
      </select></td>
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
				  echo $tth_canceled_reason;
				}	
		 ?></td>
    </tr>
    
    
    <tr>
      <td valign="top">No Berita Acara</td>
      <td valign="top">:</td>
      <td><input type="text" id="txt_ba_no" name="txt_ba_no" size="30" maxlength="50" value="<?php if ($cru=='i') echo ""; else echo $tth_ba_no;?>"></td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top"></td>
    </tr>
    <tr>
      <td valign="top">Purchase No</td>
      <td valign="top">:</td>
      <td><input type="text" id="txt_po_no" name="txt_po_no" size="30" maxlength="50" value="<?php if ($cru=='i') echo ""; else echo $tth_po_no; ;?>"></td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top"></td>
    </tr>
    <tr>
      <td><input type="button" id="btn_add" name="btn_add" value="Tambah" onClick="call_add_data()">
	      <input type="button" id="btn_delete" name="btn_delete" value="Hapus" onClick="call_delete_data()">
	      <input type="hidden" name="txt_rows" id="txt_rows" <?php if ($id!='')
			           {
					       echo "value=".mysqli_num_rows($exec_get_transfer_detail);	  
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
	  <table width="120%" border="0" cellspacing="1" cellpadding="1" id="tbl_detail" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col">
            <input type="checkbox" id="check_all_data" name="check_all_data" value="1" onClick="select_unselect_all()"/></th>
          <th width="13%" scope="col">Kode Aset </th>
          <th width="23%" scope="col">Deskripsi Isi Aset </th>
          <th width="10%" scope="col">Serial No</th>
          <th width="8%" scope="col">Kapasitas</th>
          <th width="9%" scope="col">Kategori</th>
          <th width="5%" scope="col">Qty</th>
          <th width="9%" scope="col">Lokasi Asal </th>
          <th width="7%" scope="col">Dibatalkan</th>
          <th width="14%" scope="col">Keterangan</th>
        </tr>
        <?php  		   
			  if ($id!='')
			     {					
				   $no=0;
				   while ($field_get_transfer_detail=mysqli_fetch_array($exec_get_transfer_detail)) 
				         {
						   $no++;
						   $ttd_id=$field_get_transfer_detail['ttd_id']; 
						   $itemd_id= $field_get_transfer_detail['itemd_id']; 
						   $itemd_code=$field_get_transfer_detail['itemd_code'];
						   $masti_name=$field_get_transfer_detail['masti_name'];
						   $itemd_serial_no=$field_get_transfer_detail['itemd_serial_no'];
						   $masti_capacity=$field_get_transfer_detail['itemd_capacity']." ".$field_get_transfer_detail['uom_name'];
						   $cati_name=$field_get_transfer_detail['cati_name'];
						   $itemd_qty=$field_get_transfer_detail['itemd_qty']." Cylinder";
						   $whsl_from=$field_get_transfer_detail['whsl_id_from'];
						   //$whsl_id_to=$field_get_transfer_detail['whsl_id_to'];
						   $ttd_is_canceled=$field_get_transfer_detail['ttd_is_canceled'];
					       $ttd_notes=$field_get_transfer_detail['ttd_notes'];
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $itemd_id;?>"/></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><?php echo $whsl_from;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_ttd_is_canceled_<?php echo $itemd_id;?>" name="cb_ttd_is_canceled_<?php echo $itemd_id;?>" 
					                    value="1" <?php if ($ttd_is_canceled=='1') 
										                   {
														     echo "checked='checked' disabled='disabled'";
														   }  ?> onClick="call_canceled_item('<?php echo $itemd_id;?>','<?php echo $ttd_id;?>')"><?php if ($ttd_is_canceled=='1') 
														                   echo "Ya"; 
																	   else 
																	       echo "Tidak";?></td>
					 <td class="td_lb"><input type="text" id="ttd_notes_<?php echo $itemd_id;?>" name="ttd_notes_<?php echo $itemd_id;?>" 
					                    value="<?php echo $ttd_notes;?>"  <?php if ($ttd_is_canceled=='1') echo "readonly='readonly'"; ?> size="25"></td>
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
      <td align="right"><input name="btn_save" type="button" id="btn_save" value="Simpan" onClick="input_data()" <?php if ($tth_is_canceled=='Ya' || $cru=='d') echo "disabled='disabled'";?>/>
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
			 var transfer_code=document.getElementById('txt_code').value.trim(); 
			 var date_transfer=document.getElementById('txt_tth_date').value.trim();
			 var d=date_transfer.substring(0,2);
			 var m=date_transfer.substring(3,5);
			 var y=date_transfer.substring(6,10);
			 var transfer_date=Date.parse(y+'-'+m+'-'+d);
			 var branch_id_to=document.getElementById('s_branch_to').value.trim(); 
			 var sender_id=document.getElementById('s_employee_sender').value.trim(); 
			 var total_detail=document.getElementById('txt_rows').value;
             
			 if (transfer_code=='')
			    {
				  alert('No Transaksi harus diisi!');
				  return (false)
				}
		     else
			 if (date_transfer=='')		
			    {
				  alert('Tanggal Perpindahan harus diisi!');
				  return (false)
				}
			 else
			 if (branch_id_to=='0')		
			    {
				  alert('Kantor Cabang tujuan harus diisi!');
				  return (false)
				}
			 else
			 if (sender_id=='0')		
			    {
				  alert('Nama Pengirim harus diisi!');
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
							 y='cb_ttd_is_canceled_'+chk_data[i].value;
							 if (x=='u')
							    {
							      document.getElementById(y).disabled=false;
								}   
						   }
					   f_cru_transfer.action='../../data/transfer/input_transfer.php?b='+branch_id;
				       f_cru_transfer.submit();	 
				     } 
				} 
		   }
		   
  function call_add_data()
           {
		     var cru='<?php echo $cru;?>';
		     var branch_id='<?php echo $branch_id_to;?>';
			 var branch_id_data=document.getElementById('s_branch_trans').value;
		     var w=1500;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
			 var t=(screen.height/2)-(h/2);
			 open_child=window.open("../../data/transfer/pick_tube.php?b="+branch_id+"&bt="+branch_id_data+"&c="+cru, "f_pick_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0, scrollbars=Yes");
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
		   
  function call_empty_strg(x)
           {
	 	     var id='strg_detail_'+x;
	 		 document.getElementById(id).value='';
		   }		    
		   
  function call_validation_number(number)
           {
			  if (!/^[0-9.]+$/.test(number.value))
	             {
	               number.value = number.value.substring(0,number.value.length-1000);
	             }
		   }		
  function call_calculate_lead_time()
           {
		     var date_po=document.getElementById('txt_poh_date').value.trim();
			 var d_1=date_po.substring(0,2);
			 var m_1=date_po.substring(3,5);
			 var y_1=date_po.substring(6,10);
			 var poh_date=Date.parse(y_1+'-'+m_1+'-'+d_1);
			 var date_eta=document.getElementById('txt_poh_eta').value.trim();
			 var d_2=date_eta.substring(0,2);
			 var m_2=date_eta.substring(3,5);
			 var y_2=date_eta.substring(6,10);
			 var eta_date=Date.parse(y_2+'-'+m_2+'-'+d_2);
          //   selisih=Math.round(((eta_date-poh_date)/86400000)/7);
		     selisih=((eta_date-poh_date)/86400000)/7; 
			    document.getElementById('txt_poh_lead_time_delivery').value=rounding(selisih);
		   }
	
  function call_canceled_item(x,y)
           {
		     var id_branch=document.getElementById('s_branch_trans').value;
		     var z=document.getElementById('cb_ttd_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_transfer.action='../../data/transfer/canceled_item.php?id='+y+'&b='+id_branch;
				       f_cru_transfer.submit();
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






