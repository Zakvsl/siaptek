<style type="text/css">
* {
     padding: 0px;
     margin: 0px;
     font-family: calibri;
  }

#login {
         width: 99%;
		 height:auto;
         /*height: 100vh; */
         background-image:url(wika_2.jpg); 
         background-size: cover; 
         background-repeat: no-repeat; 
         position:absolute; 
       }

.center {
          width: 350px;
          height: auto;
          margin: 0 auto;
          margin-top: 100px;
          background-color: #f0f0f0;
          box-shadow: 2px 2px 16px 0px #757575;
          padding: 40px;
        }

.center h2 {
             font-size: 40px;
             text-align: center;
             color: #757575;
             padding-bottom: 40px;
           }

.f_log_in {
            width: 100%;
          }

.itpw {
                 width: 100%;
                 padding: 13px 10px;
                 margin: 5px 0px;
                 background-color: #dbdbdb;
                 border: 3px solid #dbdbdb;
                 color: #757575;
                 transition: all 0.7s;
               }

.its {
       width: 99.7%;
       font-size: 19px;
       color: #f5f5f5;
       padding: 12px;
       margin: 5px 0;
       /*background-color: #004d40; */
	   background-color:#0054a6;
       border: none;
       transition: all 0.4s;
     }

.itpw:focus {
              border-bottom: 3px solid #004d40;
              color: #004d40
            }

.its:hover , .its:focus {
                           opacity: 0.7;
                           cursor: pointer;
                        }

.center p {
            margin: 20px 0;
            text-align: center;
            font-size: 14px;
          }

.center p a {
              color: #757575;
            }

  @media screen and (min-width:1500px) 
                                      {
                                        .center {
                                                  width: 350px;
                                                }
                                      }

  @media screen and (max-width:900px) 
                                     {
                                       #login {
                                                background-size: 100% 100%;
                                              }

                                       .its {
                                              width: 100%;
                                            }

                                       .itpw {
                                               font-size: 14px;
                                               width: 90%;
                                               padding: 13px 3%;
                                             }

                                       .center {
                                                 width: 230px;
                                               }

                                       .center p {
                                                   font-size: 12px;
                                                 }

                                     }

  @media screen and (max-width:350px) 
                                     {
                                       .center {
                                                 padding: 20px;
                                                 width: 75%;
                                               } 
                                     }
</style>

<?php
// Database connection should already be included from check_session.php
if (!isset($db_connection)) {
    include "../../library/db_connection.php";
}

// Process login form
if (isset($_POST['btn_log_in']))
{
    $user_name = mysqli_real_escape_string($db_connection, $_POST['txt_user_name']);
    $password = $_POST['txt_password'];
    
    $query_check_user = "SELECT * FROM users WHERE users_name='$user_name' AND users_password=md5('$password')";
    $exec_check_user = mysqli_query($db_connection, $query_check_user);
    
    if (!$exec_check_user) {
        ?>
        <script language="javascript">
            alert('Query error: <?php echo addslashes(mysqli_error($db_connection)); ?>');
        </script>
        <?php
    } else {
        $total_data = mysqli_num_rows($exec_check_user);
        $field_users = mysqli_fetch_array($exec_check_user);
        
        if ($total_data > 0)
        {
            if ($field_users['users_status'] == '1')
            {
                ?>
                <script language="javascript">
                    alert('Status User InActive!\nSilahkan hubungi bagian Administrator!');
                </script>
                <?php 
            }
            else
            {		
                $q_check_branch = "SELECT * FROM branch";
                $exec_check_branch = mysqli_query($db_connection, $q_check_branch);
                
                if (mysqli_num_rows($exec_check_branch) > 0)
                {
                    $_SESSION['ses_user_id'] = $field_users['users_id'];
                    ?> 
                    <script language="javascript">
                        var open_child = null;
                        var un = '<?php echo $user_name; ?>';
                        var w = 475;
                        var h = 150;
                        var l = (screen.width / 2) - (w / 2);
                        var t = (screen.height / 2) - (h / 2);
                        open_child = window.open("../data/log_in_out/choose_branch.php?un=" + un, "f_choose_branch", "location=no,resizable=no, height=" + h + ", width=" + w + ", left=" + l + ", top=" + t + ", toolbar=0"); 
                        
                        function disable_parent_window() {
                            if (open_child && !open_child.closed) {
                                open_child.focus();
                            }	
                        }
                    </script> 	
                    <?php
                }	
                else
                {	   
                    if ($field_users['users_level'] == '0')
                    {
                        $_SESSION['ses_siaptek_admin'] = $user_name;
                        $_SESSION['ses_user_id'] = $field_users['users_id'];
                        $_SESSION['ses_user_naming'] = $field_users['users_names'];
                        $_SESSION['ses_user_level'] = $field_users['users_level'];
                        $_SESSION['ses_super_admin'] = "yes";
                        $_SESSION['ses_branch'] = 'D';
                        $_SESSION['ses_whs'] = 'D';
                        $_SESSION['ses_item_category'] = 'D';
                        $_SESSION['ses_master_item'] = 'D';
                        $_SESSION['ses_item_detail'] = 'D';
                        $_SESSION['ses_item_detail_all'] = 'D';
                        $_SESSION['ses_summary_item'] = 'D';
                        $_SESSION['ses_transfer_item'] = 'D';
                        $_SESSION['ses_receipt_transfer_item'] = 'D';
                        $_SESSION['ses_issuing'] = 'D';
                        $_SESSION['ses_returning'] = 'D';
                        $_SESSION['ses_broken'] = 'D';
                        $_SESSION['ses_write_off'] = 'D';
                        $_SESSION['ses_dispossal'] = 'D';
                        $_SESSION['ses_change_description'] = 'D';
                        $_SESSION['ses_issuing_report'] = 'D';
                        $_SESSION['ses_returning_report'] = 'D';
                        $_SESSION['ses_broken_report'] = 'D';
                        $_SESSION['ses_write_off_report'] = 'D';
                        $_SESSION['ses_dispossal_report'] = 'D';
                        $_SESSION['ses_change_description_report'] = 'D';
                        $_SESSION['ses_position_report'] = 'D';
                        $_SESSION['ses_aging_report'] = 'D';
                        $_SESSION['ses_soa_report'] = 'D';
                        $_SESSION['ses_vendor_item_report'] = 'D';
                        $_SESSION['ses_history_movement_report'] = 'D';
                        $_SESSION['ses_transfer_report'] = 'D';
                        $_SESSION['ses_doc_flow_report'] = 'D';
                        $_SESSION['ses_employee'] = 'D';
                        $_SESSION['ses_uom'] = 'D';
                        $_SESSION['ses_customer_type'] = 'D';
                        $_SESSION['ses_customer'] = 'D';
                        $_SESSION['ses_vendor'] = 'D';
                        $_SESSION['ses_apt_period'] = 'D';
                        
                        echo "<meta http-equiv='refresh' content='0; url=../../index/index.php'>";
                        exit;
                    }
                    else
                    {
                        ?>
                        <script language="javascript">
                            alert('Kantor cabang belum ada yang diinput!');
                        </script>
                        <?php 
                    }
                }
            }   
        }
        else
        {
            ?>
            <script language="javascript">
                alert('User tidak terdaftar!\nSilahkan coba log in kembali!');
                document.getElementById('txt_user_name').focus();
            </script>
            <?php
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Form Log In</title>
</head>
<body>
 <div id="login">
      <div class="center">
           <form id="f_log_in" name="f_log_in" method="post" action="" onSubmit="return call_validation_user()">
                 <table width="100%" border="0" cellspacing="2" cellpadding="2">
                        <tr>
                            <th colspan="3" scope="col"><marquee behavior="alternate" scrolldelay="150">LOGIN TO YOUR ACCOUNT</marquee></th>
                        </tr>
                        <tr>
                          <td width="50%"><input class="itpw" name="txt_user_name" id="txt_user_name" type="text" placeholder="username"/></td>
                        </tr>
                        <tr>
                          <td width="50%"><input class="itpw" name="txt_password" id="txt_password" type="password" placeholder="password"/></td>
                        </tr>
                        <tr>
                          <td width="50%"><input class="its" name="btn_log_in" type="submit" id="btn_log_in" value="Log In" /></td>
                        </tr>
                 </table>
           </form>
      </div>
 </div>

<script language="javascript">
  function call_validation_user()
           {
		     var x=document.getElementById('txt_user_name').value;
			 var y=document.getElementById('txt_password').value;
			 var error_message='';
			 if (document.getElementById('txt_user_name').value.trim()=='' && document.getElementById('txt_password').value.trim()=='')
                 error_message='Silahkan masukan user name dan password anda!';
			 else	 
			 if (document.getElementById('txt_user_name').value.trim()=='')
			     error_message='Silahkan masukan user name anda!';
			 else	 
			 if (document.getElementById('txt_password').value.trim()=='')
			     error_message='Silahkan masukan password anda!'; 	 	 
			 if (error_message!='')
			    { 
				  alert(error_message);
				  if (document.getElementById('txt_user_name').value.trim()=='')
				      document.getElementById('txt_user_name').focus();
				  else	  
				  if (document.getElementById('txt_password').value.trim()=='')
				      document.getElementById('txt_password').focus();
				  return (false);
				}
			 else
			    return (true);		
		   }
  function trim(str)
           {
              return str.replace(/^\s+|\s+$/g,'');
		   }  		   
</script>

</body>
</html>