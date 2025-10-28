<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $as_of_date=get_date_2($_POST['txt_aging_date']);
  if (isset($_POST['s_customer']))
      $customer=$_POST['s_customer'];
  else
      $customer=""; 
  $total_cust=count($customer);
  $cust_id='';
  
  $include_tube="0";
  if (isset($_POST['cb_tube']))
      $include_tube=$_POST['cb_tube'];
	  
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
	 }
  else
	 $cust_id="SELECT cust_id FROM customer WHERE branch_id='$branch_id'";
	 	
  /*
     $q_get_report="SELECT issuing_header.cust_id, cust_code, cust_name, issuingh_id AS issuingh_id_1, issuingh_code, issuingh_date,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    issuing_detail.issuingh_id=issuingh_id_1 AND issuingh_date<='$as_of_date'),0) AS total,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    issuing_detail.issuingh_id=issuingh_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))<31),0) AS total_1_30,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    issuing_detail.issuingh_id=issuingh_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>30 AND                
								    (DATEDIFF('$as_of_date',issuingh_date))<61 ),0) AS total_31_60,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    issuing_detail.issuingh_id=issuingh_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>60 AND 
								   (DATEDIFF('$as_of_date',issuingh_date))<91 ),0) AS total_61_90,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    issuing_detail.issuingh_id=issuingh_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>90 AND 
								   (DATEDIFF('$as_of_date',issuingh_date))<121 ),0) AS total_91_120,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    issuing_detail.issuingh_id=issuingh_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>120),0) AS total_121
               FROM issuing_header
               INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
               WHERE issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_date<='$as_of_date' AND issuingh_type='0' AND 
			         issuingh_status!='2' AND issuingh_is_canceled='0' AND issuing_header.cust_id IN ($cust_id) 
			   ORDER BY issuing_header.cust_id, issuingh_date ASC"; */
  $q_get_report="SELECT IH.cust_id, cust_code, cust_name, IH.issuingh_id, issuingh_code, issuingh_date,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                        issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`))),0) AS total,
                        IFNULL((SELECT SUM(issuingd_qty)
                                FROM issuing_detail 
                                INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                WHERE issuingd_is_canceled='0' AND 
                                      issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                      issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                           WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                            )
                                                         ) AND
				                     (DATEDIFF('$as_of_date',issuingh_date))<31),0) AS total_1_30,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE issuingd_is_canceled='0' AND 
                                       issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND
                                      (DATEDIFF('$as_of_date',issuingh_date))>30 AND (DATEDIFF('$as_of_date',issuingh_date))<61),0) AS total_31_60,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE issuingd_is_canceled='0' AND 
                                       issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>60 AND (DATEDIFF('$as_of_date',issuingh_date))<91),0) AS total_61_90,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE issuingd_is_canceled='0' AND 
                                       issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>90 AND (DATEDIFF('$as_of_date',issuingh_date))<121 ),0) AS total_91_120,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE issuingd_is_canceled='0' AND 
                                       issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>120),0) AS total_121 
                 
                  FROM issuing_header IH
                  INNER JOIN issuing_detail ID ON IH.issuingh_id=ID.issuingh_id
                  INNER JOIN customer CUST ON IH.cust_id=CUST.cust_id
                  WHERE  IH.branch_id='$branch_id' AND CUST.branch_id='$branch_id' AND issuingh_type='0' AND issuingd_is_canceled='0' AND 
                         issuingh_is_canceled='0' AND 
                         issuingh_date<='$as_of_date' AND 
                         issuingd_id NOT IN (SELECT issuingd_id 
                                             FROM return_detail 
                                             WHERE reth_id IN (SELECT reth_id 
                                                               FROM return_header 
                                                               WHERE branch_id='$branch_id' AND reth_date<='$as_of_date' AND issuingh_id=IH.`issuingh_id`)) AND
                        IH.cust_id IN ($cust_id)
                  GROUP BY issuingh_code
                  ORDER BY IH.cust_id, issuingh_date ASC";
  $exec_get_report=mysqli_query($db_connection, $q_get_report);
  //echo $q_get_report;
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_Aging_Report.xls';
       $header="SUMMARY AGING REPORT"."\n";
	   $as_of_dates="AS Of Date : ".$as_of_date."\n\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="Nama Customer"."\t"."Kode Pengeluaran"."\t"."Tanggal"."\t"."Total"."\t"."1-30"."\t"."31-60"."\t"."61-90"."\t"."91-120"."\t".">120"."\n"; 
	   $first_data="Yes";
	   $no=0;
       $cust_id=""; 
	   $total=0;
	   $total_30=0;
	   $total_31_60=0;
	   $total_61_90=0;
	   $total_91_120=0;
	   $total_121=0;
	   $total_1=0;
	   $total_30_1=0;
	   $total_31_60_1=0;
	   $total_61_90_1=0;
	   $total_91_120_1=0;
	   $total_121_1=0;
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   if ($cust_id!=$field_data['cust_id'])
		          {
				    if ($first_data=="Yes" && $no!=0)
					   {
					     $rows_data=$rows_data.""."\t".""."\t"."Sub Total ".$field_data['cust_code']."\t".$total."\t".$total_30."\t".$total_31_60."\t".$total_61_90."\t".$total_91_120."\t".$total_121."\n"; 
					   }
					   
					$total=0;
					$total_30=0;
					$total_31_60=0;
					$total_61_90=0;
				    $total_91_120=0;
					$total_121=0;
					$no++;
			        $rows_data=$rows_data.$field_data['cust_code']." - ".$field_data['cust_name']."\t".$field_data['issuingh_code']."\t".$field_data['issuingh_date']."\t".$field_data['total']."\t".$field_data['total_1_30']."\t".$field_data['total_31_60']."\t".$field_data['total_61_90']."\t".$field_data['total_91_120']."\t".$field_data['total_121']."\n";
			      }
			   else	 
			      {
				    $first_data="Yes";  
			        $rows_data=$rows_data.""."\t".$field_data['issuingh_code']."\t".$field_data['issuingh_date']."\t".$field_data['total']."\t".$field_data['total_1_30']."\t".$field_data['total_31_60']."\t".$field_data['total_61_90']."\t".$field_data['total_91_120']."\t".$field_data['total_121']."\n";			
			      }
			   
			   $no_1=0;
			   if ($include_tube=='1')
				  {
					$no_1=0;
					$rows_data=$rows_data.""."\t".""."\t".""."\t"."No"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial No"."\t".""."\t".""."\n"; 
					/*$q_get_issuing_detail="SELECT itemd_code, masti_name, itemd_serial_no
                                           FROM issuing_detail id
                                           INNER JOIN issuing_header ih ON ih.issuingh_id=id.issuingh_id
                                           INNER JOIN item_detail itd ON itd.itemd_id=id.itemd_id
                                           INNER JOIN master_item mi ON mi.masti_id=itd.masti_id
                                           WHERE issuingh_code='".$field_data['issuingh_code']."' AND id.issuingd_is_canceled='0' AND id.issuingd_is_return='0'"; */
					$q_get_issuing_detail="SELECT itemd_code, masti_name, itemd_serial_no
										   FROM issuing_detail 
										   INNER JOIN item_detail ON item_detail.`itemd_id`=issuing_detail.`itemd_id`
                                           INNER JOIN master_item ON master_item.`masti_id`=item_detail.`masti_id`
                                           INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                           WHERE issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND 
												 issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND issuing_header.branch_id='$branch_id' AND 
												 issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
																     WHERE reth_id IN (SELECT reth_id 
                                                                                       FROM return_header 
                                                                                       WHERE branch_id='$branch_id' AND reth_date<='$as_of_date' AND 
																							 issuingh_id=issuing_header.`issuingh_id`)) AND
																 issuingh_code='".$field_data['issuingh_code']."'";
					$exec_get_issuing_detail=mysqli_query($db_connection, $q_get_issuing_detail);
					while ($field_issuing_detail=mysqli_fetch_array($exec_get_issuing_detail))
						  {
							$no_1++;
							$rows_data=$rows_data.""."\t".""."\t".""."\t".$no_1."\t".$field_issuing_detail['itemd_code']."\t".$field_issuing_detail['masti_name']."\t".$field_issuing_detail['itemd_serial_no']."\t".""."\t".""."\n"; 
						  } 	
				  }
				  	
			   $total=$total+$field_data['total'];
		       $total_30=$total_30+$field_data['total_1_30'];
		       $total_31_60=$total_31_60+$field_data['total_31_60'];
			   $total_61_90=$total_61_90+$field_data['total_61_90'];
		       $total_91_120=$total_91_120+$field_data['total_91_120'];
			   $total_121=$total_121+$field_data['total_121'];
			   $total_1=$total_1+$field_data['total'];
		       $total_30_1=$total_30_1+$field_data['total_1_30'];
			   $total_31_60_1=$total_31_60_1+$field_data['total_31_60'];
			   $total_61_90_1=$total_61_90_1+$field_data['total_61_90'];
			   $total_91_120_1=$total_91_120_1+$field_data['total_91_120'];
			   $total_121_1=$total_121_1+$field_data['total_121']; 

		       $cust_id=$field_data['cust_id'];
			 }
		
	   $rows_data=$rows_data.""."\t".""."\t"."Sub Total ".$field_data['cust_code']."\t".$total."\t".$total_30."\t".$total_31_60."\t".$total_61_90."\t".$total_91_120."\t".$total_121."\n";	
	   $rows_data=$rows_data.""."\t".""."\t"."Grand Total"."\t".$total_1."\t".$total_30_1."\t".$total_31_60_1."\t".$total_61_90_1."\t".$total_91_120_1."\t".$total_121_1."\n";	 
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Aging_Report.xls");
       echo  $header.$as_of_dates.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ditemukan!');
			   history.back();
			</script>
		<?php
	 } 	   
?>