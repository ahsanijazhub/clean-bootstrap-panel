<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Auth_lib');
        $this->auth_lib->require_login();
    }

    /**
     * Dashboard index page
     */
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'page_title' => 'Dashboard',
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Dashboard', 'url' => '']
            ]
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/dashboard/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }
}