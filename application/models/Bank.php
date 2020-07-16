<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends OP_Model{
    protected $table_name = 'user_bank';
    protected $default_primary_name = 'id';
    protected $child_resource_model=[];
}