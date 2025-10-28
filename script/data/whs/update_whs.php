<?php
 include "../../library/check_session.php";
 include "../../library/db_connection.php";	
 $project_id=$_SESSION['ses_project'];
 if (isset($_GET['c']))
    {
	  if (mysqli_real_escape_string($db_connection, $_GET['c'])=='u')
	     {
		   $id=htmlspecialchars($_COOKIE['id']);
		   $code=htmlspecialchars($_COOKIE['code']);
		   $name=htmlspecialchars($_COOKIE['name']);
		   mysqli_autocommit($db_connection, false);
		   $q_update_classification="UPDATE classification_material SET classm_code='$code', classm_name='$name' WHERE classm_id='$id' AND prj_id='$project_id'";
           $exec_update_classification=mysqli_query($db_connection, $q_update_classification);
           if ($exec_update_classification)
              {
                mysqli_commit($db_connection);
				setcookie("id","");
                setcookie("code","");
	            setcookie("name","");
                ?>
                   <script language="javascript">
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
                     alert('Something Wrong!\nClassification could not be saved!');
                     window.close();
                   </script>
                <?php
              }
		 }
	} 
 else	
	{
	  setcookie("id","");
      setcookie("code","");
	  setcookie("name","");
	  ?>
	     <script language="javascript">
		   window.close();
		 </script>
	  <?php 
	}
?>