<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_user_model');
    }

    /**
     * Login page
     */
    public function login()
    {
        // Redirect if already logged in
        if ($this->session->userdata('is_logged_in')) {
            redirect('dashboard');
        }

        $data = [
            'title' => 'Login - SOZO Manager',
            'error' => $this->session->flashdata('error'),
            'success' => $this->session->flashdata('success')
        ];

        $this->load->view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function do_login()
    {
        // Check if AJAX request
        $is_ajax = $this->input->is_ajax_request();

        // Validate input
        $email = $this->input->post('email', true);
        $password = $this->input->post('password');
        $remember = $this->input->post('remember');

        if (empty($email) || empty($password)) {
            if ($is_ajax) {
                return $this->_json_response(false, 'Please enter email and password.');
            }
            $this->session->set_flashdata('error', 'Please enter email and password.');
            redirect('login');
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($is_ajax) {
                return $this->_json_response(false, 'Please enter a valid email address.');
            }
            $this->session->set_flashdata('error', 'Please enter a valid email address.');
            redirect('login');
        }

        // Verify credentials
        $result = $this->Admin_user_model->verify_login($email, $password);

        if (!$result['success']) {
            if ($is_ajax) {
                return $this->_json_response(false, $result['message']);
            }
            $this->session->set_flashdata('error', $result['message']);
            redirect('login');
        }

        // Set session data
        $user = $result['user'];
        $session_data = [
            'user_id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'is_superadmin' => $user->is_superadmin,
            'company_id' => $user->company_id,
            'role_id' => $user->role_id,
            'is_logged_in' => true
        ];

        $this->session->set_userdata($session_data);

        // Get redirect URL
        $redirect_url = $this->session->userdata('redirect_url');
        $this->session->unset_userdata('redirect_url');

        if (empty($redirect_url) || strpos($redirect_url, 'login') !== false) {
            $redirect_url = site_url('dashboard');
        }

        if ($is_ajax) {
            return $this->_json_response(true, 'Login successful!', ['redirect' => $redirect_url]);
        }

        redirect($redirect_url);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }

    /**
     * JSON response helper
     */
    private function _json_response($success, $message, $data = [])
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($response));
    }
}
