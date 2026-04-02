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
            'page_title' => 'Settings'
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/settings/index', $data);
        $this->load->view('admin/layouts/footer');
    }
}