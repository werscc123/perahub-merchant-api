<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language extends OP_Model{
    protected $default_primary_name ='id';
    protected $table_name = 'language';
    protected $primary = 'id';
    protected $display_primary_name = 'lang_id';
}