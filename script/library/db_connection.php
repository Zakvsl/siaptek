<?php
 $host="localhost";
 $user_name="root";
 $password="";
 $database="siaptek";
 $db_connection=mysqli_connect($host, $user_name, $password);
 if (! $db_connection)
    {
	 // mysql_error();
	  ?>
	  <script type="text/javascript">
	    alert("Koneksi gagal!!");
  	  </script>
	  <?php 
	} 
 else	
     mysqli_select_db ($db_connection, $database) or die ("Database tidak ditemukan!!");
?>
   	