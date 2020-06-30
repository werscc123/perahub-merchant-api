<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/***
 * 验证码资源
 */
class Captchas extends OP_Controller {
    protected $is_auth = false;
    protected $resource_model = 'captcha';
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('tool');
        $this->load->helper('captcha');
    }
}