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
  include "../../library/library_function.php";
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
	 $cust_id="SELECT cust_id FROM customer";
	 /*
     $q_get_data="SELECT issuing_header.cust_id, cust_code, cust_name, issuingh_id AS issuingh_id_1, issuingh_code, issuingh_date,
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
			         issuingh_is_canceled='0' AND issuing_header.cust_id IN ($cust_id)
			   ORDER BY issuing_header.cust_id, issuingh_date ASC";   
	 $q_get_data="SELECT IH.cust_id, cust_code, cust_name, IH.issuingh_id, issuingh_code, issuingh_date,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id`),0) AS total,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
									  (DATEDIFF('$as_of_date',issuingh_date))<31),0) AS total_1_30,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>30 AND (DATEDIFF('$as_of_date',issuingh_date))<61),0) AS total_31_60,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>60 AND (DATEDIFF('$as_of_date',issuingh_date))<91),0) AS total_61_90,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>90 AND (DATEDIFF('$as_of_date',issuingh_date))<121 ),0) AS total_91_120,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=IH.issuingh_id AND
                                       reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>120),0) AS total_121
                  FROM issuing_header IH
                  INNER JOIN issuing_detail ID ON IH.issuingh_id=ID.issuingh_id
                  INNER JOIN customer CUST ON IH.cust_id=CUST.cust_id
                  INNER JOIN return_header RH ON RH.issuingh_id=IH.issuingh_id
                  INNER JOIN return_detail RD ON RD.reth_id=RH.reth_id
                  WHERE IH.branch_id='$branch_id' AND CUST.branch_id='$branch_id' AND RH.branch_id='$branch_id' AND IH.issuingh_id=ID.issuingh_id AND 
				        IH.cust_id=CUST.cust_id AND issuingd_is_canceled='0' AND issuingh_date<='$as_of_date'  AND issuingh_type='0' AND 
                        issuingh_is_canceled='0' AND RH.reth_date<='$as_of_date' AND ID.issuingd_id!=RD.issuingd_id AND
                        IH.cust_id IN ($cust_id)
                  GROUP BY issuingh_code
                  ORDER BY IH.cust_id, issuingh_date ASC"; */
     //echo $q_get_data;
	 $q_get_data="SELECT IH.cust_id, cust_code, cust_name, IH.issuingh_id, issuingh_code, issuingh_date,
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
	 $exec_get_data=mysqli_query($db_connection, $q_get_data);
     //echo $q_get_data;
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
		
<form id="f_customer" name="f_customer" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2">
    <tr>
      <td scope="col"><h2>DETAIL AGING REPORT</h2></td>
      <td align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></td>
    </tr>
    <tr>
      <td width="56%" scope="col">As Of : <?php echo get_date_1($as_of_date);?></td>
      <td width="43%" align="right"><input type="button" name="btn_back" value="Kembali" onclick="call_back()"/></td>
    </tr>

    <tr>
      <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="2%" scope="col" class="th_ltb">No</th>
          <th width="26%" scope="col" class="th_ltb">Nama Customer</th>
          <th width="14%" scope="col" class="th_ltb">Kode Pengeluaran</th>
          <th width="6%" scope="col" class="th_ltb">Tanggal</th>
          <th width="7%" scope="col" class="th_ltb">Total</th>
          <th width="7%" scope="col" class="th_ltb">1-30</th>
          <th width="8%" scope="col" class="th_ltb">31-60</th>
          <th width="7%" scope="col" class="th_ltbr">61-90</th>
          <th width="7%" scope="col" class="th_ltbr">91-120</th>
          <th width="8%" scope="col" class="th_ltbr">&gt;120</th>
          </tr>
		  <?php
			$no=0;
			$first_data="Yes";
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
		    while ($data=mysqli_fetch_array($exec_get_data))
				  {
   		  ?>
          <tr>
		  <?php 
		             if ($cust_id!=$data['cust_id'])
		                {
						  if ($first_data=="Yes" && $no!=0)
						     {
						       echo "<tr bgcolor='#CCCCCC'>";
						       echo "<td></td>";
						       echo "<td></td>";
						       echo "<td></td>";
						       echo "<td></td>";
						       echo "<td></td>";
						       echo "<td><font color='#FF0000'><b>$total</b></font></td>";
						       echo "<td><font color='#0000FF'><b>$total_30</b></font></td>";
						       echo "<td><font color='#0000FF'><b>$total_31_60</b></font></td>";
						       echo "<td><font color='#0000FF'><b>$total_61_90</b></font></td>";
						       echo "<td><font color='#0000FF'><b>$total_91_120</b></font></td>";
						       echo "<td><font color='#0000FF'><b>$total_121</b></font></td>";
						       echo "</tr>";
						     }
						  
					      $total=0;
					      $total_30=0;
					      $total_31_60=0;
					      $total_61_90=0;
					      $total_91_120=0;
					      $total_121=0;
					      $no++;
		  ?> 
                          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data['cust_id'];?>" /></td>
                          <td align="center" class="td_lb"><font color='#0000FF'><?php echo $no;?></font></td>
                          <td class="td_lb"><font color='#0000FF'><?php echo $data['cust_code']." - ".$data['cust_name'];?></font></td>
		  <?php
					    } 
				     else
				        {
						  $first_data="Yes";      
		  ?> 
                          <td></td>
                          <td></td>
                          <td></td>
		  <?php  
					    } 
		  ?>		
                     <td class="td_lb"><font color='#0000FF'><?php echo $data['issuingh_code'];?></font></td>
		             <td class="td_lbr"><font color='#0000FF'><?php echo get_date_1($data['issuingh_date']);?></font></td>
                     <td class="td_lb"><font color='#0000FF'><?php echo $data['total'];?></font></td>
                     <td class="td_lb"><font color='#0000FF'><?php echo $data['total_1_30'];?></font></td>
                     <td class="td_lb"><font color='#0000FF'><?php echo $data['total_31_60'];?></font></td>
                     <td width="2%" class="td_lb"><font color='#0000FF'><?php echo $data['total_61_90'];?></font></td>
                     <td width="2%" class="td_lb"><font color='#0000FF'><?php echo $data['total_91_120'];?></font></td>
                     <td width="2%" class="td_lbr"><font color='#0000FF'><?php echo $data['total_121'];?></font></td>
                     </tr><?php 
					           if ($include_tube=='1')
							      {
					                $no_1=0;
					                echo "<tr>";
							        echo "    <td colspan='4'></td>";
							        echo "    <td colspan='7'>";
							        echo "        <table class='table-list'>";
							        echo "               <tr>";
							        echo "                   <th>No</th>";
							        echo "                   <th>Kode Aset</th>";
							        echo "                   <th>Deskripsi Isi Aset</th>";
							        echo "                   <th>Serial No</th>";
							        echo "               </tr>";
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
																 issuingh_code='".$data['issuingh_code']."'";
							       // echo $q_get_issuing_detail;
									$exec_get_issuing_detail=mysqli_query($db_connection, $q_get_issuing_detail);
							        while ($field_issuing_detail=mysqli_fetch_array($exec_get_issuing_detail))
							              {
									        $no_1++;
							    	        echo "       <tr>";
							    	        echo "           <td>".$no_1."</td>";
							    	        echo "           <td>".$field_issuing_detail['itemd_code']."</td>";
							    	        echo "           <td>".$field_issuing_detail['masti_name']."</td>";
							    	        echo "           <td>".$field_issuing_detail['itemd_serial_no']."</td>";
							    	        echo "       </tr>";   
									      }
							        echo "        </table>";
							        echo "    </td> ";
							        echo "</tr>";
								  }
								
		                       $total=$total+$data['total'];
		                       $total_30=$total_30+$data['total_1_30'];
					           $total_31_60=$total_31_60+$data['total_31_60'];
					           $total_61_90=$total_61_90+$data['total_61_90'];
					           $total_91_120=$total_91_120+$data['total_91_120'];
					           $total_121=$total_121+$data['total_121'];
					           $total_1=$total_1+$data['total'];
					  
		                       $total_30_1=$total_30_1+$data['total_1_30'];
					           $total_31_60_1=$total_31_60_1+$data['total_31_60'];
					           $total_61_90_1=$total_61_90_1+$data['total_61_90'];
					           $total_91_120_1=$total_91_120_1+$data['total_91_120'];
					           $total_121_1=$total_121_1+$data['total_121'];
		                       $cust_id=$data['cust_id'];
		          } 

		    echo "<tr bgcolor='#CCCCCC'>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
		    echo "<td></td>";
		    echo "<td></td>";
		    echo "<td><font color='#FF0000'><b>$total</b></font></td>";
		    echo "<td><font color='#0000FF'><b>$total_30</b></font></td>";
		    echo "<td><font color='#0000FF'><b>$total_31_60</b></font></td>";
		    echo "<td><font color='#0000FF'><b>$total_61_90</b></font></td>";
		    echo "<td><font color='#0000FF'><b>$total_91_120</b></font></td>";
		    echo "<td><font color='#0000FF'><b>$total_121</b></font></td>";
		    echo "</tr>";		
		
			echo "<tr>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td><hr width='100%'><font color='#0000FF'><b>$total_1</b></font></td>";
			echo "<td><hr width='100%'><font color='#FF0000'><b>$total_30_1</b></font></td>";
			echo "<td><hr width='100%'><font color='#FF0000'><b>$total_31_60_1</b></font></td>";
			echo "<td><hr width='100%'><font color='#FF0000'><b>$total_61_90_1</b></font></td>";
			echo "<td><hr width='100%'><font color='#FF0000'><b>$total_91_120_1</b></font></td>";
			echo "<td><hr width='100%'><font color='#FF0000'><b>$total_121_1</b></font></td>";
			echo "</tr>";
	    ?>
      </table></td>
        <td width="0%" align="left">&nbsp;</td>
        <td width="1%" align="right">&nbsp;</td>
    </tr>
  </table>
</form>

<script language="javascript">
   function call_back()
            {
			  history.back();
			}
</script>

</body>
</html>
