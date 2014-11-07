<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
	
	// This is the clause to search through the books borrowed today for a specific book
	
	$pattern = '/'.$_POST['bksearch'].'/i';
	
	foreach($dailyborrowed as $book){
		if(preg_match($pattern, $book['bookid']) || preg_match($pattern, $book['title'])){
			$totaldaily += 1;
		}
	}
	
	foreach($lateborrowed as $book){
		if(preg_match($pattern, $book['bookid']) || preg_match($pattern, $book['title'])){
			$totallate += 1;
			$lateresult[] = $book;
		}
	}
	
	foreach($allborrowed as $book){
		if(preg_match($pattern, $book['bookid']) || preg_match($pattern, $book['title'])){
			$totalborrowed += 1;
			$borrowedresult[] = $book;
		}
	}
	
	foreach($alllibbooks as $book){
		if(preg_match($pattern, $book['bookid']) || preg_match($pattern, $book['title'])){
			$totalall += 1;
			$bookresult = $book;
		}
	}
	
	include_once('search.html');