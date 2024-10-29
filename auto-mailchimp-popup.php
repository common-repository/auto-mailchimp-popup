<?php
/*
  Plugin Name: Auto Mailchimp Popup
  Plugin URI: http://www.atomixstudios.com
  Description: Auto Mailchimp Popup is Mailchimp based Popup Plugin for Wordpress.
  Version: 1.0
  Author: Atomix Studios
  Author URI: http://www.atomixstudios.com
 */


class Bs_Mail_Popup {
    public function Bs_Instance() {
      include dirname(__FILE__) . '/shortcode.php';
      include dirname(__FILE__) . '/inc/class.setting-api.php';
      include dirname(__FILE__) . '/inc/bs-setting.php';
    }

    public function Bs_GetInstance() {
        $this->Bs_Instance();
    }

}



$var = new Bs_Mail_Popup();

$var->Bs_GetInstance();