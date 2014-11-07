<?php
	session_start();
    date_default_timezone_set('Africa/Nairobi');
    $now = date("y-m-d", time());
    $now_full = date("y-m-d h:i:s a", time());
    if (get_magic_quotes_gpc()){
        $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
        while (list($key, $val) = each($process)){
            foreach ($val as $k => $v){
                unset($process[$key][$k]);
                if (is_array($v)){
                    $process[$key][stripslashes($k)] = $v;
                    $process[] = &$process[$key][stripslashes($k)];
                    }
                else{
                    $process[$key][stripslashes($k)] = stripslashes($v);
                    }
                } 
            }
        unset($process);
    }
    
    //Below is the function to send an email with or without attachments
    
    function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
        if ($filename != '' && $path != ''){
            $file = $path.$filename;
            $file_size = filesize($file);
            $handle = fopen($file, "r");
            $content = fread($handle, $file_size);
            fclose($handle);
            $content = chunk_split(base64_encode($content));
            $uid = md5(uniqid(time()));
            $name = basename($file);
            $header = "From: ".$from_name." <".$from_mail.">\r\n";
            $header .= "Reply-To: ".$replyto."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
            $header .= "This is a multi-part message in MIME format.\r\n";
            $header .= "--".$uid."\r\n";
            $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
            $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $header .= $message."\r\n\r\n";
            $header .= "--".$uid."\r\n";
            $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
            $header .= "Content-Transfer-Encoding: base64\r\n";
            $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
            $header .= $content."\r\n\r\n";
            $header .= "--".$uid."--";
            if (mail($mailto, $subject, "", $header)) {
                return 'E-mail Sent successfully'; // or use booleans here
            } else {
                return 'E-mail not Sent';
            }
        }else{
            $header = "From: ".$from_name." <".$from_mail.">\r\n";
            $header .= "Reply-To: ".$replyto."\r\n";
            if (mail($mailto, $subject, "", $header)) {
                return 'E-mail Sent successfully'; // or use booleans here
            } else {
                return 'E-mail not Sent';
            }
        }
    }
    
    //This is the creation of the connection to the database
    
    try {
        $dbc = new PDO('mysql:host=localhost;dbname=bplms', 'root', 'mulepiemmason');
        $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbc->exec('SET NAMES "utf8"');
    }
    catch (PDOException $e){
        $error = 'Error connecting to database.';
        include 'error.html.php';
        exit();
    }
    
    //This is the clause to login into the system
    
    if ((isset($_POST['username'])) && (isset($_POST['pass']))){
        try {
            $sql = 'SELECT userid, email, firstname, surname, username, password FROM users where username =\''.$_POST['username'].'\'';
            $users = $dbc->query($sql);
        }
        catch (PDOException $e) {
            $error = 'Error Logging in. Check your Username or Password'.$e;
            include_once($_SERVER['DOCUMENT_ROOT'].'/login.html');
            exit(); 
        }
        while ($row = $users->fetch()) {
            $userdetails = array('username' => $row['username'],
                                'userid' => $row['userid'],
                                'password' => $row['password'],
                                'fname' => $row['firstname'],
                                'email' => $row['email'],
                                'sname' => $row['surname']);
        }
        if ($userdetails['password'] == md5($_POST['pass'])){
            $_SESSION['uid'] = $userdetails['userid'];
            $_SESSION['user'] = $userdetails['username'];
            $_SESSION['pass'] = $userdetails['password'];
            $_SESSION['email'] = $userdetails['email'];
            $_SESSION['username'] = $userdetails['sname']." ".$userdetails['fname'];
            $user = $_SESSION['username'];
            header('Location: /');
            exit();
        }
        else{
            $error = 'Error Logging in. Check your Username or Password';
            include_once($_SERVER['DOCUMENT_ROOT'].'/login.html');
            exit(); 
        }
    }
    
    //Below is the clause to logout of the system
    
    if (isset($_GET['logout'])){
    		$_SESSION = array();
        session_destroy();
        $error = 'You have Logged Out';
        header('Location: /');
        exit();
        }
        
    //Below is the clause to display the memers currently in the library
    
    try {
        $sql = 'select * from daily order by date desc';
        $users = $dbc->query($sql);
    }
    catch (PDOException $e) {
        $error = 'Error Collecting Members in Library'.$e;
        include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
        exit(); 
    }
    while ($row = $users->fetch()) {
        $dailyin[] = array('username' => $row['username'],
                            'date' => $row['date'],
                            'address' => $row['address'],
                            'sex' => $row['sex'],
                            'purpose' => $row['purpose'],
                            'checkout' => $row['checkout']);
    }
    
    foreach($dailyin as $person){
	    if($person['checkout'] == '0000-00-00 00:00:00'){
		    $stillin[]= array('username' => $person['username'],
                            'date' => $person['date'],
                            'address' => $person['address'],
                            'sex' => $person['sex'],
                            'purpose' => $person['purpose'],
                            'checkout' => $person['checkout']);
	    }
    }
    
    //Below is the clause to check a user out of the library
    
    if (isset($_GET['checkout'])){
			try{
	  		$sql = 'UPDATE daily SET checkout = NOW() where date = \''.$_GET['checkout'].'\'';
	  		 $s = $dbc->prepare($sql);
         $s->execute();
			}
			catch(PDOException $e){
				$error = 'Sorry we could not checkout the user';
				include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
				exit();
	    }
	    header('Location: .');
			exit();
		}
		
		//Below is the clause to return a book to the library
    
    if (isset($_GET['return'])){
	    	try{
	    		// Clause to indicate that the book is returned
	    		if($_GET['page']=='internal'){
			  		$sql = 'UPDATE iborrowed SET returned = 1 where borrowid = \''.$_GET['borrowid'].'\'';
			  		$s = $dbc->prepare($sql);
		        $s->execute();
		      }
		      if($_GET['page']=='borrow' || $_GET['page']=='borrowed' || $_GET['page']=='late'){
		      	$sql = 'UPDATE borrowed SET returned = 1 where borrowid = \''.$_GET['borrowid'].'\'';
						$s = $dbc->prepare($sql);
						$s->execute();
					}
					if($_GET['page']=='search'){
		      	$sql = 'UPDATE borrowed SET returned = 1 where borrowid = \''.$_GET['borrowid'].'\'';
						$s = $dbc->prepare($sql);
						$s->execute();
						header('Location: /borrowed/');
						exit();
					}
	        
	        // Clause to increase the number of books available in the library 
	        $sql = 'UPDATE books SET quantity = quantity + 1 where bookid = \''.$_GET['bookid'].'\'';
		  		$s = $dbc->prepare($sql);
	        $s->execute();
				}
				catch(PDOException $e){
					$error = 'Sorry we could not return the book to the library';
					include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
					exit();
		    }
			
	    header('Location: .');
			exit();
		}
		
		//Below is the clause to get the number of items in each section
		
			//clause to get number of users available in the library at the moment
			try {
        $sql = 'select count(*) from daily where `date` like \'%'.$now.'%\' and checkout = \'0000-00-00 00:00:00\'';
        $users = $dbc->query($sql);
			}
			catch (PDOException $e) {
        $error = 'Error Collecting Number of Members in Library'.$e;
        include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
        exit(); 
			}
			while ($row = $users->fetch()) {
        $innow = $row['count(*)'];
			}
			
			//clause to get number of books borrowed today
			try {
        $sql = 'select count(*) from borrowed where `date` like \'%'.$now.'%\'';
        $users = $dbc->query($sql);
			}
			catch (PDOException $e) {
        $error = 'Error Collecting Number of Members in Library'.$e;
        include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
        exit(); 
			}
			while ($row = $users->fetch()) {
        $nowborrowed = $row['count(*)'];
			}
			
			//clause to get number of books currently borrowed
			try {
        $sql = 'select count(*) from borrowed';
        $users = $dbc->query($sql);
			}
			catch (PDOException $e) {
        $error = 'Error Collecting Number of Members in Library'.$e;
        include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
        exit(); 
			}
			while ($row = $users->fetch()) {
        $curborrowed = $row['count(*)'];
			}
			
			//clause to get number of books borrowed internally today
			try {
        $sql = 'select count(*) from iborrowed where `date` like \'%'.$now.'%\'';
        $users = $dbc->query($sql);
			}
			catch (PDOException $e) {
        $error = 'Error Collecting Number of Members in Library'.$e;
        include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
        exit(); 
			}
			while ($row = $users->fetch()) {
        $nowiborrowed = $row['count(*)'];
			}
			
			//clause to get number of late books 
			try {
        $sql = 'select count(*) from borrowed where `borrowed`.`duedate` < \''.$now.'\' and returned = 0';
        $users = $dbc->query($sql);
			}
			catch (PDOException $e) {
        $error = 'Error Collecting Number of Members in Library'.$e;
        include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
        exit(); 
			}
			while ($row = $users->fetch()) {
        $latebooks = $row['count(*)'];
			}
			
			//clause to get number of books in the library
			try {
        $sql = 'select bookid, quantity from books';
        $books = $dbc->query($sql);
			}
			catch (PDOException $e) {
        $error = 'Error Collecting Book details';
        include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
        exit(); 
			}
			while ($row = $books->fetch()) {
        $libbooks[] = array('bookid' => $row['bookid'],
        										'qty' => $row['quantity']);
			}
			
	//Below is the clause to get the currently internally borrowed books
    
  try {
      $sql = 'select borrowid, date, username, `iborrowed`.`bookid`, address, sex, returned, title from iborrowed inner join books on `books`.`bookid`=`iborrowed`.`bookid` order by date desc';
      $users = $dbc->query($sql);
  }
  catch (PDOException $e) {
      $error = 'Error Collecting Borrowed Books';
      include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
      exit(); 
  }
  while ($row = $users->fetch()) {
      $iborrowed[] = array('borrowid' => $row['borrowid'],
                          'date' => $row['date'],
                          'bookid' => $row['bookid'],
                          'address' => $row['address'],
                          'sex' => $row['sex'],
                          'returned' => $row['returned'],
                          'username' => $row['username'],
                          'title' => $row['title']);
      if($row['date'] != $now){
	      $lateiborrowed[] = $row;
      }
  }
	
  //Below is the clause to get the currently borrowed books
    
  try {
      $sql = 'select borrowid, `borrowed`.`date`, `borrowed`.`borrowerid`,  `borrowed`.`bookid`, firstname, surname, duedate, title, returned from borrowed left join books on `books`.`bookid` = `borrowed`.`bookid` left join members on `members`.`userid` = `borrowed`.`borrowerid` where `borrowed`.`date` = \''.$now.'\' ORDER BY date DESC';
      $users = $dbc->query($sql);
  }
  catch (PDOException $e) {
      $error = 'Error Collecting Borrowed Books';
      include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
      exit(); 
  }
  while ($row = $users->fetch()) {
      $dailyborrowed[] = array('borrowid' => $row['borrowid'],
                          'date' => $row['date'],
                          'borrowerid' => $row['borrowerid'],
                          'bookid' => $row['bookid'],
                          'duedate' => $row['duedate'],
                          'returned' => $row['returned'],
                          'borrower' => $row['firstname'].' '.$row['surname'],
                          'title' => $row['title']);
  }
  
  //Below is the clause to get the all time borrowed books
    
  try {
      $sql = 'select borrowid, `borrowed`.`date`, `borrowed`.`borrowerid`,  `borrowed`.`bookid`, firstname, surname, duedate, title, returned from borrowed left join books on `books`.`bookid` = `borrowed`.`bookid` left join members on `members`.`userid` = `borrowed`.`borrowerid` ORDER BY date DESC';
      $users = $dbc->query($sql);
  }
  catch (PDOException $e) {
      $error = 'Error Collecting Borrowed Books';
      include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
      exit(); 
  }
  while ($row = $users->fetch()) {
      $allborrowed[] = array('borrowid' => $row['borrowid'],
                          'date' => $row['date'],
                          'borrowerid' => $row['borrowerid'],
                          'bookid' => $row['bookid'],
                          'duedate' => $row['duedate'],
                          'returned' => $row['returned'],
                          'borrower' => $row['firstname'].' '.$row['surname'],
                          'title' => $row['title']);
  }
  //Below is the clause to get the Late borrowed books
    
  try {
      $sql = 'select borrowid, `borrowed`.`date`, `borrowed`.`borrowerid`,  `borrowed`.`bookid`, firstname, surname, duedate, title, returned from borrowed left join books on `books`.`bookid` = `borrowed`.`bookid` left join members on `members`.`userid` = `borrowed`.`borrowerid` where `borrowed`.`duedate` < \''.$now.'\' and returned = 0 ORDER BY date DESC';
      $users = $dbc->query($sql);
  }
  catch (PDOException $e) {
      $error = 'Error Collecting Borrowed Books';
      include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
      exit(); 
  }
  while ($row = $users->fetch()) {
      $lateborrowed[] = array('borrowid' => $row['borrowid'],
                          'date' => $row['date'],
                          'borrowerid' => $row['borrowerid'],
                          'bookid' => $row['bookid'],
                          'duedate' => $row['duedate'],
                          'returned' => $row['returned'],
                          'borrower' => $row['firstname'].' '.$row['surname'],
                          'title' => $row['title']);
  }
  
  //Below is the clause to get the Library members
    
  try {
      $sql = 'select userid, regdate, address,  sex, firstname, surname, age, expiry, amount from members ORDER BY regdate DESC';
      $users = $dbc->query($sql);
  }
  catch (PDOException $e) {
      $error = 'Error Collecting Borrowed Books';
      include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
      exit(); 
  }
  while ($row = $users->fetch()) {
      $libmembers[] = array('userid' => $row['userid'],
                          'regdate' => $row['regdate'],
                          'address' => $row['address'],
                          'sex' => $row['sex'],
                          'firstname' => $row['firstname'],
                          'surname' => $row['surname'],
                          'age' => $row['age'],
                          'expiry' => $row['expiry'],
                          'amount' => $row['amount']);
  }
  
  //Below is the clause to get the all the books available in the Library
    
  try {
      $sql = 'select bookid, regdate, title, category, author, quantity from books ORDER BY regdate DESC';
      $users = $dbc->query($sql);
  }
  catch (PDOException $e) {
      $error = 'Error Collecting Borrowed Books';
      include_once($_SERVER['DOCUMENT_ROOT'].'/error.html');
      exit(); 
  }
  while ($row = $users->fetch()) {
      $alllibbooks[] = array('bookid' => $row['bookid'],
                          'regdate' => $row['regdate'],
                          'title' => $row['title'],
                          'category' => $row['category'],
                          'author' => $row['author'],
                          'quantity' => $row['quantity']);
  }
  