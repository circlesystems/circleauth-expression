<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
session_start();

require_once dirname(__FILE__).'/includes/config.php';
require_once dirname(__FILE__).'/includes/functions.php';
require_once dirname(__FILE__).'/includes/circleApi.php';

$userID = getRequestParameter('userID');
$sessionID = getRequestParameter('sessionID');
$customID = getRequestParameter('customID');
$circleEmail = decodeEmail($customID);

//validate the Circle sessionID  and userID
if ($userID && $sessionID && validateUserSession($sessionID, $userID)) {
    $userSession = getSession($sessionID);

    //send the request to CircleAccess add-on
    $_SESSION['circleCallback']['userID'] = $userID;
    $_SESSION['circleCallback']['sessionID'] = $sessionID;
    $_SESSION['circleCallback']['customID'] = $customID;
    $_SESSION['circleCallback']['redirectUrl'] = REDIRECT_URL;
    $_SESSION['circleCallback']['dashboardUrl'] = CIRCLE_DASHBOARD_URL;
    $_SESSION['circleCallback']['circleEmail'] = $circleEmail;
    $_SESSION['circleCallback']['newMemberRole'] = NEW_MEMBER_DEFAULT_ROLE;
    $_SESSION['circleCallback']['memberNotExistsError'] = MEMBER_NOT_EXISTS_ERROR;
    $_SESSION['circleCallback']['addMemberNotExists'] = ADD_MEMBER_IF_NOT_EXISTS;
    $_SESSION['circleCallback']['userHashedEmails'] = $userSession['data']['userHashedEmails'];
    $_SESSION['circleCallback']['noEmailsInDevice'] = NO_EMAILS_IN_DEVICE_URL;

    //check if there are registered e-mails on user device.

    if ((count($userSession['data']['userHashedEmails']) > 0) || isset($circleEmail)) {
        expireUserSession($sessionID, $userID);
        header('location:/index.php?ACT='.CIRCLE_ACCESS_ACT);
    } else {
        header('location:'.NO_EMAILS_IN_DEVICE_URL);
    }
} else {
    echo 'Authentication error. ';
    die();
}
