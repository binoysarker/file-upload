<?php 
require_once("session.php");

require_once("class.user.php");
$auth_user = new USER();


$user_id = $_SESSION['user_session'];

if ($auth_user->is_loggedin() != '' && isset($_POST['searchValue'])) {
	$searchValue = $_POST['searchValue'];
	$user_detail = $auth_user->getAllInfo();
	$result = [];
	// $emails = [];
	foreach ($user_detail as $info) {
		$id = $info['id'];
		$user_name = $info['user_name'];
		$user_email = $info['user_email'];
		$user_number = $info['user_number'];
		$user_subject = $info['user_subject'];
		$user_info = $info['user_info'];
		$file_paths = json_decode($info['file_path']);
		// $getData;
		foreach ($file_paths as $key => $value) {
			
			if (stristr($value,$searchValue)) {
				// array_push($result,$id,$value);
				// array_push($result,'email',$user_email);
				$result[$id] = ''.$value;
				// $emails[$id] = $user_email;
				// print_r($id.' => '. $value);
				// $getData = explode('./',$value);
				// print_r(explode('./',$value));
			}
		}

	}
	// print_r($result);
	echo json_encode($result);
	// echo json_encode(['data'=>$result]);
	
	
}