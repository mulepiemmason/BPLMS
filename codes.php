<?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/helper/helpers.php');
    //FUnction to go to Login page
    
    function goto_login(){
        $error = "";
        header('Location: login.html');
    }
   
   //Clause to get to in messages page
    
    if(!(isset($_SESSION['uid']))){
        goto_login();   
    }else{
        try {
            $sql = 'SELECT readstat, attached, date,messageid,title,body,position, faculty, surname FROM messages inner join users on sourceid = userid inner join outid on messid = messageid where uid = '.$_SESSION['uid'].' ORDER BY date DESC';
            $message = $dbc->query($sql);
        }
        catch (PDOException $e) {
            $error = 'Error retrieving messages from database';
            include('error.html.php');
            exit(); 
        }
        while ($row = $message->fetch()) {
            $messages[] = array('messid' => $row['messageid'],
                                'messtitle' => $row['title'],
                                'user' => $row['position']." ".$row['faculty'],
                                'readstatus' => $row['readstat'],
                                'attached' => $row['attached'],
                                'messtext' => $row['body'],
                                'messdate' => $row['date']);
        }
        $user = $_SESSION['username'];
        include('in.html.php');
    }
    
    
    //This is a clause to open the compose page.
    
    if (isset($_GET['addmess'])){
        if(!(isset($_SESSION['uid']))){   
        }else{
            try {
                $sql = 'SELECT userid, firstname, surname, username, position, faculty FROM users ORDER BY position ASC';
                $allusers = $dbc->query($sql);
            }
            catch (PDOException $e) {
                $error = 'Error retrieving messages from database';
                include('error.html.php');
                exit(); 
            }
            while ($row = $allusers->fetch()) {
                $all[] = array('disp' => $row['position']." ".$row['faculty']." - ".$row['surname'].' '.$row['firstname'],
                                'uid' => $row['userid']);
                
            }
            $user = $_SESSION['username'];
            include 'compose.html.php';
            exit();
        }
        }
        
    if (isset($_GET['help'])){
        if(!(isset($_SESSION['uid']))){   
        }else{
            $user = $_SESSION['username'];
            include 'help.html.php';
            exit();
            }
        }
        
    if (isset($_GET['settings'])){
        if(!(isset($_SESSION['uid']))){   
        }else{
            $user = $_SESSION['username'];
            include 'settings.html.php';
            exit();
            }
        }
    
    // This is an if clause to view a single message
        
    if (isset($_GET['view'])){
        if(!(isset($_SESSION['uid']))){   
        }else{
            $true= 1;
            try {
               $sql = 'UPDATE outid SET readstat = 1 WHERE uid = '.$_SESSION['uid'].' AND messid = \''. $_GET['view'].'\'';
               $s = $dbc->prepare($sql);
               $s->execute();
               }
           catch (PDOException $e){
                $error = 'Error Changing Read Status ';
                include 'settings.html.php';
                exit();
            }
            try {
                $sql = 'SELECT firstname, surname, date, messageid, attachment, title,position,faculty, body, username FROM messages INNER JOIN users on sourceid = userid where messageid =\''. $_GET['view'].'\'';
                $message = $dbc->query($sql);
            }
            catch (PDOException $e) {
                $error = 'Error retrieving messages from database';
                include('error.html.php');
                exit(); 
            }
            while ($row = $message->fetch()) {
                $singlemess = array('disp' => $row['username']." - ".$row['position']." ".$row['faculty'],
                                    'messid' => $row['messageid'],
                                    'messtitle' => $row['title'],
                                    'user' => $row['position']." ".$row['faculty'].' - '.$row['surname']." ".$row['firstname'],
                                    'messtext' => $row['body'],
                                    'attachment' => $row['attachment'],
                                    'messdate' => $row['date']);
            }
            $user = $_SESSION['username'];
            include 'view.html.php';
            exit();
            }  
        }  
        
    if (isset($_GET['out'])){
        if(!(isset($_SESSION['uid']))){   
        }else{
            try {
                $sql = 'SELECT date, title, body, position, faculty, readstat, attached, messageid from messages inner join outid on messid = messageid inner join users on userid = uid where sourceid='.$_SESSION['uid'].' and uid !='.$_SESSION['uid'].' ORDER BY date DESC';
                $message = $dbc->query($sql);
            }
            catch (PDOException $e) {
                $error = 'Error retrieving messages from database';
                include('error.html.php');
                exit(); 
            }
            while ($row = $message->fetch()) {
                $messages[] = array('messid' => $row['messageid'],
                                    'messtitle' => $row['title'],
                                    'user' => $row['position']." ".$row['faculty'],
                                    'messtext' => $row['body'],
                                    'attached' => $row['attached'],
                                    'readstatus' => $row['readstat'],
                                    'messdate' => $row['date']);
            }
            $user = $_SESSION['username'];
            include('out.html.php');
            exit();
            }
        }
    
    //The clause below is incharge of sending the mail
    
    if (isset($_POST['messto']) && isset($_POST['messtitle']) && isset($_POST['messbody'])){
        if(!(isset($_SESSION['uid']))){   
        }else{
            try {
                $now = date("y-m-d h:i:s a", time());
                $smessid = md5(uniqid(mt_rand(), true));
                $uploaddir = './attachments/';
                $uploadfile = $uploaddir.basename($_FILES['messfile']['name']);
                move_uploaded_file($_FILES['messfile']['tmp_name'], $uploadfile);
                
                $sql = 'INSERT INTO messages SET
                    title = :title,
                    body = :mess,
                    messageid = :messid,
                    date = :date,
                    attachment = :attachment,
                    sourceid = :source';
                $s = $dbc->prepare($sql);
                $s->bindValue(':mess', $_POST['messbody']);
                $s->bindValue(':messid', $smessid);
                $s->bindValue(':date', $now);
                $s->bindValue(':attachment', $uploadfile);
                $s->bindValue(':title', $_POST['messtitle']);
                $s->bindValue(':source', $_SESSION['uid']);
                $s->execute();
                
                foreach($_POST['messto'] as $recipient){
                if($uploadfile != './attachments/'){
                        $sql = 'INSERT INTO outid SET
                            uid = :to,
                            messid = :messid,
                            attached = 1'; 
                        $s = $dbc->prepare($sql);
                        $s->bindValue(':to', $recipient);
                        $s->bindValue(':messid', $smessid);
                        $s->execute();
                }else{
                        $sql = 'INSERT INTO outid SET
                            uid = :to,
                            messid = :messid,
                            attached = 0';
                        $s = $dbc->prepare($sql);
                        $s->bindValue(':to', $recipient);
                        $s->bindValue(':messid', $smessid);
                        $s->execute();
                    }
                }
                
                //What follows Below is the code to send the message to the selected individuals emails
                foreach($_POST['messto'] as $recipient){
                    if (($recipient==6)){
                        try {
                            $sql = 'SELECT email FROM students';
                            $message = $dbc->query($sql);
                        }
                        catch (PDOException $e) {
                            $error = 'Error Retrieving E-mails';
                            include('error.html.php');
                            exit(); 
                        }
                        while ($row = $message->fetch()) {
                            if ($uploadfile == './attachments/'){
                                $my_file = '';
                                $my_path = '';    
                            }
                            else{
                                $my_file = $_FILES['messfile']['name'];
                                $my_path = $uploaddir;    
                            }
                            $my_name = $_SESSION['username'];
                            $my_mail = $_SESSION['email'];
                            $my_replyto = $_SESSION['email'];
                            $my_subject = $_POST['messtitle'];
                            $my_message = $_POST['messbody'];
                            $emailstatus = mail_attachment($my_file, $my_path, $row['email'], $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
                        }        
                    }
                    else{
                        try {
                            $sql = 'SELECT email FROM users WHERE userid = '.$recipient;
                            $message = $dbc->query($sql);
                        }
                        catch (PDOException $e) {
                            $error = 'Error Accessing User E-mail';
                            include('error.html.php');
                            exit(); 
                        }
                        while ($row = $message->fetch()) {
                            $toemail = $row['email'];
                        }
                        
                        if ($uploadfile == './attachments/'){
                            $my_file = '';
                            $my_path = '';    
                        }
                        else{
                            $my_file = $_FILES['messfile']['name']; 
                            $my_path = $uploaddir;   
                        }
                        $my_name = $_SESSION['username'];
                        $my_mail = $_SESSION['email'];
                        $my_replyto = $_SESSION['email'];
                        $my_subject = $_POST['messtitle'];
                        $my_message = $_POST['messbody'];
                        $emailstatus = mail_attachment($my_file, $my_path, $toemail, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);    
                    }    
                }
                $user = $_SESSION['username'];
                include('sent.html.php'); 
            }
            catch (PDOException $e){
                $error = 'Error adding submitted message: ';
                include 'error.html.php';
                exit();
        }
        header('Location: .');
        exit();
        }
    }
    
    // The following code block is for changing the user settings
    
    if ((isset($_POST['nuname'])) && (isset($_POST['npass'])) && (isset($_POST['nemail']))){
        if (($_POST['nuname'] == $_POST['rnuname']) && ($_POST['nuname'] != " ")){
            try {
               $sql = 'UPDATE users SET 
                    username = :nuname where userid = '.$_SESSION['uid'];
               $s = $dbc->prepare($sql);
               $s->bindValue(':nuname', $_POST['nuname']);
               $s->execute();
               }
           catch (PDOException $e){
                $error = 'Error Changing Username ';
                include 'settings.html.php';
                exit();
            }
        }
        
        if (($_POST['nemail'] == $_POST['rnemail']) && ($_POST['nemail'] != " ")){
            try {
               $sql = 'UPDATE users SET 
                    email = :nemail where userid = '.$_SESSION['uid'];
               $s = $dbc->prepare($sql);
               $s->bindValue(':nemail', $_POST['nemail']);
               $s->execute();
               }
           catch (PDOException $e){
                $error = 'Error Changing E-mail ';
                include 'error.html.php';
                exit();
            }
        }
        
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
                include 'error.html.php';
                exit();
            }
        }
    header('Location: .');
    exit();
    }  
      