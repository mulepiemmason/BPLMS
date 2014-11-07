<?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
    //FUnction to go to Login page
    
    function goto_login(){
        $error = "";
        include_once('login.html');
    }
   
   //Clause to get to in messages page
    
    if(!(isset($_SESSION['uid']))){
        goto_login();   
    }else{
        try {
            header('Location: /checkin/');
        }
        catch (PDOException $e) {
            $error = 'Error getting to Check in Page';
            include('error.html.php');
            exit(); 
        }
    }
    