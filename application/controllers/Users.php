<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Auth_lib');
        $this->auth_lib->require_login();

        if (!$this->auth_lib->has_permission('users.view')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('dashboard');
        }

        $this->load->model('Admin_user_model');
        $this->load->model('Role_model');
    }

    public function index()
    {
        $limit = 10;
        $offset = $this->input->get('page') ? ($this->input->get('page') - 1) * $limit : 0;

        $filters = [];
        if ($this->input->get('role_id')) {
            $filters['role_id'] = $this->input->get('role_id');
        }
        if ($this->input->get('search')) {
            $filters['search'] = $this->input->get('search');
        }

        $users = $this->Admin_user_model->get_all($limit, $offset, $filters);
        $total_users = $this->Admin_user_model->count_all($filters);

        $this->load->model('Role_model');
        $roles = $this->Role_model->get_all();

        $data = [
            'title' => 'Users',
            'page_title' => 'Users Management',
            'users' => $users,
            'total_users' => $total_users,
            'limit' => $limit,
            'current_page' => $this->input->get('page') ?: 1,
            'total_pages' => ceil($total_users / $limit),
            'filters' => $filters,
            'roles' => $roles
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/users/index', $data);
        $this->load->view('admin/layouts/footer');
    }

    public function create()
    {
        if (!$this->auth_lib->has_permission('users.create')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('users');
        }

        $this->load->model('Role_model');
        $roles = $this->Role_model->get_all();

        $data = [
            'title' => 'Add User',
            'page_title' => 'Add New User',
            'roles' => $roles,
            'is_superadmin' => $this->auth_lib->is_superadmin()
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/users/create', $data);
        $this->load->view('admin/layouts/footer');
    }

    public function store()
    {
        if (!$this->auth_lib->has_permission('users.create')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('users');
        }

        $first_name = trim($this->input->post('first_name', true));
        $last_name = trim($this->input->post('last_name', true));
        $email = trim($this->input->post('email', true));
        $password = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');
        $phone = trim($this->input->post('phone', true));
        $role_id = $this->input->post('role_id');
        $is_active = $this->input->post('is_active') ? 1 : 0;

        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            $this->session->set_flashdata('error', 'Please fill in all required fields');
            redirect('users/create');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Please enter a valid email address');
            redirect('users/create');
        }

        if (strlen($password) < 6) {
            $this->session->set_flashdata('error', 'Password must be at least 6 characters long');
            redirect('users/create');
        }

        if ($password !== $confirm_password) {
            $this->session->set_flashdata('error', 'Passwords do not match');
            redirect('users/create');
        }

        if ($this->Admin_user_model->get_by_email($email)) {
            $this->session->set_flashdata('error', 'Email address already exists');
            redirect('users/create');
        }

        // Only superadmin can assign admin role
        if ($role_id == 1 && !$this->auth_lib->is_superadmin()) {
            $this->session->set_flashdata('error', 'Only admins can create admin users');
            redirect('users/create');
        }

        $user_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'role_id' => $role_id,
            'is_active' => $is_active
        ];

        $user_id = $this->Admin_user_model->create($user_data);

        if ($user_id) {
            $this->session->set_flashdata('success', 'User created successfully');
            redirect('users');
        } else {
            $this->session->set_flashdata('error', 'Failed to create user');
            redirect('users/create');
        }
    }

    public function edit($id)
    {
        if (!$this->auth_lib->has_permission('users.edit')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('users');
        }

        $user = $this->Admin_user_model->get_by_id($id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('users');
        }

        $this->load->model('Role_model');
        $roles = $this->Role_model->get_all();

        $data = [
            'title' => 'Edit User',
            'page_title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
            'is_superadmin' => $this->auth_lib->is_superadmin()
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/users/edit', $data);
        $this->load->view('admin/layouts/footer');
    }

    public function update($id)
    {
        if (!$this->auth_lib->has_permission('users.edit')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('users');
        }

        $user = $this->Admin_user_model->get_by_id($id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('users');
        }

        $first_name = trim($this->input->post('first_name', true));
        $last_name = trim($this->input->post('last_name', true));
        $email = trim($this->input->post('email', true));
        $password = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');
        $phone = trim($this->input->post('phone', true));
        $role_id = $this->input->post('role_id');
        $is_active = $this->input->post('is_active') ? 1 : 0;

        if (empty($first_name) || empty($last_name) || empty($email)) {
            $this->session->set_flashdata('error', 'Please fill in all required fields');
            redirect('users/edit/' . $id);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Please enter a valid email address');
            redirect('users/edit/' . $id);
        }

        $existing_user = $this->Admin_user_model->get_by_email($email);
        if ($existing_user && $existing_user->id != $id) {
            $this->session->set_flashdata('error', 'Email address already exists');
            redirect('users/edit/' . $id);
        }

        // Only superadmin can assign admin role
        if ($role_id == 1 && !$this->auth_lib->is_superadmin()) {
            $this->session->set_flashdata('error', 'Only admins can assign admin role');
            redirect('users/edit/' . $id);
        }

        if (!empty($password)) {
            if (strlen($password) < 6) {
                $this->session->set_flashdata('error', 'Password must be at least 6 characters long');
                redirect('users/edit/' . $id);
            }

            if ($password !== $confirm_password) {
                $this->session->set_flashdata('error', 'Passwords do not match');
                redirect('users/edit/' . $id);
            }
        }

        $user_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'role_id' => $role_id,
            'is_active' => $is_active
        ];

        if (!empty($password)) {
            $user_data['password'] = $password;
        }

        if ($this->Admin_user_model->update($id, $user_data)) {
            $this->session->set_flashdata('success', 'User updated successfully');
            redirect('users');
        } else {
            $this->session->set_flashdata('error', 'Failed to update user');
            redirect('users/edit/' . $id);
        }
    }

    public function delete($id)
    {
        if (!$this->auth_lib->has_permission('users.delete')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('users');
        }

        $user = $this->Admin_user_model->get_by_id($id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('users');
        }

        if ($user->id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot delete your own account');
            redirect('users');
        }

        if ($user->is_superadmin == 1) {
            $this->session->set_flashdata('error', 'Cannot delete superadmin account');
            redirect('users');
        }

        if ($this->Admin_user_model->delete($id)) {
            $this->session->set_flashdata('success', 'User deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete user');
        }

        redirect('users');
    }
}