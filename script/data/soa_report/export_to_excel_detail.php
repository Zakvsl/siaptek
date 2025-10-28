<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $start_date=get_date_2($_POST['txt_soa_date_start']);
  $end_date=get_date_2($_POST['txt_soa_date_end']);
  $customer=$_POST['s_customer'];
  $total_cust=count($customer);
  $cust_id='';
  if ($_POST['rb_type_1']=='1')
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
	 
     $cust_id="";
	 $start_date_1=$_POST['txt_soa_date_start'];
     $end_date_1=$_POST['txt_soa_date_end'];
	 $filename='List_SOA_Report.xls';
	 $headers="PT. SURYA INDOTIM IMEX"."\n";
     $header=$headers."STATEMENT OF ACCOUNT REPORT"."\n";
	 $date='Retrived Date : '.date("d-m-Y")."\n";
	 while ($field_cust=mysqli_fetch_array($exec_get_customer))
	       {
		     $cust_id=$field_cust['cust_id'];
			 $cust_code=$field_cust['cust_code'];
			 $cust_name=$field_cust['cust_name'];
			 $cust_type=$field_cust['custtp_name'];
		     $q_get_data="SELECT issuing_header.issuingh_id AS issuingh_id_1, issuingh_code, issuingd_id AS issuingd_id_1, issuingd_qty, issuingh_do_no, issuingh_date, 
                                 DATEDIFF('$end_date',issuingh_date) AS aging, master_item.masti_id, masti_code, masti_name, uom_name,
                                 CASE issuingd_status
                                      WHEN '0' THEN 'Normal'
                                      WHEN '1' THEN 'Rental'
                                      WHEN '2' THEN 'UJM'
                                 END issuingd_status, category_item.cati_id, cati_code, cati_name, item_detail.itemd_id, itemd_code, 
								(SELECT COUNT(*) 
								 FROM return_detail
								 INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
								 WHERE reth_is_canceled='0' AND retd_is_canceled='0' AND issuingh_id=issuingh_id_1 AND 
								       issuingd_id=issuingd_id_1 AND branch_id='$branch_id' AND reth_date<='$end_date') AS is_receipt
                           FROM issuing_header
                           INNER JOIN issuing_detail ON issuing_detail.issuingh_id=issuing_header.issuingh_id 
                           INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id AND item_detail.branch_id='$branch_id'
                           INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                           INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
						   INNER JOIN uom ON uom.uom_id=master_item.uom_id_2
                           WHERE issuing_header.branch_id='$branch_id' AND issuingh_is_canceled='0' AND (issuingh_date BETWEEN '$start_date' AND '$end_date') AND 
			                     cust_id='".$cust_id."'
                           ORDER BY cati_code, issuingd_status, itemd_code, issuingh_date";
             $exec_get_data=mysqli_query($db_connection, $q_get_data);
			 $rows_data="Nama Customer"."\t".":"."\t"."[".$cust_code."] - ".$cust_name."\n"."Tipe Pelanggan"."\t".":"."\t".$cust_type."\n"."Mulai Tanggal"."\t".":"."\t".$start_date_1."\n"."Sampai Tanggal"."\t".":"."\t".$end_date_1."\n\n";	
			 $rows_data=$rows_data."No"."\t"."Kategori Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial No"."\t"."Qty"."\t"."No Transaksi"."\t"."DO No"."\t"."Tanggal Keluar"."\t"."Umur (Hari)"."\t"."Status"."\n";
			  $no=1;
			  $masti_id="";
			  $cati_id="";
			  $total=0;
		      $grand_total=0;
			  $issuingh_id='';
			  $param=0;
			  $cati_code="";
			  if (mysqli_num_rows($exec_get_data)==0)
			     {
				  $rows_data=$rows_data.""."\t".""."\t".""."\t".""."\t"."\t"."--- TIDAK ADA DETAIL DATA ---"."\t".""."\t".""."\t".""."\t".""."\n\n\n"; 
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
										 $rows_data=$rows_data.""."\t"."\t"."\t"."TOTAL :"."\t".$total." ".$field_data['uom_name']."\t".""."\t".""."\t".""."\t".""."\t"."".""."\n"; 
									 $total=0;
									 $rows_data=$rows_data.""."\t"."[".$field_data['cati_code']."] - ".$field_data['cati_name']."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\n"; 
		                           }
								   
								if ($masti_id!=$masti_id_1 || ($masti_id==$masti_id_1 && $cati_id!=$cati_id_1)) 
								   {
									  $param=1;
									  //$cati_code=$field_data['cati_code'];
								   }
								   
								if ($issuingh_id!=$issuingh_id_1 || $cati_id!=$cati_id_1)
								   {
									 $issuingh_code=$field_data['issuingh_code'];
									 $issuingh_do_no=$field_data['issuingh_do_no'];
									 $issuingh_date=get_date_1($field_data['issuingh_date']);
								   }   
								   
								$rows_data=$rows_data.$no++."\t".$cati_code."\t"."[".$field_data['itemd_code']."] - ".$field_data['masti_name']."\t".$field_data['itemd_serial_no']."\t".$field_data['issuingd_qty']." ".$field_data['uom_name']."\t".$issuingh_code."\t".$issuingh_do_no."\t".$issuingh_date."\t".$field_data['aging']."\t".$field_data['issuingd_status']."\n";

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
								$cati_code="";
                              }
						 }
				   $rows_data=$rows_data.""."\t"."\t"."\t"."TOTAL :"."\t".$total." ".$uom_name."\t".""."\t".""."\t".""."\t".""."\t"."".""."\n"; 
				   $rows_data=$rows_data.""."\t"."\t"."\t"."GRAND TOTAL :"."\t".$grand_total."\t".""."\t".""."\t".""."\t".""."\t"."".""."\n\n\n\n"; 
				 }
		     $rows_data = str_replace( "\r" , "" , $rows_data);		 
			 echo  $header."\n".$rows_data;
	       } 		   
     header("Content-type: application/vnd.ms-excel");
     header("Content-disposition: xls" . date("Y-m-d") . ".xls");
     header("Content-disposition: filename=List_SOA_Report.xls");
    // echo  $date.$header."\n".$rows_data;
	 exit;
 ?>	 
