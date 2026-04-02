<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Auth_lib');
        $this->auth_lib->require_login();

        // Only superadmin can manage roles
        if (!is_superadmin()) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('dashboard');
        }

        $this->load->model('Role_model');
        $this->load->model('Permission_model');
    }

    /**
     * List all roles
     */
    public function index()
    {
        $roles = $this->Role_model->get_all();

        // Add user count for each role
        foreach ($roles as &$role) {
            $role->user_count = $this->Role_model->count_users($role->id);
        }

        $data = [
            'title' => 'Roles',
            'page_title' => 'Roles Management',
            'roles' => $roles
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/roles/index', $data);
        $this->load->view('admin/layouts/footer');
    }

    /**
     * Create role form
     */
    public function create()
    {
        $data = [
            'title' => 'Add Role',
            'page_title' => 'Add New Role',
            'permissions_grouped' => $this->Permission_model->get_all_grouped()
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/roles/create', $data);
        $this->load->view('admin/layouts/footer');
    }

    /**
     * Store new role
     */
    public function store()
    {
        $role_name = trim($this->input->post('role_name', true));
        $role_description = trim($this->input->post('role_description', true));
        $permissions = $this->input->post('permissions');

        // Validation
        if (empty($role_name)) {
            $this->session->set_flashdata('error', 'Role name is required');
            redirect('roles/create');
        }

        $role_slug = generate_slug($role_name);

        // Check if slug exists
        if ($this->Role_model->slug_exists($role_slug)) {
            $this->session->set_flashdata('error', 'A role with this name already exists');
            redirect('roles/create');
        }

        // Create role
        $role_id = $this->Role_model->create([
            'role_name' => $role_name,
            'role_slug' => $role_slug,
            'role_description' => $role_description
        ]);

        // Sync permissions
        if ($role_id && !empty($permissions)) {
            $this->Role_model->sync_permissions($role_id, $permissions);
        }

        $this->session->set_flashdata('success', 'Role created successfully');
        redirect('roles');
    }

    /**
     * Edit role form
     */
    public function edit($id)
    {
        $role = $this->Role_model->get_by_id($id);

        if (!$role) {
            $this->session->set_flashdata('error', 'Role not found');
            redirect('roles');
        }

        // Prevent editing superadmin role
        if ($role->role_slug === 'super-admin') {
            $this->session->set_flashdata('error', 'Cannot edit Super Admin role');
            redirect('roles');
        }

        $data = [
            'title' => 'Edit Role',
            'page_title' => 'Edit Role: ' . $role->role_name,
            'role' => $role,
            'permissions_grouped' => $this->Permission_model->get_all_grouped(),
            'role_permissions' => $this->Role_model->get_permission_ids($id)
        ];

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/roles/edit', $data);
        $this->load->view('admin/layouts/footer');
    }

    /**
     * Update role
     */
    public function update($id)
    {
        $role = $this->Role_model->get_by_id($id);

        if (!$role) {
            $this->session->set_flashdata('error', 'Role not found');
            redirect('roles');
        }

        // Prevent updating superadmin role
        if ($role->role_slug === 'super-admin') {
            $this->session->set_flashdata('error', 'Cannot modify Super Admin role');
            redirect('roles');
        }

        $role_name = trim($this->input->post('role_name', true));
        $role_description = trim($this->input->post('role_description', true));
        $permissions = $this->input->post('permissions');

        // Validation
        if (empty($role_name)) {
            $this->session->set_flashdata('error', 'Role name is required');
            redirect('roles/edit/' . $id);
        }

        // Update role
        $this->Role_model->update($id, [
            'role_name' => $role_name,
            'role_description' => $role_description
        ]);

        // Sync permissions
        $this->Role_model->sync_permissions($id, $permissions ?: []);

        $this->session->set_flashdata('success', 'Role updated successfully');
        redirect('roles');
    }

    /**
     * Delete role
     */
    public function delete($id)
    {
        $role = $this->Role_model->get_by_id($id);

        if (!$role) {
            $this->session->set_flashdata('error', 'Role not found');
            redirect('roles');
        }

        // Prevent deleting superadmin role
        if ($role->role_slug === 'super-admin') {
            $this->session->set_flashdata('error', 'Cannot delete Super Admin role');
            redirect('roles');
        }

        // Check if role has users
        $user_count = $this->Role_model->count_users($id);
        if ($user_count > 0) {
            $this->session->set_flashdata('error', 'Cannot delete role. ' . $user_count . ' user(s) are assigned to this role.');
            redirect('roles');
        }

        $this->Role_model->delete($id);
        $this->session->set_flashdata('success', 'Role deleted successfully');
        redirect('roles');
    }
}
