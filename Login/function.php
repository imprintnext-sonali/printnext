<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$baseUrl = "https://dev.imprintnext.io/ems/";
$upload = './document/';
function setHolidayCalander($data){
	$year = $_POST['year'];
	global $upload;
	$targetFile = $upload. basename($_FILES["file"]["name"]);
	$txt = pathinfo($targetFile,PATHINFO_EXTENSION);
	if ($txt == 'csv') {
		$reName = 'holiday'.$year;
		$newName = $reName.'.'.$txt;
		if(move_uploaded_file($_FILES["file"]["tmp_name"], $upload.$newName)){
			return json_encode(array('message' => 'File Uploaded', 'status' => true));
		}else{
			return json_encode(array('message' => 'Please Try again', 'status' => false));
		}
	}else{
		return json_encode(array('message' => 'choose CSV File', 'status' => false));
	}
}
function getHolidayCalander($data){
	global $upload;
	$year = $_POST['year'];
	if ($open = fopen($upload.'holiday'.$year.'.csv', "r")){
		while (! feof($open)) {
			$csvs[] = (fgetcsv($open));
			$data = [];
			$columnNames = [];
			foreach ($csvs[0] as $single_csv){
				$columnNames[] = $single_csv;
			}
			foreach ($csvs as $key => $csv){
				foreach ($columnNames as $column_key => $columnName){
					$data[$key][$columnName] = $csv[$column_key];
				}
				 $json = json_encode($data);
				 return $json;
			}
		}
	}else{
		return "file doesn't exist";
	}
}
function setCommonDocument($data){
	$empId = $_POST['empid'];
	$docName = $_POST['docname'];
	$requestDate = $_POST['requestdate'];
	$dateofPost = $_POST['dateofpost'];
	$status = $_POST['status'];
	$approvedDate = $_POST['approvedate'];
	global $upload;
	$targetFile = basename($_FILES["file"]["name"]);
	move_uploaded_file($_FILES["file"]["tmp_name"],  $upload.$targetFile);
	global $obj;
	if ($empId == 0){
		$sql = "INSERT INTO document(doc_name, doc_url, request_date, date_of_post, status, approved_date)
		VALUES('$docName', '$targetFile', '$requestDate', '$dateofPost', '$status', '$approvedDate')";
		if ($obj->connect()->query($sql)) {
			return json_encode(array('message' => 'Document Data with Null value inserted' ,'status' => true));
		}else{
			return json_encode(array('message' => 'Document Failed.' ,'status' => false));
		}
	}else{
		$sql = "INSERT INTO document(emp_id,doc_name, doc_url, request_date, date_of_post, status, approved_date)
		VALUES('$empId','$docName', '$targetFile', '$requestDate', '$dateofPost', '$status', '$approvedDate')";
		if ($obj->connect()->query($sql)) {
			return json_encode(array('message' => 'Document Data with out Null value inserted' ,'status' => true));
		}else{
			return json_encode(array('message' => 'Document Failed.' ,'status' => false));
		}
	}
}
function getCommonDocuments(){
	global $baseUrl;
	global $obj;
	$sql = "SELECT * FROM document WHERE emp_id IS NULL";
	$res = $obj->connect()->query($sql);
	$result = array();
	if ($res->num_rows > 0){
		foreach ($res as $key => $value){
			$result[$key]['docname'] = $value['doc_name'];
			$result[$key]['docurl'] = $baseUrl.$value['doc_url'];
			$result[$key]['status'] = $value['status'];
		}
		$json = json_encode($result);
		return $json;
	}
}
function deleteCommonDocuments($data){
	 $docId = $data['docid'];
	 global $obj;
	 $sql = "SELECT doc_url FROM document WHERE doc_id = $docId";
	 $res = $obj->connect()->query($sql);
	 $result = array();
	 foreach ($res as $key => $value) {
	 	$result[$key]['docurl'] = $value['doc_url'];
	 	$fileName = $value['doc_url'];
	 }
	 if ($res == true) {
	 	$sql = "DELETE FROM document WHERE doc_id = $docId";
	 	if ($obj->connect()->query($sql)) {
	 		$status = unlink($fileName);
	 		// print_r($status);
	 		return json_encode(array('message' => 'Remove Succesfully' ,'status' => true));
		}else{
			return json_encode(array('message' => 'delete Failed.' ,'status' => false));
		}
	}else{
		return "choose one id";
	}
}
function displayLeaveData($data){
	global $obj;
	$mysqli =  $obj->connect();
	$sql = "SELECT `leave_record`.`leave_id`,`leave_record`.`emp_id`,`leave_record`.`leave_apply_date`,`leave_record`.`leave_from`,`leave_record`.`leave_to`,`leave_record`.`days`,`leave_record`.`leave_type`,`leave_record`.`reason`,`leave_record`.`mail_to`,`leave_record`.`contact_no`,`leave_record`.`status`,`leave_status`.`allowed_plan_leave`,`leave_status`.`taken_plan_leave`,`leave_status`.`allowed_casual_leave`,`leave_status`.`taken_plan_leave`,`leave_status`.`taken_casual_leave`,`employee_details`.`emp_name` FROM `leave_record` INNER JOIN `leave_status` ON `leave_record`.`emp_id` = `leave_status`.`emp_id`
        INNER JOIN `employee_details` ON `leave_record`.`emp_id` = `employee_details`.`emp_id`";
        $res = $mysqli->query($sql);
        $res->num_rows; 		
        $result = array();
        if ($res->num_rows > 0){
        	foreach ($res as $key => $value){
        		$result[$key]['leaveid'] = $value['leave_id'];
        		$result[$key]['emp_name'] = $value['emp_name'];
        		$result[$key]['leave_apply_date'] = $value['leave_apply_date'];
        		$result[$key]['leave_from'] = $value['leave_from'];
        		$result[$key]['leave_to'] = $value['leave_to'];
        		$result[$key]['days'] = $value['days'];
        		$result[$key]['leave_type'] = $value['leave_type'];
        		$result[$key]['reason'] = $value['reason'];
        		$result[$key]['mail_to'] = $value['mail_to'];
        		$result[$key]['contact_no'] = $value['contact_no'];
        		$result[$key]['status'] = $value['status'];
        		$result[$key]['allowed_plan_leave'] = $value['allowed_plan_leave'];
        		$result[$key]['allowed_casual_leave'] = $value['allowed_casual_leave'];
        		$result[$key]['taken_plan_leave'] = $value['taken_plan_leave'];
        		$result[$key]['taken_casual_leave'] = $value['taken_casual_leave'];
        		$result[$key]['available_plan_leave'] = $value['allowed_plan_leave'] - $value['taken_plan_leave'];
        		$result[$key]['available_casual_leave'] = $value['allowed_casual_leave'] - $value['taken_casual_leave'];
        		$days = $value['days'];
        		$paidPlanLeave = $value['allowed_plan_leave'] - $value['taken_plan_leave'];
        		$paidCasualLeave = $value['allowed_casual_leave'] - $value['taken_casual_leave'];
        	}
			// print_r($result); exit;
        	$json = json_encode($result);
        	return $json;
        }
}
function updateLeaveData($data){
	$leaveId = $data['leaveid'];
	$status = $data['status'];
	
	global $obj;
	$sql = "SELECT * FROM `leave_record` INNER JOIN `leave_status`  WHERE `leave_record`.`leave_id` = $leaveId AND `leave_status`.`emp_id` = `leave_record`.`emp_id`";
	$res = $obj->connect()->query($sql);
	$result = array();
	foreach ($res as $key => $value){
     $result[$key]['empId'] = $value['emp_id'];//11
     $result[$key]['reqDate'] = $value['days'];//3
     $result[$key]['leaveType'] = $value['leave_type'];//plan
     $result[$key]['allowed_plan_leave'] = $value['allowed_plan_leave'];//12
     $result[$key]['allowed_casual_leave'] = $value['allowed_casual_leave'];//10
     $result[$key]['taken_casual_leave'] = $value['taken_plan_leave'];//4
     $result[$key]['taken_casual_leave'] = $value['taken_casual_leave'];//2
     $result[$key]['status'] = $value['status'];//approve
     $emid = $value['emp_id'];//11
     $reqdays = $value['days']; //5
     $leaveType = $value['leave_type'];//plan
     $allowPlanleave = $value['allowed_plan_leave']; //12
     $allowCasualleave =  $value['allowed_casual_leave']; //10
     $takenPlanleave = $value['taken_plan_leave']; //4
     $takenCasualleave = $value['taken_casual_leave']; //2
     $avPlanleave = $allowPlanleave - $takenPlanleave; //12-4 =8 
     $totalPlanLeave = $reqdays + $takenPlanleave;//3+4=7
     $canclePlan = $takenPlanleave -  $reqdays;//7-3 = 4
     $avCasualLeave = $allowCasualleave - $takenCasualleave;//10-2=8
     $totalCasualLeave = $reqdays + $takenCasualleave;//5+2=7
     $cancleCasual = $takenCasualleave - $reqdays;//6-5 = 1
 }
 if ($leaveType == "Plan Leave"){
 	if ($avPlanleave > $reqdays){
 		$sql = "SELECT status FROM `leave_record` WHERE leave_id = $leaveId";
 		$res = $obj->connect()->query($sql);
 		$result = array();
 		foreach ($res as $key => $value){
 			$result[$key]['status'] = $value['status'];
 			$getstatus = ($value['status']);
 			// print_r($getstatus);exit;
 		}
 		if ($status == "Approved") {
 			global $obj;
 			$sql = "UPDATE `leave_record` SET `status` = '$status' WHERE `leave_record`.`leave_id` = $leaveId ";
 			if ($obj->connect()->query($sql)){
 				$sql = "UPDATE `leave_status` SET `taken_plan_leave` = '$totalPlanLeave' WHERE `leave_status`.`emp_id` = $emid ";
 				if ($obj->connect()->query($sql)) {
 					return json_encode(array('message' => 'Plan Leave Approved.'));
 				}
 			}
 		}
 		if ($status == "Cancelled") {
 			if ($getstatus == "Approved") {
 				$sql = "UPDATE `leave_record` SET `status` = '$status' WHERE `leave_record`.`leave_id` = $leaveId ";
 				if ($obj->connect()->query($sql)) {
 					return $sql = "UPDATE `leave_status` SET `taken_plan_leave` = '$canclePlan' WHERE `leave_status`.`emp_id` = $emid ";
 					if ($obj->connect()->query($sql)) {
 						return json_encode(array('message' => 'Your Plan Leave Canceled .'));
 					}
 				}
 			}else{
 				global $obj;
 		       $sql = "UPDATE `leave_record` SET `status` = 'Rejected' WHERE `leave_record`.`leave_id` = $leaveId ";
 		       if ($obj->connect()->query($sql)) {
 			     return json_encode(array('message' => ' Your Plan Leave Rejected.'));
 		        }
 			}
 		}
 	}else{
 		
 		return json_encode(array('message' => 'NO Plan Leave Present.'));
 	}
 }else{
 	if ($avCasualLeave > $reqdays){
 		global $obj;
 		$sql = "SELECT status FROM `leave_record` WHERE leave_id = $leaveId";
 		$res = $obj->connect()->query($sql);
 		$result = array();
 		foreach ($res as $key => $value){
 			$result[$key]['status'] = $value['status'];
 			$getstatus = ($value['status']);
 		}
 		if ($status == "Approved") {
 			global $obj;
 			$sql = "UPDATE `leave_record` SET `status` = '$status' WHERE `leave_record`.`leave_id` = $leaveId ";
 			if ($obj->connect()->query($sql)){
 				$sql = "UPDATE `leave_status` SET `taken_casual_leave` = '$totalCasualLeave' WHERE `leave_status`.`emp_id` = $emid ";
 				if ($obj->connect()->query($sql)) {
 					return json_encode(array('message' => 'casual Leave Approved.'));
 				}
 			}
 		}
 		if ($status == "Cancelled") {
 			if ($getstatus == "Approved") {
 				$sql = "UPDATE `leave_record` SET `status` = '$status' WHERE `leave_record`.`leave_id` = $leaveId ";
 				if ($obj->connect()->query($sql)) {
 					$sql = "UPDATE `leave_status` SET `taken_casual_leave` = '$cancleCasual' WHERE `leave_status`.`emp_id` = $emid ";
 					if ($obj->connect()->query($sql)) {
 						return json_encode(array('message' => 'Your Casual Leave Cancelled .'));
 					}
 				}
 			}else{
 				global $obj;
 		       $sql = "UPDATE `leave_record` SET `status` = 'Rejected' WHERE `leave_record`.`leave_id` = $leaveId ";
 		       if ($obj->connect()->query($sql)) {
 			     return json_encode(array('message' => 'Your Casual Leave already Cancelled .'));
 		        }
 				
 			}
 		}
 	}else{
 		return json_encode(array('message' => 'NO Casual Leave present.'));
 	}
 }
}

function attacheDocument($data){
	global $baseUrl;
	$empId = $data['empid'];
	//echo $empId;
	global $obj;
	if (!$empId == 0 ) {
		$sql = "SELECT * FROM `document` INNER JOIN `employee_details` ON `document`.`emp_id`= $empId AND `employee_details`.`emp_id` = `document`.`emp_id`;";
		$res = $obj->connect()->query($sql);
		$result = array();
		foreach ($res as $key => $value) {
			$result[$key]['email'] = $value['office_email'];
			$result[$key]['document'] = $value['doc_name'];
			$result[$key]['docurl'] = $baseUrl.$value['doc_url'];
            
            
		}
		   $to = $value['office_email'];
            // print_r($to);exit;
            $subject = ($value['doc_name']);
            $file = $baseUrl.$value['doc_url'];
            $from = 'sender@example.com'; 
            $fromName = 'RiaxeSystems';
            $headers = "From: $fromName"." <".$from.">";
		$htmlContent = '<h3>PHP Email with Attachment by CodexWorld</h3>
                            <p>This email is sent from the PHP script with attachment.</p>';
                            $semi_rand = md5(time());
                            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
                            $headers .= "MIME-Version: 1.0" . "Content-Type: multipart/mixed;" . " boundary='{$mime_boundary}'";
                            $message = "--{$mime_boundary}" . "Content-Type: text/html; charset=UTF-8" .
                            "Content-Transfer-Encoding: 7bit" . $htmlContent . "";
                            if (!empty($file) > 0){
                            	if (is_file($file)){
                            		$message .= "--{$mime_boundary}";
                       		        $fp =  fopen($file,"r");
                       		        $read =  fread($fp,filesize($file));
                       		        fclose($fp);
                       		        $read = chunk_split(base64_encode($read));
                       		        $message .= "Content-Type: application/octet-stream;".basename($file)."" .  
                                    "Content-Description: ".basename($file)."" . 
                                    "Content-Disposition: attachment;" . " filename=".basename($file)."; size=".filesize($file).";" .  
                                     "Content-Transfer-Encoding: base64" . $read . "";
                                }
                            }
                            $message .= "--{$mime_boundary}--"; 
                            $returnpath = "-f" . $from;
                            $mail = mail($to, $subject, $message, $headers, $returnpath);
                            //echo $mail?"<h1>Email Sent Successfully!</h1>":"<h1>Email sending failed.</h1>";
                            if($mail){
                            	return json_encode(array('message' => 'Email Sent Successfully', 'status' => true));
                            }else{
                            	return json_encode(array('message' => 'Email sending failed..', 'status' => false));
                            }
	}else{
		return "All details Show";
	}
}

/*
function attacheDocument($data){
     $from_email         = 'sonali@imprintnext.com'; //from mail, sender email address
    $recipient_email    = 'sonalishas25@gmail.com'; //recipient email address

	$sender_name    = $_POST["sender_name"];
	$reply_to_email = $_POST["sender_email"];
	$subject        = $_POST["subject"];
	 $message        = $_POST["message"];

	 $tmp_name    = $_FILES['my_file']['tmp_name'];
	 $name        = $_FILES['my_file']['name'];
	 $size        = $_FILES['my_file']['size'];  
    $type        = $_FILES['my_file']['type'];  
    $error       = $_FILES['my_file']['error'];

    $handle = fopen($tmp_name, "r");
      // set the file handle only for reading the file
    $content = fread($handle, $size); // reading the file
    
    fclose($handle);

    $encoded_content = chunk_split(base64_encode($content));
    //print_r($encoded_content); 

    $boundary = md5("random");

    $headers = "MIME-Version: 1.0"; // Defining the MIME version
    $headers .= "From:".$from_email.""; // Sender Email
    $headers .= "Reply-To: ".$reply_to_email.""; // Email address to reach back
    $headers .= "Content-Type: multipart/mixed;"; // Defining Content-Type
    $headers .= "boundary = $boundary"; //Defining the Boundary

    //plain text
    $body = "--$boundary";
    $body .= "Content-Type: text/plain; charset=ISO-8859-1";
    $body .= "Content-Transfer-Encoding: base64";
    $body .= chunk_split(base64_encode($message));
         
    //attachment
    $body .= "--$boundary";
    $body .="Content-Type: $type; name=".$name."";
    $body .="Content-Disposition: attachment; filename=".$name."";
    $body .="Content-Transfer-Encoding: base64";
    $body .="X-Attachment-Id: ".rand(1000, 99999)."";
    $body .= $encoded_content; // Attaching the encoded file with email

    $sentMailResult = mail($recipient_email, $subject, $body, $headers);
 
    if($sentMailResult )
    {
       echo "File Sent Successfully.";
       // unlink($name); // delete the file after attachment sent.
    }
    else
    {
       die("Sorry but the email could not be sent.
                    Please go back and try again!");
    }
}
*/


?>