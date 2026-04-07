<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Auth_lib');
        $this->auth_lib->require_login();
    }

    public function index()
    {
        $data = [
            'title' => 'Settings',
            'page' => 'settings/index',
            'page_title' => 'Settings',
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Settings', 'url' => 'settings']
            ]
        ];

        $this->load->view('admin/index', $data);
    }
}