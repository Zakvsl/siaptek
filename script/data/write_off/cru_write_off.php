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
                            $("#txt_woh_date").datepicker(
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
  $woh_type=0;
  $woh_sources=0;
  $woh_is_canceled='Tidak';
  
  if ($cru=='u')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_write_off_header="SELECT woh_id, woh_code, woh_date, woh_sources, brokh_id, woh_reason, 
	                                   CASE woh_is_canceled
                                            WHEN '0' THEN 'Tidak'
											WHEN '1' THEN 'Ya'  
									   END woh_is_canceled, woh_canceled_date,
	                                   woh_canceled_reason, woh_notes, created_by, created_time, updated_by, updated_time
                                FROM write_off_header
                                WHERE branch_id='$branch_id' AND woh_id='$id'"; 
	   $exec_get_write_off_header=mysqli_query($db_connection, $q_get_write_off_header);
	   $total_get_write_off_header=mysqli_num_rows($exec_get_write_off_header);
	   $field_write_off_header=mysqli_fetch_array($exec_get_write_off_header);
	//   echo $q_get_write_off_header;
	   if ($total_get_write_off_header==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Transaksi Penghapusan Aset yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $q_get_write_off_detail="SELECT wod_id, wod_id, brokd_id, write_off_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
			                                cati_name, wod_qty, wod_is_canceled, wod_notes
                                     FROM write_off_detail
                                     INNER JOIN write_off_header ON write_off_header.woh_id=write_off_detail.woh_id
                                     INNER JOIN item_detail ON item_detail.itemd_id=write_off_detail.itemd_id
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                     WHERE write_off_header.branch_id='$branch_id' AND write_off_detail.woh_id='$id'";				
			$exec_get_write_off_detail=mysqli_query($db_connection, $q_get_write_off_detail);
			$woh_id=$field_write_off_header['woh_id'];
		    $trans_code=$field_write_off_header['woh_code'];
			$trans_code_1=$field_write_off_header['woh_code'];
			$woh_date=get_date_1($field_write_off_header['woh_date']);
			$woh_sources=$field_write_off_header['woh_sources'];
			$brokh_id=$field_write_off_header['brokh_id'];
			$woh_reason=$field_write_off_header['woh_reason'];
			$woh_notes=$field_write_off_header['woh_notes'];
			$woh_is_canceled=$field_write_off_header['woh_is_canceled'];
			$woh_canceled_date=$field_write_off_header['woh_canceled_date'];
			$woh_canceled_reason=$field_write_off_header['woh_canceled_reason'];
			$q_get_maker="SELECT users_names FROM users WHERE users_id='".$field_write_off_header['created_by']."'";
			$exec_get_maker=mysqli_query($db_connection, $q_get_maker);
			$field_maker=mysqli_fetch_array($exec_get_maker);
			$created_by=$field_maker['users_names']." ".$field_write_off_header['created_time'];
			if ($field_write_off_header['created_by']!='')
			   {
			     $q_get_updater="SELECT users_names FROM users WHERE users_id='".$field_write_off_header['updated_by']."'";
			     $exec_get_updater=mysqli_query($db_connection, $q_get_updater);
			     $field_maker=mysqli_fetch_array($exec_get_updater);
			     $updated_by=$field_maker['users_names']." ".$field_write_off_header['updated_time'];
			   }
			else
			   $updated_by='-';
		  }	  
	 }
  else
     {
	   $id="";
       $trans_code=get_format_number($db_connection, 'WO',$branch_id);
	 }
  //echo $q_get_write_off_detail;
  $current_date=date('d-m-Y');
 // echo $woh_sources;
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_write_off" id="f_cru_write_off" onSubmit="">
  <table width="1280" border="0" cellspacing="1" cellpadding="1" id="tbl_issuing">
    <tr>
      <th colspan="6" scope="col" align="right">Kantor Cabang    :
        <select id="s_branch_trans" name="s_branch_trans" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></th>
    </tr>
    <tr>
      <th colspan="6" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH TRANSAKSI PENGHAPUSAN ASET";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UPDATE TRANSAKSI PENGHAPUSAN ASET";
															 }
													?>	  </th>
    </tr>
    
    <tr>
      <td valign="top">No Transaksi * <br>
        Tanggal * </td>
      <td valign="top">:<br>:</td>
      <td valign="top"><input name="txt_code" type="text" id="txt_code" size="30" maxlength="50"   
	     value="<?php 
		     if ($cru=='i')
			    {
				  echo $trans_code;
				}
		     else
			 if ($cru=='u')
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
			 if ($cru=='u')
			    {
				  echo $woh_id;
				}		
		 ?>"/>
      <input name="txt_code_1" type="hidden" id="txt_code_1" <?php 
		     if ($cru=='i')
			    {
				  echo "value=''";
				}
		     else
			 if ($cru=='u')
			    {
				  echo "value='$trans_code_1'";
				}		
		 ?>/><br>
      <input name="txt_woh_date" type="text" id="txt_woh_date"  readonly="readonly" size="10" 
		 <?php 
			if ($id!='')
				echo "value=".$woh_date; 
			else 
		        echo "value='$current_date'";
		 ?>></td>
      <td valign="top">Keterangan</td>
      <td valign="top">:</td>
    <td><textarea name="txt_woh_notes" id="txt_woh_notes" cols="35"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $woh_notes;
				}
		 ?></textarea></tr>
    <tr>
      <td width="16%" valign="top">Sumber Transaksi * </td>
      <td width="0%" valign="top">:</td>
      <td width="39%" valign="top"><?php 
						if (isset($_POST['rb_woh_sources']))
						    $woh_sources=$_POST['rb_woh_sources'];

					     if ($woh_sources=='0')
						    {
							  echo "<input id='rb_woh_sources' name='rb_woh_sources' type='radio' value='0' checked='checked' onchange='submit()'> Internal";
                              echo "<input id='rb_woh_sources' name='rb_woh_sources' type='radio' value='1' onchange='submit()'> Kerusakan</td>";
						    }
						 else
						 if ($woh_sources=='1')
						    {
						      echo "<input id='rb_woh_sources' name='rb_woh_sources' type='radio' value='0' onchange='submit()'> Internal";
                              echo "<input id='rb_woh_sources' name='rb_woh_sources' type='radio' value='1' checked='checked' onchange='submit()'> Kerusakan</td>";
							}	
						 else  
						    {
							  echo "<input id='rb_woh_sources' name='rb_woh_sources' type='radio' value='0' checked='checked' onchange='submit()'> Internal";
                              echo "<input id='rb_woh_sources' name='rb_woh_sources' type='radio' value='1' onchange='submit()'> Kerusakan</td>";
						    }
					  ?></td>
      <td width="18%" valign="top">Dibatalkan</td>
      <td width="0%" valign="top">:</td>
      <td width="26%" valign="top"><?php 
		     if ($cru=='i')
			    {
				  echo "Tidak";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $woh_is_canceled;
				}	
		 ?></td>
    </tr>
    <tr>
      <td valign="top">No Transaksi Kerusakan * </td>
      <td valign="top">:</td>
      <td valign="top"><select id="s_broken" name="s_broken" onChange="submit()">
        <option value="0">-Pilih Kode Kerusakan-</option>
        <?php
						   if ($woh_sources=='1')
						      {
							    if (isset($_POST['s_broken']))
						            $brokh_id=$_POST['s_broken'];
								
								if ($cru=='i')	
							        $q_get_broken="SELECT brokh_id, brokh_code
                                                   FROM broken_header
                                                   WHERE brokh_is_canceled='0' AND brokh_status NOT IN ('2','4','6') AND branch_id='$branch_id'";
								else
								    $q_get_broken="SELECT brokh_id, brokh_code
                                                   FROM broken_header
                                                   WHERE brokh_is_canceled='0' AND brokh_id='$brokh_id' AND branch_id='$branch_id'";
								$exec_broken=mysqli_query($db_connection, $q_get_broken);
								if (mysqli_num_rows($exec_broken)>0)
								   {
								     while ($field_broken=mysqli_fetch_array($exec_broken))
									       {
										     if ($brokh_id==$field_broken['brokh_id'])
											     $selected="selected='selected'";
											 else
											     $selected="";
											 echo "<option value='".$field_broken['brokh_id']."' $selected>".$field_broken['brokh_code']."</option>";
										   }
								   }
							  }			   
						?>
      </select>
      <td valign="top">Alasan Pembatalan</td>
      <td valign="top">:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "-";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $woh_canceled_reason;
				}	
		 ?></td>
    </tr>
    <tr>
      <td valign="top">Alasan Penghapusan * </td>
      <td valign="top">:</td>
      <td valign="top"><textarea name="txt_woh_reason" id="txt_woh_reason" cols="35"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $woh_reason;
				}
		 ?></textarea></td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td><!---<input type="button" id="btn_add" name="btn_add" value="Tambah" onClick="call_add_data()"> --->
	      <input type="button" id="btn_add" name="btn_add" value="Add" onClick="call_add_data()">
	      <input type="button" id="btn_delete" name="btn_delete" value="Hapus" onClick="call_delete_data()">
	      <input type="hidden" name="txt_rows" id="txt_rows" <?php if ($id!='')
			           {
					       echo "value=".mysqli_num_rows($exec_get_write_off_detail);	  
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
          <th width="27%" scope="col">Deskripsi Isi Aset </th>
          <th width="10%" scope="col">Serial No</th>
          <th width="7%" scope="col">Kapasitas</th>
          <th width="10%" scope="col">Kategori</th>
          <th width="5%" scope="col">Qty</th>
          <th width="7%" scope="col">Dibatalkan</th>
          <th width="19%" scope="col">Notes</th>
        </tr>
        <?php  
		      if ($id=='' && $woh_sources=='1')
			     {
				   $q_get_write_off_detail="SELECT brokd_id, broken_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, 
                                            brokd_notes, brokd_is_canceled 
                                            FROM broken_detail 
                                            INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id 
                                            INNER JOIN item_detail ON item_detail.itemd_id=broken_detail.itemd_id 
											INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                            INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                            INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                            WHERE broken_header.branch_id='$branch_id' AND broken_detail.brokh_id='$brokh_id' AND brokd_is_canceled='0' AND 
											        brokd_is_wo='0' AND brokd_is_dispossed='0'";
				   //echo $q_get_write_off_detail;
				   $exec_get_write_off_detail=mysqli_query($db_connection, $q_get_write_off_detail);
				   $no=0;
				   while ($field_get_write_off_detail=mysqli_fetch_array($exec_get_write_off_detail)) 
				         {
						   $no++;
						   $brokd_id=$field_get_write_off_detail['brokd_id']; 
						   $itemd_id= $field_get_write_off_detail['itemd_id']; 
						   $itemd_code=$field_get_write_off_detail['itemd_code'];
						   $masti_name=$field_get_write_off_detail['masti_name'];
						   $itemd_serial_no=$field_get_write_off_detail['itemd_serial_no'];
						   $masti_capacity=$field_get_write_off_detail['itemd_capacity']." ".$field_get_write_off_detail['uom_name'];
						   $cati_name=$field_get_write_off_detail['cati_name'];
						   $itemd_qty=$field_get_write_off_detail['itemd_qty']." Cylinder";
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $itemd_id;?>"/>
					                <input type="hidden" id="txt_brokd_id_<?php echo $itemd_id;?>" name="txt_brokd_id_<?php echo $itemd_id;?>" value="<?php echo $brokd_id;?>"></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_wod_is_canceled_<?php echo $itemd_id;?>" name="cb_wod_is_canceled_<?php echo $itemd_id;?>" 
					                    value="1"  disabled='disabled'>Tidak</td>
					 <td class="td_lb"><input type="text" id="txt_wod_notes_<?php echo $itemd_id;?>" name="txt_wod_notes_<?php echo $itemd_id;?>" 
					                    value="" size="35"></td>
				 </tr><script language="javascript">
				        var x='<?php echo $no; ?>';
						document.getElementById('txt_rows').value=x;
				      </script>     	
		    <?php		   
						 }	
				 }
			  else	
			  if ($id!='')	  
			     {			
				        $no=0;
				        while ($field_get_write_off_detail=mysqli_fetch_array($exec_get_write_off_detail)) 
				              {
						        $no++; 
						        $wod_id=$field_get_write_off_detail['wod_id'];
								$brokd_id= $field_get_write_off_detail['brokd_id'];
						        $itemd_id= $field_get_write_off_detail['itemd_id']; 
						        $itemd_code=$field_get_write_off_detail['itemd_code'];
						        $masti_name=$field_get_write_off_detail['masti_name'];
						        $itemd_serial_no=$field_get_write_off_detail['itemd_serial_no'];
						        $masti_capacity=$field_get_write_off_detail['itemd_capacity']." ".$field_get_write_off_detail['uom_name'];
						        $cati_name=$field_get_write_off_detail['cati_name'];
						        $itemd_qty=$field_get_write_off_detail['wod_qty']." Cylinder";
						        $wod_is_canceled=$field_get_write_off_detail['wod_is_canceled'];
					            $wod_notes=$field_get_write_off_detail['wod_notes'];
			 ?>
		         <tr>
			       <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $itemd_id;?>"/>
				           <input type="hidden" id="txt_brokd_id_<?php echo $itemd_id;?>" name="txt_brokd_id_<?php echo $itemd_id;?>" value="<?php echo $brokd_id;?>"></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_wod_is_canceled_<?php echo $itemd_id;?>" name="cb_wod_is_canceled_<?php echo $itemd_id;?>" 
					                    value="1" <?php if ($wod_is_canceled=='1') 
										                   {
														     echo "checked='checked' disabled='disabled'";
														   }  ?> onClick="call_canceled_item('<?php echo $itemd_id;?>','<?php echo $wod_id;?>')"><?php if ($wod_is_canceled=='1') 
														                   echo "Ya"; 
																	   else 
																	       echo "Tidak";?></td>
					 <td class="td_lb"><input type="text" id="txt_wod_notes_<?php echo $itemd_id;?>" name="txt_wod_notes_<?php echo $itemd_id;?>" 
					                    value="<?php echo $wod_notes;?>"  <?php if ($wod_is_canceled=='1') echo "readonly='readonly'"; ?> size="35"></td>
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
			 if ($cru=='u')
			    {
				  echo $created_by;
				}		
		 ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right"><input name="btn_save" type="button" id="btn_save" value="Simpan" onClick="input_data()" <?php if ($woh_is_canceled=='Ya') echo "disabled='disabled'";?>/>
        <input name="btn_new" type="reset" id="btn_new" value="Baru" <?php if ($cru=='u') echo "disabled='disabled'"; ?>/>
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
			 if ($cru=='u')
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
  var x='<?php echo $woh_sources;?>';
  if (x=='1')
      document.getElementById('btn_add').disabled=true;
  else
      document.getElementById('btn_add').disabled=false;

  function input_data()
           {
		     var branch_id='<?php echo $branch_id;?>';
			 var woh_code=document.getElementById('txt_code').value.trim(); 
			 var write_off_date=document.getElementById('txt_woh_date').value;
			 var d=write_off_date.substring(0,2);
			 var m=write_off_date.substring(3,5);
			 var y=write_off_date.substring(6,10);
			 var write_off_date=Date.parse(y+'-'+m+'-'+d);
			 var sources='<?php echo $woh_sources;?>'
			 var brokh_id=document.getElementById('s_broken').value.trim(); 
			 var disposs_reason=document.getElementById('txt_woh_reason').value.trim();
			 var total_detail=document.getElementById('txt_rows').value;
			 if (woh_code=='')
			    {
			      alert('No Transaksi harus diisi!');
			    }
			 else
			 if (write_off_date=='')
			    {
				  alert('Tanggal harus diisi!');
				}
		     else
			 if (sources=='1' && brokh_id=='0')
			    {
				  alert('No Transaksi Kerusakan harus diisi!');
				}
		     else
			 if (disposs_reason=='')		
			    {
				  alert('Alasan Penghapusan harus diisi!');
				}
			 else
			 if (total_detail<=0) 
			    {
				  alert('Detail Data harus diisi!');
				}
			 else	
			    {   
				  var x='<?php echo $cru;?>';
				  chk_data=document.getElementsByName('cb_data[]');
				  for (i=0; i<document.getElementsByName('cb_data[]').length;i++)
					  {
					    chk_data[i].checked=true;	
					    y='cb_wod_is_canceled_'+chk_data[i].value;
					    if (x=='u')
						    document.getElementById(y).disabled=false;
					  }
			       f_cru_write_off.action='../../data/write_off/input_write_off.php?b='+branch_id;
				   f_cru_write_off.submit();
				} 
		   }

  function call_add_data()
           {
		     var w=1500;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
			 var t=(screen.height/2)-(h/2);
			 open_child=window.open("../../data/write_off/pick_tube.php", "f_pick_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0,scrollbars=Yes");
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
	
  function call_canceled_item(x,y)
           {
		     var id_branch=document.getElementById('s_branch_trans').value;
		     var z=document.getElementById('cb_wod_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_write_off.action='../../data/write_off/canceled_item.php?id='+y+'&b='+id_branch;
				       f_cru_write_off.submit();
					 }
				  else
				     z.checked=false;
				}  
		   }	
</script>






