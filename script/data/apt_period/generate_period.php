<?php
 include "../../library/check_session.php";
 include "../../library/db_connection.php";
 
 if (isset($_POST['s_last_period']))
    {
      $last_year=$_POST['s_last_period'];
 
      $q_check_period="SELECT * FROM apt_period WHERE aptp_year='$last_year'";
      $exec_check_period=mysqli_query($db_connection, $q_check_period);
      if (mysqli_num_rows($exec_check_period)>0)
         {
	       ?>
	          <script language="javascript">
		         alert('Period Transaksi sudah terbentuk!');
			     window.location="../../index/index.php?page=period";
		      </script>
	       <?php
	     }
      else
         {
	       $prev_year=$last_year-1;
           $q_check_prev_period="SELECT * FROM apt_period WHERE aptp_year='$prev_year'";
           $exec_check_prev_period=mysqli_query($db_connection, $q_check_prev_period);
           if (mysqli_num_rows($exec_check_prev_period)>0)
              {
		        mysqli_query($db_connection, 'begin');
                $q_input_new_period="INSERT INTO apt_period (aptp_month_1, aptp_month_2, aptp_year, aptp_is_active_period, aptp_is_closed) 
		                                             VALUES ('1','Januari','$last_year','1','0'),
												            ('2','Februari','$last_year','1','0'),
													        ('3','Maret','$last_year','1','0'),
													        ('4','April','$last_year','1','0'),
													        ('5','Mei','$last_year','1','0'),
													        ('6','Juni','$last_year','1','0'),
													        ('7','Juli','$last_year','1','0'),
													        ('8','Agustus','$last_year','1','0'),
													        ('9','September','$last_year','1','0'),
													        ('10','Oktober','$last_year','1','0'),
													        ('11','November','$last_year','1','0'),
													        ('12','Desember','$last_year','1','0')";
		        $exec_input_new_period=mysqli_query($db_connection, $q_input_new_period);
		        if ($exec_input_new_period)
		           {
			         mysqli_query($db_connection, 'commit');
			         ?>
				        <script language="javascript">
				           window.location="../../index/index.php?page=period";
				        </script>
				     <?php
			       } 
		        else
		           {
			         mysqli_query($db_connection, 'rollback');
	                 ?>
	                   <script language="javascript">
		                  alert('Terjadi kesalahan!\nSilahkan hubungi Programmer Anda!');
			              window.location="../../index/index.php?page=period";
		               </script>
	                 <?php
			       }
	          }
           else	  	  
	          {
	            ?>
	               <script language="javascript">
			          var prev_last_year='<?php echo $prev_year;?>';
		              alert('Period Transaksi tahun sebelumnya ('+prev_last_year+') belum terbentuk!');
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
			window.location="../../index/index.php?page=period";
		 </script>
	   <?php	  
	}
?>