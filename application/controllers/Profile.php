<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Auth_lib');
        $this->auth_lib->require_login();
    }

    public function index()
    {
        $user = $this->auth_lib->user;

        $data = [
            'title' => 'My Profile',
            'page_title' => 'My Profile',
            'user' => $user
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/profile/index', $data);
        $this->load->view('admin/layouts/footer');
    }
}