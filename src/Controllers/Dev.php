<?php

namespace bone\Controllers;

use \App as App;
use \bone\Variable as Vars;
use \bone\Faux as Faux;

class Dev extends \Controllers\Index {

    /**
     *
     * @var \bone\Faux 
     */
    private $faux;
    
    /**
     * Name of the extened Faux class for this app
     * @var type 
     */
    public $faux_class_name;
    
    /**
     *
     * @var type 
     */
    public $layout;
    
    /**
     *
     * @var type 
     */    
    public $wrapper;

    /**
     * 
     * @param type $method
     */
    public function onBeforeRender($method = '') {
        if (defined('FAUX_CLASS')) {
            $faux_name = FAUX_CLASS;
            $this->faux = new $faux_name(\bone\Settings::$db);
        } else {
            $this->sendRedirect('/');
        }
    }

    /**
     * @servable true
     * @http_method get
     * @input_as_array false
     */
    public function reset() {

        $gate = (isset($this->request->args[0])) ? $this->request->args[0] : null;
        $this->content = (is_null($gate)) ? "<p>Must send a table name in the path" .
                " beyond 'reset/'</p>" : $this->faux->reset($gate);

        $this->layout = $this->renderView(\bone\Settings::$layout_view);
        return $this->renderView(\bone\Settings::$wrapper_view);
    }

    /**
     * @servable true
     * @http_method get
     * @input_as_array false
     */
    public function populate() {


        $gate = (isset($this->request->args[0])) ? $this->request->args[0] : null;
        $this->content = (is_null($gate)) ? "<p>Must send a table name in the path" .
                " beyond 'populate/'</p>" : $this->faux->populate($gate);
        $this->layout = $this->renderView(\bone\Settings::$layout_view);
        return $this->renderView(\bone\Settings::$wrapper_view);
    }

    /**
     * Delegate as another user
     * @param integer $user_id The roswell id to delegate as
     * @return boolean If the suggestion  was sent or not
     *
     * @servable true
     */
    public function actAs() {

        if (\bone\Settings::$dev_group) {

            $user_id = isset($this->request->args[0]) ? $this->request->args[0] : App::$real_user->roswell_id;
            
            if ($user_id == App::$real_user->roswell_id || App::$real_user->belongsToGroup(\bone\Settings::$dev_group)
            ) {


                unset($_SESSION['user']);
                App::setUser($user_id);
                

                App::$logger->delegation(Array(
                    'action' => 'login',
                    'roswell_id' => App::$real_user->roswell_id,
                    'acting_as' => $user_id
                ));
                $this->sendRedirect('/');
                return true;
            }
            return false;
        } else {
                        
            $this->sendRedirect('/');
        }
    }

//    public function actAsY() {
//
//        $user_id = isset($this->request->args[0]) ? $this->request->args[0] : App::$real_user->roswell_id;
//
//        if ($user_id == App::$real_user->roswell_id || App::$real_user->belongsToGroup(TASKTOOL_DEVELOP)
//        ) {
//
//
//            unset($_SESSION['user']);
//            App::setUser($user_id);
//            App::$logger->delegation(Array(
//                'action' => 'login',
//                'roswell_id' => App::$real_user->roswell_id,
//                'acting_as' => $user_id
//            ));
//            $this->sendRedirect('/');
//            return true;
//        }
//
//        return false;
//    }

}
