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
                            $("#txt_start_date").datepicker(
							 {
                               dateFormat : "dd-mm-yy",
                               changeMonth : true,
                               changeYear : true
                             }
							                          );
						    $("#txt_end_date").datepicker(
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
      <td colspan="5"><strong>LAPORAN PERGERAKAN ASET</strong></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Tipe Tampilan </td>
      <td valign="top">:</td>
      <td>	      
	      <?php
		       if (isset($_POST['btn_find']))
			      {
				    if ($_POST['rb_type_1']=='0')
					   {
					     $checked1_1="checked='checked'";
						 $checked1_2="";
					   }
					else
					   {
					     $checked1_1="";
					     $checked1_2="checked='checked'";
					   } 
				  }
			   else 
			      {
				    $checked1_1="checked='checked'";
					$checked1_2="";
				  }
		  ?>
	      <input id="rb_type_1" name="rb_type_1" type="radio" value="0" onChange="call_disable_item(0)" <?php echo $checked1_1;?>>Tampilkan Semua
          <input id="rb_type_1" name="rb_type_1" type="radio" value="1" onChange="call_disable_item(1)" <?php echo $checked1_2;?>>Tampilkan Sebagian</td>
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
				      if (isset($_POST['rb_type_1'])=='0')
					      echo "disabled='disabled'";
					  else
					      echo "";
				 ?>
				 /></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="127" valign="top">Pilih Aset</td>
      <td width="5" valign="top">:</td>
      <td width="271"><?php
	                    if (isset($_POST['btn_find']))
						    $find_text="AND (itemd_code like '%".$_POST['txt_find_tube']."%' OR masti_name like '%".$_POST['txt_find_tube']."%' OR 
							             itemd_serial_no like '%".$_POST['txt_find_tube']."%')";
						else
						    $find_text="";  
	                    $q_get_item="SELECT item_detail.itemd_id, itemd_code, masti_name 
						             FROM item_detail
									 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
									 WHERE branch_id='$branch_id' $find_text ORDER BY masti_name, itemd_code ASC";
						 $exec_get_item=mysqli_query($db_connection,$q_get_item);
						 $total_item=mysqli_num_rows($exec_get_item);
	                   ?><select name="s_item[]" size='10' multiple="multiple" id="s_item[]" 
					             style="width:400px" 
								 <?php
								    if ($checked1_1!="")
									    echo "disabled='disabled'";
								 ?> >
	                   <?php
						  
						  while ($field_item=mysqli_fetch_array($exec_get_item))
						        {
								  echo "<option value='".$field_item['itemd_id']."'>".$field_item['masti_name']." [".$field_item['itemd_code']."]</option>";
								}
					   ?></select></td>
	  <td>
	    <input type="button" name="btn_to_right" value=">>>" onClick="move_index('left')"/><br />
		<input type="button" name="btn_to_left" value="<<<" onClick="move_index('right')"/>	  </td>
	  <td> 
	      <select name="s_item_1[]"  multiple="multiple" id="s_item_1[]" style="width:400px" size="10">
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
						         $exec_get_item_1=mysqli_query($db_connection,$q_get_item_1); 
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
		  </select>	  </td> 
    </tr>
    <tr>
      <td>Mulai dari</td>
      <td>:</td>
      <td><input type="text" id="txt_start_date" name="txt_start_date" 
	             value="<?php 
				            if (isset($_POST['btn_find']))
							    echo $_POST['txt_start_date'];
						    else
							    echo $current_date;
				        ?>"/></td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Sampai dengan</td>
      <td>:</td>
      <td><input type="text" id="txt_end_date" name="txt_end_date" 
	             value="<?php 
				            if (isset($_POST['btn_find']))
							    echo $_POST['txt_end_date'];
						    else
				                echo $current_date;
				        ?>"/></td>
	  <td>&nbsp;</td>
	  <td><label></label></td>
    </tr>
    
    <tr>
      <td valign="top" nowrap="nowrap">Tampilkan Semua Cabang</td>
      <td valign="top">:</td>
      <td><input type="checkbox" id="cb_all" name="cb_all" value="1" >Ya</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top" nowrap="nowrap">Tampilkan Secara</td>
      <td valign="top">:</td>
      <td>
	      <?php
		       if (isset($_POST['btn_find']))
			      {
				    if ($_POST['rb_type_2']=='0')
					   {
					     $checked2_1="checked='checked'";
						 $checked2_2="";
					   }
					else
					   {
					     $checked2_1="";
					     $checked2_2="checked='checked'";
					   } 
				  }
			   else 
			      {
				    $checked2_1="checked='checked'";
					$checked2_2="";
				  }
		  ?>
	      <input id="rb_type_2" name="rb_type_2" type="radio" value="0" <?php echo $checked2_1;?>>Langsung
          <input id="rb_type_2" name="rb_type_2" type="radio" value="1" <?php echo $checked2_2;?>>Export ke Excel</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="button" id="btn_process" name="btn_proses" value="Proses" onClick="call_process()"/></td>
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
		     var type_1=document.getElementById('rb_type_1').checked; 
			 var lg_choose_item=document.getElementById('s_item[]').length; 
			 var lg_choose_item_1=document.getElementById('s_item_1[]').length; 
			 var type_2=document.getElementById('rb_type_2').checked; 
			 var start_date=document.getElementById('txt_start_date').value;
			 var d_1=start_date.substring(0,2);
			 var m_1=start_date.substring(3,5);
			 var y_1=start_date.substring(6,10);
			 var start_date=Date.parse(y_1+'-'+m_1+'-'+d_1);
			 var end_date=document.getElementById('txt_end_date').value;
			 var d_2=end_date.substring(0,2);
			 var m_2=end_date.substring(3,5);
			 var y_2=end_date.substring(6,10);
			 var end_date=Date.parse(y_2+'-'+m_2+'-'+d_2);
			 var y=0;
			 for (i=0;i<lg_choose_item;i++)
			     {
				   x=document.getElementById('s_item[]');
				   if (x[i].selected)
				      {
					    y++;
					  } 
				 }
				 
			 if (type_1==false && lg_choose_item_1==0)
			    {
			      alert('Pilih Aset yang akan ditampilkan!');
				  exit;
				}  
			 else
			 if (start_date>end_date)
			    {
				  alert('Tanggal "Mulai Dari" harus lebih kecil daripada tanggal "Sampai Dengan"!');
				  exit;
				}
			 else
			    {
				  if (type_1==false && lg_choose_item_1>0)
				     {
					   list_item=document.getElementById('s_item_1[]');
					   for (i=0;i<lg_choose_item_1;i++)
					       {
						     list_item.options[i].selected=true;
						   }
					 }
					 
				  if (type_2==true)
				      form1.action='../data/history_movement_report/view_data_history_movement.php';
				  else
			         form1.action='../data/history_movement_report/export_to_excel.php';
			      form1.submit();
				}  
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
		 
 function select_all_item()
          {
		    var x=document.getElementById('s_item_1[]');
			lg_x=x.length;
			for (i=0;i<lg_x;i++)
			    {
				  x.options[i].selected=true;
				}
		  }

</script>



