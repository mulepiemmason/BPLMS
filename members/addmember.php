<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
	
	if($_POST['fname']==''){
	  $errormess = 'Please input the users <strong>First Name</strong>';
	  include_once('members.html');
	  exit();
	  }
	if($_POST['sname']==''){
		  $errormess = 'Please input the users <strong>Surname</strong>';
		  include_once('members.html');
		  exit();
	  }
	if($_POST['sex']=='Sex'){
		  $errormess = 'Please input the users <strong>Sex</strong>';
		  include_once('members.html');
		  exit();
	  }
	if($_POST['age']==''){
		  $errormess = 'Please input the users <strong>Age</strong>';
		  include_once('members.html');
		  exit();
	  }
	if($_POST['address']==''){
		  $errormess = 'Please input the users <strong>Address</strong>';
		  include_once('members.html');
		  exit();
	  }
	if($_POST['amount']==''){
		  $errormess = 'Please input the <strong>Amount of money </strong>the user has paid';
		  include_once('members.html');
		  exit();
	  }
	  
	//Below is the Clause to check whether the User exists in the System
	foreach($libmembers as $person){
		if($person['firstname'] == $_POST['fname'] && $person['surname'] == $_POST['sname'] && $person['sex'] == $_POST['sex'] && $person['age'] == $_POST['age'] && $person['address'] == $_POST['address'] && $person['expiry'] > $now){
			$errormess = 'Sorry the user is already registered';
			include_once('members.html');
			exit();
		}
	}
	
	try{
		$sql = 'INSERT INTO members SET
						userid = NULL, 
						address = :address,
						sex = :sex,
						expiry = :expiry,
						firstname = :firstname,
						surname = :surname,
						age = :age,
						amount = :amount,
						regdate = CURRENT_DATE()';
		$s = $dbc->prepare($sql);
		$s->bindValue(':address', $_POST['address']);
		$s->bindValue(':sex', $_POST['sex']);
		$s->bindValue(':firstname', $_POST['fname']);
		$s->bindValue(':surname', $_POST['sname']);
		$s->bindValue(':age', $_POST['age']);
		$s->bindValue(':amount', $_POST['amount']);
		$s->bindValue(':expiry', date("y-m-d", time()+(($_POST['amount']/2000)*31536000)));
		$s->execute();
		
	}
	catch(PDOException $e){
		$error = 'Sorry could not add the user into the system';
		include_once($_SERVER['DOCUMENT_ROOT'].'error.html.php');
    exit();
	} 
	header('Location: .');
	exit();