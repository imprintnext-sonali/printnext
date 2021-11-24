<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Origin,Access-Control-Allow-Methods,Authorization , X-Requested-With');
$data = json_decode(file_get_contents("php://input"),true);
$action  = $_GET['action'];
include ('allfunction.php');
include ('function.php');

switch ($action) {
	case 'insert':
    employeeDetails($data);
    break;
    case 'update':
    updateData($data);
    break;
    case 'loginup':
    loginUpdate($data);
    break;
    case 'display':
    displayData($data);
    break;    
    case 'delete':
    deleteData($data);
    break;
    case 'valid':
    validateLogin($data);
    break;
    case 'search':
    search($data);
    break;
    case 'userinsert':
    userDetails($data);
    break;
    case 'moduleinsert':
    moduleInsert($data);
    break;
    case 'relation':
    userModuleRelation($data);
    break;
    case 'team':
    teamInfoDisplay($data);
    break;
    case 'utype':
    userTypeSearch($data);
    break;
    case 'emailcheck':
    emailCheck($data);
    break;
    case 'required':
    requiedDocuments($data);
    break;
    case 'leave':
    readLeaveData();
    break;

    case 'fileupload':
    echo setHolidayCalander($data);
    break;
    case 'getholiday':
    echo getHolidayCalander($data);
    break;
    case 'setdoc':
    echo setCommonDocument($data);
    break;
    case 'getdoc':
    echo getCommonDocuments();
    break;
    case 'deletedoc':
    echo deleteCommonDocuments($data);
    break;
    case 'displayleave':
    echo displayLeaveData($data);
    break;
    case 'updateleave':
    echo updateLeaveData($data);
    break;
    case 'sendemail':
    attacheDocument($data);
    break;
    default:
    echo "No function is executed";
}

?>