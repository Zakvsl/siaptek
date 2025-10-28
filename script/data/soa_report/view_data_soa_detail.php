<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title></head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../../library/style.css";
  include "../../library/db_connection.php";
  if (isset($_GET['v']))
      $view=$_GET['v'];
  else
      $view='1';
  /*
  $start_date=get_date_2($_POST['txt_soa_date_start']);
  $end_date=get_date_2($_POST['txt_soa_date_end']);
  $customer=$_POST['s_customer'];
  $total_cust=count($customer);
   */
  
  if ($view=='0')
     {
	   include "../../library/library_function.php";
       $start_date=get_date_2($_POST['txt_soa_date_start']);
       $end_date=get_date_2($_POST['txt_soa_date_end']);
	   $start_date_1=$_POST['txt_soa_date_start'];
       $end_date_1=$_POST['txt_soa_date_end'];
       if (isset($_POST['s_customer']))
           $customer=$_POST['s_customer'];
       else
           $customer="";
       $total_cust=count($customer);
	   $rb_type=$_POST['rb_view_type'];
	 }
  else
     {
	   $start_date=$start_dates;
       $end_date=$end_dates;
	   $start_date_1=$start_dates_1;
       $end_date_1=$end_dates_1;
       $customer=$customers;
       $total_cust=count($customer);  
	   $rb_type=$rb_types;
	 }  
  $cust_id='';
  /*if ($_POST['rb_type_1']=='1') */
  if ($rb_type=='1')
     { 
	   if ($total_cust==0)
          {
	        ?>
	           <script language="javascript">
		         alert('Tidak ada Customer yang dipilih!');
				 history.back();
		       </script>
	        <?php
			exit;
	      }
		  
	   foreach($customer as $customer_id)
	           {
			     if ($cust_id=='')
			 	     $cust_id="'".$customer_id."'";
		 		 else
				     $cust_id=$cust_id.",'".$customer_id."'";
			   }	
	   $q_get_customer="SELECT cust_id, cust_code, cust_name, custtp_name 
	                    FROM customer 
						LEFT JOIN customer_type ON customer_type.custtp_id=customer.custtp_id
						WHERE cust_id IN ($cust_id) AND cust_type='0' AND branch_id='$branch_id'";	  
	 }
  else
     {
	    $cust_id="SELECT cust_id FROM customer WHERE cust_type='0'";
		$q_get_customer="SELECT cust_id, cust_code, cust_name , custtp_name
		                 FROM customer 
						 LEFT JOIN customer_type ON customer_type.custtp_id=customer.custtp_id
						 WHERE cust_type='0' AND branch_id='$branch_id'";	
     }		
  //echo $q_get_customer;
  $exec_get_customer=mysqli_query($db_connection, $q_get_customer);
  if (mysqli_num_rows($exec_get_customer)==0)
     {
	   ?>
	      <script language="javascript">
		    alert('Data Customer tidak ditemukan!');
			history.back();
		  </script>
	   <?php 
	 }
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

<form id="f_soa_report" name="f_soa_report" method="post" action="">
 <p align="right"><input type="button" id="btn_print" value="Print" onclick="print_doc()"/></p>
  <?php 
     $cust_id="";
	 $date_start=$start_date_1;
     $date_end=$end_date_1;
	 while ($field_cust=mysqli_fetch_array($exec_get_customer))
	       {
		     $cust_id=$field_cust['cust_id'];
			 $cust_code=$field_cust['cust_code'];
			 $cust_name=$field_cust['cust_name'];
			 $cust_type=$field_cust['custtp_name'];
		     $q_get_data="SELECT issuing_header.issuingh_id AS issuingh_id_1, issuingh_code, issuingd_id AS issuingd_id_1, issuingd_qty, issuingh_do_no, issuingh_date, 
                                 DATEDIFF('$end_date',issuingh_date) AS aging, master_item.masti_id, masti_code, masti_name, itemd_serial_no, uom_name,
                                 CASE issuingd_status
                                      WHEN '0' THEN 'Normal'
                                      WHEN '1' THEN 'Rental'
                                      WHEN '2' THEN 'UJM'
                                 END issuingd_status, category_item.cati_id, cati_code, cati_name, item_detail.itemd_id, itemd_code, 
								(SELECT COUNT(*) 
								 FROM return_detail
								 INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
								 WHERE reth_is_canceled='0' AND retd_is_canceled='0' AND issuingh_id=issuingh_id_1 AND 
								       issuingd_id=issuingd_id_1 AND branch_id='$branch_id' AND reth_date<='$date_end') AS is_receipt
                           FROM issuing_header
                           INNER JOIN issuing_detail ON issuing_detail.issuingh_id=issuing_header.issuingh_id 
                           INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id AND item_detail.branch_id='$branch_id'
                           INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                           INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
						   INNER JOIN uom ON uom.uom_id=master_item.uom_id_2
                           WHERE issuing_header.branch_id='$branch_id' AND issuingh_is_canceled='0' AND (issuingh_date BETWEEN '$start_date' AND '$end_date') AND 
			                     issuingh_type='0' AND cust_id='".$cust_id."'
                           ORDER BY cati_code, issuingd_status, itemd_code, issuingh_date";
			 //echo $q_get_data."<br>";
             $exec_get_data=mysqli_query($db_connection, $q_get_data);  					   
  ?>
                 
                       <table width="100%" border="0">
                         <tr>
                             <td colspan="10"><b>PT. SURYA INDOTIM IMEX<b></td>
                         </tr>
                         <tr>
                             <td colspan="10"><b>STATEMENT OF ACCOUNT</b></td>
                         </tr>
                         <tr>
                             <td colspan="10"></td>
                         </tr>
                         <tr>
                             <td colspan="2">Nama Pelanggan</td>
                             <td colspan="2">:&nbsp;<?php echo "[".$cust_code."] - ".$cust_name;?></td>
							 <td colspan="5"></td>
                         </tr>
                         <tr>
                             <td colspan="2">Tipe Pelanggan</td>
                             <td colspan="2">:&nbsp;<?php echo $cust_type;?></td>
							 <td colspan="5"></td>
                         </tr>
                         <tr>
                             <td colspan="2">Mulai Tanggal</td>
                             <td colspan="2">:&nbsp;<?php echo $date_start;?></td>
							 <td colspan="5"></td>
                         </tr>
                         <tr>
                             <td colspan="2">Sampai Tanggal</td>
                             <td colspan="2">:&nbsp;<?php echo $date_end;?></td>
							 <td colspan="5"></td>
                         </tr>
                         <tr>
                             <td colspan="10"></td>
                         </tr>
                         <tr>
                             <td colspan="10">
							    <table width="100%" border="0" class="table-list">
                                   <tr>
                                       <th>No</th>
                                       <th>Categori Aset</th>
                                       <th>Deskripsi Isi Aset </th>
                                       <th>Serial No</th>
                                       <th>Qty</th>
                                       <th>No Transaksi</th>
                                       <th>DO No</th>
                                       <th>Tanggal Keluar</th>
                                       <th>Umur (Hari)</th>
                                       <th>Status</th>
                                   </tr>
								      <?php 
									      $no=1;
										  $masti_id="";
										  $cati_id="";
										  $total=0;
										  $grand_total=0;
										  $issuingh_id='';
										  $param=0;
										  $uom_name="";
										  if (mysqli_num_rows($exec_get_data)==0)
										     {
											   ?>
											   <tr><td colspan="10" align="center"><font color="#0000FF"><b>--- TIDAK ADA DETAIL DATA ---</b></font></td></tr>
											   <?php 
											 }
										  else
										     {
									           while ($field_data=mysqli_fetch_array($exec_get_data))
										             {
												       $issuingh_id_1=$field_data['issuingh_id_1'];
												       if ($field_data['is_receipt']=='0')
												          {
												            $masti_id_1=$field_data['masti_id'];
												            $cati_id_1=$field_data['cati_id'];
												            if ($cati_id!=$cati_id_1)
												               {
													             if ($cati_id!='')
													                {
														              ?>
															             <tr>
															                  <td></td>
																	          <td colspan="3" align="right"><font color="#0000FF"><b>TOTAL :</b></font></td>
																	          <td><font color="#0000FF"><b><?php echo $total." ".$field_data['uom_name'];?></b></font></td>
																	          <td colspan="5"></td>
															             </tr>
															          <?php 
														            }
													             $total=0;
								      ?>
													                <tr>
														                 <td></td>
														                 <td colspan="9"><font color="#0000FF"><b><?php echo "[".$field_data['cati_code']."] - ".$field_data['cati_name'];?></b></font></td>
														            </tr>
									  <?php
													           }
									  ?>
                                                                    <tr>
                                                                        <td align="right" width="1%"><?php echo $no++;?></td>
                                                                        <td width="8%"><?php
															                                 if ($masti_id!=$masti_id_1 || ($masti_id==$masti_id_1 && $cati_id!=$cati_id_1)) 
																						        {
																						          $param=1;
															                                      //echo $field_data['cati_code'];
																						        }
																						     if ($issuingh_id!=$issuingh_id_1 || $cati_id!=$cati_id_1)
												                                                {
													                                              $issuingh_code=$field_data['issuingh_code'];
													                                              $issuingh_do_no=$field_data['issuingh_do_no'];
													                                              $issuingh_date=get_date_1($field_data['issuingh_date']);
													                                            }
																			           ?>															            </td>
                                                                        <td width="10%"><?php echo "[".$field_data['itemd_code']."] - ".$field_data['masti_name'];?></td>
                                                                        <td width="5%"><?php echo $field_data['itemd_serial_no'];?></td>
                                                                        <td width="4%"><?php echo $field_data['issuingd_qty']." ".$field_data['uom_name'];?></td>
                                                                        <td width="6%"><?php echo $issuingh_code;?></td>
                                                                        <td width="5%"><?php echo $issuingh_do_no;?></td>
                                                                        <td width="4%"><?php echo $issuingh_date;?></td>
                                                                        <td width="4%"><?php echo $field_data['aging'];?></td>
                                                                        <td width="4%"><?php echo $field_data['issuingd_status'];?></td>
													                </tr>
									  <?php
									                        $total=$total+$field_data['issuingd_qty'];
													        $grand_total=$grand_total+$field_data['issuingd_qty'];
									                        $masti_id=$masti_id_1;
												            $cati_id=$cati_id_1;
												            $uom_name=$field_data['uom_name'];
													        $issuingh_id=$issuingh_id_1;
													        $issuingh_code='';
													        $issuingh_do_no='';
													        $issuingh_date='';
													        $param=0;
                                                          }
											         }
									  ?>
									                                <tr>
															            <td></td>
														                <td colspan="3" align="right"><font color="#0000FF"><b>TOTAL :</b></font></td>
															            <td><font color="#0000FF"><b><?php echo $total." ".$uom_name;?></b></font></td>
															            <td colspan="5"></td>
													                </tr>
															        <tr>
															            <td></td>
														                <td colspan="3" align="right"><font color="#FF0000"><b>GRAND TOTAL :</b></font></td>
															            <td><font color="#FF0000"><b><?php echo $grand_total;?></b></font></td>
															            <td colspan="5"></td>
															        </tr>
									  <?php
									         }
									  ?>
                                 </table>
							 </td>
                        </tr>
                       </table>
  <?php
             echo "<br><br><hr></br></br>";
           }
  ?>
</form>


<script language="javascript">
   function call_back()
            {
			  history.back();
			}
   function print_doc()
            {
			  window.print();
			}
</script>

</body>
</html>
