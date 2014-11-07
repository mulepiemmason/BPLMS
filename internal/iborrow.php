<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
	
	if($_POST['username']==''){
	  $errormess = 'Please input the users <strong>Users Name</strong>';
	  include_once('borrow.html');
	  exit();
	  }
	if($_POST['uaddress']==''){
	  $errormess = 'Please input the users <strong>Users Address</strong>';
	  include_once('borrow.html');
	  exit();
	  }
	if($_POST['bkid']==''){
		  $errormess = 'Please input the users <strong>Book ID</strong>';
		  include_once('borrow.html');
		  exit();
	  }
	if($_POST['sex']=='Sex'){
		  $errormess = 'Please input the users <strong>Sex</strong>';
		  include_once('borrow.html');
		  exit();
	  }
	if(isset($_POST['userid'])){
		foreach($inborrowed as $person){
			if($person['borrowerid'] == $_POST['userid']){
				$errormess = 'Sorry the user has already borrowed a book';
				include_once('borrow.html');
				exit();
			}
		}
	}
	if($_POST['bkid']!=''){
		foreach($libbooks as $book){
			if($book['bookid'] == $_POST['bkid'] && $book['qty'] == 0){
				$errormess = 'Sorry the Books are out of stock';
				include_once('borrow.html');
				exit();
			}
		}
	}
	try{
    $borrowid = md5(uniqid(mt_rand(), true));
		$sql = 'INSERT INTO iborrowed SET
						borrowid = :borrowid, 
						username = :username,
						bookid = :bookid,
						address = :address,
						returned = :returned,
						date = NOW(),
						sex = :sex';
		$s = $dbc->prepare($sql);
		$s->bindValue(':borrowid', $borrowid);
		$s->bindValue(':username', $_POST['username']);
		$s->bindValue(':bookid', $_POST['bkid']);
		$s->bindValue(':address', $_POST['uaddress']);
		$s->bindValue(':sex', $_POST['sex']);
		$s->bindValue(':returned', '0');
		$s->execute();
		
		// Update the number of books available in the Library
		$sql = 'UPDATE books SET quantity = quantity-1 WHERE bookid = :bookid';
		$s = $dbc->prepare($sql);
		$s->bindValue(':bookid', $_POST['bkid']);
		$s->execute();
		
	}
	catch(PDOException $e){
		$error = 'Sorry could not check in the user';
		include_once($_SERVER['DOCUMENT_ROOT'].'error.html.php');
    exit();
	} 
	header('Location: .');
	exit();