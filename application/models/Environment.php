<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Environment extends OP_Model
{
    protected $table_name = 'environment';
    protected $primary = '';
    protected $primary_name = 'id';
    protected $display_primary_name = 'env_id';
}