<?php
/**
 * circleAccess Module.
 */
include_once 'system/ee/ExpressionEngine/Addons/member/mod.member.php';
include_once 'system/ee/ExpressionEngine/Addons/member/mod.member_auth.php';
include_once 'system/ee/ExpressionEngine/Service/Encrypt/Encrypt.php';

use ExpressionEngine\Service\Encrypt\Encrypt;

class CircleAccess
{
    protected $userID;
    protected $sessionID;
    protected $customID;
    protected $redirectUrl;
    protected $dashboardUrl;
    protected $circleEmail;
    protected $newMemberRole;
    protected $newCircleUser;
    protected $userHashedEmails;
    protected $addUserIfNotExists;
    protected $memberNotExistsError;

    public function init()
    {
        session_start();
        $customID = (isset($_SESSION['circleCallback']['customID'])) ? $_SESSION['customID'] : '';
        $this->userID = $_SESSION['circleCallback']['userID'];
        $this->sessionID = $_SESSION['circleCallback']['sessionID'];
        $this->customID = $customID;
        $this->redirectUrl = $_SESSION['circleCallback']['redirectUrl'];
        $this->dashboardUrl = $_SESSION['circleCallback']['dashboardUrl'];
        $this->circleEmail = $_SESSION['circleCallback']['circleEmail'];
        $this->newMemberRole = $_SESSION['circleCallback']['newMemberRole'];
        $this->userHashedEmails = $_SESSION['circleCallback']['userHashedEmails'];
        $this->addUserIfNotExists = $_SESSION['circleCallback']['addMemberNotExists'];
        $this->memberNotExistsError = $_SESSION['circleCallback']['memberNotExistsError'];
        $this->handleRequest();
        $_SESSION['circleCallback']['error'] = '';
    }

    protected function handleRequest()
    {
        //check if the email exists on ExpressionEngine
        $memberId = $this->getMemberId($this->userHashedEmails);

        //check if there is an email in the callback
        if (!$memberId && $this->circleEmail != '') {
            $this->addMember();
        }

        if (intval($memberId) > 0) {
            $this->doLoginByMemberId($memberId);
        } else {
            //If the "Add New Member if not exists" option is on, then redirect to
            //the Circle Dashboard and get the login email.
            if ($this->addUserIfNotExists == '1') {
                ee()->functions->redirect($this->dashboardUrl);
            } else {
                ee()->functions->redirect($_SESSION['circleCallback']['noEmailsInDevice']);
            }
        }
    }

    protected function getMemberId($userHashedEmails)
    {
        //check if there is at least one email hash
        if (count($userHashedEmails) == 0) {
            echo 'No user emails';

            return null;
        }
        $tableName = ee()->db->dbprefix('members');
        $hashedEmails = implode("','", $userHashedEmails);
        $sql = "select member_id from $tableName where SHA2(email,256) in ('".$hashedEmails."')";

        $query = ee()->db->query($sql);
        if ($query->result_array()[0]['member_id']) {
            return $query->result_array()[0]['member_id'];
        }

        return null;
    }

    protected function doLoginByMemberId($memberId)
    {
        ee()->session->create_new_session($memberId);
        //redirect the member
        if ($this->redirectUrl != '') {
            ee()->functions->redirect($this->redirectUrl);
            die();
        } else {
            ee()->functions->redirect('/admin.php');
        }
    }

    protected function addMember()
    {
        if ($this->addUserIfNotExists == 1) {
            $this->addExpressionMember();
        } else {
            $_SESSION['circleCallback']['error'] = $this->memberNotExistsError;
            ee()->functions->redirect('/admin.php');
        }
    }

    protected function addExpressionMember()
    {
        ee()->load->library('auth');
        $encrypt = new Encrypt('343a91ea311929fb443745abfc7c89eab06274f9');

        try {
            $data['username'] = $this->circleEmail;
            $data['password'] = ee()->auth->hash_password(substr(random_string('md5'), 0, 8))['password'];
            $data['email'] = $this->circleEmail;
            $data['unique_id'] = random_string('encrypt');
            $data['join_date'] = ee()->localize->now;
            $data['group_id'] = $this->newMemberRole;
            $data['crypt_key'] = $encrypt->generateKey();
            $data['role_id'] = $this->newMemberRole;
            $data['ip_address'] = '127.0.0.1';
            $data['language'] = 'english';
            $data['screen_name'] = $this->circleEmail;
        } catch (Exception $error) {
            print_r($error);
        }

        $member_obj = ee('Model')->make('Member');
        $member_obj->set($data)->save();
        $memberId = $member_obj->member_id;

        if (intval($memberId) > 0) {
            $this->doLoginByMemberId($memberId);
        } else {
            print_r('Error creating new member.');
            die();
        }
    }
}
