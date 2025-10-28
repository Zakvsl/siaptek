<?php
 include "../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title></head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../library/style.css";
  include "../library/db_connection.php";
  include "../library/library_function.php";
  
  $array_field=array("issuingh_code","issuingh_date","itemd_code", "masti_name", "itemd_capacity", "itemd_serial_no", "itemd_acquired_date", "cati_name", "itemd_status", "cust_name");
  $array_operator=array("=","!=","like");
  $array_sorting=array("asc", "desc");
  $continue='0';
  $row=100;	
  if (isset($_POST['s_page']))
     {
	   $paged=$_POST['s_page']; 
	   $first_data=(($paged-1)*$row);
	 }
  else
     {  
	   $paged=1;
	   $first_data=0;
	 }	  
  				
  if (isset($_POST['s_sorting_field'])) 
     {
	   $field_to_sort=$_POST['s_sorting_field'];
	 }				
  else
     {
	   $field_to_sort='issuingh_code';
	 }
  if (isset($_POST['s_sort']))
     {
	   $type_to_sort=$_POST['s_sort'];
	 } 	 
  else
     {
	   $type_to_sort='desc';
	 }	 
  	 
  $ordered_by="ORDER BY ".$field_to_sort." ".$type_to_sort;				
  $field="";
  $field_to_convert="";
  $operator="";
  $text_to_find_1="";			
				
  if (isset($_POST['btn_find']))
	 {
	   $field=$_POST['s_field'];
	   $field_to_convert=$_POST['s_field'];
	   $operator=$_POST['s_operator'];
	   $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
	   			 
	   if ($operator=='like')
	       $find_text=" like '%".$text_to_find_1."%'";
	   else
	      $find_text=" ".$operator."'".$text_to_find_1."'";	
	   
	   if ($text_to_find_1=='')	  
	      {
		    $continue='1';
		    ?>
			   <script language="javascript">
				 alert('Kata yang akan dicari harus diisi!');
				 window.location.href='javascript:history.back(1)';
			   </script>
		     <?php 
		  }
	   else
	   	  {		
		    $continue='0';			 
			$find_data=$field.$find_text." ".$ordered_by;	
			$q_show_item_vendor="SELECT issuingh_code, issuingh_date, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, 
			                            itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                        CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, cust_name, 
										DATEDIFF(NOW(), issuingh_date) AS issuing_aging
                                 FROM issuing_detail
                                 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                                 INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
								 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                                 INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
                                 WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
								       issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_type='1' AND $find_data 
								 LIMIT $first_data,$row"; 
			$q_page="SELECT count(item_detail.itemd_id) as total_page 
	                 FROM issuing_detail
                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                     INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                     WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
					       issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_type='1' AND $find_data";  	 
		  }
	 }
  else
  if (isset($_POST['s_sorting_field']) || isset($_POST['s_sort']))
     {
	   $field=$_POST['s_field'];
	   $operator=$_POST['s_operator'];
	   $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
	   $sorting="ORDER BY ".$_POST['s_sorting_field']." ".$_POST['s_sort'];
			 
	   if ($operator=='like')
	       $find_text=" like '%".$text_to_find_1."%'";
	   else
	      $find_text=" ".$operator."'".$text_to_find_1."'";	
	   
	   $continue='0';
	   if ($text_to_find_1=='')
	      {
		    $q_show_item_vendor="SELECT issuingh_code, issuingh_date, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, 
			                            itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                        CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, cust_name, 
										DATEDIFF(NOW(), issuingh_date) AS issuing_aging
                                 FROM issuing_detail
                                 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                                 INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
								 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                                 INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
                                 WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
								       issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_type='1' 
								 $sorting LIMIT $first_data,$row"; 
			$q_page="SELECT count(item_detail.itemd_id) as total_page 
	                 FROM issuing_detail
                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                     INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                     WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
					       issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_type='1' "; 
		  }
	   else 
	      {	  
	        $find_data=$field.$find_text;	
	        $q_show_item_vendor="SELECT issuingh_code, issuingh_date, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, 
			                            itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                        CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, cust_name, 
										DATEDIFF(NOW(), issuingh_date) AS issuing_aging
                                 FROM issuing_detail
                                 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                                 INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
								 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                                 INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
                                 WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
								       issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_type='1' AND $find_data $sorting 
							     LIMIT $first_data,$row"; 
			$q_page="SELECT count(item_detail.itemd_id) as total_page 
	                 FROM issuing_detail
                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                     INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                     WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
					       issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_type='1' AND $find_data";  
		  }			 
	 }
  else	 
  	 {
	   $q_show_item_vendor="SELECT issuingh_code, issuingh_date, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, 
			                            itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                        CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, cust_name, 
										DATEDIFF(NOW(), issuingh_date) AS issuing_aging
                            FROM issuing_detail
                            INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                            INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
							INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                            INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                            INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                            INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
                            WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
								  issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_type='1' 
						          $ordered_by LIMIT $first_data,$row"; 
	   $q_page="SELECT count(item_detail.itemd_id) as total_page 
	            FROM issuing_detail
                INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
				INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
                WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
					  issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_type='1' "; 
	 }	

// echo $q_show_item_vendor."<br>";	
// echo $q_page; 
	
?>
<style>
            #action {
                background-color : black;
                width : 120px;
                height : 28px;
                border-bottom : 1px solid #ccc;
            }
            
            #action ul {
                padding:0;
                margin:0;
                list-style-type:none;
            }
             
            #action ul li {
                float:left;
                position : relative;
            }
             
            #action ul li a {
                display:block;
                padding:5px 10px;
                color:#fff;
                text-decoration:none;
                font-family: calibri;
				font-size:14px;
            }
             
            #action ul li a:hover {
                background-color:#72b626;
            }
             
            /* Menu Dropdown */
            
            #action ul li ul {
                display: none;
            }
             
            #action ul li:hover ul {
                display:block;
                position: absolute;
            }
            
            #action ul li:hover ul li a {
                display:block;
                background-color : black;
                color : #fff;
                width : 100px;
                border-bottom : 1px solid #ccc;
            }
            
            #action ul li:hover ul li a:hover {
                background-color : #72b626;
            }
            
            #action ul li:hover > a {
			    background: #72b626;
	  	    }
        </style>
		
<form id="f_item_vendor" name="f_item_vendor" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" class="table-list-home">
    <tr>
      <td width="69%" scope="col"><h2>LAPORAN PENGELUARAN ASET KE VENDOR </h2></td>
      <td colspan="2" align="right">Kantor Cabang    : 
        <select id="s_branch" name="s_branch" style="width:300px">
		  <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td scope="col"><div align="left">
	    <?php 
		   if (isset($_POST['s_field']))
		      {
			    $field=$_POST['s_field'];
			    echo "<select name='s_field' id='s_field'>";
				foreach ($array_field as $fields)
				        {
						  if ($field==$fields)
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  ?>	  
						     <option value='<?php echo $fields;?>' <?php echo $selected_field;?>>
						  <?php	 
						         if ($fields=='issuingh_code')
								     echo "No Transaksi";
								 else
								 if ($fields=='issuingh_date')
								     echo "Tanggal";
								 else
								 if ($fields=='itemd_code')
								     echo "Kode Aset";
								 else
								 if ($fields=='masti_name')
								     echo "Deskripsi Isi Aset";
								 else
								 if ($fields=='itemd_serial_no')
								     echo "Serial No";   
								 else
								 if ($fields=='itemd_capacity')
								     echo "Kapasitas";
							     else
								 if ($fields=='itemd_acquired_date')
								     echo "Tanggal Perolehan"; 
								 else
								 if ($fields=='cati_name')
								     echo "Kategori"; 
								 else
								 if ($fields=='itemd_status')
								     echo "Status"; 
								 else
								 if ($fields=='cust_name')
								     echo "Nama Vendor"; 	 
									 
						   ?>			 
						     </option>
						  <?php	 
						}     
				echo "</select>";
			  }
		   else
		   	  {
		?>	 
        <select name="s_field" id="s_field">
		  <option value="issuingh_code">No Transaksi</option>
		  <option value="issuingh_date">Tanggal</option>
		  <option value="itemd_code">Kode Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="itemd_serial_no">Serial No</option>
		  <option value="itemd_capacity">Kapasitas</option>
		  <option value="itemd_acquired_date">Tanggal Perolehan</option>
		  <option value="cati_name">Kategori</option>
		  <option value="itemd_status">Status</option>
		  <option value="cust_name">Nama Vendor</option>
        </select>
		<?php } 
		if (isset($_POST['s_operator']))
		   { 
		     $operator=$_POST['s_operator'];
			 echo "<select name='s_operator' id='s_operator'>";
			 foreach($array_operator as $operators)
			        {
					  if ($operator==$operators)
					      $selected_operator="selected='selected'";
					  else
					      $selected_operator="";
					  ?>
					     <option value='<?php echo $operators;?>' <?php echo $selected_operator;?>><?php echo $operators?></option>
					  <?php	  	  
					}
			 echo "</select>";
		   }
		else
		   {   	 
		?>
        <select name="s_operator">
		  <option value="=">=</option>
		  <option value="!=">!=</option>
		  <option value="like">Any</option>
        </select>
		<?php }?>
        <input type="text" name="txt_find_1" id="txt_find_1"
		 value="<?php 
		         if (isset($_POST['txt_find_1']))
				     echo $_POST['txt_find_1']; 
				 else 
				     echo "";	    
		       ?>">
        <input type="submit" name="btn_find" id="btn_find" value="Cari"/>
      </div></td>
      <td width="21%" align="right" nowrap="nowrap">Urut Berdasarkan    :
        <select id="s_sorting_field" name="s_sorting_field" onchange="submit()">
          <?php 
		         if (isset($_POST['s_sorting_field']))
				    { 
					  foreach ($array_field as $field) :
					          {
							    if ($_POST['s_sorting_field']==$field)
								   $selected_field="selected";
								else
								   $selected_field="";  
								?>
          <option value="<?php echo $field;?>" <?php echo $selected_field; ?>>
          <?php 
									          if ($field=='issuingh_code')
								                  echo "No Transaksi";
								              else
											  if ($field=='issuingh_date')
								                  echo "Tanggal";
								              else
											  if ($field=='itemd_code')
								                  echo "Kode Aset";
								              else
								              if ($field=='masti_name')
								                  echo "Deskripsi Isi Aset";
								              else
								              if ($field=='itemd_serial_no')
								                  echo "Serial No";   
								              else
											  if ($field=='itemd_capacity')
								                  echo "Kapasitas";
							                  else
								              if ($field=='itemd_acquired_date')
								                  echo "Tanggal Perolehan"; 
								              else
								              if ($field=='cati_name')
								                  echo "Kategori"; 
								              else
								              if ($field=='itemd_status')
								                  echo "Status"; 
								              else
								              if ($field=='cust_name')
								                  echo "Nama Vendor"; 
											?>
          </option>
          <?php    
							  }
					  endforeach;  
					} 
			     else
				    {
					  ?>
          <option value="issuingh_code">No Transaksi</option>
		  <option value="issuingh_date">Tanggal</option>
		  <option value="itemd_code">Kode Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="itemd_serial_no">Serial No</option>
		  <option value="itemd_capacity">Kapasitas</option>
		  <option value="itemd_acquired_date">Tanggal Perolehan</option>
		  <option value="cati_name">Kategori</option>
		  <option value="itemd_status">Status</option>
		  <option value="cust_name">Nama Vendor</option>
          <?php
					}		
		   ?>
        </select>
        <select name="s_sort" id="s_sort" onchange="submit()">
          <?php
						      if (isset($_POST['s_sort']))
							     {
								   foreach ($array_sorting as $sorted) :
								           {
										     if ($_POST['s_sort']==$sorted)
											    $selected_sort="selected";
											 else
											    $selected_sort="";	 
								             ?>
          <option value="<?php echo $sorted; ?>" <?php echo $selected_sort; ?>>
            <?php if ($sorted=="asc") echo "Asc"; else echo "Desc"; ?>
          </option>
          <?php	
										   }
								   endforeach;		    	   
								 }
							  else
							     {
								   ?>
          <option value="asc">Asc</option>
          <option value="desc" selected="selected">Desc</option>
          <?php 
								 }	      
						?>
        </select></td>
<td width="9%" align="left"><div id="action" align="left">
                              <ul id="nav">
                                <li><a href="" title="Retrive Refill Report" onclick="call_export()">Ekspor Data</a></li>
                              </ul>
                            </div></td>
    </tr>

    <tr>
      <td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="2%" scope="col" class="th_ltb">No</th>
          <th width="12%" scope="col" class="th_ltb">No Transaksi </th>
          <th width="5%" scope="col" class="th_ltb">Tanggal</th>
          <th width="7%" scope="col" class="th_ltb">Kode Aset </th>
          <th width="18%" scope="col" class="th_ltb">Deskripsi Isi Aset </th>
          <th width="7%" scope="col" class="th_ltb">Serial No </th>
          <th width="7%" scope="col" class="th_ltb">Kapasitas</th>
          <th width="5%" scope="col" class="th_ltb">Qty</th>
          <th width="6%" scope="col" class="th_ltb">Umur</th>
          <th width="8%" scope="col" class="th_ltb">Kategori</th>
          <th width="5%" scope="col" class="th_ltb">Status</th>
          <th width="16%" scope="col" class="th_ltb">Nama Vendor </th>
          </tr>
		  <?php
		     if ($continue=='0')
			    {
			 	  $exec_query=mysqli_query($db_connection, $q_show_item_vendor);
				  $total_item_vendor=mysqli_num_rows($exec_query);
				  $no=0;
				  while ($data_item_vendor=mysqli_fetch_array($exec_query))
				        {
					      $no++;
   		  ?>
        <tr>
          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data_item_vendor['itemd_id'];?>" /></td>
          <td align="center" class="td_lb"><?php echo $no;?></td>
          <td class="td_lb"><?php echo $data_item_vendor['issuingh_code'];?></td>
          <td class="td_lb"><?php echo $data_item_vendor['issuingh_date'];?></td>
          <td class="td_lb"><?php echo $data_item_vendor['itemd_code'];?></td>
          <td class="td_lb"><?php echo $data_item_vendor['masti_name'];?></td>
          <td class="td_lb"><?php echo $data_item_vendor['itemd_serial_no'];?></td>
          <td class="td_lb"><?php echo $data_item_vendor['itemd_capacity']." ".$data_item_vendor['uom_name'];?></td>
          <td class="td_lb"><?php echo $data_item_vendor['itemd_qty']." Cylinder";?></td>
          <td class="td_lb"><?php echo $data_item_vendor['issuing_aging'];?>&nbsp;hari</td>
          <td class="td_lb"><?php echo $data_item_vendor['cati_name'];?></td>
          <td class="td_lb"><?php echo $data_item_vendor['itemd_status'];?></td>
          <td class="td_lb"><?php echo $data_item_vendor['cust_name'];?></td>
          </tr><?php }} ?>
      </table></td>
    </tr>
	      <?php
              $query_exec=mysqli_query($db_connection, $q_page) or die (mysqli_error());
              $total_rows=mysqli_fetch_array($query_exec);
              $maks_rows=ceil($total_rows['total_page']/$row);
		  ?>	  
																								   
	<tr>
        <td align="left">Total : <?php echo $total_rows['total_page'];?>&nbsp;data</td>
        <td colspan="2" align="right">Halaman ke  : 
		                                                                               <select name="s_page" id="s_page" onchange="document.f_item_vendor.submit()">
																								 <?php
																								      $pages=array();
																								      if ($maks_rows==0)
																								         {
																									       $maks_rows=1;
																									     }
                                                                                        		      $i=0;
                                                                                     			      while ($i<$maks_rows)
			                                                                                                {
		                                                                                    		          $i++;
																										      $pages[]=$i;
			                                                                                         	     }
																									  if (isset($_POST['s_page']))
																									     {
																										   foreach ($pages as $page)
																										           {
																												     if ($_POST['s_page']==$page)
																													    {
																														  $selected_page="selected";
																														}
																												     else
																													    {
																														  $selected_page="";
																														}	
																													 ?>
																													    <option value="<?php echo $page;?>" <?php echo $selected_page;?>><?php echo $page;?></option>
																													 <?php		
																												   }
																								//		   endforeach;		   
																										 }
																									  else
																									     {
																										   foreach ($pages as $page)
																										           {
																													 ?>
																													    <option value="<?php echo $page;?>"><?php echo $page;?></option>  
																													 <?php		
																												   }
																								//		   endforeach;		   
																										 }	 		 
																								 ?> 
	                                                                                   </select>
		                                                                               dari
		                                                                               <?php  echo $maks_rows; ?></td>
    </tr>
  </table>
</form>

<script language="javascript">
    var open_child=null;
   
    function select_unselect_all(x)
           {
		     var check_select=document.getElementsByName('check_all_data');
		     var select_unselect_all_data = document.getElementsByName('check_data[]');
			 
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
	
		   
	function input_data()
	         {
			   var w=415;
			   var h=420;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   open_child=window.open("../data/item_vendor/cru_item_vendor.php?c=i", "f_cru_item_vendor", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
			 }	
			 
	function disable_parent_window()  // akan dipanggil di tag <body>
	         {
			   if (open_child && !open_child.closed) 
			      {
                    open_child.focus();
				  }	
			 }		    
	 
	function update_data()
	         {
			   var w=415;
			   var h=420;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   var x=document.getElementsByName('check_data[]').length;
			   var y=document.getElementsByName('check_data[]');
			   var z=0;
			   var id='';
               for (i=0; i<x; i++)
			       {
				     if (y[i].checked==true)
				        {
					      z++;   
						  if (z==1)
						     {
						       var value_id=[y[i].value];	
						     }
						  else
						     {
					           value_id.push(y[i].value);  // memasukan nilai array pada array yang sudah ada
						     }       
					    }   
				   } 	
				 
			   if (z==0)
			      {
				    alert('Tidak ada data yang dipilih!');
				  }
			   else
			   if (z>1)
			      {
				    alert('Silahkan pilih salah satu data!');  
				  }	  
		       else
			      { 
				    open_child=window.open('../data/item_vendor/cru_item_vendor.php?c=u&id='+value_id, 'f_cru_item_vendor', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
				  }
			 }  
			 
  function delete_data()
           {
		     var x=document.getElementsByName('check_data[]').length;
			 var y=document.getElementsByName('check_data[]');
			 var z=0;
			 var id='';
             for (i=0; i<x; i++)
			     {
				   if (y[i].checked==true)
				      {
					    z++;   
						if (z==1)
						   {
						     var value_id=[y[i].value];	
						   }
						else
						   {
					         value_id.push(y[i].value);  // memasukan nilai array pada array yang sudah ada
						   }       
					  }   
				 } 	
				 
			 if (z==0)
			    {
				  alert('Tidak ada data yang dipilih!');
				}
		     else
			    {
				  var answer=confirm('Apakah yakin akan menghapus data terpilih?');
				  if (answer)
				     {
					   document.f_item_vendor.action="../data/item_vendor/delete_item_vendor.php";
					   document.f_item_vendor.submit();
					 }  
				}
		   }   
			 
  function active_inactive()
           {
		     var x=document.getElementsByName('check_data[]').length;
			 var y=document.getElementsByName('check_data[]');
			 var z=0;
			 var id='';
             for (i=0; i<x; i++)
			     {
				   if (y[i].checked==true)
				      {
					    z++;   
						if (z==1)
						   {
						     var value_id=[y[i].value];	
						   }
						else
						   {
					         value_id.push(y[i].value);  // memasukan nilai array pada array yang sudah ada
						   }       
					  }   
				 } 	
				 
			 if (z==0)
			    {
				  alert('Tidak ada data yang dipilih!');
				}
		     else
			    {
				  var answer=confirm('Apakah yakin Item akan di Activasi/InActivasi?');
				  if (answer)
				     {
					   document.f_item_vendor.action="../data/item_vendor/active_invactive_item.php";
					   document.f_item_vendor.submit();
					 }  
				}
		   }
		   
	function call_export()
	         {
			   var field='<?php echo $field_to_convert;?>';
	           var operator='<?php echo $operator;?>';
	           var text_to_find='<?php echo $text_to_find_1;?>';
			   open_child=window.open("../data/vendor_item_report/export_to_excel.php?f="+field+"&o="+operator+"&t="+text_to_find);
			 }  
</script>

</body>
</html>
