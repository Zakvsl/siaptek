<?php
 include "../../library/check_session.php";
 include "../../library/db_connection.php";
 
 if (isset($_GET['p']))
    {
      $close_period=$_GET['p'];
 
      $q_check_period="SELECT * FROM apt_period WHERE aptp_id='$close_period'";
      $exec_check_period=mysqli_query($db_connection, $q_check_period);
	  $data=mysqli_fetch_array($exec_check_period);
      if (mysqli_num_rows($exec_check_period)>0)
         {
           if ($data['aptp_is_active_period']=='1' && $data['aptp_is_closed']=='1')
		      {
	            ?>
	              <script language="javascript">
		            alert('Period Transaksi sudah di tutup sebelumnya!');
			        window.location="../../index/index.php?page=period";
		          </script>
	            <?php			     
			  }
		   else
		   if ($data['aptp_is_active_period']!='0' || $data['aptp_is_closed']!='0')
		      {
	            ?>
	              <script language="javascript">
		            alert('Period Transaksi yang akan ditutup, bukan merupakan period Aktif saat ini!');
			        window.location="../../index/index.php?page=period";
		          </script>
	            <?php			  
			  }
		   else
		      {
			    mysqli_query($db_connection, 'begin');
				$is_continue='1'; 
				$old_month_period=$data['aptp_month_1'];
				$old_year_period=$data['aptp_year'];
			    $q_update_period="UPDATE apt_period SET aptp_is_active_period='1', aptp_is_closed='1' WHERE aptp_id='$close_period'"; 
				$exec_update_period=mysqli_query($db_connection, $q_update_period);
				if ($exec_update_period)
				   {
				     if ($old_month_period=='12')
					    {
						  $new_month_period=1;
						  $new_month_period_1='Januari';
						  $new_year_period=$old_year_period+1;
						  
						  $q_check_new_period="SELECT * FROM apt_period WHERE aptp_year='$new_year_period'";
						  $exec_check_new_period=mysqli_query($db_connection, $q_check_new_period);
                          if (mysqli_num_rows($exec_check_new_period)==0)
                             {
							   $q_input_new_period="INSERT INTO apt_period (aptp_month_1, aptp_month_2, aptp_year, aptp_is_active_period, aptp_is_closed) 
		                                            VALUES ('1','Januari','$new_year_period','0','0'),
												           ('2','Februari','$new_year_period','1','0'),
													       ('3','Maret','$new_year_period','1','0'),
													       ('4','April','$new_year_period','1','0'),
													       ('5','Mei','$new_year_period','1','0'),
													       ('6','Juni','$new_year_period','1','0'),
													       ('7','Juli','$new_year_period','1','0'),
													       ('8','Agustus','$new_year_period','1','0'),
													       ('9','September','$new_year_period','1','0'),
													       ('10','Oktober','$new_year_period','1','0'),
													       ('11','November','$new_year_period','1','0'),
													       ('12','Desember','$new_year_period','1','0')";
		                       $exec_input_new_period=mysqli_query($db_connection, $q_input_new_period);
							   if (!$exec_input_new_period)
							      {
								    $is_continue='0';
			                        mysqli_query($db_connection, 'rollback');
	                                ?>
	                                  <script language="javascript">
		                                 alert('Terjadi kesalahan!\nSilahkan hubungi Programmer Anda!');
			                             window.location="../../index/index.php?page=period";
		                              </script>
	                                <?php
								  }
							   else
							      {
							        $is_continue='2';
								  }	
							 }
						}
					 else
					    {
						  $new_month_period=$old_month_period+1;
						  if ($new_month_period=='2')
						      $new_month_period_1='Februari';
						  else
						  if ($new_month_period=='3')
						      $new_month_period_1='Maret';
						  else
						  if ($new_month_period=='4')
						      $new_month_period_1='April';
						  else
						  if ($new_month_period=='5')
						      $new_month_period_1='Mei';
						  else
						  if ($new_month_period=='6')
						      $new_month_period_1='Juni';
						  else
						  if ($new_month_period=='7')
						      $new_month_period_1='Juli';
						  else
						  if ($new_month_period=='8')
						      $new_month_period_1='Agustus';
						  else
						  if ($new_month_period=='9')
						      $new_month_period_1='September';
						  else
						  if ($new_month_period=='10')
						      $new_month_period_1='Oktober';
						  else
						  if ($new_month_period=='11')
						      $new_month_period_1='November';
						  else
						  if ($new_month_period=='12')
						      $new_month_period_1='Desember';
							  
						  $new_year_period=$old_year_period;
						}
						
				     if ($is_continue=='1')
					    {
						  $q_update_new_period="UPDATE apt_period SET aptp_is_active_period='0', aptp_is_closed='0' 
						                        WHERE aptp_month_1='$new_month_period' AND aptp_year='$new_year_period'";
						  $exec_new_period=mysqli_query($db_connection, $q_update_new_period);
						  if ($exec_new_period)
						     {
			                   mysqli_query($db_connection, 'commit');
	                           ?>
	                              <script language="javascript">
								     var new_month='<?php echo $new_month_period_1;?>';
									 var new_year='<?php echo $new_year_period;?>';
								     alert('Period Transaksi berhasil ditutup!\nPeriod Aktif Transaksi baru adalah : '+new_month+' - '+new_year);
			                         window.location="../../index/index.php?page=period";
		                          </script>
	                           <?php	
							 }
						  else
						     {
			                   mysqli_query($db_connection, 'rollback');
	                           ?>
	                             <script language="javascript">
		                            alert('Terjadi keasalahn!\nSilahkan hubungi Programmer Anda!');
			                        window.location="../../index/index.php?page=period";
		                         </script>
	                           <?php
							 } 
					    }
				     else
					 if ($is_continue=='2')
					    {
						  mysqli_query($db_connection, 'commit');
						  ?>
						     <script language="javascript">
						        var x='<?php echo $new_year_period;?>';
						        alert('Period Transaksi berhasil ditutup!\nPeriod Aktif Transaksi baru adalah : Januari - '+x);
								window.location="../../index/index.php?page=period";
						     </script>
					      <?php
						}
					 else
					    {
			              mysqli_query($db_connection, 'commit');
	                      ?>
	                        <script language="javascript">
							    var new_month='<?php echo $new_month_period_1;?>';
							    var new_year='<?php echo $new_year_period;?>';
							    alert('Period Transaksi berhasil ditutup!\nPeriod Aktif Transaksi baru adalah : '+new_month+' - '+new_year);
			                    window.location="../../index/index.php?page=period";
		                    </script>
	                      <?php						  
						}
				   }
				else
				   {
			         mysqli_query($db_connection, 'rollback');
	                 ?>
	                   <script language="javascript">
		                  alert('Terjadi keasalahn!\nSilahkan hubungi Programmer Anda!');
			              window.location="../../index/index.php?page=period";
		               </script>
	                 <?php
				   }
			  }
	     }
      else
         {
	       ?>
	          <script language="javascript">
		         alert('Period Transaksi sudah di tutup sebelumnya!');
			     window.location="../../index/index.php?page=period";
		      </script>
	       <?php	         
		 }
    }
 else
    {
	  ?>
		 <script language="javascript">
			window.location="../../index/index.php?page=period";
		 </script>
	   <?php	  
	}
?>