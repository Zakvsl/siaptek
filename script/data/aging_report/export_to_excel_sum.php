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
	 $cust_id="SELECT cust_id FROM customer";
	 /*	
     $q_get_report="SELECT cust_id AS cust_id_1, cust_code, cust_name,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date'),0) AS total,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))<31),0) AS total_1_30,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>30 AND 
								   (DATEDIFF('$as_of_date',issuingh_date))<61 ),0) AS total_31_60,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>60 AND 
								   (DATEDIFF('$as_of_date',issuingh_date))<91 ),0) AS total_61_90,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>90 AND 
								   (DATEDIFF('$as_of_date',issuingh_date))<121 ),0) AS total_91_120,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>120),0) AS total_121
               FROM customer
               WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id IN ($cust_id)";
			   */
  $q_get_report="SELECT cust_id AS cust_id_1, cust_code, cust_name,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                        issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`))),0) AS total,
                        IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                      issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                           WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                            )
                                                         ) AND
				                     (DATEDIFF('$as_of_date',issuingh_date))<31),0) AS total_1_30,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND
                                      (DATEDIFF('$as_of_date',issuingh_date))>30 AND (DATEDIFF('$as_of_date',issuingh_date))<61),0) AS total_31_60,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>60 AND (DATEDIFF('$as_of_date',issuingh_date))<91),0) AS total_61_90,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>90 AND (DATEDIFF('$as_of_date',issuingh_date))<121 ),0) AS total_91_120,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>120),0) AS total_121 
                  FROM customer
                  WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id IN ($cust_id)";
  //echo $q_get_report."<br>";
  $exec_get_report=mysqli_query($db_connection, $q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_Aging_Report.xls';
       $header="SUMMARY AGING REPORT"."\n";
	   $as_of_dates="AS Of Date : ".$as_of_date."\n\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="Kode Customer"."\t"."Nama Customer"."\t"."Total"."\t"."1-30"."\t"."31-60"."\t"."61-90"."\t"."91-120"."\t".">120"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   $rows_data=$rows_data.$field_data['cust_code']."\t".$field_data['cust_name']."\t".$field_data['total']."\t".$field_data['total_1_30']."\t".$field_data['total_31_60']."\t".$field_data['total_61_90']."\t".$field_data['total_91_120']."\t".$field_data['total_121']."\n";
			 }
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