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
 <link type="text/css" rel="stylesheet" href="../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../library/development-bundle/ui/ui.core.js"></script>
    <script src="../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_date_1").datepicker(
							 {
                               dateFormat : "dd-mm-yy",
                               changeMonth : true,
                               changeYear : true
                             }
							                          );		
							$("#txt_date_2").datepicker(
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
  include "../library/style.css";
  include "../library/db_connection.php";
  include "../library/library_function.php";
  $current_date=date('d-m-Y');
  $array_field=array("reth_code", "reth_date", "issuingh_code", "cust_name", "itemd_code", "masti_name", "itemd_serial_no", "cati_name", "retd_is_canceled");
  $array_operator=array("=",">","<",">=","<=","!=","between","like");
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
	   $field_to_sort='reth_code';
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
  $text_to_find_2="";			
				
  if (isset($_POST['btn_find']))
	 {
	   $field=$_POST['s_field'];
	   $field_to_convert=$_POST['s_field'];
	   $operator=$_POST['s_operator'];
	   if ($field=='reth_date')
	      {
		    if ($operator=='between')
			   {
			     $text_to_find_1=get_date_2(htmlspecialchars(trim($_POST['txt_date_1']))); 
				 $text_to_find_2=get_date_2(htmlspecialchars(trim($_POST['txt_date_2']))); 
			   }
			else
			   {
			     $text_to_find_1=get_date_2(htmlspecialchars(trim($_POST['txt_date_1'])));
			   }
		  }
	   else
	      {  
	        $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
		  } 	
	   
	   			 
	   if ($operator=='like')
	       $find_text=" like '%".$text_to_find_1."%'";
	   else
	   if ($operator=='between')
	      { 
	       $find_text=" between '".$text_to_find_1."' AND '".$text_to_find_2."'";
		  } 
	   else
	      $find_text=" ".$operator."'".$text_to_find_1."'";	
	   
	   if ($field=='reth_date' && $operator=='between' && ($text_to_find_2<$text_to_find_1))
	      {
			 $continue='1';
			 ?>
			    <script language="javascript">
				  alert('Batas tanggal pertama harus lebih kecil daripada batas tanggal kedua!');
				  window.location.href='javascript:history.back(1)';
			    </script>
		     <?php  
			 exit;
		  }
	   else
	   if ($text_to_find_1=='')	  
	      {
		    $continue='1';
		    ?>
			   <script language="javascript">
				 alert('Kata yang akan dicari harus diisi!');
				 window.location.href='javascript:history.back(1)';
			   </script>
		    <?php 
			exit;
		  }
	   else
	   	  {		
		    $continue='0';			 
			$find_data=$field.$find_text." ".$ordered_by;	
			$q_show_returning_report="SELECT reth_code, reth_date, issuingh_code, cust_name, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
                                      itemd_qty, cati_name, 
                                      CASE retd_is_canceled
                                           WHEN '0' THEN 'Tidak'
                                           WHEN '1' THEN 'Ya'
                                      END retd_is_canceled
                                      FROM return_detail
                                      INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                                      INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                                      INNER JOIN issuing_detail ON issuing_header.issuingh_id=issuing_detail.issuingh_id AND issuing_detail.issuingd_id=return_detail.issuingd_id 
                                      INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
									  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                      INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                      INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                      INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                      WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND 
									        $find_data LIMIT $first_data,$row";
			$q_page="SELECT count(*) as total_page 
	                 FROM return_detail
                     INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                     INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                     INNER JOIN issuing_detail ON issuing_header.issuingh_id=issuing_detail.issuingh_id AND issuing_detail.issuingd_id=return_detail.issuingd_id 
                     INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                     WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND $find_data";  	 
		  }
	 }
  else
  if (isset($_POST['s_sorting_field']) || isset($_POST['s_sort']))
     {
	   $field=$_POST['s_field'];
	   $operator=$_POST['s_operator'];
	   if ($field=='reth_date')
	      {
		    if ($operator=='between')
			   {
			     $text_to_find_1=get_date_2(htmlspecialchars(trim($_POST['txt_date_1']))); 
				 $text_to_find_2=get_date_2(htmlspecialchars(trim($_POST['txt_date_2']))); 
			   }
			else
			   {
			     $text_to_find_1=get_date_2(htmlspecialchars(trim($_POST['txt_date_1'])));
			   }
		  }
	   else
	      {  
	        $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
		  } 
	//   $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
	   $sorting="ORDER BY ".$_POST['s_sorting_field']." ".$_POST['s_sort'];
			 
	   if ($operator=='like')
	       $find_text=" like '%".$text_to_find_1."%'";
	   else
	   if ($operator=='between')
	       $find_text=" between '".$text_to_find_1."' AND '".$text_to_find_2."'";
	   else
	      $find_text=" ".$operator."'".$text_to_find_1."'";	
	   
	   $continue='0';
	   if ($text_to_find_1=='')
	      {
		    $q_show_returning_report="SELECT reth_code, reth_date, issuingh_code, cust_name, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
                                      itemd_qty, cati_name, 
                                      CASE retd_is_canceled
                                           WHEN '0' THEN 'Tidak'
                                           WHEN '1' THEN 'Ya'
                                      END retd_is_canceled
                                      FROM return_detail
                                      INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                                      INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                                      INNER JOIN issuing_detail ON issuing_header.issuingh_id=issuing_detail.issuingh_id AND issuing_detail.issuingd_id=return_detail.issuingd_id 
                                      INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
									  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                      INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                      INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                      INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                      WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' 
								      $sorting LIMIT $first_data,$row"; 
			$q_page="SELECT count(*) as total_page 
	                 FROM return_detail
                     INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                     INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                     INNER JOIN issuing_detail ON issuing_header.issuingh_id=issuing_detail.issuingh_id AND issuing_detail.issuingd_id=return_detail.issuingd_id 
                     INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                     WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' "; 
		  }
	   else 
	      {	  
	        $find_data=$field.$find_text;	
	        $q_show_returning_report="SELECT reth_code, reth_date, issuingh_code, cust_name, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
                                      itemd_qty, cati_name, 
                                      CASE retd_is_canceled
                                           WHEN '0' THEN 'Tidak'
                                           WHEN '1' THEN 'Ya'
                                      END retd_is_canceled
                                      FROM return_detail
                                      INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                                      INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                                      INNER JOIN issuing_detail ON issuing_header.issuingh_id=issuing_detail.issuingh_id AND issuing_detail.issuingd_id=return_detail.issuingd_id 
                                      INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
									  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                      INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                      INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                      INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                      WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND 
									        $find_data $sorting LIMIT $first_data,$row"; 
			$q_page="SELECT count(*) as total_page 
	                 FROM return_detail
                     INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                     INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                     INNER JOIN issuing_detail ON issuing_header.issuingh_id=issuing_detail.issuingh_id AND issuing_detail.issuingd_id=return_detail.issuingd_id 
                     INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                     WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND $find_data";  
		  }			 
	 }
  else	 
  	 {
	   $q_show_returning_report="SELECT reth_code, reth_date, issuingh_code, cust_name, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
                                 itemd_qty, cati_name, 
                                 CASE retd_is_canceled
                                      WHEN '0' THEN 'Tidak'
                                      WHEN '1' THEN 'Ya'
                                 END retd_is_canceled
                                 FROM return_detail
                                 INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                                 INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                                 INNER JOIN issuing_detail ON issuing_header.issuingh_id=issuing_detail.issuingh_id AND issuing_detail.issuingd_id=return_detail.issuingd_id 
                                 INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
								 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                 INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                 WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' $ordered_by 
								 LIMIT $first_data,$row"; 
	   $q_page="SELECT count(*) as total_page 
	            FROM return_detail
                INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                INNER JOIN issuing_detail ON issuing_header.issuingh_id=issuing_detail.issuingh_id AND issuing_detail.issuingd_id=return_detail.issuingd_id 
                INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
				INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id'"; 
	 }	

// echo $q_show_returning_report."<br>";	
// echo $q_page; 
 $field="";	
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
		
<form id="f_returning_report" name="f_returning_report" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" class="table-list-home">
    <tr>
      <td width="69%" scope="col"><h2>LAPORAN PENGEMBALIAN ASET </h2></td>
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
			    echo "<select name='s_field' id='s_field' onchange='show_hide()'>";
				foreach ($array_field as $fields)
				        {
						  if ($field==$fields)
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  ?>	  
						     <option value='<?php echo $fields;?>' <?php echo $selected_field;?>>
						  <?php	 
						         if ($fields=='reth_code')
								     echo "No Transaksi"; 
								 else
								 if ($fields=='reth_date')
								     echo "Tanggal"; 
								 else
								 if ($fields=='issuingh_code')
								     echo "No Ref Pengeluaran"; 
								 else
								 if ($fields=='cust_name')
								     echo "Nama Customer/Vendor"; 
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
								 if ($fields=='cati_name')
								     echo "Kategori"; 
								 else
								 if ($fields=='retd_is_canceled')
								     echo "Dibatalkan"; 
						   ?>			 
						     </option>
						  <?php	 
						}     
				echo "</select>";
			  }
		   else
		   	  { 
		?>	 
        <select name="s_field" id="s_field" onchange="show_hide()">
		  <option value="reth_code">No Transaksi</option>
		  <option value="reth_date">Tanggal</option>
		  <option value="issuingh_code">No Ref Pengeluaran</option>
		  <option value="cust_name">Nama Customer/Vendor</option>
		  <option value="itemd_code">Kode Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="itemd_serial_no">Serial No</option>
		  <option value="cati_name">Kategori</option>
		  <option value="retd_is_canceled">Dibatalkan</option>
        </select>
		<?php } 
		if (isset($_POST['s_operator']))
		   { 
		     $operator=$_POST['s_operator'];
			 echo "<select name='s_operator' id='s_operator' onchange='show_hide()'>";
			 foreach($array_operator as $operators)
			        {
					  $selected_operator="";
					  $id_operator='';
					  if ($operator==$operators)
					     {
						   $selected_operator="selected='selected'";
						   if ($field=='reth_date')
						      {
							    if ($operators=='=') 
								    $id_operator="id='op_e' style='display:list-item'";
							    else
								if ($operators=='>') 
								    $id_operator="id='op_gt' style='display:list-item'";
							    else
								if ($operators=='<') 
								    $id_operator="id='op_lt' style='display:list-item'";
							    else
								if ($operators=='>=') 
								    $id_operator="id='op_gte' style='display:list-item'";
							    else
								if ($operators=='<=') 
								    $id_operator="id='op_lte' style='display:list-item'";
							    else
								if ($operators=='!=') 
								    $id_operator="id='op_neq' style='display:list-item'";
							    else
								if ($operators=='between') 
								    $id_operator="id='op_between' style='display:list-item'";
							    else
								if ($operators=='like') 
								    $id_operator="id='op_like' style='display:list-item'";
							  }
						   else
						      { 
							    if ($operators=='=') 
								    $id_operator="id='op_e' style='display:list-item'";
							    else
								if ($operators=='>') 
								    $id_operator="id='op_gt' style='display:none'";
							    else
								if ($operators=='<') 
								    $id_operator="id='op_lt' style='display:none'";
							    else
								if ($operators=='>=') 
								    $id_operator="id='op_gte' style='display:none'";
							    else
								if ($operators=='<=') 
								    $id_operator="id='op_lte' style='display:none'";
							    else
								if ($operators=='!=') 
								    $id_operator="id='op_neq' style='display:list-item'";
							    else
								if ($operators=='between') 
								    $id_operator="id='op_between' style='display:none'";
							    else
								if ($operators=='like') 
								    $id_operator="id='op_like' style='display:list-item'";
							  } 
						   
						 }  
					  else
					     {
						   if ($operators=='=') 
						      {
							    $selected_operator="selected='selected'";
							    $id_operator="id='op_e' style='display:list-item'";
							  }	
						   else
						   if ($operators=='>') 
							   $id_operator="id='op_gt' style='display:none'";
						   else
						   if ($operators=='<') 
						       $id_operator="id='op_lt' style='display:none'";
						   else
					       if ($operators=='>=') 
						       $id_operator="id='op_gte' style='display:none'";
						   else
					       if ($operators=='<=') 
						       $id_operator="id='op_lte' style='display:none'";
					       else
					       if ($operators=='!=') 
						       $id_operator="id='op_neq' style='display:list-item'";
					       else
					       if ($operators=='between') 
						       $id_operator="id='op_between' style='display:none'";
					       else
					       if ($operators=='like') 
						       $id_operator="id='op_like' style='display:list-item'";
						 }  
					  ?>
					     <option value='<?php echo $operators;?>' <?php echo $selected_operator; echo $id_operator;?>><?php echo $operators;?></option>
					  <?php	  	  
					}
			 echo "</select>";
		   }
		else
		   {   	 
		?>
        <select name="s_operator" id="s_operator" onchange="show_hide()">
		  <option value="=" id="op_e" style="display:list-item">=</option>
		  <option value=">" id="op_gt" style="display:none">></option>
		  <option value="<" id="op_lt" style="display:none"><</option>
		  <option value=">=" id="op_gte" style="display:none">>=</option>
		  <option value="<=" id="op_lte" style="display:none"><=</option>
		  <option value="!=" id="op_neq" style="display:list-item">!=</option>
		  <option value="between" id="op_between" style="display:none">between</option>
		  <option value="like"  id="op_like" style="display:list-item">like</option>
        </select>
		<?php }?>
		<div id="div_1" style="display:<?php if ($field!='reth_date') echo "inline"; else echo "none";?>"><input type="text" name="txt_find_1" id="txt_find_1"
		 value="<?php 
		         if (isset($_POST['txt_find_1']))
				     echo $_POST['txt_find_1']; 
				 else 
				     echo "";	    
		       ?>"></div>
        <div id="div_2" style="display:<?php if ($field=='reth_date') echo 'inline'; else echo 'none';?>">
		   <input name="txt_date_1" type="text" id="txt_date_1" readonly="readonly" size="10" 
		                                       <?php 
											      if (isset($_POST['txt_date_1']))
												      echo "value='".$_POST['txt_date_1']."'";
												  else
											          echo "value='$current_date'";
											   ?>/></div>
		<div id="div_3" style="display:<?php if ($field=='reth_date' && $operator=='between') echo 'inline'; else echo 'none';?>">
		       <input name="txt_date_2" type="text" id="txt_date_2"  readonly="readonly" size="10" 
		                                       <?php 
											      if (isset($_POST['txt_date_2']))
												      echo "value='".$_POST['txt_date_2']."'";
												  else
											          echo "value='$current_date'";
											   ?>/></div>
        <input type="submit" name="btn_find" id="btn_find" value="Cari"/><div id="div_4" style="display:none">&nbsp;0=Tidak, 1=Ya</div>
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
									          if ($field=='reth_code')
								                  echo "No Transaksi"; 
								              else
								              if ($field=='reth_date')
								                  echo "Tanggal"; 
								              else
								              if ($field=='issuingh_code')
								                 echo "No Ref Pengeluaran"; 
								              else
								              if ($field=='cust_name')
								                 echo "Nama Customer/Vendor"; 
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
								              if ($field=='cati_name')
								                  echo "Kategori"; 
								              else
								              if ($field=='retd_is_canceled')
								                  echo "Dibatalkan"; 
											?>
          </option>
          <?php    
							  }
					  endforeach;  
					} 
			     else
				    {
					  ?>
          <option value="reth_code">No Transaksi</option>
		  <option value="reth_date">Tanggal</option>
		  <option value="issuingh_code">No Ref Pengeluaran</option>
		  <option value="cust_name">Nama Customer/Vendor</option>
		  <option value="itemd_code">Kode Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="itemd_serial_no">Serial No</option>
		  <option value="cati_name">Kategori</option>
		  <option value="retd_is_canceled">Dibatalkan</option>
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
                                <li><a href='' title="Retrive Returning Report" onclick="call_export()">Ekspor Data</a></li>
                              </ul>
                            </div></td>
    </tr>

    <tr>
      <td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb">No</th>
          <th width="11%" scope="col" class="th_ltb">No Transaksi </th>
          <th width="6%" scope="col" class="th_ltb">Tanggal</th>
          <th width="12%" scope="col" class="th_ltb">No Ref Pengeluaran </th>
          <th width="15%" scope="col" class="th_ltb">Nama Customer/Vendor </th>
          <th width="8%" scope="col" class="th_ltb">Kode Aset </th>
          <th width="16%" scope="col" class="th_ltb">Deskripsi Isi Aset </th>
          <th width="6%" scope="col" class="th_ltb">Serial No </th>
          <th width="7%" scope="col" class="th_ltb">Kapasitas</th>
          <th width="4%" scope="col" class="th_ltb">Qty</th>
          <th width="7%" scope="col" class="th_ltb">Kategori</th>
          <th width="6%" scope="col" class="th_ltb">Dibatalkan</th>
          </tr>
		  <?php
		     if ($continue=='0')
			    {
			 	  $exec_query=mysqli_query($db_connection, $q_show_returning_report);
				  $total_returning_report=mysqli_num_rows($exec_query);
				  $no=0;
				  while ($data_returning_report=mysqli_fetch_array($exec_query))
				        {
					      $no++;
   		  ?>
        <tr>
          <td align="center" class="td_lb"><?php echo $no;?></td>
          <td class="td_lb"><?php echo $data_returning_report['reth_code'];?></td>
          <td class="td_lb"><?php echo get_date_1($data_returning_report['reth_date']);?></td>
          <td class="td_lb"><?php echo $data_returning_report['issuingh_code'];?></td>
          <td class="td_lb"><?php echo $data_returning_report['cust_name'];?></td>
          <td class="td_lb"><?php echo $data_returning_report['itemd_code'];?></td>
          <td class="td_lb"><?php echo $data_returning_report['masti_name'];?></td>
          <td class="td_lb"><?php echo $data_returning_report['itemd_serial_no'];?></td>
          <td class="td_lb"><?php echo $data_returning_report['itemd_capacity']." ".$data_returning_report['uom_name'];?></td>
          <td class="td_lb"><?php echo $data_returning_report['itemd_qty']." Cylinder";?></td>
          <td class="td_lb"><?php echo $data_returning_report['cati_name'];?></td>
          <td class="td_lb"><?php echo $data_returning_report['retd_is_canceled'];?></td>
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
		                                                                               <select name="s_page" id="s_page" onchange="document.f_returning_report.submit()">
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

<?php
 if (isset($_POST['btn_find']) || (isset($_POST['s_sorting_field']) || isset($_POST['s_sort'])))
    {
	  $field=$_POST['s_field'];
	  $operator=$_POST['s_operator'];
		    ?>
			  <script language="javascript">
			     var x='<?php echo $field;?>';
				 var y='<?php echo $operator;?>';
		       if (x=='retd_is_canceled')
			      {
				    document.getElementById('op_gt').style.display='none';
					document.getElementById('op_lt').style.display='none';
					document.getElementById('op_gte').style.display='none';
					document.getElementById('op_lte').style.display='none';
					document.getElementById('op_between').style.display='none';
				    document.getElementById('div_1').style.display='inline';
					document.getElementById('div_2').style.display='none';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='inline';
				  }
			   else
			   if (x=='reth_date' && y=='between')
			      {
				    document.getElementById('op_gt').style.display='list-item';
					document.getElementById('op_lt').style.display='list-item';
					document.getElementById('op_gte').style.display='list-item';
					document.getElementById('op_lte').style.display='list-item';
					document.getElementById('op_between').style.display='list-item';
			        document.getElementById('div_1').style.display='none';
					document.getElementById('div_2').style.display='inline';
					document.getElementById('div_3').style.display='inline';
					document.getElementById('div_4').style.display='none';
				  }	
			   else
			   if (x=='reth_date' && y!='between')
			      {
				    document.getElementById('op_gt').style.display='list-item';
					document.getElementById('op_lt').style.display='list-item';
					document.getElementById('op_gte').style.display='list-item';
					document.getElementById('op_lte').style.display='list-item';
					document.getElementById('op_between').style.display='list-item';
			        document.getElementById('div_1').style.display='none';
					document.getElementById('div_2').style.display='inline';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='none';
				  }	
			   else
			      {
				    document.getElementById('op_gt').style.display='none';
					document.getElementById('op_lt').style.display='none';
					document.getElementById('op_gte').style.display='none';
					document.getElementById('op_lte').style.display='none';
					document.getElementById('op_between').style.display='none';
			        document.getElementById('div_1').style.display='inline';
					document.getElementById('div_2').style.display='none';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='none';
				  }
			  </script>	  	
			<?php
    }
?>

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
			   open_child=window.open("../data/returning_report/cru_returning_report.php?c=i", "f_cru_returning_report", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
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
				    open_child=window.open('../data/returning_report/cru_returning_report.php?c=u&id='+value_id, 'f_cru_returning_report', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
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
					   document.f_returning_report.action="../data/returning_report/delete_returning_report.php";
					   document.f_returning_report.submit();
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
					   document.f_returning_report.action="../data/returning_report/active_invactive_item.php";
					   document.f_returning_report.submit();
					 }  
				}
		   }
		   
	function call_export()
	         {
			   var field='<?php echo $field_to_convert;?>';
	           var operator='<?php echo $operator;?>';
	           var text_to_find_1='<?php echo $text_to_find_1;?>';
			   if (field=='reth_date')
			      {
				    if (operator=='between')
					   { 
					     var text_to_find_2='<?php echo $text_to_find_2;?>';
					     open_child=window.open("../data/returning_report/export_to_excel.php?f="+field+"&o="+operator+"&t1="+text_to_find_1+"&t2="+text_to_find_2);
					   }
					else
					   open_child=window.open("../data/returning_report/export_to_excel.php?f="+field+"&o="+operator+"&t1="+text_to_find_1); 
				  }
			   else 
			      open_child=window.open("../data/returning_report/export_to_excel.php?f="+field+"&o="+operator+"&t1="+text_to_find_1);
			 }  
	
	function show_hide()
	         {
			   var x=document.getElementById('s_field').value;
			   var y=document.getElementById('s_operator').value;
			   if (x=='retd_is_canceled')
			      {
				    var j=document.getElementById('s_operator').value.trim();
					if (j!='=' && j!='!=' && j!='like')
					     document.getElementById('s_operator').value='=';
				    document.getElementById('op_gt').style.display='none';
					document.getElementById('op_lt').style.display='none';
					document.getElementById('op_gte').style.display='none';
					document.getElementById('op_lte').style.display='none';
					document.getElementById('op_between').style.display='none';
				    document.getElementById('div_1').style.display='inline';
					document.getElementById('div_2').style.display='none';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='inline';
				  }
			   else
			   if (x=='reth_date' && y=='between')
			      {
				    document.getElementById('op_gt').style.display='list-item';
					document.getElementById('op_lt').style.display='list-item';
					document.getElementById('op_gte').style.display='list-item';
					document.getElementById('op_lte').style.display='list-item';
					document.getElementById('op_between').style.display='list-item';
				    document.getElementById('txt_find_1').value='';
			        document.getElementById('div_1').style.display='none';
					document.getElementById('div_2').style.display='inline';
					document.getElementById('div_3').style.display='inline';
					document.getElementById('div_4').style.display='none';
				  }	
			   else
			   if (x=='reth_date' && y!='between')
			      {
				    document.getElementById('op_gt').style.display='list-item';
					document.getElementById('op_lt').style.display='list-item';
					document.getElementById('op_gte').style.display='list-item';
					document.getElementById('op_lte').style.display='list-item';
					document.getElementById('op_between').style.display='list-item';
				    document.getElementById('txt_find_1').value='';
			        document.getElementById('div_1').style.display='none';
					document.getElementById('div_2').style.display='inline';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='none';
				  }	
			   else
			      {
				    var j=document.getElementById('s_operator').value.trim();
					if (j!='=' && j!='!=' && j!='like')
					     document.getElementById('s_operator').value='=';
				    document.getElementById('op_gt').style.display='none';
					document.getElementById('op_lt').style.display='none';
					document.getElementById('op_gte').style.display='none';
					document.getElementById('op_lte').style.display='none';
					document.getElementById('op_between').style.display='none';
			        document.getElementById('div_1').style.display='inline';
					document.getElementById('div_2').style.display='none';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='none';
				  }	
			 }
</script>

</body>
</html>
