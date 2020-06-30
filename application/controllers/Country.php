<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/***
 * 验证码资源
 */
class Country extends OP_Controller {
    protected $is_auth = false;
    protected $resource_model = 'countrym';
    public function __construct()
    {
        parent::__construct();
    }
}