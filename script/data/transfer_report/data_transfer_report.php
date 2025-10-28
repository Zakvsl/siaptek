<?php
  include "../library/check_session.php";
  include "../library/db_connection.php";
  $branch_id=$_SESSION['ses_id_branch'];
?> 
 <link type="text/css" rel="stylesheet" href="../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../library/development-bundle/ui/ui.core.js"></script>
    <script src="../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_date").datepicker(
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
  $current_date=date('d-m-Y');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <table width="425" border="0" class="table-list-home">
    <tr>
      <td colspan="5" align="center"><strong>LAPORAN PERPINDAHAN ASET</strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="127" nowrap="nowrap">Tipe Perpindahan</td>
      <td width="5">:</td>
      <td width="271"><?php
	                     if (isset($_POST['rb_type_1']))
						     $type_transfer=$_POST['rb_type_1'];
						 else
						     $type_transfer='0';
						 
						 if ($type_transfer=='0')
						    {
							  echo "<input type='radio' id='rb_type_1' name='rb_type_1' value='0' checked='checked' onclick='submit()'/>Keluaran";
                              echo "<input type='radio' id='rb_type_1' name='rb_type_1' value='1' onclick='submit()'/>Masukan</td>";
							}
						 else
						    {
							  echo "<input type='radio' id='rb_type_1' name='rb_type_1' value='0' onclick='submit()'/>Keluaran";
                              echo "<input type='radio' id='rb_type_1' name='rb_type_1' value='1' checked='checked' onclick='submit()'/>Masukan</td>";
							}
	                  ?>    
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	</tr>
    <tr>
      <td>Tipe Tampilan </td>
      <td>:</td>
      <td><?php
	        if (isset($_POST['rb_type_2']))
				$type_view=$_POST['rb_type_2'];
			else
				$type_view='0';
			if ($type_view=='0')
			   {
				 echo "<input type='radio' id='rb_type_2' name='rb_type_2' value='0' checked='checked' onclick='call_disable_item(0)'/>Tampilkan Semua";
                 echo "<input type='radio' id='rb_type_2' name='rb_type_2' value='1' onclick='call_disable_item(1)'/>Tampilkan Sebagian</td>";
			   }
			else
			   {
				 echo "<input type='radio' id='rb_type_2' name='rb_type_2' value='0' onclick='call_disable_item(0)'/>Tampilkan Semua";
                 echo "<input type='radio' id='rb_type_2' name='rb_type_2' value='1' checked='checked' onclick='call_disable_item(1)'/>Tampilkan Sebagian</td>";
			   }
	      ?>    
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	</tr>
    <tr>
      <td valign="top">Cari Aset</td>
      <td valign="top">:</td>
      <td><input type="text" id="txt_find_tube" name="txt_find_tube" 
	             <?php
				     if (isset($_POST['btn_find']))
					     echo "value='".$_POST['txt_find_tube']."'";
					 else
					     echo "value=''";
				 ?>/>
        <input type="submit" id="btn_find" name="btn_find" value="Cari" onClick="select_all_item()" 
		       <?php
			       if ($type_view=='0')
				       echo "disabled='disabled'";
			   ?>/></td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Pilih Aset</td>
      <td valign="top">:</td>
      <td><?php
	                if (isset($_POST['btn_find']))
					    $find_text="AND (itemd_code like '%".$_POST['txt_find_tube']."%' OR masti_name like '%".$_POST['txt_find_tube']."%' OR 
							             itemd_serial_no like '%".$_POST['txt_find_tube']."%')";
				    else
						$find_text=""; 
	                
					if ($type_transfer=='0')
	                    $q_get_item="SELECT DISTINCT(item_detail.itemd_id), itemd_code, masti_name 
                                     FROM item_detail
                                     INNER JOIN transfer_detail ON transfer_detail.itemd_id=item_detail.itemd_id 
                                     INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                     WHERE transfer_header.branch_id='$branch_id' $find_text
                                     ORDER BY masti_name ASC";
					else
					    $q_get_item="SELECT DISTINCT(item_detail.itemd_id), itemd_code, masti_name 
                                     FROM item_detail
                                     INNER JOIN receipt_transfer_detail ON receipt_transfer_detail.itemd_id=item_detail.itemd_id 
                                     INNER JOIN receipt_transfer_header ON receipt_transfer_header.rth_id=receipt_transfer_detail.rth_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                     WHERE receipt_transfer_header.branch_id='$branch_id' $find_text 
                                     ORDER BY masti_name ASC";
									 
						 $exec_get_item=mysqli_query($db_connection, $q_get_item);
						 $total_item=mysqli_num_rows($exec_get_item);
	                   ?><select name="s_item[]" size='10' multiple="multiple" id="s_item[]" 
					     style="width:400px" <?php 
						                         if ($type_view=='0')
												     echo "disabled='disabled'";
												 else
												     echo "";
						                     ?>>
                       <?php
						  
						  while ($field_item=mysqli_fetch_array($exec_get_item))
						        {
								  echo "<option value='".$field_item['itemd_id']."'>".$field_item['masti_name']." [".$field_item['itemd_code']."]</option>";
								}
						?>
      </select>	  </td>
	  <td><input type="button" name="btn_to_right" value="&gt;&gt;&gt;" onClick="move_index('left')"/>
	    <br />
        <input type="button" name="btn_to_left" value="&lt;&lt;&lt;" onClick="move_index('right')"/></td>
	  <td><select name="s_item_1[]"  multiple="multiple" id="s_item_1[]" style="width:400px" size="10">
        <?php
		               if (isset($_POST['btn_find']))
			              {
				            $itemd_id='';
							$list_item=array();
					        if (isset($_POST['s_item_1']))
                                $list_item=$_POST['s_item_1'];
				            if (count($list_item)>0)
					           {
						         foreach ($list_item as $tube_id)
								         {
								           if ($itemd_id=='')
										       $itemd_id="'".$tube_id."'";
								           else
										       $itemd_id=$itemd_id.",'".$tube_id."'";
								         }
						         $q_get_item_1="SELECT item_detail.itemd_id, itemd_code, masti_name 
						                        FROM item_detail
									            INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
									            WHERE branch_id='$branch_id' AND itemd_id IN ($itemd_id)
										        ORDER BY masti_name, itemd_code ASC";
						         $exec_get_item_1=mysqli_query($db_connection, $q_get_item_1); 
								 if (mysqli_num_rows($exec_get_item_1)>0)
								    {
									  while ($field_item_1=mysqli_fetch_array($exec_get_item_1))
						                    {
								              echo "<option value='".$field_item_1['itemd_id']."'>".$field_item_1['masti_name']." [".$field_item_1['itemd_code']."]</option>";
								            }
									}
						       }
				          }
				  ?>
      </select></td>
    </tr>
    <tr>
      <td>Per Tanggal</td>
      <td>:</td>
      <td><input type="text" id="txt_date" name="txt_date" 
	       value="<?php 
		             if (isset($_POST['txt_date']))
					     $current_date=$_POST['txt_date'];
					 echo $current_date;
		          ?>"/>
	  </td>
	  <td></td>
	  <td></td>
    </tr>
    <tr>
      <td>Tampilkan Secara</td>
      <td>:</td>
      <td nowrap="nowrap"><?php
	                           if (isset($_POST['rb_type_3']))
				                   $type_as=$_POST['rb_type_3'];
			                   else
				                   $type_as='0';
			                   if ($type_as=='0')
			                      {
				                    echo "<input type='radio' id='rb_type_3' name='rb_type_3' value='0' checked='checked'/>Tampilkan Langsung";
                                    echo "<input type='radio' id='rb_type_3' name='rb_type_3' value='1'/>Export ke Excel</td>";
			                      }
			                   else
			                      {
				                    echo "<input type='radio' id='rb_type_3' name='rb_type_3' value='0'/>Tampilkan Langsung";
                                    echo "<input type='radio' id='rb_type_3' name='rb_type_3' value='1' checked='checked'/>Export ke Excel</td>";
			                      }
	      ?>    
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	</tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="button" id="btn_process" name="btn_proses" value="Proses" onClick="call_process()"/>
	  </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>	  
    </tr>
  </table>
</form>
</body>
</html>

<script language="javascript">
  function call_process()
           {
		     var type1=document.getElementById('rb_type_1').checked; 
			 var type2=document.getElementById('rb_type_2').checked; 
			 var type3=document.getElementById('rb_type_3').checked; 
			 var lg_choose_item_1=document.getElementById('s_item_1[]').length; 
			 
			 if (type2==false && lg_choose_item_1==0)
			    {
			      alert('Pilih Aset yang akan ditampilkan!');
				  exit;
				}
			 else
			 if (type2==false && lg_choose_item_1>0)
			    {
				  list_item=document.getElementById('s_item_1[]');
				  for (i=0;i<lg_choose_item_1;i++)
					  {
						list_item.options[i].selected=true;
					  }
				}
							 
			 if (type1==true && type3==true)
			     form1.action='../data/transfer_report/view_data_transfer_outgoing.php';
			 else
			 if (type1==false && type3==true)
			     form1.action='../data/transfer_report/view_data_transfer_incoming.php';
			 else
			 if (type1==true && type3==false)
			     form1.action='../data/transfer_report/export_to_excel_outgoing.php';
			 else
			 if (type1==false && type3==false)
			     form1.action='../data/transfer_report/export_to_excel_incoming.php';
			 form1.submit();  
		   }
		 
 function call_disable_item(x)
          {
		    if (x=='0')
			   {
			     document.getElementById('s_item[]').disabled=true;
				 document.getElementById('btn_find').disabled=true;
			   }	 
			else
			if (x=='1')
			   {
			     document.getElementById('s_item[]').disabled=false;
				 document.getElementById('btn_find').disabled=false;
			   }	 
		  }

 function move_index(a)
         {
		   if (a=='left')
		      {
                var x=document.getElementById('s_item[]');
		        var y=document.getElementById('s_item_1[]');
			  }
		   else
		      {
                var x=document.getElementById('s_item_1[]');
		        var y=document.getElementById('s_item[]');  
			  }
			  
		   var lg_option=x.length;
		   var delete_option=[];
		   for (i=0;i<lg_option;i++)
		       {
			     if (x[i].selected==true)
				    {
					  var nilai_text=x[i].text;
		              var nilai_value=x[i].value;
					  y.options[y.options.length] = new Option(nilai_text, nilai_value);
					  delete_option.push(i);
					}
			   }
		   
		   lg_delete_option=delete_option.length; 
		   while (lg_delete_option>0)
		         {
				   x.remove(delete_option[lg_delete_option-1]);
				   lg_delete_option--;
				 }	
		 }
</script>



