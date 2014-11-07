<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
	
	
  function goto_login(){
		header('Location: /');
  }
 
 
 //Clause to get to display the search results
  
  if(!(isset($_SESSION['uid']))){
      goto_login();   
  }else{
      try {
          include_once('search.html');
      }
      catch (PDOException $e) {
          $error = 'Error getting to Check in Page';
          include_once($_SERVER['DOCUMENT_ROOT'].'error.html.php');
          exit(); 
      }
  }
  