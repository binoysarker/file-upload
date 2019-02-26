<?php 
require_once("session.php");

require_once("class.user.php");
$auth_user = new USER();


$user_id = $_SESSION['user_session'];

$stmt1 = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
$stmt1->execute(array(":user_id"=>$user_id));

$userRow=$stmt1->fetch(PDO::FETCH_ASSOC);

$stmt2 = $auth_user->runQuery("SELECT * FROM upload_info");
$stmt2->execute();

$upload_data=$stmt2->fetchAll(PDO::FETCH_ASSOC);
// var_dump($upload_data);
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
        <a href="profile.php"><span class="glyphicon glyphicon-user"></span> profile</a></h1>
        <hr />
        
        <p class="h4">User Gallery Page</p>
        <form class="navbar-form navbar-left" role="search" id="search-form">
          <div class="form-group">
            <input type="text" id="search-input" class="form-control" placeholder="Search">
          </div>
          <button type="submit" class="btn btn-default" id="search-button">Submit</button>
        </form>
        <div class="clearfix"></div>
        <!-- show documents section -->
        <div class="row" id="gallery_content">
          <?php 
           
          ?>
          <?php foreach ($upload_data as $data): ?>
            <?php foreach (json_decode($data['file_path']) as $file): ?>
              <?php if (file_exists($file)): ?>
                <?php $file_extension = pathinfo($file)['extension']; ?>
                <?php
                $image_file_extensions = ['jpg','png','heic','psd','cam','jpg-large','igo','pic','jpeg','tiff','gif','jp2','bmp','webp','cal','png-large','ecw','cpt','raw','tif','ico','dds','ulg','ige','emf','jpf','bip','vpe','pano','c4','als','g3p','dng','bm2','jpe','mjpg','jpge','pip','icon','xcf','bgb','jif','rgb','bpt','bm','fpg','mac','pjpeg','pjpg','jpd','bmz','jpg3','pig','bitmap'];
                $document_file_extensions = ['docx','pdf','doc','pub','edoc','_pdf','ete'];
                $video_file_extensions = ['mp4','wve','avi','mkv','flv','wmv','mpg','3gp','ogv','mpeg'];

                 ?>
                
                <?php if (in_array($file_extension, $image_file_extensions)): ?>
                  <div class="col-xs-6 col-md-3">
                    <img src="<?php echo $file ?>" alt="" width="300" height="300"> 
                  </div>
                <?php endif ?>
                <?php if (in_array($file_extension, $document_file_extensions)): ?>
                  <div class="col-xs-6 col-md-3">
                    <iframe src="<?php echo $file ?>" width="300" height="300"></iframe> 
                  </div>
                <?php endif ?>
                <?php if (in_array($file_extension, $video_file_extensions)): ?>
                <div class="col-xs-6 col-md-3">
                <video src="<?php echo $file ?>"  controls width="300" height="300"></video>
              </div>
                  
                <?php endif ?>
              <?php endif ?>
              
            <?php endforeach ?>
            
          <?php endforeach ?>
          ...
        </div>


        <p class="blockquote-reverse" style="margin-top:200px;">
          Programming Blog Featuring Tutorials on PHP, MySQL, Ajax, jQuery, Web Design and More...<br /><br />
          <a href="http://www.codingcage.com/2015/04/php-login-and-registration-script-with.html">tutorial link</a>
        </p>

      </div>


    </div>
    <script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        
        $('#search-form').on('submit',function (e) {
          e.preventDefault();
          var inputValue = $('#search-input').val();
          // console.log(e.keyCode);
          $.post('search.php',{'searchValue':inputValue},function (data) {
            // console.log(data);
            
            var jsonData = JSON.parse(data);
            // console.log(jsonData);
            var image_file_extensions = ['jpg','png','heic','psd','cam','jpg-large','igo','pic','jpeg','tiff','gif','jp2','bmp','webp','cal','png-large','ecw','cpt','raw','tif','ico','dds','ulg','ige','emf','jpf','bip','vpe','pano','c4','als','g3p','dng','bm2','jpe','mjpg','jpge','pip','icon','xcf','bgb','jif','rgb','bpt','bm','fpg','mac','pjpeg','pjpg','jpd','bmz','jpg3','pig','bitmap'];
            var document_file_extensions = ['docx','pdf','doc','pub','edoc','_pdf','ete'];
            var video_file_extensions = ['mp4','wve','avi','mkv','flv','wmv','mpg','3gp','ogv','mpeg'];


            $('#gallery_content').html('');
            for(var id in jsonData){
              var file_path = jsonData[id];
              var n = file_path.lastIndexOf('.');
              var extension = file_path.substring(n+1);
              // console.log(extension);
              
              if (image_file_extensions.includes(extension)) {
                // console.log('image ' , id,file_path);
                $('#gallery_content').append('<div class="col-xs-6 col-md-3"><img src="'+file_path+'" alt="" width="300" height="300" data-id="'+id+'"> </div>');
              }
              if(document_file_extensions.includes(extension)){
                // console.log('document ' , id,file_path);
                // $('#gallery_content').html('');
                $('#gallery_content').append('<div class="col-xs-6 col-md-3"><iframe src="'+file_path+'" width="300" height="300" data-id="'+id+'"></iframe></div>');
              }
              if(video_file_extensions.includes(extension)){
                // console.log('video ' , id,file_path);
                // $('#gallery_content').html('');
                $('#gallery_content').append('<div class="col-xs-6 col-md-3"><video src="'+file_path+'"  controls width="300" height="300" data-id="'+id+'"></video></div>');
              }
            }
            
          });
        })
      });
    </script>
  </body>
  </html>