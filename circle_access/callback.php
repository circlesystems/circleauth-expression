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

    header('location:/index.php?ACT='.CIRCLE_ACCESS_ACT);
} else {
    echo 'Authentication error. ';
    die();
}
