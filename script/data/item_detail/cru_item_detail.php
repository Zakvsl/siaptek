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
                            $("#txt_itemd_acquired_date").datepicker(
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
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  if ($cru=='u')
      $id=mysqli_real_escape_string($db_connection, $_GET['id']);
  else
      $id="";
  $masti_id="";
  $masti_name="";
  $masti_capacity="";
  $uom_name_1="";
  $uom_name_2="";
  $cati_name="";
  $itemd_status='0';
  $whsl_id='0';
  $vend_id="";
  
  if ($cru=='u')
     {
	   $q_get_item_detail="SELECT masti_id, item_detail.itemd_id, itemd_code, itemd_serial_no, itemd_acquired_date, whsl_id, vend_id, itemd_qty, itemd_status,
	                              itemd_month, itemd_year, itemd_weight,
                                  CASE itemd_is_broken
                                       WHEN '0' THEN 'Tidak'
                                       WHEN '1' THEN 'Ya'
                                  END itemd_is_broken,
                                  CASE itemd_is_dispossed
                                       WHEN '0' THEN 'Tidak'
                                       WHEN '1' THEN 'Ya'
                                  END itemd_is_dispossed, original_branch_id, itemd_capacity, uom_id
                           FROM item_detail
                           WHERE item_detail.itemd_id='$id'";
	//   echo $q_get_item_detail;
	   $exec_get_item_detail=mysqli_query($db_connection, $q_get_item_detail);
	   $total_item_detail=mysqli_num_rows($exec_get_item_detail);
	   $field_item_detail=mysqli_fetch_array($exec_get_item_detail);
	   if ($total_item_detail==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Item yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $itemd_id=$field_item_detail['itemd_id'];
		    $itemd_code=$field_item_detail['itemd_code'];
			$itemd_code_1=$field_item_detail['itemd_code'];
			$masti_id=$field_item_detail['masti_id'];
			$itemd_month=$field_item_detail['itemd_month'];
			$itemd_year=$field_item_detail['itemd_year'];
			$itemd_weight=$field_item_detail['itemd_weight'];
			$itemd_serial_no=$field_item_detail['itemd_serial_no'];
			$itemd_acquired_date=get_date_1($field_item_detail['itemd_acquired_date']);
			$itemd_capacity=$field_item_detail['itemd_capacity'];
			$uom_id=$field_item_detail['uom_id'];
			$itemd_qty=$field_item_detail['itemd_qty'];
			$itemd_status=$field_item_detail['itemd_status'];
			$itemd_is_broken=$field_item_detail['itemd_is_broken'];
			$itemd_is_dispossed=$field_item_detail['itemd_is_dispossed'];
			$whsl_id=$field_item_detail['whsl_id'];
			$vend_id=$field_item_detail['vend_id'];
			$original_branch_id=$field_item_detail['original_branch_id'];
			$q_get_item_master="SELECT masti_name, masti_capacity, masti_capacity, uom_id_1, uom_id_2,
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2, cati_name
                                FROM master_item
                                INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
								WHERE masti_id='$masti_id'"; 
			$exec_get_item_master=mysqli_query($db_connection, $q_get_item_master);
			$total_item_master=mysqli_num_rows($exec_get_item_master);
			if ($total_item_master>0)
			   {
				 $field_item_master=mysqli_fetch_array($exec_get_item_master);
				 $masti_name=$field_item_master['masti_name'];
				 $cati_name=$field_item_master['cati_name'];
			   }
		    else
			   {
				 $masti_name='Not Found';
				 $cati_name='Not Found';
			   }  
			
		  }	  
	 }
  $current_date=date('d-m-Y');
?>

<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_cru_item_detail" id="f_cru_item_detail">
  <table width="447" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH DATA ASET BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA ASET";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="33%">Kode Aset </td>
      <td width="1%">:</td>
      <td width="66%"><label>
      <input name="txt_code" type="text" id="txt_code" size="30" maxlength="50"   
	     value="<?php 
		     if (isset($_POST['txt_code']) && !isset($_POST['btn_save']))
			     $itemd_code=$_POST['txt_code'];
				 
		     if ($cru=='i')
			    {
				  if (isset($_POST['txt_code']) && !isset($_POST['btn_save']))
				     echo  $_POST['txt_code'];
				  else
				      echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $itemd_code;
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
				  echo $itemd_id;
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
				  echo $itemd_code_1;
				}		
		 ?>"/>
      </label></td>
    </tr>
    

    <tr>
      <td nowrap="nowrap">Deskripsi Isi Aset</td>
      <td>:</td>
      <td><select id="s_master_item" name="s_master_item" onChange="submit()" style="width:300px">
	        <?php
			   if (isset($_POST['s_master_item']) && !isset($_POST['btn_save']))
			      { 
				    $masti_id=$_POST['s_master_item'];
					$q_get_item_master="SELECT masti_id, masti_name, masti_capacity, masti_capacity, uom_id_1, uom_id_2,
                                              (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
                                              (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2, cati_name
                                        FROM master_item
                                        INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
										WHERE masti_id='$masti_id'"; 
					$exec_get_item_master=mysqli_query($db_connection, $q_get_item_master);
					$total_item_master=mysqli_num_rows($exec_get_item_master);
					if ($total_item_master>0)
					   {
					     $field_item_master=mysqli_fetch_array($exec_get_item_master);
					     $masti_name=$field_item_master['masti_name'];
						 $cati_name=$field_item_master['cati_name'];
					   }
					else
					   {
					     $masti_name='Not Found';
						 $cati_name='Not Found';
					   }  					
				  }	
				   
			   $q_get_master_item="SELECT masti_id, masti_code, masti_name
                                   FROM master_item
                                   INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
								   ORDER BY masti_name ASC";
			   $exec_get_master_item=mysqli_query($db_connection, $q_get_master_item);
			   $total_master_item=mysqli_num_rows($exec_get_master_item);
			   if ($total_master_item>0)
			      {
				    echo "<option value='0'>-Pilih Nama Item-</option>";
				    while ($field_master_item=mysqli_fetch_array($exec_get_master_item))
					      { 
						    $selected='';
						    if ($masti_id!='')
							   {
							     if ($masti_id==$field_master_item['masti_id'])
								     $selected="selected='selected'";
								 else 
								     $selected='';
							   } 
						    echo "<option value='".$field_master_item['masti_id']."' $selected>".$field_master_item['masti_name']." - ".$field_master_item['masti_code']."</option>";
						  }
				  }
			   else
			      {
				    echo "<option value='0'>-Tidak Ada Item-</option>";
				  }	  
			?>
          </select></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td><textarea id="txt_masti_name" name="txt_masti_name" readonly="readonly" cols="35"><?php echo $masti_name;?></textarea></td>
    </tr>
    <tr>
      <td valign="top">Serial No </td>
      <td valign="top">:</td>
      <td><input name="txt_itemd_serial_no" type="text" id="txt_itemd_serial_no"  size="30"
		 value="<?php 
		     if (isset($_POST['txt_itemd_serial_no']) && !isset($_POST['btn_save']))
			     $itemd_serial_no=$_POST['txt_itemd_serial_no'];
			 
				 
		     if ($cru=='i')
			    {
				  if (isset($_POST['txt_code']) && !isset($_POST['btn_save']))
				     echo $_POST['txt_itemd_serial_no'];
				  else
				      echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $itemd_serial_no;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td valign="top">Tahun Pembuatan </td>
      <td valign="top">:</td>
      <td>
	      <?php
		       if ($cru=='i')
			      {
				    $selected_1="selected='selected'";
					$selected_2="";
					$selected_3="";
					$selected_4="";
					$selected_5="";
					$selected_6="";
					$selected_7="";
					$selected_8="";
					$selected_9="";
					$selected_10="";
					$selected_11="";
					$selected_12="";
				  }
			   else 
			      {
				    $selected_1="";
					$selected_2="";
					$selected_3="";
					$selected_4="";
					$selected_5="";
					$selected_6="";
					$selected_7="";
					$selected_8="";
					$selected_9="";
					$selected_10="";
					$selected_11="";
					$selected_12="";
					
				    if ($itemd_month=='01')
					    $selected_1="selected='selected'";
					else
					if ($itemd_month=='02')
					    $selected_2="selected='selected'";
					else
					if ($itemd_month=='03')
					    $selected_3="selected='selected'";
					else
					if ($itemd_month=='04')
					    $selected_4="selected='selected'";
					else
					if ($itemd_month=='05')
					    $selected_5="selected='selected'";
					else
					if ($itemd_month=='06')
					    $selected_6="selected='selected'";
					else
					if ($itemd_month=='07')
					    $selected_7="selected='selected'";
					else
					if ($itemd_month=='08')
					    $selected_8="selected='selected'";
					else
					if ($itemd_month=='09')
					    $selected_9="selected='selected'";
					else
					if ($itemd_month=='10')
					    $selected_10="selected='selected'";
					else
					if ($itemd_month=='11')
					    $selected_11="selected='selected'";
					else
					if ($itemd_month=='12')
					    $selected_12="selected='selected'";
					
				  }
		  ?>
        <select id="s_month" name="s_month">
		     <option value="01" <?php echo $selected_1; ?>>01</option>
			 <option value="02" <?php echo $selected_2; ?>>02</option>
			 <option value="03" <?php echo $selected_3; ?>>03</option>
			 <option value="04" <?php echo $selected_4; ?>>04</option>
			 <option value="05" <?php echo $selected_5; ?>>05</option>
			 <option value="06" <?php echo $selected_6; ?>>06</option>
			 <option value="07" <?php echo $selected_7; ?>>07</option>
			 <option value="08" <?php echo $selected_8; ?>>08</option>
			 <option value="09" <?php echo $selected_9; ?>>09</option>
			 <option value="10" <?php echo $selected_10; ?>>10</option>
			 <option value="11" <?php echo $selected_11; ?>>11</option>
			 <option value="12" <?php echo $selected_12; ?>>12</option>
        </select>
        <input type="text" id="txt_year" name="txt_year" size="15" onKeyUp="validAngka(this)"
		       value="<?php
			                if ($cru=='i')
							    echo date('Y');
							else
							    echo $itemd_year;
			          ?>">
      </td>
    </tr>
    <tr>
      <td valign="top">Berat</td>
      <td valign="top">:</td>
      <td>
        <input type="text" id="txt_weight" name="txt_weight"  onkeyup="validAngka(this)" size="10"
		       value="<?php 
			                if ($cru=='i')
							    echo '0';
							else
							    echo $itemd_weight;
			          ?>">&nbsp;Kg
	  </td>
    </tr>
    <tr>
      <td valign="top">Kapasitas</td>
      <td valign="top">:</td>
      <td><label>
        <input type="text" id="txt_capacity" name="txt_capacity" size="10"  onKeyUp="validAngka(this)"
		       value="<?php 
			                if ($cru=='i')
							    echo '0';
							else
							    echo $itemd_capacity;
			          ?>">
        <select id="s_uom" name="s_uom">
          <?php
		      $q_get_uom="SELECT uom_id, uom_code, uom_name FROM uom";
			  $exec_get_uom=mysqli_query($db_connection, $q_get_uom);
			  $total_get_uom=mysqli_num_rows($exec_get_uom);
	          if ($cru=='i')
			     {
			       echo "<option value='0'>-Pilih Satuan-</option>";
				   if ($total_get_uom>0)
				      {
					    while ($field_uom=mysqli_fetch_array($exec_get_uom))
					           echo "<option value='".$field_uom['uom_id']."'>".$field_uom['uom_code']." - ".$field_uom['uom_name']."</option>";
					  }
			     }
		      else
			  if ($cru=='u')
			     {
			       echo "<option value='0'>-Pilih Satuan-</option>";
			       if ($total_get_uom>0)
				      {
					    while ($field_uom=mysqli_fetch_array($exec_get_uom))
					          {
							    if ($uom_id!='')
							       {
								     if ($uom_id==$field_uom['uom_id'])
								         $selected="selected='selected'";
								     else
								         $selected="";
								   }  
					            echo "<option value='".$field_uom['uom_id']."' $selected>".$field_uom['uom_code']." - ".$field_uom['uom_name']."</option>";
							  } 
					  }	
			     }
			?>
        </select>
      </label></td>
    </tr>
    
    <tr>
      <td valign="top">Qty</td>
      <td valign="top">:</td>
      <td><?php
	        if ($cru=='i')
			    echo "1&nbsp;Cylinder";
			else
			if ($cru=='u')
			   {
			     echo $itemd_qty."&nbsp;Cylinder";
			   }
	      ?></td>
    </tr>
    
    <tr>
      <td nowrap="nowrap">Tanggal Perolehan </td>
      <td>:</td>
      <td><input name="txt_itemd_acquired_date" type="text" id="txt_itemd_acquired_date"  readonly="readonly" size="10"
		  <?php 
			if ($id!='')
				echo "value=".$itemd_acquired_date; 
			else 
		        echo "value='$current_date'";
		  ?>></td>
    </tr>
    <tr>
      <td>Kategori</td>
      <td>:</td>
      <td><?php echo $cati_name;?></td>
    </tr>
    <tr>
      <td>Status</td>
      <td>:</td>
      <td>
	      <?php
		    if (isset($_POST['rb_status']))
			    $itemd_status=$_POST['rb_status'];
			    
				
			if ($itemd_status=='0')
			   {
				 $selected_1="checked='checked''";
				 $selected_2="''";
			   }
			else
			if ($itemd_status=='1')
			   {
				 $selected_1="''";
				 $selected_2="checked='checked''";
			   }
			else
			   {
				 $selected_1="checked='checked''";
				 $selected_2="''";
			   }
			   
		    echo "<input id='rb_status' name='rb_status' type='radio' value='0' $selected_1>Aktif";
            echo "<input id='rb_status' name='rb_status' type='radio' value='1' $selected_2>Tidak Aktif"; 	 
		  ?></td>
    </tr>
    <tr>
      <td>Rusak</td>
      <td>:</td>
      <td><?php
	        if ($cru=='i')
			    echo "Tidak";
			else
			if ($cru=='u')
			   {
			     echo $itemd_is_broken;
			   }
	      ?></td>
    </tr>
    <tr>
      <td>Dijual</td>
      <td>:</td>
      <td><?php
	        if ($cru=='i')
			    echo "Tidak";
			else
			if ($cru=='u')
			   {
			     echo $itemd_is_dispossed;
			   }
	      ?></td>
    </tr>
    <tr>
      <td>Lokasi Gudang </td>
      <td>:</td>
      <td><select id="s_whs" name="s_whs" <?php if ($cru=='u') echo "disabled='disabled'";?> style="width:300px">
        <?php 
		     $level="";
		     $level_2="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_3="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_4="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_5="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
		     $q_get_whs="SELECT CAST(whsl_id AS CHAR) AS whsl_id, whsl_name, CAST(whsl_id AS CHAR) AS whsl_path, whsl_code, whsl_level, whsl_type 
                         FROM warehouse_location
                         WHERE whsl_level='1' AND branch_id='$branch_id' 
                         UNION 
                         SELECT CAST(whsl_id AS CHAR) AS whsl_id, whsl_name, whsl_parent_path, whsl_code, whsl_level, whsl_type 
                         FROM warehouse_location 
                         WHERE whsl_level!='1' AND branch_id='$branch_id' 
                         ORDER BY whsl_path, whsl_name";
			 $exec_get_whs=mysqli_query($db_connection, $q_get_whs);
		     while ($field=mysqli_fetch_array($exec_get_whs))
			       {
				     if ($field['whsl_level']=='2')
					    {
					      $level=$level_2;
						}   
					 else
					 if ($field['whsl_level']=='3')
					    {
					      $level=$level_3;
						} 
					 else
					 if ($field['whsl_level']=='4')
					    {
					      $level=$level_4;
						} 
					 else
					 if ($field['whsl_level']=='5')
					    {
					      $level=$level_5;
					 	} 
					 
					 if ($field['whsl_type']=='0')
					     $disabled="disabled='disabled'";
					 else
					     $disabled="";	 
					 
					 if (isset($_POST['s_whs']) && !isset($_POST['btn_save']))
					    {
						  if ($whsl_id==$field['whsl_id'])
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  echo "<option value='".$field['whsl_id']."' $selected_field $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";	  	  
						}
					 else
					 if ($cru=='i')
					     echo "<option value='".$field['whsl_id']."' $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";
					 else
					 if ($cru=='u')	
					    {
						  if ($whsl_id==$field['whsl_id'])
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  echo "<option value='".$field['whsl_id']."' $selected_field $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";	  
						}	 
					 $level=''; 
					 $disabled="";
				   }		
		  ?>
      </select></td>
    </tr>
    <tr>
      <td>Vendor</td>
      <td>:</td>
      <td><select id="s_vendor" name="s_vendor" style="width:300px">
	        <?php
			  if (isset($_POST['s_vendor']) && !isset($_POST['btn_save']))
			      $vend_id=$_POST['s_vendor'];
			  $q_get_vendor="SELECT cust_id, cust_code, cust_name
                             FROM customer
							 WHERE cust_type='1' AND cust_status='0' AND branch_id='$branch_id'
							 ORDER BY cust_name ASC";
			  $exec_get_vendor=mysqli_query($db_connection, $q_get_vendor);
			  $total_vendor=mysqli_num_rows($exec_get_vendor);
			  if ($total_vendor>0)
			     {
				   echo "<option value'0'>-Pilih Vendor-</option>";
				   while ($field_vendor=mysqli_fetch_array($exec_get_vendor))
				         {
						   $selected_1='';
						   if ($vend_id!='')
						      {
							    if ($vend_id==$field_vendor['cust_id'])
								    $selected="selected='selected'";
								else 
								    $selected='';
						      }
						   echo "<option value='".$field_vendor['cust_id']."' $selected>".$field_vendor['cust_name']." - ".$field_vendor['cust_code']."</option>";
						 }
				 }
			  else
			     {
				   echo "<option value'0'>-Tidak Ada Vendor-</option>";
				 }	 
			?>
          </select></td>
    </tr>
    <tr>
      <td>Pemilik Aset </td>
      <td>:</td>
      <td><?php
	        if ($cru=='i')
	            $id_branch=$branch_id;
		    else		
			    $id_branch=$original_branch_id;
			$q_get_branch="SELECT CONCAT(branch_code,' - ',branch_name) AS branch_name FROM branch WHERE branch_id='$id_branch'";
		    $exec_get_branch=mysqli_query($db_connection, $q_get_branch);
		    $field_get_branch=mysqli_fetch_array($exec_get_branch);
			echo $field_get_branch['branch_name'];
	      ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><label>
      <input name="btn_save" type="submit" id="btn_save" value="Simpan"/>              
      <input name="btn_new" type="reset" id="btn_new" value="Baru"/>
      <input name="btn_close" type="button" id="btn_close" value="Tutup" onClick="window.close()"/>
      </label></td>
    </tr>
  </table>
</form>

<script language='javascript'>
  function validAngka(a)
           {
	         if (!/^[0-9.]+$/.test(a.value))
	            {
	              a.value = a.value.substring(0,a.value.length-1000);
	            }
           }
</script>

<?php 
  if (isset($_POST['btn_save']))
     {
	   $itemd_id=htmlspecialchars($_POST['txt_id']);
	   $itemd_code=htmlspecialchars($_POST['txt_code']);
	   $itemd_code_1=htmlspecialchars($_POST['txt_code_1']);
	   $masti_id=htmlspecialchars($_POST['s_master_item']);
	   $itemd_month=$_POST['s_month'];
	   $itemd_year=$_POST['txt_year'];
	   $itemd_weight=$_POST['txt_weight'];
	   $itemd_capacity=$_POST['txt_capacity'];
	   $uom_id=$_POST['s_uom'];
	   $itemd_serial_no=htmlspecialchars($_POST['txt_itemd_serial_no']);
	   $itemd_acquired_date=htmlspecialchars($_POST['txt_itemd_acquired_date']);
	   $d=substr($itemd_acquired_date,0,2);
	   $m=substr($itemd_acquired_date,3,2);
	   $y=substr($itemd_acquired_date,6,4); 
	   $itemd_acquired_date=$y."-".$m."-".$d;
	   $itemd_status=$_POST['rb_status'];
	   $whsl_id=$_POST['s_whs'];
	   $vend_id=$_POST['s_vendor'];
	   $q_get_master_item="SELECT * FROM master_item WHERE masti_id='$masti_id'";
	   $q_get_uom="SELECT * FROM uom WHERE uom_id='$uom_id'";
	   $q_get_vendor="SELECT * FROM customer WHERE cust_id='$vend_id'";
	   $exec_get_master_item=mysqli_query($db_connection, $q_get_master_item);
	   $exec_get_uom=mysqli_query($db_connection, $q_get_uom);
	   $exec_get_vendor=mysqli_query($db_connection, $q_get_vendor);
	   $total_uom=mysqli_num_rows($exec_get_uom);
	   $total_master_item=mysqli_num_rows($exec_get_master_item);
	   $total_vendor=mysqli_num_rows($exec_get_vendor); 
	   $field_vendor=mysqli_fetch_array($exec_get_vendor);
	   if (trim($itemd_code==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Kode Aset harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if (trim($masti_id=='') && $cru=='i')
	      {
		    ?>
			  <script language="javascript">
			    alert('Deskripsi Isi Aset harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else  
	   if ($itemd_year=='' || $itemd_year<0)
	      {
		    ?>
			  <script language="javascript">
			    alert('Tahun pembuatan harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
       else
	   if ($itemd_weight=='' || $itemd_weight<0)
	      {
		    ?>
			  <script language="javascript">
			    alert('Berat harus lebih besar atau sama dengan 0!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
       else
	   if ($itemd_capacity=='' || $itemd_capacity<0)
	      {
		    ?>
			  <script language="javascript">
			    alert('Kapasitas harus lebih besar atau sama dengan 0!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
       else
	   if ($total_uom==0)
	      {
		    ?>
			  <script language="javascript">
			    alert('Satuan tidak ditemukan!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
       else	   
	   if ($itemd_acquired_date=='')
	      {
		    ?>
			  <script language="javascript">
			    alert('Tanggal Perolehan harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else	 
	   if (trim($whsl_id=='') && $cru=='i')
	      {
		    ?>
			  <script language="javascript">
			    alert('Lokasi Gudang harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if ($total_vendor==0)
	      {
		    ?>
			  <script language="javascript">
			    alert('Vendor tidak ditemukan!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if ($field_vendor['cust_status']=='1')
	      {
		    ?>
			  <script language="javascript">
			    alert('Status Vendor adalah InActive!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if ($itemd_id=='')  //jika tambah data
	      {
		    $q_check_item_detail="select *, branch_name from item_detail 
			                      inner join branch on branch.branch_id=item_detail.branch_id 
								  where itemd_code='$itemd_code'";
		    $q_get_whs="SELECT * FROM warehouse_location WHERE whsl_id='$whsl_id'";
			$exec_check_item_detail=mysqli_query($db_connection, $q_check_item_detail);
			$exec_get_whs=mysqli_query($db_connection, $q_get_whs);
	        $total_whs=mysqli_num_rows($exec_get_whs);
	        $field_whs=mysqli_fetch_array($exec_get_whs);
			if (mysqli_num_rows($exec_check_item_detail)>0)
			   {
			     $branch_name=mysqli_fetch_array($exec_check_item_detail);
			     ?>
			       <script language="javascript">
				     var x='<?php echo $branch_name['branch_name'];?>';
		             alert('Duplikasi Kode Item pada Kantor Cabang : '+x);
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			if ($total_whs==0)
	           {
		         ?>
			       <script language="javascript">
			         alert('Lokasi Gudang tidak ditemukan!');
				     window.location.href='javascript:history.back(1)';
			       </script>
			     <?php 
		       }
	        else
	        if ($field_whs['whsl_type']=='0')
		       {
		         ?>
                     <script language="javascript">
				       alert('Lokasi Gudang yang dipilih harus bertipe Sub Lokasi Gudang!');
				       window.location.href='javascript:history.back(1)';
			         </script>
		         <?php 
		       }
	        else
			   {
			      mysqli_autocommit($db_connection, false);
			      $q_input_item_detail="INSERT INTO item_detail (branch_id, masti_id, itemd_code, itemd_month, itemd_year, itemd_weight, itemd_capacity, uom_id, itemd_serial_no, 
				                                                 itemd_acquired_date, vend_id, itemd_qty, itemd_status, itemd_is_broken, itemd_is_dispossed, whsl_id, 
																 original_branch_id)
					                    VALUES ('$branch_id', '$masti_id','$itemd_code', '$itemd_month', '$itemd_year', '$itemd_weight', '$itemd_capacity', '$uom_id', 
										        '$itemd_serial_no', '$itemd_acquired_date','$vend_id', '1', '$itemd_status','0','0','$whsl_id','$branch_id')";
			//	  $q_input_item_whs="INSERT INTO item_detail_branch (branch_id, whsl_id, itemd_id) VALUES ('$branch_id','$whsl_id', (SELECT item_detail.itemd_id FROM item_detail 
			//	                                 INNER JOIN item_detail_branch ON item_detail_branch.itemd_id=item_detail.itemd_id 
			//									 WHERE branch_id='$branch_id' AND itemd_code='$itemd_code'))";
				  $exec_input_item_detail=mysqli_query($db_connection, $q_input_item_detail);
				  if ($exec_input_item_detail)
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
				//	   echo $q_input_item_detail;
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
		    $q_check_item_detail_transfer="SELECT * FROM transfer_detail WHERE itemd_id='$itemd_id'";
			$q_check_item_detail_issuing="SELECT * FROM issuing_detail WHERE itemd_id='$itemd_id'";
			$q_check_item_detail_return="SELECT * FROM return_detail WHERE itemd_id='$itemd_id'";
			$q_check_item_detail_broken="SELECT * FROM broken_detail WHERE itemd_id='$itemd_id'";
			$q_check_item_detail_write_off="SELECT * FROM write_off_detail WHERE itemd_id='$itemd_id'";
			$q_check_item_detail_dispossal="SELECT * FROM dispossal_detail WHERE itemd_id='$itemd_id'";
			$q_check_item_detail_change_description="SELECT * FROM change_item_description_detail WHERE itemd_id='$itemd_id'";
		//	echo $q_check_item_detail_issuing;
			$exec_check_item_detail_transfer=mysqli_query($db_connection, $q_check_item_detail_transfer);
			$exec_check_item_detail_issuing=mysqli_query($db_connection, $q_check_item_detail_issuing);
			$exec_check_item_detail_return=mysqli_query($db_connection, $q_check_item_detail_return);
			$exec_check_item_detail_broken=mysqli_query($db_connection, $q_check_item_detail_broken);
			$exec_check_item_detail_write_off=mysqli_query($db_connection, $q_check_item_detail_write_off);
			$exec_check_item_detail_dispossal=mysqli_query($db_connection, $q_check_item_detail_dispossal);
			$exec_check_item_detail_change_description=mysqli_query($db_connection, $q_check_item_detail_change_description);
			if (mysqli_num_rows($exec_check_item_detail_transfer)>0 || mysqli_num_rows($exec_check_item_detail_issuing)>0 || mysqli_num_rows($exec_check_item_detail_return)>0 || 
			    mysqli_num_rows($exec_check_item_detail_broken)>0 || mysqli_num_rows($exec_check_item_detail_write_off)>0 || mysqli_num_rows($exec_check_item_detail_dispossal)>0 || 
				mysqli_num_rows($exec_check_item_detail_change_description)>0)
			    {
				  $item_transfer='Tidak';
				  $item_issuing='Tidak';
				  $item_return='Tidak';
				  $item_broken='Tidak';
				  $item_write_off='Tidak';
				  $item_dispossal='Tidak';
				  $item_change_description='Tidak';
				  if (mysqli_num_rows($exec_check_item_detail_transfer)>0)
				      $item_transfer='Ya';
				  if (mysqli_num_rows($exec_check_item_detail_issuing)>0)
				      $item_issuing='Ya';
				  if (mysqli_num_rows($exec_check_item_detail_return)>0)
				      $item_return='Ya';
			      if (mysqli_num_rows($exec_check_item_detail_broken)>0)
				      $item_broken='Ya';
				  if (mysqli_num_rows($exec_check_item_detail_write_off)>0)
				      $item_write_off='Ya';
				  if (mysqli_num_rows($exec_check_item_detail_dispossal)>0)
				      $item_dispossal='Ya';
				  if (mysqli_num_rows($exec_check_item_detail_change_description)>0)
				      $item_change_description='Ya';
				  ?>
				    <script language="javascript">
					  var transfer='<?php echo $item_transfer;?>';
				      var issuing='<?php echo $item_issuing;?>';
				      var returns='<?php echo $item_return;?>';
				      var broken='<?php echo $item_broken;?>';
				      var wo='<?php echo $item_write_off;?>';
				      var disp='<?php echo $item_dispossal;?>';
				      var chd='<?php echo $item_change_description;?>';
					  alert('Item tidak dapat diupdate, Sudah digunakan pada Transaksi berikut ini :\n1. Transfer : '+transfer+'\n2. Pengeluaran : '+issuing+'\n3. Pengembalian : '+returns+'\n4. Kerusakan : '+broken+'\n5. Penghapusan : '+wo+'\n6. Penjualan : '+disp+'\n7. Perubahan Deskripsi : '+chd);
					  window.location.href='javascript:history.back(1)';
					</script>
				 <?php
				}
			else
			   {   
  		         $q_check_item_detail="select * from item_detail where itemd_id='$itemd_id'";
			     $exec_check_item_detail=mysqli_query($db_connection, $q_check_item_detail);
				 $field_item=mysqli_fetch_array($exec_check_item_detail);
			     if (mysqli_num_rows($exec_check_item_detail)>0)
			        {	
					  if ($itemd_status=='1' && $field_item['itemd_position']!='Internal')
					     {
						   ?>
                              <script language="javascript">
						         alert('Item tidak bisa diinactivekan!\nPosisi Item sedang berada diluar!');
							     window.location.href='javascript:history.back(1)';
					          </script>
				           <?php   
						 }
					  else
			          if ($itemd_code==$itemd_code_1)
			             {	 
			               mysqli_autocommit($db_connection, false);
				           $q_update_item_detail="UPDATE item_detail SET itemd_code='$itemd_code', masti_id='$masti_id', 
						                                 itemd_month='$itemd_month', itemd_year='$itemd_year', itemd_weight='$itemd_weight', 
														 itemd_capacity='$itemd_capacity', uom_id='$uom_id', itemd_serial_no='$itemd_serial_no', 
						                                 itemd_acquired_date='$itemd_acquired_date', vend_id='$vend_id', itemd_qty='1', itemd_status='$itemd_status'
						  	                      WHERE itemd_id='$itemd_id'";	
				       //    echo $q_update_item_detail;
						   $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
				           if ($exec_update_item_detail)
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
					        $q_check_item_detail="select *, branch_name 
							                      from item_detail 
												  inner join branch on branch.branch_id=item_detail.branch_id
												  where itemd_code='$itemd_code'";
					        $exec_check_item_detail=mysqli_query($db_connection, $q_check_item_detail);
					        if (mysqli_num_rows($exec_check_item_detail)==0)
					           {
						         mysqli_autocommit($db_connection, false);
				                 $q_update_item_detail="UPDATE item_detail SET itemd_code='$itemd_code', masti_id='$masti_id', itemd_month='$itemd_month', 
								                               itemd_year='$itemd_year', itemd_weight='$itemd_weight', itemd_capacity='$itemd_capacity', uom_id='$uom_id', 
															   itemd_serial_no='$itemd_serial_no', itemd_acquired_date='$itemd_acquired_date', vend_id='$vend_id', 
															   itemd_qty='1', itemd_status='$itemd_status'
						  	                            WHERE itemd_id='$itemd_id'";				
                               //  echo $q_update_item_detail;
							     $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
				                 if ($exec_update_item_detail)
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
								  $branch_name=mysqli_fetch_array($exec_check_item_detail);
					              ?>
                                     <script language="javascript">
									   var x='<?php echo $branch_name['branch_name'];?>';
						               alert('Duplikasi Kode Item Pada Kantor Cabang : '+x);
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
		                 alert('Item yang akan diupdate tidak ditemukan!');
				         window.close();
			           </script>
		             <?php  
				  }	 
			   }     
		  }	  
	 }
?>






