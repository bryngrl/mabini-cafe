<?php
require_once "../Controllers/UserController.php"; // Cinapital q lang uli

$controller = new UserController($db);

if ($subresource === 'login') {
    $controller->login();
    exit;
}   


switch ($method) {
    case 'GET':
        $id ? $controller->show($id) : $controller->index();
        break;

    case 'POST':
        if($subresource == 'forgotpasswordotp')
        $controller->forgotpassOTP();
        else if($subresource == 'verifyotp')
        $controller->verify_otp();
        else if($subresource == 'signupotp'){
            $controller->signupOTP();
        }
       else
        $controller->store();
        break;

    case 'PUT':

        if ($id) {
            $controller->update($id);
        }else if($subresource == 'changepassword')
             $controller->changePasswordController();
        break;

    case 'DELETE':
        if ($id) {
            $controller->destroy($id);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
