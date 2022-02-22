<?php

use ExpressionEngine\Service\Addon\Installer;

/**
 * Authenticate Module update class.
 */
class CircleAccess_upd extends Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function install()
    {
        //create the table circle_access
        $tableName = ee()->db->dbprefix('circle_access');
        $sql = 'CREATE TABLE IF NOT EXISTS '.$tableName.' (
        user_id varchar(65) NOT NULL,
        user_email varchar(255) NOT NULL,
        member_id varchar(20) NOT NULL,
        PRIMARY KEY `member_id` (`member_id`)
        ) DEFAULT CHARACTER SET '.ee()->db->escape_str(ee()->db->char_set).' COLLATE '.ee()->db->escape_str(ee()->db->dbcollat);

        ee()->db->query($sql);

        //insert the add-on into the actions table
        $data = [
            'class' => 'CircleAccess',
            'method' => 'init',
        ];
        ee()->db->insert('actions', $data);

        //insert the add-on into the modules table
        $data = [
            'module_name' => 'CircleAccess',
            'module_version' => '1.0.0',
        ];
        ee()->db->insert('modules', $data);

        return true;
    }

    public function uninstall()
    {
        //drop the table circle_access
        $tableName = ee()->db->dbprefix('circle_access');
        ee()->db->query('drop table '.$tableName);

        //remove the add-on from modules
        $tableName = ee()->db->dbprefix('modules');
        $sql = 'delete from '.$tableName." where module_name='CircleAccess'";
        ee()->db->query($sql);

        //remove the add-on from actions
        $tableName = ee()->db->dbprefix('actions');
        $sql = 'delete from '.$tableName." where class='CircleAccess'";
        ee()->db->query($sql);

        return true;
    }
}
