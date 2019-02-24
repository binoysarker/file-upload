<?php

require_once("session.php");

require_once("class.user.php");
$auth_user = new USER();


$user_id = $_SESSION['user_session'];

$stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
$stmt->execute(array(":user_id"=>$user_id));

$userRow=$stmt->fetch(PDO::FETCH_ASSOC);



/**
 * working with the upload data
 */


if ($auth_user->is_loggedin()!="" && isset($_POST['myData'])) {
  // code...
  $uname = strip_tags($_POST['myData']['user_name']);
  $uemail = strip_tags($_POST['myData']['user_email']);
  $unumber = strip_tags($_POST['myData']['user_number']);
  $usubject = strip_tags($_POST['myData']['user_subject']);
  $uinfo = strip_tags($_POST['myData']['user_info']);
  $upaths = $_POST['myData']['file_paths'];
  
  $json = $_POST ['myData']['user_name'];
  var_dump($uname,$uemail,$unumber,$usubject,$uinfo,$upaths);
  exit();
  if (empty($uname)) {
    $error[] = 'provide user name !';
  }
  else if (!preg_match("/^[a-zA-Z0-9 ]*$/",$uname)) {
    $error[] = "user name should have letters and white space";
  }
  else if (empty($uemail)) {
    $error[] = 'provide user email !';
  }
  else if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
    $error[] = "Invalid email format";
  }
  else if (empty($unumber)) {
    $error[] = 'provide phone number !';
  }
  else if(!preg_match("/^\([0-9]{3}\)[0-9]{3}-[0-9]{3}$/", $unumber)) {
    $error[] = 'Invalid phone number !';
  }
  else if (empty($usubject)) {
    $error[] = 'provide subject !';
  }
  else if (empty($uinfo)) {
    $error[] = 'provide file information !';
  }
}
else {
  // upload files first
  // Count # of uploaded files in array
  if (isset($_FILES['file_upload'])) {

    $total = count($_FILES['file_upload']['name']);
    $_SESSION['file_number'] = $total;

    if (empty($_FILES['file_upload']['name'])) {
      $error[] = 'please select file !';
    }

    else{
    // echo json_encode($_FILES['file_upload']);
    // exit();
      for( $i=0 ; $i < $total ; $i++ ) {

        $tmpFilePath = $_FILES['file_upload']['tmp_name'][$i];

        if ($tmpFilePath != ""){
          $newFilePath = "./uploadFiles/" . $_FILES['file_upload']['name'][$i];

          if(move_uploaded_file($tmpFilePath, $newFilePath)) {
              // var_dump($newFilePath);
              //Handle other code here
              // store in the databse
            $result = $auth_user->uploadFile($newFilePath);
          // var_dump($result);

          }
        }
      }
    // now get the uploaded data
      $stmt = $auth_user->runQuery("SELECT * FROM uploads ORDER BY id DESC LIMIT ".$total);
      $stmt->execute();

      $userRow=$stmt->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($userRow);
    }
  }
}

?>
