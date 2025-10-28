<?php
  ob_start();
  include "../../library/check_session.php";
  $project_id=$_SESSION['ses_project'];
  include "../../library/db_connection.php";

        include "../../library/excel_reader.php";
        $target=basename($_FILES["txt_file"]["name"]);
        $file_tmp_name = $_FILES["txt_file"]["tmp_name"];
        if ($target=='')
           {	
	         ?>
		       <script language="javascript">
		        alert('No File Selected!');
		       </script>
	         <?php 
	        }
         else
         if (move_uploaded_file($file_tmp_name,$target))
	        {	 
	          $data=new Spreadsheet_Excel_Reader($_FILES['txt_file']['name'],false);
	          $row=$data->rowcount($sheet_index=0);
	          $query_data="";
			  $current_date=date('d-m-Y');
			  $current_hour=date("H:i:s");
			  $doc_name="PO-".$current_date.$current_hour;
	                for ($i=2; $i<=$row; $i++)
	                    {
				          $temp_id=$doc_name;
      		              $project_code=htmlspecialchars($data->val($i,1));
			              $vend_code=htmlspecialchars($data->val($i,2));
			              $poh_no=htmlspecialchars($data->val($i,3));
			              $poh_date=htmlspecialchars($data->val($i,4));
			              $poh_etd=htmlspecialchars($data->val($i,5));
						  $poh_eta=htmlspecialchars($data->val($i,6));
			              $poh_contract_no=htmlspecialchars($data->val($i,7));
			              $poh_notes_header=htmlspecialchars($data->val($i,8));
						  $mmat_code=htmlspecialchars($data->val($i,9));
						  $isometric_no=htmlspecialchars($data->val($i,10));
						  $qty=htmlspecialchars($data->val($i,11));
						  $notes_detail=htmlspecialchars($data->val($i,12));
						  
			              if ($q_data=='')
			                  $q_data="('$temp_id','$project_code','$vend_code','$poh_no','$poh_date','$poh_etd','$poh_eta','$poh_contract_no','$poh_notes_header',
							            '$mmat_code','$isometric_no','$qty','$notes_detail')";
		                  else		
			                  $q_data=$q_data.",('$temp_id','$project_code','$vend_code','$poh_no','$poh_date','$poh_etd','$poh_eta','$poh_contract_no','$poh_notes_header',
							                     '$mmat_code','$isometric_no','$qty','$notes_detail')";
			             }
	                $q_input_data="INSERT INTO poh_temporary (poht_id, prj_code, vend_code, poh_code, poh_date, poh_etd, poh_eta, poh_contract_no, poh_notes_header, mmat_code, 
					                                          dwgd_isometric_no, qty, notes_detail)
					               VALUES  ".$q_data;						  			   
				//	echo $q_input_data;
	                mysqli_autocommit($db_connection, false);
                    $exec_input_data=mysqli_query($db_connection,$q_input_data);
	                if ($exec_input_data)
		               {  
				         $q_check_data_temporary="SELECT *, 'Project Not Found' AS Notes FROM poh_temporary WHERE poht_id='$temp_id' AND prj_code NOT IN (SELECT                                                  prj_code FROM project WHERE prj_id='$project_id')
                                                  UNION
                                                  SELECT *, 'Vendor Not Found' FROM poh_temporary WHERE poht_id='$temp_id' AND vend_code NOT IN (SELECT                                                  vend_code FROM vendor WHERE prj_id='$project_id')
                                                  UNION
												  SELECT *, 'Check PO Date, ETD and ETA' FROM poh_temporary WHERE poht_id='$temp_id' AND (poh_date>poh_etd OR poh_date>poh_eta OR 
												  poh_date='0000-00-00' OR poh_etd='0000-00-00' OR poh_eta='0000-00-00' OR poh_etd>poh_eta)
                                                  UNION
                                                  SELECT *, 'Duplicate PO No' FROM poh_temporary WHERE poht_id='$temp_id' AND poh_code IN (SELECT poh_code 
												  FROM po_header WHERE prj_id='$project_id')
                                                  UNION
												  SELECT *, 'Material Not Found' FROM poh_temporary WHERE poht_id='$temp_id' AND mmat_code NOT IN (SELECT mmat_code FROM 
												  master_material WHERE prj_id='$project_id')
                                                  UNION
												  SELECT *, 'Isometric No Not Found' FROM poh_temporary WHERE poht_id='$temp_id' AND dwgd_isometric_no NOT IN 
												 (SELECT dwgd_isometric_no FROM drawing_detail INNER JOIN drawing_header ON drawing_header.dwgh_id=drawing_detail.dwgh_id 
												  WHERE prj_id='$project_id')
                                                  UNION
												  SELECT poht_id, prj_code, vend_code, poh_code, poh_date, poh_etd, poh_eta, poh_contract_no, poh_notes_header, mmat_code AS 
												         mmat_code_1, dwgd_isometric_no, qty, notes_detail, 'Material Not Found at Drawing' FROM poh_temporary WHERE 
														 poht_id='$temp_id' AND CONCAT((SELECT mmat_id FROM master_material WHERE mmat_code=mmat_code_1 AND 
														 prj_id='$project_id'),'-',dwgd_isometric_no) NOT IN (SELECT CONCAT(mmat_id,'-',dwgd_isometric_no) 
														 FROM drawing_detail INNER JOIN drawing_header ON drawing_header.dwgh_id=drawing_detail.dwgh_id WHERE prj_id='$project_id')
												  UNION
                                                  SELECT *, 'Qty Must be Numeric' FROM poh_temporary WHERE poht_id='$temp_id' AND qty NOT REGEXP('(^[0-9]+$)')
                                                  UNION
												  SELECT *, 'Qty Must be Greater than 0' FROM poh_temporary WHERE poht_id='$temp_id' AND qty<=0
                                                  ORDER BY notes ASC";
						 $exec_data_temporary=mysqli_query($db_connection,$q_check_data_temporary);
						 if (mysqli_num_rows($exec_data_temporary)>0)
						    {
							  $q_delete_data_temp="DELETE FROM poh_temporary WHERE poht_id='$temp_id'";	
							  mysqli_query($db_connection,$q_delete_data_temp);
							  $no=1;
							  $filename='PO_Upload.xls';
                              $header="PURCHASE ORDER UPLOAD ERROR\n";
                              $date='Retrived Date : '.date("d-m-Y")."\n"; 
							  $rows_data="No"."\t"."Project_Code"."\t"."Vendor_Code"."\t"."PO_NO"."\t"."PO_Date_(YYYY-MM-DD)"."\t"."ETD_Date_(YYYY-MM-DD)"."\t"."ETA_Date_(YYYY-MM-DD)"."\t"."Contract_No"."\t"."Notes_Header"."\t"."Material_Code"."\t"."Isometric_Line_No_Drawing"."\t"."Qty"."\t"."Notes_Detail"."\n"; 
							  while ($field_data_temporary=mysqli_fetch_array($exec_data_temporary))
							        {
									  $rows_data=$rows_data.$no++."\t".$field_data_temporary['prj_code']."\t".$field_data_temporary['vend_code']."\t".$field_data_temporary['poh_code']."\t".$field_data_temporary['poh_date']."\t".$field_data_temporary['poh_etd']."\t".$field_data_temporary['poh_eta']."\t".$field_data_temporary['poh_contract_no']."\t".$field_data_temporary['poh_notes_header']."\t".$field_data_temporary['mmat_code']."\t".$field_data_temporary['dwgd_isometric_no']."\t".$field_data_temporary['qty']."\t".$field_data_temporary['notes_detail']."\t".$field_data_temporary['Notes']."\n";
									}
							  $rows_data = str_replace( "\r" , "" , $rows_data);
                              header("Content-type: application/vnd.ms-excel");
                              header("Content-disposition: xls" . date("Y-m-d") . ".xls");
                              header("Content-disposition: filename=Master_Material_Upload.xls");
                              echo  $header.$date.$column."\n".$rows_data;  
							  exit;	
							}
					     else
						    {
							  $q_get_poh_temporary="SELECT poht_id, prj_code AS prj_code_1, poh_code,
                                                    (SELECT prj_id FROM project WHERE prj_code=prj_code_1 AND prj_id='$project_id') AS prj_id_1, poh_date, poh_etd, poh_eta,
													vend_code AS vend_code_1, (SELECT vend_id FROM vendor WHERE prj_id=prj_id_1 AND vend_code=vend_code_1) AS vend_id,
													poh_contract_no, poh_notes_header,
													mmat_code AS mmat_code_1, (SELECT mmat_id FROM master_material WHERE prj_id=prj_id_1 AND mmat_code=mmat_code_1) AS mmat_id_1,
													dwgd_isometric_no AS dwgd_isometric_no_1, (SELECT dwgd_id FROM drawing_detail INNER JOIN drawing_header ON 
													drawing_header.dwgh_id=drawing_detail.dwgh_id WHERE mmat_id=mmat_id_1 AND dwgd_isometric_no=dwgd_isometric_no_1) as dwgd_id,
													qty, notes_detail
                                                    FROM poh_temporary WHERE poht_id='$temp_id' ORDER BY poh_code ASC";
						//	  echo $q_get_poh_temporary."<br><br>";
							  $exec_get_poh_temporary=mysqli_query($db_connection,$q_get_poh_temporary, $db_connection) or die ('Error Eng '.mysqli_error());
							  if (($exec_get_poh_temporary) && (mysqli_num_rows($exec_get_poh_temporary)>0))
							      {
								    $input_poh_data='';
									$poh_code_1='';
							        while ($field_data=mysqli_fetch_array($exec_get_poh_temporary))
							              {
										    if ($poh_code_1!=$field_data['poh_code'])
											    {
												  if ($input_poh_header=='')
												      $input_poh_header="('".$field_data['prj_id_1']."','".$field_data['poh_code']."','".$field_data['poh_date']."','".$field_data['poh_etd']."','".$field_data['poh_eta']."','".$field_data['vend_id']."','".$field_data['poh_contract_no']."','".$field_data['poh_notes_header']."')";
												  else
												      $input_poh_header=$input_poh_header.", ('".$field_data['prj_id_1']."','".$field_data['poh_code']."','".$field_data['poh_date']."','".$field_data['poh_etd']."','".$field_data['poh_eta']."','".$field_data['vend_id']."','".$field_data['poh_contract_no']."','".$field_data['poh_notes_header']."')";
												}
										  
									        if ($input_poh_data=='')
											    $input_poh_data="((SELECT poh_id FROM po_header WHERE prj_id='".$field_data['prj_id_1']."' AND poh_code='".$field_data['poh_code']."'),'".$field_data['dwgd_id']."','".$field_data['qty']."','".$field_data['notes_detail']."')"; 
											else
											    $input_poh_data=$input_poh_data.", ((SELECT poh_id FROM po_header WHERE prj_id='".$field_data['prj_id_1']."' AND poh_code='".$field_data['poh_code']."'),'".$field_data['dwgd_id']."','".$field_data['qty']."','".$field_data['notes_detail']."')"; 
									        $poh_code_1=$field_data['poh_code'];
										  }
							 
							        $q_input_data_header="INSERT INTO po_header (prj_id, poh_code, poh_date, poh_etd, poh_eta, vend_id, poh_contract_no, poh_notes)
							                       VALUES ".$input_poh_header;
									$q_input_data_detail="INSERT INTO po_detail (poh_id, dwgd_id, pod_qty, pod_notes) VALUES ".$input_poh_data;			   
						  	        $q_delete_data_temp="DELETE FROM poh_temporary WHERE poht_id='$temp_id'";
								//	echo $q_input_data_header."<br>";
								//	echo $q_input_data_detail."<br>";
								//	echo $q_delete_data_temp;
								    $exec_input_data_header=mysqli_query($db_connection,$q_input_data_header);
									$exec_input_data_detail=mysqli_query($db_connection,$q_input_data_detail);// or die ('Error Eng '.mysqli_error());	
							        $exec_delete_data_temp=mysqli_query($db_connection,$q_delete_data_temp);// or die mysqli_error;	
							        if ($exec_input_data_header && $exec_input_data_detail && $exec_delete_data_temp)
							           {
		                                 mysqli_commit($db_connection); 
			   	                         ?>
				                            <script language="javascript">
				                              alert("Upload success!");
					                          opener.location.reload(true);
					                          window.close();
				                            </script>
				                         <?php
								       }
							        else
							           {
								          mysqli_rollback($db_connection);
				                          ?>
				                            <script language="javascript">
					                          alert('Something wrong!\nPlease contact your programmer!');
				                              window.location.href='javascript:history.back(1)';
					                        </script>
				                          <?php	
									   }  
								 }	   
							}		
	                   }
	                else
	                   {
			             mysqli_rollback($db_connection);
				         ?>
				            <script language="javascript">
					          alert('Something wrong!\nPlease check and reupload the file!');
				           //   window.location.href='javascript:history.back(1)';
					        </script>
				         <?php	 
		               }   	
		    }	  

?>