<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
	
	if($_POST['userid']==''){
	  $errormess = 'Please input the users <strong>User ID</strong>';
	  include_once('borrow.html');
	  exit();
	  }
	if($_POST['bkid']==''){
		  $errormess = 'Please input the users <strong>Book ID</strong>';
		  include_once('borrow.html');
		  exit();
	  }
	if($_POST['duration']==''){
		  $errormess = 'Please input the users <strong>Borrowing Duration in days</strong>';
		  include_once('borrow.html');
		  exit();
	  }
	
	if($_POST['userid']!=''){
		foreach($allborrowed as $person){
			if($person['borrowerid'] == $_POST['userid'] && $person['returned'] == 0){
				$errormess = 'Sorry the user has already borrowed a book';
				include_once('borrow.html');
				exit();
			}
		}
	}
	
	if($_POST['userid']!=''){
		foreach($allborrowed as $person){
			if($person['borrowerid'] == $_POST['userid'] && $person['returned'] == 0){
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
		$sql = 'INSERT INTO borrowed SET
						borrowid = :borrowid, 
						borrowerid = :borrowerid,
						bookid = :bookid,
						duedate = :duedate,
						returned = :returned,
						date = CURRENT_DATE()';
		$s = $dbc->prepare($sql);
		$s->bindValue(':borrowid', $borrowid);
		$s->bindValue(':borrowerid', $_POST['userid']);
		$s->bindValue(':bookid', $_POST['bkid']);
		$s->bindValue(':duedate', date("y-m-d", time()+($_POST['duration']*86400)));
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