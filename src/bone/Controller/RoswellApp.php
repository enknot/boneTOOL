<?php

use \sb\Gateway as Gateway;
use bone\Settings as Settings;


class RoswellApp {

    /**
     * The acting application user
     *
     * @var User
     */
    public static $user;

    /**
     * The actual application user
     *
     * @var User
     */
    public static $real_user;

    /**
     * 
     * @todo: test this functionality.
     * Sets App::$user and App::$real_user
     * @param string $identifier Optional - the nt_login or roswell id of the user you wish to act as
     */
    public static function setUser($identifier = '') {

        if (Gateway::$command_line) {
            App::$real_user = new \Models\User();
            App::$real_user->uname = 'commandline';
            App::$real_user->dname = 'Command Line';
            App::$real_user->roswell_id = 0;
            App::$real_user->is_admin = 1;
            App::$user = App::$real_user;
        } else if (is_subclass_of(Gateway::$controller_class, '\Controllers\NoAuth')) {
            self::$real_user = new \Models\User();
            self::$real_user->uname = 'noauth';
            self::$real_user->dname = 'noauth';
            self::$real_user->roswell_id = 0;
            self::$user = self::$real_user;
        } else {
            
            if(!isset(Settings::$dev_group)){
                return false;
            }
            
            if (isset($_SESSION['real_user']) && isset($_SESSION['user'])) {

                App::$real_user = unserialize($_SESSION['real_user']);
                App::$user = unserialize($_SESSION['user']);
            } else {

                App::$real_user = App::$directory->currentUser();
                App::$real_user->is_admin = in_array(Settings::$dev_group, App::$real_user->workgroups);

                if ($identifier) {
                    App::$user = App::$directory->currentUser($identifier);
                    App::$user->is_admin = in_array(Settings::$dev_group, App::$user->workgroups);
                } else {
                    App::$user = App::$real_user;
                }
            }
        }

        $_SESSION['real_user'] = serialize(App::$real_user);
        $_SESSION['user'] = serialize(App::$user);

        if (App::$user->roswell_id == App::$real_user->roswell_id) {
            App::$user->acting_as_self = true;
        } else {
            App::$user->acting_as_self = false;
        }
    }

}
