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

    // ee()->db->dbprefix
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
        $this->handleRequest();
        $_SESSION['circleCallback']['error'] = '';
    }

    protected function getCircleUser($userID)
    {
        if (!$userID) {
            print_r('UserID is null.');

            return null;
        }
        $circleTableName = ee()->db->dbprefix('circle_access');
        $membersTableName = ee()->db->dbprefix('members');

        $sql = 'SELECT '.$circleTableName.'.member_id FROM  '.$circleTableName.'   
        INNER join '.$membersTableName.' on '.$membersTableName.'.member_id= '.$circleTableName.'.member_id';
        $sql .= ' where user_id = "'.$userID.'"';

        $query = ee()->db->query($sql);
        if ($query->result_array()[0]['member_id']) {
            return $query->result_array()[0]['member_id'];
        }

        return null;
    }

    protected function getMemberIdByEmail($email)
    {
        if (!$email) {
            return null;
        }
        ee()->db->select('member_id, username, password');
        ee()->db->from('members');
        ee()->db->where('email', $email);

        $query = ee()->db->get();
        if ($query) {
            return $query->result_array()[0];
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

    protected function handleRequest()
    {
        //do the login if the user exists
        $memberId = $this->getCircleUser($this->userID);

        //check if there is an email in the callback and if there is a
        //member associated with this email.
        if (!$memberId && $this->circleEmail != '') {
            $this->addMember();
        }

        if (intval($memberId) > 0) {
            $this->doLoginByMemberId($memberId);
        } else {
            //redirect to Circle Dashboard and get the login email.
            ee()->functions->redirect($this->dashboardUrl);
        }
    }

    protected function addMemberToCircleAccess($memberId)
    {
        //remove a previous Circle userId / email
        $tableName = ee()->db->dbprefix('circle_access');
        $sql = 'delete from '.$tableName.' where member_id='.$memberId;
        ee()->db->query($sql);

        //insert the member
        $data['member_id'] = $memberId;
        $data['user_id'] = $this->userID;
        $data['user_email'] = $this->circleEmail;
        ee()->db->insert('circle_access', $data);
        $this->doLoginByMemberId($memberId);
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
            //add the member to circle_access table and do the member login
            $this->addMemberToCircleAccess($memberId);
        } else {
            print_r('Error creating new member.');
            die();
        }
    }

    protected function addMember()
    {
        //check if there is a member with the email from request
        ee()->db->select('member_id, email');
        ee()->db->from('members');
        ee()->db->where('email', $this->circleEmail);
        $query = ee()->db->get();

        if ($query->result_array()[0]['member_id']) {
            //do the insert into circle_access table and login the member
            $this->addMemberToCircleAccess($query->result_array()[0]['member_id']);
        } else {
            if ($_SESSION['circleCallback']['addMemberNotExists'] == 1) {
                $this->addExpressionMember();
            } else {
                $_SESSION['circleCallback']['error'] = $_SESSION['circleCallback']['memberNotExistsError'];
                ee()->functions->redirect('/admin.php');
            }
        }
    }
}
