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
if ($auth_user->is_loggedin()!="" && isset($_POST['btn-upload'])) {
  $uname = strip_tags($_POST['user_name']);
  $uemail = strip_tags($_POST['user_email']);
  $unumber = strip_tags($_POST['user_number']);
  $usubject = strip_tags($_POST['user_subject']);
  $uinfo = strip_tags($_POST['user_info']);
  // Count # of uploaded files in array
  $total = count($_FILES['file_upload']['name']);
  // var_dump($uname,$uemail,$unumber,$usubject,$uinfo);

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
  else if (empty($_FILES['file_upload']['name'])) {
    $error[] = 'please select file !';
  }

  else{
    for( $i=0 ; $i < $total ; $i++ ) {

      $tmpFilePath = $_FILES['file_upload']['tmp_name'][$i];

      if ($tmpFilePath != ""){
        $newFilePath = "./uploadFiles/" . $_FILES['file_upload']['name'][$i];

        if(move_uploaded_file($tmpFilePath, $newFilePath)) {
              // var_dump($newFilePath);
              //Handle other code here
              // store in the databse
          $result = $auth_user->uploadFile($uname,$uemail,$unumber,$usubject,$uinfo,$newFilePath);
          var_dump($result);
        }
      }
    }
    $auth_user->redirect('video-gallery.php');
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
  <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
  <script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
  <link rel="stylesheet" href="style.css" type="text/css"  />
  <title>welcome - <?php print($userRow['user_email']); ?></title>
</head>

<body>

  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="http://www.codingcage.com">Coding Cage</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li class="active"><a href="http://www.codingcage.com/2015/04/php-login-and-registration-script-with.html">Back to Article</a></li>
          <li><a href="http://www.codingcage.com/search/label/jQuery">jQuery</a></li>
          <li><a href="http://www.codingcage.com/search/label/PHP">PHP</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">

          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
             <span class="glyphicon glyphicon-user"></span>&nbsp;Hi' <?php echo $userRow['user_email']; ?>&nbsp;<span class="caret"></span></a>
             <ul class="dropdown-menu">
              <li><a href="profile.php"><span class="glyphicon glyphicon-user"></span>&nbsp;View Profile</a></li>
              <li><a href="logout.php?logout=true"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Sign Out</a></li>
            </ul>
          </li>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </nav>


  <div class="clearfix"></div>


  <div class="container-fluid" style="margin-top:80px;">

    <div class="container">

    	<label class="h5">welcome : <?php print($userRow['user_name']); ?></label>
      <hr />

      <h1>
        <a href="home.php"><span class="glyphicon glyphicon-home"></span> home</a> &nbsp;
        <a href="compose.php"><span class="glyphicon glyphicon-home"></span> compose</a> &nbsp;
        <a href="video-gallery.php"><span class="glyphicon glyphicon-home"></span> Gallery</a> &nbsp;
        <a href="profile.php"><span class="glyphicon glyphicon-user"></span> profile</a></h1>
        <hr />

        <p class="h4">User Home Page</p>
        <!-- upload form section -->
        <div class="upload-form">
          <form class="form-upload" method="post" id="upload-form" action="" enctype="multipart/form-data">

            <h2 class="form-upload-heading">Upload Files.</h2><hr />

            <div id="error">
               
             </div>

             <div class="form-group">
              <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Username" required />
              <span id="check-uname"></span>
            </div>
            <div class="form-group">
              <input type="email" class="form-control" name="user_email" id="user_email" placeholder="E mail" required />
              <span id="check-email"></span>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="user_number" id="user_number" placeholder="Phone (727)688-5945" required />
              <span id="check-e"></span>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="user_subject" id="user_subject" placeholder="Subject" required />
              <span id="check-subject"></span>
            </div>
            <div class="form-group">
              <textarea name="user_info" id="user_info" placeholder="Information" class="form-control" rows="10"></textarea>
            </div>

            <div class="form-group">
              <input type="file" name="file_upload[]" id="file_upload" multiple value="" placeholder="Upload" >
            </div>
            <div class="form-group" id="show_files">

            </div>


            <div class="form-group">
              <button type="submit" id="btn-upload" name="btn-upload" class="btn btn-primary">
                <i class="glyphicon glyphicon-log-in"></i> &nbsp; Upload
              </button>
            </div>
          </form>
        </div>


        <p class="blockquote-reverse" style="margin-top:200px;">
          Programming Blog Featuring Tutorials on PHP, MySQL, Ajax, jQuery, Web Design and More...<br /><br />
          <a href="http://www.codingcage.com/2015/04/php-login-and-registration-script-with.html">tutorial link</a>
        </p>

      </div>


    </div>

    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $('#error').hide();
        var file_paths = [];
        // form submit section
        $('#upload-form').on('submit',function(e){
          e.preventDefault();
          var user_name = $('#user_name').val();
          var user_email = $('#user_email').val();
          var user_number = $('#user_number').val();
          var user_subject = $('#user_subject').val();
          var user_info = $('#user_info').val();
          var myData = {'user_name':user_name,'user_email':user_email,'user_number':user_number,'user_subject':user_subject,'user_info':user_info,'file_paths':file_paths};
         
          $.post('upload.php',{'myData':myData},function(data){
            var jsonData = JSON.parse(data);
            // console.log(jsonData.error);
            if (jsonData.error.length > 0) {
              $('#error').show();
              for (var i = 0; i < jsonData.error.length; i++) {
                $('#error').append('<div class="alert alert-danger"><i class="glyphicon glyphicon-warning-sign"></i> &nbsp;'+jsonData.error[i]+'  !</div>');
              }
            }
            
          })
          
        });

        // file upload section
        $('#file_upload').change(function (e) {
          e.preventDefault();
          var formData2 = new FormData($(this).parents('form')[0]);
          $.ajax({
            url: 'upload.php',
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) {
                // alert("Data Uploaded: "+data);
                // console.log(typeof(JSON.parse(data)));
                var jsonData = JSON.parse(data);
                for (var key in jsonData) {
                  $('#show_files').append('<h6>'+jsonData[key].upload_path.replace('./uploadFiles/', '')+'</h6>');
                  file_paths.push(jsonData[key].upload_path);
                }

            },
            data: formData2,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
          // console.log($(this).parents('form')[0][5]);
        })
      });
    </script>

  </body>
  </html>
