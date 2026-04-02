<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Auth_lib');
        $this->auth_lib->require_login();

        // Check if user has permission to view users
        if (!$this->auth_lib->has_permission('users.view')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('dashboard');
        }

        $this->load->model('Admin_user_model');
        $this->load->model('Role_model');
        $this->load->model('Company_model'); // Assuming this exists or will be created
    }

    /**
     * List all users
     */
    public function index()
    {
        $limit = 10;
        $offset = $this->input->get('page') ? ($this->input->get('page') - 1) * $limit : 0;

        $filters = [];
        if ($this->input->get('company_id')) {
            $filters['company_id'] = $this->input->get('company_id');
        }
        if ($this->input->get('role_id')) {
            $filters['role_id'] = $this->input->get('role_id');
        }
        if ($this->input->get('search')) {
            $filters['search'] = $this->input->get('search');
        }

        // Filter by user's company if not superadmin
        $current_user = $this->auth_lib->user;
        if (!$this->auth_lib->is_superadmin() && $current_user->company_id) {
            $filters['company_id'] = $current_user->company_id;
        }

        $users = $this->Admin_user_model->get_all($limit, $offset, $filters);
        $total_users = $this->Admin_user_model->count_all($filters);

        // Get roles and companies for filters
        $this->load->model('Role_model');
        $this->load->model('Company_model');
        $roles = $this->Role_model->get_all();

        // Show all companies for superadmin, only user's company for company admins
        if ($this->auth_lib->is_superadmin()) {
            $companies = $this->Company_model->get_all();
        } else {
            $companies = $current_user->company_id ?
                [$this->Company_model->get_by_id($current_user->company_id)] :
                [];
        }

        $data = [
            'title' => 'Users',
            'page_title' => 'Users Management',
            'users' => $users,
            'total_users' => $total_users,
            'limit' => $limit,
            'current_page' => $this->input->get('page') ?: 1,
            'total_pages' => ceil($total_users / $limit),
            'filters' => $filters,
            'roles' => $roles,
            'companies' => array_filter($companies), // Remove null values
            'is_superadmin' => $this->auth_lib->is_superadmin()
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/users/index', $data);
        $this->load->view('admin/layouts/footer');
    }

    /**
     * Create user form
     */
    public function create()
    {
        // Check create permission
        if (!$this->auth_lib->has_permission('users.create')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('users');
        }

        $this->load->model('Role_model');
        $this->load->model('Company_model');
        $roles = $this->Role_model->get_all();

        // Show all companies for superadmin, only user's company for company admins
        $current_user = $this->auth_lib->user;
        if ($this->auth_lib->is_superadmin()) {
            $companies = $this->Company_model->get_all();
        } else {
            $companies = $current_user->company_id ?
                [$this->Company_model->get_by_id($current_user->company_id)] :
                [];
        }

        $data = [
            'title' => 'Add User',
            'page_title' => 'Add New User',
            'roles' => $roles,
            'companies' => array_filter($companies), // Remove null values
            'is_superadmin' => $this->auth_lib->is_superadmin(),
            'current_user_company_id' => $current_user->company_id
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/users/create', $data);
        $this->load->view('admin/layouts/footer');
    }

    /**
     * Store new user
     */
    public function store()
    {
        // Check create permission
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
        $company_id = $this->input->post('company_id');
        $is_active = $this->input->post('is_active') ? 1 : 0;

        // Validation
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

        // Check if email exists
        if ($this->Admin_user_model->get_by_email($email)) {
            $this->session->set_flashdata('error', 'Email address already exists');
            redirect('users/create');
        }

        // For company admins, force their company assignment
        $current_user = $this->auth_lib->user;
        if (!$this->auth_lib->is_superadmin() && $current_user->company_id) {
            $company_id = $current_user->company_id;
        }

        // Create user
        $user_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'role_id' => $role_id,
            'company_id' => $company_id ?: null,
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

    /**
     * Edit user form
     */
    public function edit($id)
    {
        // Check edit permission
        if (!$this->auth_lib->has_permission('users.edit')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('users');
        }

        $user = $this->Admin_user_model->get_by_id($id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('users');
        }

        // Check if company admin can edit this user
        $current_user = $this->auth_lib->user;
        if (!$this->auth_lib->is_superadmin() && $current_user->company_id) {
            if ($user->company_id != $current_user->company_id) {
                $this->session->set_flashdata('error', 'You can only edit users from your company');
                redirect('users');
            }
        }

        $this->load->model('Role_model');
        $this->load->model('Company_model');
        $roles = $this->Role_model->get_all();

        // Show all companies for superadmin, only user's company for company admins
        if ($this->auth_lib->is_superadmin()) {
            $companies = $this->Company_model->get_all();
        } else {
            $companies = $current_user->company_id ?
                [$this->Company_model->get_by_id($current_user->company_id)] :
                [];
        }

        $data = [
            'title' => 'Edit User',
            'page_title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
            'companies' => array_filter($companies), // Remove null values
            'is_superadmin' => $this->auth_lib->is_superadmin(),
            'current_user_company_id' => $current_user->company_id
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/users/edit', $data);
        $this->load->view('admin/layouts/footer');
    }

    /**
     * Update user
     */
    public function update($id)
    {
        // Check edit permission
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
        $company_id = $this->input->post('company_id');
        $is_active = $this->input->post('is_active') ? 1 : 0;

        // Validation
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $this->session->set_flashdata('error', 'Please fill in all required fields');
            redirect('users/edit/' . $id);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Please enter a valid email address');
            redirect('users/edit/' . $id);
        }

        // Check if email exists (excluding current user)
        $existing_user = $this->Admin_user_model->get_by_email($email);
        if ($existing_user && $existing_user->id != $id) {
            $this->session->set_flashdata('error', 'Email address already exists');
            redirect('users/edit/' . $id);
        }

        // Password validation if provided
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

        // For company admins, force their company assignment
        $current_user = $this->auth_lib->user;
        if (!$this->auth_lib->is_superadmin() && $current_user->company_id) {
            $company_id = $current_user->company_id;
        }

        // Update user
        $user_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'role_id' => $role_id,
            'company_id' => $company_id ?: null,
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

    /**
     * Delete user
     */
    public function delete($id)
    {
        // Check delete permission
        if (!$this->auth_lib->has_permission('users.delete')) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('users');
        }

        $user = $this->Admin_user_model->get_by_id($id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('users');
        }

        // Check if company admin can delete this user
        $current_user = $this->auth_lib->user;
        if (!$this->auth_lib->is_superadmin() && $current_user->company_id) {
            if ($user->company_id != $current_user->company_id) {
                $this->session->set_flashdata('error', 'You can only delete users from your company');
                redirect('users');
            }
        }

        // Prevent deleting self
        if ($user->id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot delete your own account');
            redirect('users');
        }

        // Prevent deleting superadmin
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