<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
	include_once('settings.html');
	
	// The following code block is for changing the user settings
    
  if (isset($_POST['npass'])){
      if ((md5($_POST['opass']) == $_SESSION['pass']) && ($_POST['npass'] == $_POST['rnpass']) && ($_POST['npass'] != "")){
          try {
             $sql = 'UPDATE users SET 
                  password = :npass where userid = '.$_SESSION['uid'];
             $s = $dbc->prepare($sql);
             $s->bindValue(':npass', md5($_POST['npass']));
             $s->execute();
             }
           catch (PDOException $e){
                $error = 'Error Changing Password ';
                include_once($_SERVER['DOCUMENT_ROOT'].'/error.html.php');
                exit();
            }
        }
    header('Location: .');
    exit();
    }