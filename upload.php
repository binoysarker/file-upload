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


  $userData = ['error'=>[],'data'=>[]];
if ($auth_user->is_loggedin()!="" && isset($_POST['myData'])) {
  // code...
  $uname = strip_tags($_POST['myData']['user_name']);
  $uemail = strip_tags($_POST['myData']['user_email']);
  $unumber = strip_tags($_POST['myData']['user_number']);
  $usubject = strip_tags($_POST['myData']['user_subject']);
  $uinfo = strip_tags($_POST['myData']['user_info']);
  if (isset($_POST['myData']['file_paths'])) {
    $upaths = $_POST['myData']['file_paths'];
  }else{
    array_push($userData['error'],'please select a file !');
  }
  
  $json = $_POST ['myData']['user_name'];
  // var_dump($uname,$uemail,$unumber,$usubject,$uinfo,$upaths);
  // exit();
  if (empty($uname)) {
    array_push($userData['error'],'provide user name !');
  }
  else if (!preg_match("/^[a-zA-Z0-9 ]*$/",$uname)) {
    array_push($userData['error'],"user name should have letters and white space");
  }
  else if (empty($uemail)) {
    array_push($userData['error'],'provide user email !');
  }
  else if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
    array_push($userData['error'],"Invalid email format");
  }
  else if (empty($unumber)) {
    array_push($userData['error'],'provide phone number !');
  }
  else if(!preg_match("/^\([0-9]{3}\)[0-9]{3}-[0-9]{3}$/", $unumber)) {
    array_push($userData['error'],'Invalid phone number !');
  }
  else if (empty($usubject)) {
    array_push($userData['error'],'provide subject !');
  }
  else if (empty($uinfo)) {
    array_push($userData['error'],'provide file information !');
    
  }

  echo json_encode($userData);

  // now insert the json data in the database
  if (isset($upaths)) {
    foreach ($upaths as $path) {
      $auth_user->insertJsonData($uname,$uemail,$unumber,$usubject,$uinfo,$path);
    }
  }
}
else {
  // upload files first
  // Count # of uploaded files in array
  if (isset($_FILES['file_upload'])) {

    $total = count($_FILES['file_upload']['name']);
    $_SESSION['file_number'] = $total;

    if (empty($_FILES['file_upload']['name'])) {
      array_push($userData['error'],'please select file !');
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
            $auth_user->uploadFile($newFilePath);
          

          }
        }
      }
    // now get the uploaded data
      $stmt = $auth_user->runQuery("SELECT * FROM uploads ORDER BY id DESC LIMIT ".$total);
      $stmt->execute();

      $userData['data']=$stmt->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($userData);
    }
  }

}

?>
