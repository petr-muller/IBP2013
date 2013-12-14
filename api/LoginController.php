<?php
session_start();
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once "$_SERVER[DOCUMENT_ROOT]/IBPGit/utilities/QueryParameter.php";
include_once "$_SERVER[DOCUMENT_ROOT]/IBPGit/daoImplementation/security/UserDao.php";
include_once "$_SERVER[DOCUMENT_ROOT]/IBPGit/daoImplementation/security/NameDao.php";
include_once "$_SERVER[DOCUMENT_ROOT]/IBPGit/service/security/UserService.php";
/**
 * Description of LoginController
 *
 * @author Filip
 */
class LoginController {
    //put your code here
    const AUTHORIZE = "authorize";
    const DEAUTHORIZE = "deauthorize";
    const USERSNAME = "usersname";
    private $UserService;
    
    function __construct() {
        $this->UserService = new UserService();
    }
    
    /**
     * Authorize user 
     * @param User $user
     * @return boolean
     */
    function Authorize($user)    {
        return $this->UserService->AuthorizeUser($user);
    }
    
    /**
     * Get name of logged in user
     * @return null
     */
    function GetUsersName() {
        return null;
    }
    
    /**
     * Deathorize user
     * @param type $user
     */
    function Deathorize()  {
        session_unset();
        return true;
    }
}

// Get data
$data = file_get_contents('php://input');
// Decode json data
$user = json_decode($data);

if (isset($_GET["method"]))    {
    
    $LoginController = new LoginController();
    
    switch ($_GET["method"]) {
        case LoginController::AUTHORIZE:
            $result->result = $LoginController->Authorize($user);
            break;

        case LoginController::DEAUTHORIZE:
            $result->result = $LoginController->Deathorize();
            break;
        
        case LoginController::USERSNAME:
            $result->result = $LoginController->GetUsersName();
            break;
            
        default:
            $result->result = false;
            break;
    }
    exit(json_encode($result));
}
?>