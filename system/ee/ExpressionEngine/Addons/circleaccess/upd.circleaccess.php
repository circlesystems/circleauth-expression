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
