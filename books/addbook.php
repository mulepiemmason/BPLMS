<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
	
	if($_POST['bkid']==''){
	  $errormess = 'Please input the <strong>Book ID</strong>';
	  include_once('members.html');
	  exit();
	  }
	if($_POST['bktitle']==''){
		  $errormess = 'Please input the <strong>Title</strong> of the book';
		  include_once('members.html');
		  exit();
	  }
	if($_POST['category']=='Category'){
		  $errormess = 'Please input the <strong>Category</strong> of the book';
		  include_once('members.html');
		  exit();
	  }
	if($_POST['bkauthor']==''){
		  $errormess = 'Please input the <strong>Author</strong> of the book';
		  include_once('members.html');
		  exit();
	  }
	if($_POST['bkqty']==''){
		  $errormess = 'Please input the <strong>Number of books available</strong>';
		  include_once('members.html');
		  exit();
	  }
	  
	//Below is the Clause to check whether the User exists in the System
	foreach($alllibbooks as $book){
		if($person['bookid'] == $_POST['bkid'] && $person['title'] == $_POST['bktitle'] && $person['category'] == $_POST['category'] && $person['author'] == $_POST['bkauthor']){
			$_POST['bkqty']= $person['quantity'] + 1;
		}
	}
	
	try{
		$sql = 'INSERT INTO books SET
						bookid = :bookid, 
						title = :title,
						category = :category,
						author = :author,
						quantity = :quantity,
						regdate = CURRENT_DATE()';
		$s = $dbc->prepare($sql);
		$s->bindValue(':bookid', $_POST['bkid']);
		$s->bindValue(':title', $_POST['bktitle']);
		$s->bindValue(':category', $_POST['category']);
		$s->bindValue(':author', $_POST['bkauthor']);
		$s->bindValue(':quantity', $_POST['bkqty']);
		$s->execute();
		
	}
	catch(PDOException $e){
		$error = 'Sorry could not add the Book into the system';
		include_once($_SERVER['DOCUMENT_ROOT'].'error.html.php');
    exit();
	} 
	header('Location: .');
	exit();