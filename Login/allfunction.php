<?php
include 'conn.php';
$obj = new EmployeeData();
session_start();
function  LoginDetails($data){
  $userName  = $data['uname'];
  $userPassword  = md5($data['upassword']);
  $userRole  = $data['urole'];
  $empId = $data['empid'];
  global $obj;
  $sql = "INSERT INTO login_page (user_id,user_password,user_role,emp_id)VALUES('$userName', '$userPassword', '$userRole','$empId')";
  $obj->connect()->query($sql);
}

function employeeDetails($data){
  $empName = $data['ename'];
  $empCode = $data['ecode'];
  $officeMail = $data['offMail'];
  $department = $data['department'];
  $designation = $data['designation'];
  $joinDate = $data['joinDate'];
  $dob = $data['dob'];
  $gender = $data['gender'];
  $country = $data['country'];
  $state = $data['state'];
  $city = $data['city'];
  $pincode = $data['pincode'];
  $currAddress = $data['caddress'];
  $perAddress = $data['paddress'];
  $qualification = $data['qualification'];
  $personalMail = $data['pmail'];
  $contact = $data['phone'];
  $parentContact = $data['pcontact'];
  $pancard = $data['pan'];
  $aadhar = $data['aadhar'];
  $drivingLicense = $data['dl'];
  $profilePic = $data['image'];
  $status = $data['status'];
  $userType = $data['usertype'];
  global $obj;
  
 $valStatus = (array) json_decode(existValueCheck($data));
  if($valStatus['status'] == 0)
  {
    $mysqli = $obj->connect();
    $sql = "INSERT INTO employee_details (emp_name, emp_code, office_email, department, designation,joining_date,gender,dob,  country, state, city,pincode,current_address,permanent_address,qualification,personal_email,contact_no,
      guardian_contact_no,pancard_no,aadhar_card, driving_license, profile_image, status,user_type)
    VALUES('$empName', '$empCode', '$officeMail', '$department', '$designation', '$joinDate','$gender','$dob',  '$country', '$state', '$city', '$pincode', '$currAddress', '$perAddress','$qualification', '$personalMail', '$contact', '$parentContact', '$pancard', '$aadhar','$drivingLicense','$profilePic', '$status','$userType')";
    $result = $mysqli->query($sql);
    $id =  $mysqli->insert_id;
    if($id){
      echo json_encode(array('message' => 'employee Data Inserted.', 'status' => true));
    }else{
      echo json_encode(array('message' => 'Failed.', 'status' => false));
    }
    LoginDetails($data);
    return 1;
  }else{
    echo json_encode(array('message' => $valStatus['message'], 'status' => false));
  }
}
function validateLogin($data){
  $userName  = $data['uname'];
  $userPassword  = md5($data['upassword']);
  global $obj;
  $sql = "SELECT * from login_page where user_id = '$userName'AND user_password = '$userPassword'";
  $result = $obj->connect()->query($sql);
 // $count =   $result->num_rows;
 if ($result->num_rows > 0){
    $output = (array) $result->fetch_object();
    echo json_encode(array('message' => 'login Success' ,'status' => 'true','emp_id' => $output['emp_id']));
    $_SESSION["EMP"] = $output['emp_id'];
  }else{
    echo json_encode(array('message' => 'login failed' ,'status' => 'false'));
  }
  // if (!filter_var($userName, FILTER_VALIDATE_EMAIL)) {
  //   echo json_encode(array('message' => 'invalid email address' ,'status' => 'true'));
  // }else{
  //   echo json_encode(array('message' => 'Valid emailid' ,'status' => 'true'));
  // }

}
function displayData($data){
  $pageno = $data['pageno'];
  $order = $data['order'];
  $perPage = $data['perPage'];
  global $obj;
  if (!isset ($_GET['page'])){
    $page = 1;
  }else{
    $page = $_GET['page'];
  }
  $firstPage = ($pageno * $perPage) - $perPage; //7*1-1=6 4*2-2=6
  $sql = "SELECT *FROM employee_details ORDER BY emp_name $order LIMIT " . $firstPage . ',' . $perPage;
  $res = $obj->connect()->query($sql);
  if ($res->num_rows > 0){
    $output = $res->fetch_all(MYSQLI_ASSOC);
    echo json_encode($output);
  }else{
    echo json_encode(array('message' => 'Data cant show' ,'status' => 'true'));
  }
}

function updateData($data){
  $empId = $data['eid'];
  $empName = $data['ename'];
  $empCode = $data['ecode'];
  $officeMail = $data['offMail'];
  $department = $data['department'];
  $designation = $data['designation'];
  $joinDate = $data['joinDate'];
  $dob = $data['dob'];
  $gender = $data['gender'];
  $country = $data['country'];
  $state = $data['state'];
  $city = $data['city'];
  $pincode = $data['pincode'];
  $currAddress = $data['caddress'];
  $perAddress = $data['paddress'];
  $qualification = $data['qualification'];
  $personalMail = $data['pmail'];
  $contact = $data['phone'];
  $parentContact = $data['pcontact'];
  $pancard = $data['pan'];
  $aadhar = $data['aadhar'];
  $drivingLicense = $data['dl'];
  $profilePic = $data['image'];
  $status = $data['status'];
  global $obj;
  $sql = " UPDATE employee_details SET emp_name = '$empName', emp_code = '$empCode', office_email = '$officeMail', department = '$department', designation = '$designation', joining_date = '$joinDate', gender = '$gender',dob = '$dob', country = '$country', state = '$state', city = '$city', pincode = '$pincode', current_address = '$currAddress',permanent_address = '$perAddress',qualification = '$qualification', personal_email = '$personalMail', contact_no = '$contact',guardian_contact_no = '$parentContact',pancard_no = '$pancard',aadhar_card = '$aadhar', driving_license = '$drivingLicense', profile_image = '$profilePic', status = '$status' WHERE emp_id = {$empId} ";
  if($obj->connect()->query($sql)) {
    echo json_encode(array('message' => 'employee Data updated.', 'status' => true));
  }else{
    echo json_encode(array('message' => 'update Failed.', 'status' => false));
  }
} 
function deleteData($data){
  $empId = $data['eid'];
  global $obj;
  $sql="DELETE from employee_details WHERE emp_id= {$empId} ";
  if($obj->connect()->query($sql)) {
    echo json_encode(array('message' => 'Delete Successful.', 'status' => true));
  }else{
    echo json_encode(array('message' => 'Delete Failed.', 'status' => false));
  }
}
function loginUpdate($data){
  $empId = $data['eid'];
  $userName  = $data['uname'];
  $userPassword  = $data['upassword'];
  global $obj;
  $sql = "UPDATE login_page SET user_password = '$userPassword' WHERE login_id = {$empId} ";
  if($obj->connect()->query($sql)) {
    echo json_encode(array('message' => 'login Data updated.', 'status' => true));
  }else{
    echo json_encode(array('message' => 'login Failed.', 'status' => false));
  }
}
function existValueCheck($data){
  $empCode = $data['ecode'];
  $officeMail = $data['offMail'];
  global $obj;
  $sql = "SELECT * FROM employee_details WHERE emp_code = '$empCode' OR office_email = '$officeMail'";
  $res = $obj->connect()->query($sql);
  if ($res->num_rows > 0){
    return json_encode(array('message' => 'Check MailId or empCode' ,'status' => 1));
  }else{
   return json_encode(array('message' => 'Data not available' ,'status' => 0 ));
  }
}
function search($data){
  $search = $data['search'];
  global $obj;
  echo $sql = "SELECT *FROM employee_details WHERE emp_name LIKE '%{$search}%' or office_email LIKE '{$search}%' ";
  $res = $obj->connect()->query($sql);
  if ($res->num_rows > 0){
    $output = $res->fetch_all(MYSQLI_ASSOC);
    echo json_encode($output);
  }else{
    echo json_encode(array('message' => 'No search Found' ,'status' => 'true'));
  }
}
function userDetails($data){
  $empCode = $data['empcode'];
  $userRole = $data['urole'];
  global $obj;
  $sql = "INSERT INTO user_type(emp_code,user_role)VALUES('$empCode','$userRole')";
  if($obj->connect()->query($sql)) {
    echo json_encode(array('message' => 'userType inserted.', 'status' => true));
  }else{
    echo json_encode(array('message' => 'userType Failed.', 'status' => false));
  }
}
function moduleInsert($data){
  $modName = $data['modname'];
  global $obj;
  $sql = "INSERT INTO user_module(mod_name)VALUES('$modName')";
  if($obj->connect()->query($sql)) {
    echo json_encode(array('message' => 'userModule inserted.', 'status' => true));
  }else{
    echo json_encode(array('message' => 'userModule Failed.', 'status' => false));
  }
}
function userModuleRelation($data){
  $moduleId = $data['moduleid'];
  $userId = $data['userid'];
  global $obj;
  $sql = "INSERT INTO user_module_relation(mod_id,user_id)VALUES('$moduleId','$userId')";
  if($obj->connect()->query($sql)) {
    echo json_encode(array('message' => 'userModuleRelation inserted.', 'status' => true));
  }else{
    echo json_encode(array('message' => 'userModuleRelation Failed.', 'status' => false));
  }
}
function teamInfoDisplay($data){
  $empName = $data['empname'];
  $teamName = $data['teamname'];
  $managerId = $data['managerid'];
  $tlId = $data['tid'];
  $department = $data['department'];
  global $obj;
  $sql = "INSERT INTO team_info(emp_name,team_name,manager_id,tl_id,department)
  VALUES('$empName','$teamName','$managerId','$tlId','$department')";
  if($obj->connect()->query($sql)) {
    echo json_encode(array('message' => 'Team information inserted.', 'status' => true));
  }else{
    echo json_encode(array('message' => 'Team information Failed.', 'status' => false));
  }
}
function userTypeSearch($data){
  global $obj;
  echo $sql = "SELECT *FROM user_type";
  $res = $obj->connect()->query($sql);
  if ($res->num_rows > 0){
    $output = $res->fetch_all(MYSQLI_ASSOC);
    echo json_encode($output);
  }else{
    echo json_encode(array('message' => 'user type not found' ,'status' => 'true'));
  }
}
// session_destroy();
// session_unset($_SESSION["empid"]);
// echo "id remove successfully";
?>