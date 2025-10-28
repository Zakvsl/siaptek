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
                            $("#txt_disph_date").datepicker(
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
  $cust_id="";
  $emp_id="";
  $disph_sources=0;
  $disph_is_canceled='Tidak';
  
  $disph_type=0;
  if ($cru=='u' || $cru=='d')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_dispossal_header="SELECT disph_id, disph_code, disph_date, disph_sources, brokh_id, disph_reason, cust_id, emp_id_dispossed_by, 
	                                   CASE disph_is_canceled
                                            WHEN '0' THEN 'Tidak'
											WHEN '1' THEN 'Ya'  
									   END disph_is_canceled, disph_canceled_date, 
	                                   disph_canceled_reason, disph_notes, created_by, created_time, updated_by, updated_time
                                FROM dispossal_header
                                WHERE branch_id='$branch_id' AND disph_id='$id'"; 
	   $exec_get_dispossal_header=mysqli_query($db_connection,$q_get_dispossal_header);
	   $total_get_dispossal_header=mysqli_num_rows($exec_get_dispossal_header);
	   $field_dispossal_header=mysqli_fetch_array($exec_get_dispossal_header);
	//   echo $q_get_dispossal_header;
	   if ($total_get_dispossal_header==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Transaksi Penjualan Aset yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $q_get_dispossal_detail="SELECT dispd_id, dispd_id, brokd_id, dispossal_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
			                                cati_name, dispd_qty, dispd_is_canceled, dispd_notes
                                     FROM dispossal_detail
                                     INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
                                     INNER JOIN item_detail ON item_detail.itemd_id=dispossal_detail.itemd_id
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                     WHERE dispossal_header.branch_id='$branch_id' AND dispossal_detail.disph_id='$id'";				
			$exec_get_dispossal_detail=mysqli_query($db_connection,$q_get_dispossal_detail);
			$disph_id=$field_dispossal_header['disph_id'];
		    $trans_code=$field_dispossal_header['disph_code'];
			$trans_code_1=$field_dispossal_header['disph_code'];
			$disph_date=get_date_1($field_dispossal_header['disph_date']);
			$disph_sources=$field_dispossal_header['disph_sources'];
			$brokh_id=$field_dispossal_header['brokh_id'];
			$disph_reason=$field_dispossal_header['disph_reason'];
			$cust_id=$field_dispossal_header['cust_id'];
			$emp_id=$field_dispossal_header['emp_id_dispossed_by'];
			$disph_notes=$field_dispossal_header['disph_notes'];
			$disph_is_canceled=$field_dispossal_header['disph_is_canceled'];
			$disph_canceled_date=$field_dispossal_header['disph_canceled_date'];
			$disph_canceled_reason=$field_dispossal_header['disph_canceled_reason'];
			$q_get_maker="SELECT users_names FROM users WHERE users_id='".$field_dispossal_header['created_by']."'";
			$exec_get_maker=mysqli_query($db_connection,$q_get_maker);
			$field_maker=mysqli_fetch_array($exec_get_maker);
			$created_by=$field_maker['users_names']." ".$field_dispossal_header['created_time'];
			if ($field_dispossal_header['created_by']!='')
			   {
			     $q_get_updater="SELECT users_names FROM users WHERE users_id='".$field_dispossal_header['updated_by']."'";
			     $exec_get_updater=mysqli_query($db_connection,$q_get_updater);
			     $field_maker=mysqli_fetch_array($exec_get_updater);
			     $updated_by=$field_maker['users_names']." ".$field_dispossal_header['updated_time'];
			   }
			else
			   $updated_by='-';
		  }	  
	 }
  else
     { 
	   $id="";
       $trans_code=get_format_number($db_connection, 'DSP',$branch_id);
	 }
  //echo $q_get_dispossal_detail;
  $current_date=date('d-m-Y');
 // echo $disph_sources;
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_dispossal" id="f_cru_dispossal" onSubmit="">
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
															   echo "TAMBAH TRANSAKSI PENJUALAN ASET BARU";
															 }
														  else
														  if ($cru=='u' || $cru=='d')
														     {
															   echo "UPDATE TRANSAKSI PENJUALAN ASET";
															 }
													?>	  </th>
    </tr>
    
    <tr>
      <td>No Transaksi  * </td>
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
				  echo $disph_id;
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
      <td>Nama Customer * </td>
      <td>:</td>
      <td><select name="s_customer" id="s_customer" onChange="submit()" style="width:300px"><option value="0">-Pilih Customer-</option>
        <?php
		    if (isset($_POST['s_customer']))
			    $cust_id=$_POST['s_customer'];
				
		    $q_get_customer="SELECT cust_name, cust_id, cust_code
                             FROM customer
						     WHERE cust_type='0' AND branch_id='$branch_id' ORDER BY cust_name ASC";
		 	$exec_get_customer=mysqli_query($db_connection,$q_get_customer);
			while ($field_customer=mysqli_fetch_array($exec_get_customer))
			      {    	
				    if ($cust_id==$field_customer['cust_id'])
						$selected="selected='selected'";
			        else
						$selected="";
			        echo "<option value='".$field_customer['cust_id']."' $selected>".$field_customer['cust_name']." - [".$field_customer['cust_code']."]</option>";
			      }
		  ?>
      </select>    </tr>
    <tr>
      <td width="16%" valign="top">Tanggal Penjualan * </td>
      <td width="0%" valign="top">:</td>
      <td width="39%" valign="top"><input name="txt_disph_date" type="text" id="txt_disph_date"  readonly="readonly" size="10" 
		 <?php 
			if ($id!='')
				echo "value=".$disph_date; 
			else 
		        echo "value='$current_date'";
		 ?>></td>
      <td width="18%" valign="top">Dijual Oleh *</td>
      <td width="0%" valign="top">:</td>
      <td width="26%" valign="top"><select name="s_employee" id="s_employee" style="width:300px">
        <?php
		    if (isset($_POST['s_employee']))
			    $emp_id=$_POST['s_employee'];
				
		    $q_get_employee="SELECT emp_id, emp_code, emp_name
                             FROM employee
                             WHERE branch_id='$branch_id'";
		 	$exec_get_employee=mysqli_query($db_connection,$q_get_employee);
			echo "<option value='0'>-Pilih Penerima-</option>";
			while ($field_employee=mysqli_fetch_array($exec_get_employee))
			      {
					if ($emp_id==$field_employee['emp_id'])
						$selected="selected='selected'"; 
				    else
						$selected=''; 
			        echo "<option value='".$field_employee['emp_id']."' $selected>".$field_employee['emp_name']." - [".$field_employee['emp_code']."]</option>";
			      }
		  ?>
      </select></td>
    </tr>
    <tr>
      <td valign="top">Sumber Transaksi * </td>
      <td valign="top">:</td>
      <td valign="top"><?php 
						if (isset($_POST['rb_disph_sources']))
						    $disph_sources=$_POST['rb_disph_sources'];

					     if ($disph_sources=='0')
						    {
							  echo "<input id='rb_disph_sources' name='rb_disph_sources' type='radio' value='0' checked='checked' onchange='submit()'> Internal";
                              echo "<input id='rb_disph_sources' name='rb_disph_sources' type='radio' value='1' onchange='submit()'> Kerusakan</td>";
						    }
						 else
						 if ($disph_sources=='1')
						    {
						      echo "<input id='rb_disph_sources' name='rb_disph_sources' type='radio' value='0' onchange='submit()'> Internal";
                              echo "<input id='rb_disph_sources' name='rb_disph_sources' type='radio' value='1' checked='checked' onchange='submit()'> Kerusakan</td>";
							}	
						 else  
						    {
							  echo "<input id='rb_disph_sources' name='rb_disph_sources' type='radio' value='0' checked='checked' onchange='submit()'> Internal";
                              echo "<input id='rb_disph_sources' name='rb_disph_sources' type='radio' value='1' onchange='submit()'> Kerusakan</td>";
						    }
					  ?>
      <td valign="top">Keterangan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_disph_notes" id="txt_disph_notes" cols="35"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $disph_notes;
				}
		 ?></textarea></td>
    </tr>
    <tr>
      <td valign="top">No Ref Kerusakan * </td>
      <td valign="top">:</td>
      <td valign="top"><select id="s_broken" name="s_broken" onChange="submit()"><option value="0">-Pilih Kode Kerusakan-</option>
                        <?php
						   if ($disph_sources=='1')
						      {
							    if (isset($_POST['s_broken']))
						            $brokh_id=$_POST['s_broken'];
								
								if ($cru=='i')	
							        $q_get_broken="SELECT brokh_id, brokh_code
                                                   FROM broken_header
                                                   WHERE brokh_is_canceled='0' AND brokh_status NOT IN ('2','4','6')  AND branch_id='$branch_id'";
								else
								    $q_get_broken="SELECT brokh_id, brokh_code
                                                   FROM broken_header
                                                   WHERE brokh_is_canceled='0' AND brokh_id='$brokh_id' AND branch_id='$branch_id'";
								$exec_broken=mysqli_query($db_connection,$q_get_broken);
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
				  echo $disph_is_canceled;
				}	
		 ?></td>
    </tr>
    <tr>
      <td valign="top">Alasan Dijual * </td>
      <td valign="top">:</td>
      <td valign="top"><textarea name="txt_disph_reason" id="txt_disph_reason" cols="35"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $disph_reason;
				}
		 ?></textarea></td>
      <td valign="top">Alasan Pembatalan</td>
      <td valign="top">:</td>
      <td valign="top"><?php 
		     if ($cru=='i')
			    {
				  echo "-";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $disph_canceled_reason;
				}	
		 ?></td>
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
	      <input type="button" id="btn_add" name="btn_add" value="Tambah" onClick="call_add_data()">
	      <input type="button" id="btn_delete" name="btn_delete" value="Hapus" onClick="call_delete_data()">
	      <input type="hidden" name="txt_rows" id="txt_rows" <?php if ($id!='')
			           {
					       echo "value=".mysqli_num_rows($exec_get_dispossal_detail);	  
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
          <th width="3%" scope="col">
            <input type="checkbox" id="check_all_data" name="check_all_data" value="1" onClick="select_unselect_all()"/></th>
          <th width="12%" scope="col">Kode Aset </th>
          <th width="27%" scope="col">Deskripsi Isi Aset </th>
          <th width="10%" scope="col">Serial No</th>
          <th width="7%" scope="col">Kapasitas</th>
          <th width="10%" scope="col">Kategori</th>
          <th width="5%" scope="col">Qty</th>
          <th width="7%" scope="col">Dibatalkan</th>
          <th width="19%" scope="col">Notes</th>
        </tr>
        <?php  
		      if ($id=='' && $disph_sources=='1')
			     {
				   $q_get_dispossal_detail="SELECT brokd_id, broken_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, 
                                            brokd_notes, brokd_is_canceled 
                                            FROM broken_detail 
                                            INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id 
                                            INNER JOIN item_detail ON item_detail.itemd_id=broken_detail.itemd_id 
											 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                            INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                            INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                            WHERE broken_header.branch_id='$branch_id' AND broken_detail.brokh_id='$brokh_id' AND brokd_is_canceled='0' AND 
											      brokd_is_wo='0' AND  brokd_is_dispossed='0'";
				//   echo $q_get_dispossal_detail;
				   $exec_get_dispossal_detail=mysqli_query($db_connection,$q_get_dispossal_detail);
				   $no=0;
				   while ($field_get_dispossal_detail=mysqli_fetch_array($exec_get_dispossal_detail)) 
				         {
						   $no++;
						   $brokd_id=$field_get_dispossal_detail['brokd_id']; 
						   $itemd_id= $field_get_dispossal_detail['itemd_id']; 
						   $itemd_code=$field_get_dispossal_detail['itemd_code'];
						   $masti_name=$field_get_dispossal_detail['masti_name'];
						   $itemd_serial_no=$field_get_dispossal_detail['itemd_serial_no'];
						   $masti_capacity=$field_get_dispossal_detail['itemd_capacity']." ".$field_get_dispossal_detail['uom_name'];
						   $cati_name=$field_get_dispossal_detail['cati_name'];
						   $itemd_qty=$field_get_dispossal_detail['itemd_qty']." Cylinder";
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
		             <td class="td_lb"><input type="checkbox" id="cb_dispd_is_canceled_<?php echo $itemd_id;?>" name="cb_dispd_is_canceled_<?php echo $itemd_id;?>" 
					                    value="1"  disabled='disabled'>Tidak</td>
					 <td class="td_lb"><input type="text" id="txt_dispd_notes_<?php echo $itemd_id;?>" name="txt_dispd_notes_<?php echo $itemd_id;?>" 
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
				        while ($field_get_dispossal_detail=mysqli_fetch_array($exec_get_dispossal_detail)) 
				              {
						        $no++; 
						        $dispd_id=$field_get_dispossal_detail['dispd_id'];
								$brokd_id= $field_get_dispossal_detail['brokd_id'];
						        $itemd_id= $field_get_dispossal_detail['itemd_id']; 
						        $itemd_code=$field_get_dispossal_detail['itemd_code'];
						        $masti_name=$field_get_dispossal_detail['masti_name'];
						        $itemd_serial_no=$field_get_dispossal_detail['itemd_serial_no'];
						        $masti_capacity=$field_get_dispossal_detail['itemd_capacity']." ".$field_get_dispossal_detail['uom_name'];
						        $cati_name=$field_get_dispossal_detail['cati_name'];
						        $itemd_qty=$field_get_dispossal_detail['dispd_qty']." Cylinder";
						        $dispd_is_canceled=$field_get_dispossal_detail['dispd_is_canceled'];
					            $dispd_notes=$field_get_dispossal_detail['dispd_notes'];
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
		             <td class="td_lb"><input type="checkbox" id="cb_dispd_is_canceled_<?php echo $itemd_id;?>" name="cb_dispd_is_canceled_<?php echo $itemd_id;?>" 
					                    value="1" <?php if ($dispd_is_canceled=='1') 
										                   {
														     echo "checked='checked' disabled='disabled'";
														   }  ?> onClick="call_canceled_item('<?php echo $itemd_id;?>','<?php echo $dispd_id;?>')"><?php if ($dispd_is_canceled=='1') 
														                   echo "Ya"; 
																	   else 
																	       echo "Tidak";?></td>
					 <td class="td_lb"><input type="text" id="txt_dispd_notes_<?php echo $itemd_id;?>" name="txt_dispd_notes_<?php echo $itemd_id;?>" 
					                    value="<?php echo $dispd_notes;?>"  <?php if ($dispd_is_canceled=='1') echo "readonly='readonly'"; ?> size="35"></td>
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
      <td align="right"><input name="btn_save" type="button" id="btn_save" value="Simpan" onClick="input_data()" <?php if ($disph_is_canceled=='Ya' || $cru=='d') echo "disabled='disabled'";?>/>
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
  var x='<?php echo $disph_sources;?>';
  if (x=='1')
      document.getElementById('btn_add').disabled=true;
  else
      document.getElementById('btn_add').disabled=false;

  function input_data()
           {
		     var branch_id='<?php echo $branch_id;?>';
			 var disph_code=document.getElementById('txt_code').value.trim(); 
			 var dispossal_date=document.getElementById('txt_disph_date').value;
			 var d=dispossal_date.substring(0,2);
			 var m=dispossal_date.substring(3,5);
			 var y=dispossal_date.substring(6,10);
			 var dispossal_date=Date.parse(y+'-'+m+'-'+d);
			 var sources='<?php echo $disph_sources;?>'
			 var brokh_id=document.getElementById('s_broken').value.trim(); 
			 var disposs_reason=document.getElementById('txt_disph_reason').value.trim();
			 var cust_id=document.getElementById('s_customer').value; 
			 var emp_id=document.getElementById('s_employee').value; 
			 var total_detail=document.getElementById('txt_rows').value;
			 if (disph_code=='')
			    {
			      alert('No Transaksi harus diisi!');
			    }
			 else
			 if (dispossal_date=='')
			    {
				  alert('Tanggal Penjualan harus diisi!');
				}
		     else
			 if (sources=='1' && brokh_id=='0')
			    {
				  alert('No Ref Kerusakan harus diisi!');
				}
		     else
			 if (disposs_reason=='')		
			    {
				  alert('Alasan Penjualan harus diisi!');
				}
			 else
			 if (cust_id=='0')
			    {
			      alert('Nama Customer harus diisi!');
			    }
			 else
			 if (emp_id=='0')
			    {
			      alert('Penjual harus diisi!');
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
					    y='cb_dispd_is_canceled_'+chk_data[i].value;
					    if (x=='u')
						    document.getElementById(y).disabled=false;
					  }
			       f_cru_dispossal.action='../../data/dispossal/input_dispossal.php?b='+branch_id;
				   f_cru_dispossal.submit();
				} 
		   }

  function call_add_data()
           {
		     var w=1500;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
			 var t=(screen.height/2)-(h/2);
			 open_child=window.open("../../data/dispossal/pick_tube.php", "f_pick_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0,scrollbars=Yes");
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
		     var z=document.getElementById('cb_dispd_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_dispossal.action='../../data/dispossal/canceled_item.php?id='+y+'&b='+id_branch;
				       f_cru_dispossal.submit();
					 }
				  else
				     z.checked=false;
				}  
		   }	
</script>






