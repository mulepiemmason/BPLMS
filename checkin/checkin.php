<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
	
	if($_POST['username']==''){
	  $errormess = 'Please input the users <strong>Name</strong>';
	  include_once('checkin.html');
	  }
	elseif($_POST['uaddress']==''){
		  $errormess = 'Please input the users <strong>Address</strong>';
		  include_once('checkin.html');
	  }
	elseif($_POST['usex']=='Sex'){
		  $errormess = 'Please input the users <strong>Sex</strong>';
		  include_once('checkin.html');
	  }
	elseif($_POST['upurpose']==''){
		  $errormess = 'Please input the users <strong>Purpose of visiting the library</strong>';
		  include_once('checkin.html');
	  }
	else{
		  	try{
				$sql = 'INSERT INTO daily SET 
								username = :username,
								address = :address,
								sex = :sex,
								purpose = :purpose,
								date = NOW(),
								checkout = :checkout';
				$s = $dbc->prepare($sql);
				$s->bindValue(':username', $_POST['username']);
				$s->bindValue(':address', $_POST['uaddress']);
				$s->bindValue(':sex', $_POST['usex']);
				$s->bindValue(':checkout', '0000-00-00 00:00:00');
				$s->bindValue(':purpose', $_POST['upurpose']);
				$s->execute();
				
			}
			catch(PDOException $e){
				$error = 'Sorry could not check in the user';
				include_once($_SERVER['DOCUMENT_ROOT'].'error.html.php');
		    exit();
			}
	  }
	header('Location: .');
	exit();