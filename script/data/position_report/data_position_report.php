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
  $array_field=array("itemd_code", "masti_name", "itemd_serial_no", "itemd_capacity", "itemd_acquired_date", "cati_name", "itemd_status", "itemd_is_broken","itemd_position");
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
	   $field_to_sort='itemd_code';
	 }
  if (isset($_POST['s_sort']))
     {
	   $type_to_sort=$_POST['s_sort'];
	 } 	 
  else
     {
	   $type_to_sort='asc';
	 }	 
  	 
  $ordered_by="ORDER BY ".$field_to_sort." ".$type_to_sort;	
  $field="";			
  $field_1="";
  $field_2="";	
  $field_to_convert_1="";
  $field_to_convert_2="";
  $operator="";		
  $text_to_find_1="";
  $text_to_find_2="";	
				
  if (isset($_POST['btn_find']))
	 {
	   if ($_POST['s_field']=='itemd_position')
	      {
		    $field_1='itemd_position';
			$field_2='itemd_position';
			$field_to_convert_1='itemd_position';
			$field_to_convert_2='itemd_position';
		  }
	   else
	      {
	        $field_1=$_POST['s_field'];
			$field_2=$_POST['s_field'];
			$field_to_convert_1=$_POST['s_field'];
			$field_to_convert_2=$_POST['s_field'];
		  }	
	   
	   $operator=$_POST['s_operator'];
	   if ($field_1=='itemd_acquired_date')
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
	   
	   if ($field_1=='itemd_acquired_date' && $operator=='between' && ($text_to_find_2<$text_to_find_1))
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
			$find_data_1=$field_1.$find_text;	
			$find_data_2=$field_2.$find_text;	
			$q_show_position_report="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name,  itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken, 
											itemd_position, '-' AS vendor_name, '-' AS customer_name
                                     FROM item_detail 
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                     WHERE itemd_position IN ('Internal','In Transit') AND itemd_status='0' AND item_detail.branch_id='$branch_id' AND $find_data_1
                                     UNION
                                     SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken,
								            CASE issuingh_type WHEN '0' THEN 'Customer' WHEN '1' THEN 'Vendor' END itemd_position,
								            CASE cust_type WHEN '0' THEN '-' WHEN '1' THEN cust_name END vendor_name,
								            CASE cust_type WHEN '0' THEN cust_name WHEN '1' THEN '-' END customer_name
                                     FROM issuing_detail 
                                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                                     INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                     WHERE itemd_position IN ('Vendor','Customer') AND itemd_status='0' AND issuingd_is_return='0' AND issuingd_is_canceled='0' AND 
									       issuing_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND 
										   issuingh_is_canceled='0' AND $find_data_2
								     $ordered_by
								     LIMIT $first_data,$row"; 
			$q_page="SELECT((SELECT COUNT(*)
                             FROM item_detail 
							 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                             INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                             INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                             WHERE itemd_position IN ('Internal','In Transit') AND itemd_status='0' AND item_detail.branch_id='$branch_id' AND $find_data_1)+
                            (SELECT COUNT(*)
                             FROM issuing_detail 
                             INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                             INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
							 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                             INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                             INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                             INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                             WHERE itemd_position IN ('Vendor','Customer') AND issuingd_is_return='0' AND issuingd_is_canceled='0' AND issuing_header.branch_id='$branch_id' AND 
                                   item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_is_canceled='0' AND $find_data_2)) AS total_page"; 	 
		  }
	 }
  else
  if (isset($_POST['s_sorting_field']) || isset($_POST['s_sort']))
     {
	   if ($_POST['s_field']=='itemd_position')
	      {
		    $field_1='itemd_position';
			$field_2='itemd_position';
			$field_to_convert_1='itemd_position';
			$field_to_convert_2='itemd_position';
		  }
	   else
	      {
	        $field_1=$_POST['s_field'];
			$field_2=$_POST['s_field'];
			$field_to_convert_1=$_POST['s_field'];
			$field_to_convert_2=$_POST['s_field'];
		  }	
	   $operator=$_POST['s_operator'];
	   if ($field_1=='itemd_acquired_date')
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
		    $q_show_position_report="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name,  itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken, 
											itemd_position, '-' AS vendor_name, '-' AS customer_name
                                     FROM item_detail 
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                     WHERE itemd_position IN ('Internal','In Transit') AND itemd_status='0' AND item_detail.branch_id='$branch_id'
                                     UNION
                                     SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken,
								            CASE issuingh_type WHEN '0' THEN 'Customer' WHEN '1' THEN 'Vendor' END itemd_position,
								            CASE cust_type WHEN '0' THEN '-' WHEN '1' THEN cust_name END vendor_name,
								            CASE cust_type WHEN '0' THEN cust_name WHEN '1' THEN '-' END customer_name
                                     FROM issuing_detail 
                                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                                     INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                     WHERE itemd_position IN ('Vendor','Customer') AND itemd_status='0' AND issuingd_is_return='0' AND issuingd_is_canceled='0' AND 
									       issuing_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND 
										   issuingh_is_canceled='0' 
									 $sorting LIMIT $first_data,$row"; 
			$q_page="SELECT((SELECT COUNT(*)
                             FROM item_detail 
							 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                             INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                             INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                             WHERE itemd_position IN ('Internal','In Transit') AND itemd_status='0' AND item_detail.branch_id='$branch_id')+
                            (SELECT COUNT(*)
                             FROM issuing_detail 
                             INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                             INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
							 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                             INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                             INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                             INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                             WHERE itemd_position IN ('Vendor','Customer') AND issuingd_is_return='0' AND issuingd_is_canceled='0' AND issuing_header.branch_id='$branch_id' AND 
                                   item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_is_canceled='0')) AS total_page"; 
		  }
	   else 
	      {	  
	        $find_data_1=$field_1.$find_text;	
			$find_data_2=$field_2.$find_text;	
	        $q_show_position_report="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name,  itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken, 
											itemd_position, '-' AS vendor_name, '-' AS customer_name
                                     FROM item_detail 
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                     WHERE itemd_position IN ('Internal','In Transit') AND itemd_status='0' AND item_detail.branch_id='$branch_id' AND $find_data_1
                                     UNION
                                     SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken,
								            CASE issuingh_type WHEN '0' THEN 'Customer' WHEN '1' THEN 'Vendor' END itemd_position,
								            CASE cust_type WHEN '0' THEN '-' WHEN '1' THEN cust_name END vendor_name,
								            CASE cust_type WHEN '0' THEN cust_name WHEN '1' THEN '-' END customer_name
                                     FROM issuing_detail 
                                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                                     INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                     WHERE itemd_position IN ('Vendor','Customer') AND itemd_status='0' AND issuingd_is_return='0' AND issuingd_is_canceled='0' AND 
									       issuing_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND 
										   issuingh_is_canceled='0' AND $find_data_2
								     $sorting LIMIT $first_data,$row"; 
			
			$q_page="SELECT((SELECT COUNT(*)
                             FROM item_detail 
							 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                             INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                             INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                             WHERE itemd_position IN ('Internal','In Transit') AND itemd_status='0' AND item_detail.branch_id='$branch_id' AND $find_data_1)+
                            (SELECT COUNT(*)
                             FROM issuing_detail 
                             INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                             INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
							 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                             INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                             INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                             INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                             WHERE itemd_position IN ('Vendor','Customer') AND issuingd_is_return='0' AND issuingd_is_canceled='0' AND issuing_header.branch_id='$branch_id' AND 
                                  item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_is_canceled='0' AND $find_data_2)) AS total_page"; 
		  }			 
	 }
  else	 
  	 {
	   $q_show_position_report="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken, 
											itemd_position, '-' AS vendor_name, '-' AS customer_name
                                FROM item_detail 
								INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                WHERE itemd_position IN ('Internal','In Transit') AND itemd_status='0' AND item_detail.branch_id='$branch_id'
                                UNION
                                SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                       CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                       CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken,
								       CASE issuingh_type WHEN '0' THEN 'Customer' WHEN '1' THEN 'Vendor' END itemd_position,
								       CASE cust_type WHEN '0' THEN '-' WHEN '1' THEN cust_name END vendor_name,
								       CASE cust_type WHEN '0' THEN cust_name WHEN '1' THEN '-' END customer_name
                                FROM issuing_detail 
                                INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                                INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
								INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                WHERE itemd_position IN ('Vendor','Customer') AND itemd_status='0' AND issuingd_is_return='0' AND issuingd_is_canceled='0' AND 
									  issuing_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND 
								      issuingh_is_canceled='0'
								$ordered_by LIMIT $first_data,$row"; 
	   $q_page="SELECT((SELECT COUNT(*)
                        FROM item_detail 
						INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                        INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                        INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                        WHERE itemd_position IN ('Internal','In Transit') AND itemd_status='0' AND item_detail.branch_id='$branch_id')+
                       (SELECT COUNT(*)
                        FROM issuing_detail 
                        INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                        INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
						INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                        INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                        INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                        INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                        WHERE itemd_position IN ('Vendor','Customer') AND issuingd_is_return='0' AND issuingd_is_canceled='0' AND issuing_header.branch_id='$branch_id' AND 
                              item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_is_canceled='0')) AS total_page"; 
	 }	

 // echo $q_show_position_report."<br>";	
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
		
<form id="f_item_detail" name="f_item_detail" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" class="table-list-home">
    <tr>
      <td width="69%" scope="col"><h2>LAPORAN POSISI ASET </h2></td>
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
								 if ($fields=='itemd_is_broken')
								     echo "Rusak"; 
								 if ($fields=='itemd_position')
								     echo "Posisi";  
									 
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
		  <option value="itemd_code">Kode Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="itemd_serial_no">Serial No</option>
		  <option value="itemd_capacity">Kapasitas</option>
		  <option value="itemd_acquired_date">Tanggal Perolehan</option>
		  <option value="cati_name">Kategori</option>
		  <option value="itemd_status">Status</option>
		  <option value="itemd_is_broken">Rusak</option>
		  <option value="itemd_position">Posisi</option>
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
						   if ($field=='itemd_acquired_date')
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
                        <option value="&gt;" id="op_gt" style="display:none">></option>
                        <option value="&lt;" id="op_lt" style="display:none"><</option>
                        <option value="&gt;=" id="op_gte" style="display:none">>=</option>
                        <option value="&lt;=" id="op_lte" style="display:none"><=</option>
                        <option value="!=" id="op_neq" style="display:list-item">!=</option>
                        <option value="between" id="op_between" style="display:none">between</option>
                        <option value="like"  id="op_like" style="display:list-item">like</option>
        </select>
		<?php }?>
        <div id="div_1" style="display:<?php if ($field!='itemd_acquired_date') echo "inline"; else echo "none";?>"><input type="text" name="txt_find_1" id="txt_find_1"
		 value="<?php 
		         if (isset($_POST['txt_find_1']))
				     echo $_POST['txt_find_1']; 
				 else 
				     echo "";	    
		       ?>"></div>
        <div id="div_2" style="display:<?php if ($field=='itemd_acquired_date') echo 'inline'; else echo 'none';?>">
		   <input name="txt_date_1" type="text" id="txt_date_1" readonly="readonly" size="10" 
		                                       <?php 
											      if (isset($_POST['txt_date_1']))
												      echo "value='".$_POST['txt_date_1']."'";
												  else
											          echo "value='$current_date'";
											   ?>/></div>
		<div id="div_3" style="display:<?php if ($field=='itemd_acquired_date' && $operator=='between') echo 'inline'; else echo 'none';?>">
		       <input name="txt_date_2" type="text" id="txt_date_2"  readonly="readonly" size="10" 
		                                       <?php 
											      if (isset($_POST['txt_date_2']))
												      echo "value='".$_POST['txt_date_2']."'";
												  else
											          echo "value='$current_date'";
											   ?>/></div>
        <input type="submit" name="btn_find" id="btn_find" value="Cari"/><div id="div_4" style="display:none">&nbsp;[ 0=Active, 1=InActive ]</div><div id="div_5" style="display:none">&nbsp;[ 0=Tidak, 1=Ya ]</div><div id="div_6" style="display:none">&nbsp;[ 0=Tidak, 1=Ya ]</div><div id="div_7" style="display:none">&nbsp;[ 0=Tidak, 1=Ya ]</div>
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
											  if ($field=='itemd_is_broken')
								                  echo "Rusak"; 
											  if ($field=='itemd_position')
								                  echo "Posisi";
											?>
          </option>
          <?php    
							  }
					  endforeach;  
					} 
			     else
				    {
					  ?>
          <option value="itemd_code">Kode Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="itemd_serial_no">Serial No</option>
		  <option value="itemd_capacity">Kapasitas</option>
		  <option value="itemd_acquired_date">Tanggal Perolehan</option>
		  <option value="cati_name">Kategori</option>
		  <option value="itemd_status">Status</option>
		  <option value="itemd_is_broken">Rusak</option>
		  <option value="itemd_position">Posisi</option>
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
          <option value="desc">Desc</option>
          <?php 
								 }	      
						?>
        </select></td>
<td width="9%" align="left"><div id="action" align="left">
                              <ul id="nav">
                                <li><a href="" title="Retrive Position Report" onclick="call_export()">Ekspor Data</a></li>
                              </ul>
                            </div></td>
    </tr>

    <tr>
      <td colspan="3">
	  <div STYLE=" height: 100%; width: 100%; font-size: 12px; overflow: auto;">
	  <table width="110%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="1%" scope="col" class="th_ltb" nowrap="nowrap">No</th>
          <th width="9%" scope="col" class="th_ltb" nowrap="nowrap">Kode Aset </th>
          <th width="19%" scope="col" class="th_ltb" nowrap="nowrap">Deskripsi Isi Aset </th>
          <th width="7%" scope="col" class="th_ltb" nowrap="nowrap">Serial No</th>
          <th width="9%" scope="col" class="th_ltb" nowrap="nowrap">Kapasitas</th>
          <th width="6%" scope="col" class="th_ltb" nowrap="nowrap">Qty</th>
          <th width="11%" scope="col" class="th_ltb" nowrap="nowrap">Tanggal Perolehan</th>
          <th width="12%" scope="col" class="th_ltb" nowrap="nowrap">Kategori</th>
          <th width="6%" scope="col" class="th_ltb" nowrap="nowrap">Status</th>
          <th width="5%" scope="col" class="th_ltb" nowrap="nowrap">Rusak</th>
          <th width="7%" scope="col" class="th_ltb" nowrap="nowrap">Posisi</th>
          <th width="10%" scope="col" class="th_ltb" nowrap="nowrap">Nama Vendor</th>
          <th width="10%" scope="col" class="th_ltb" nowrap="nowrap">Nama Customer </th>
          </tr>
		  <?php
		     if ($continue=='0')
			    {
			 	  $exec_query=mysqli_query($db_connection, $q_show_position_report);
				  $total_item_detail=mysqli_num_rows($exec_query);
				  $no=0;
				  $font_1="</font>"; 
				  while ($data_item_detail=mysqli_fetch_array($exec_query))
				        {
					      $no++;
						  if ($data_item_detail['itemd_status']=='InActive') 
						      echo $font="<font color='#FF0000'>";
						  else
						      echo $font="<font color='#000000'>";
						     
						      
   		  ?>
        <tr>
          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data_item_detail['itemd_id'];?>" /></td>
          <td align="center" class="td_lb"><?php echo $no;?></td>
          <td class="td_lb" nowrap="nowrap"><a href="" onclick="show_pic_item_detail('<?php echo $data_item_detail['itemd_id'];?>')"><?php echo $data_item_detail['itemd_code'];?></a></td>
          <td class="td_lb" nowrap="nowrap"><?php echo $font.$data_item_detail['masti_name'].$font_1;?></td>
          <td class="td_lb"><?php echo $font.$data_item_detail['itemd_serial_no'].$font_1;?></td>
          <td class="td_lb"><?php echo $font.$data_item_detail['itemd_capacity']." ".$data_item_detail['uom_name'].$font_1;?></td>
          <td class="td_lb" nowrap="nowrap"><?php echo $font.$data_item_detail['itemd_qty']." Cylinder".$font_1;?></td>
          <td class="td_lb"><?php echo $font.get_date_1($data_item_detail['itemd_acquired_date']).$font_1;?></td>
          <td class="td_lb" nowrap="nowrap"><?php echo $font.$data_item_detail['cati_name'].$font_1;?></td>
          <td class="td_lb"><?php echo $font.$data_item_detail['itemd_status'].$font_1;?></td>
          <td class="td_lb"><?php echo $font.$data_item_detail['itemd_is_broken'].$font_1;?></td>
          <td class="td_lb"><?php echo $font.$data_item_detail['itemd_position'].$font_1;?></td>
          <td class="td_lb" nowrap="nowrap"><?php echo $font.$data_item_detail['vendor_name'].$font_1;?></td>
          <td class="td_lb" nowrap="nowrap"><?php echo $font.$data_item_detail['customer_name'].$font_1;?></td>
          </tr><?php }} ?>
      </table>
	  </div>
	  </td>
    </tr>
	      <?php
              $query_exec=mysqli_query($db_connection, $q_page) or die (mysqli_error());
              $total_rows=mysqli_fetch_array($query_exec);
              $maks_rows=ceil($total_rows['total_page']/$row);
		  ?>	  
																								   
	<tr>
        <td align="left">Total : <?php echo $total_rows['total_page'];?>&nbsp;data</td>
        <td colspan="2" align="right">Halaman ke  : 
		                                                                               <select name="s_page" id="s_page" onchange="document.f_item_detail.submit()">
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
		       if (x=='itemd_status')
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
					document.getElementById('div_5').style.display='none';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
				  }
			   else
			   if (x=='itemd_is_broken')
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
					document.getElementById('div_5').style.display='inline';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
				  }
			   else
			   if (x=='itemd_acquired_date' && y=='between')
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
					document.getElementById('div_5').style.display='none';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
				  }	
			   else
			   if (x=='itemd_acquired_date' && y!='between')
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
					document.getElementById('div_5').style.display='none';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
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
					document.getElementById('div_5').style.display='none';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
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
			   open_child=window.open("../data/item_detail/cru_item_detail.php?c=i", "f_cru_item_detail", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
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
				    open_child=window.open('../data/item_detail/cru_item_detail.php?c=u&id='+value_id, 'f_cru_item_detail', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
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
					   document.f_item_detail.action="../data/item_detail/delete_item_detail.php";
					   document.f_item_detail.submit();
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
					   document.f_item_detail.action="../data/item_detail/active_invactive_item.php";
					   document.f_item_detail.submit();
					 }  
				}
		   }
		   
	function call_export()
	         {
			   var field='<?php echo $field_to_convert_1;?>';
			   if (field=='itemd_position')
			      {
				    field_1='itemd_position';
				    field_2='itemd_position';
				  }
			   else
			      {
				    field_1=field;
				    field_2=field;
				  }
	           var operator='<?php echo $operator;?>';
			   var text_to_find_1='<?php echo $text_to_find_1;?>';
			   if (field=='itemd_acquired_date')
			      {
				    if (operator=='between')
					   { 
					     var text_to_find_2='<?php echo $text_to_find_2;?>';
					   open_child=window.open("../data/position_report/export_to_excel.php?f1="+field_1+"&f2="+field_2+"&o="+operator+"&t1="+text_to_find_1+"&t2="+text_to_find_2);
					   }
					else
					   open_child=window.open("../data/position_report/export_to_excel.php?f1="+field_1+"&f2="+field_2+"&o="+operator+"&t1="+text_to_find_1); 
				  }
			   else 
			       open_child=window.open("../data/position_report/export_to_excel.php?f1="+field_1+"&f2="+field_2+"&o="+operator+"&t1="+text_to_find_1);
			 }  
	
	function show_hide()
	         {
			   var x=document.getElementById('s_field').value;
			   var y=document.getElementById('s_operator').value;
			   if (x=='itemd_status')
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
					document.getElementById('div_5').style.display='none';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
				  }
			   else
			   if (x=='itemd_is_broken')
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
					document.getElementById('div_5').style.display='inline';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
				  }
			   else
			   if (x=='itemd_acquired_date' && y=='between')
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
					document.getElementById('div_5').style.display='none';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
				  }	
			   else
			   if (x=='itemd_acquired_date' && y!='between')
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
					document.getElementById('div_5').style.display='none';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
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
					document.getElementById('div_5').style.display='none';
					document.getElementById('div_6').style.display='none';
					document.getElementById('div_7').style.display='none';
				  }	
			 }
</script>

</body>
</html>
