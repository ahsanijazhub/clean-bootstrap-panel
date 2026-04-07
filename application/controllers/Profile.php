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
            'page' => 'profile/index',
            'page_title' => 'My Profile',
            'user' => $user,
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'My Profile', 'url' => 'profile']
            ]
        ];

        $this->load->view('admin/index', $data);
    }
}