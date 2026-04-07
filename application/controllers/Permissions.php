<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Auth_lib');
        $this->auth_lib->require_login();

        // Only superadmin can manage permissions
        if (!is_superadmin()) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('dashboard');
        }

        $this->load->model('Permission_model');
    }

    // ==================== PERMISSIONS ====================

    /**
     * List all permissions
     */
    public function index()
    {
        $data = [
            'title' => 'Permissions',
            'page' => 'permissions/index',
            'page_title' => 'Permissions Management',
            'permissions_grouped' => $this->Permission_model->get_all_grouped(),
            'groups' => $this->Permission_model->get_all_groups(),
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Permissions', 'url' => 'permissions/index']
            ]
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Create permission form
     */
    public function create()
    {
        $data = [
            'title' => 'Add Permission',
            'page' => 'permissions/create',
            'page_title' => 'Add New Permission',
            'groups' => $this->Permission_model->get_all_groups(),
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Permissions', 'url' => site_url('permissions/index')],
                ['title' => 'Add Permission', 'url' => 'permissions/create']
            ]
        ];


        $this->load->view('admin/index', $data);
    }

    /**
     * Store new permission
     */
    public function store()
    {
        $perm_name = trim($this->input->post('perm_name', true));
        $perm_slug = trim($this->input->post('perm_slug', true));
        $group_id = $this->input->post('permission_group_id');

        // Validation
        if (empty($perm_name) || empty($perm_slug) || empty($group_id)) {
            $this->session->set_flashdata('error', 'All fields are required');
            redirect('permissions/create');
        }

        // Check if slug exists
        if ($this->Permission_model->slug_exists($perm_slug)) {
            $this->session->set_flashdata('error', 'A permission with this slug already exists');
            redirect('permissions/create');
        }

        $this->Permission_model->create([
            'perm_name' => $perm_name,
            'perm_slug' => $perm_slug,
            'permission_group_id' => $group_id
        ]);

        $this->session->set_flashdata('success', 'Permission created successfully');
        redirect('permissions');
    }

    /**
     * Edit permission form
     */
    public function edit($id)
    {
        $permission = $this->Permission_model->get_by_id($id);

        if (!$permission) {
            $this->session->set_flashdata('error', 'Permission not found');
            redirect('permissions');
        }

        $data = [
            'title' => 'Edit Permission',
            'page' => 'permissions/edit',
            'page_title' => 'Edit Permission',
            'permission' => $permission,
            'groups' => $this->Permission_model->get_all_groups(),
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Permissions', 'url' => site_url('permissions/index')],
                ['title' => 'Edit Permission', 'url' => 'permissions/edit/' . $id]
            ]
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Update permission
     */
    public function update($id)
    {
        $permission = $this->Permission_model->get_by_id($id);

        if (!$permission) {
            $this->session->set_flashdata('error', 'Permission not found');
            redirect('permissions');
        }

        $perm_name = trim($this->input->post('perm_name', true));
        $perm_slug = trim($this->input->post('perm_slug', true));
        $group_id = $this->input->post('permission_group_id');

        // Validation
        if (empty($perm_name) || empty($perm_slug) || empty($group_id)) {
            $this->session->set_flashdata('error', 'All fields are required');
            redirect('permissions/edit/' . $id);
        }

        // Check if slug exists (excluding current)
        if ($this->Permission_model->slug_exists($perm_slug, $id)) {
            $this->session->set_flashdata('error', 'A permission with this slug already exists');
            redirect('permissions/edit/' . $id);
        }

        $this->Permission_model->update($id, [
            'perm_name' => $perm_name,
            'perm_slug' => $perm_slug,
            'permission_group_id' => $group_id
        ]);

        $this->session->set_flashdata('success', 'Permission updated successfully');
        redirect('permissions');
    }

    /**
     * Delete permission
     */
    public function delete($id)
    {
        $permission = $this->Permission_model->get_by_id($id);

        if (!$permission) {
            $this->session->set_flashdata('error', 'Permission not found');
            redirect('permissions');
        }

        $this->Permission_model->delete($id);
        $this->session->set_flashdata('success', 'Permission deleted successfully');
        redirect('permissions');
    }

    // ==================== PERMISSION GROUPS ====================

    /**
     * List all groups
     */
    public function groups()
    {
        $groups = $this->Permission_model->get_all_groups();

        // Add permission count for each group
        foreach ($groups as &$group) {
            $group->perm_count = $this->Permission_model->count_permissions_in_group($group->id);
        }

        $data = [
            'title' => 'Permission Groups',
            'page' => 'permissions/groups',
            'page_title' => 'Permission Groups',
            'groups' => $groups,
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Permissions', 'url' => site_url('permissions/index')],
                ['title' => 'Permissions Groups', 'url' => site_url('permissions/groups')]
            ]

        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Create group form
     */
    public function create_group()
    {
        $data = [
            'title' => 'Add Group',
            'page' => 'permissions/create_group',
            'page_title' => 'Add Permission Group',
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Permissions', 'url' => site_url('permissions/index')],
                ['title' => 'Permissions Groups', 'url' => site_url('permissions/groups')],
                ['title' => 'Add Permission Group', 'url' => 'permissions/create_group']
            ]
        ];


        $this->load->view('admin/index', $data);
    }

    /**
     * Store new group
     */
    public function store_group()
    {
        $group_name = trim($this->input->post('perm_group_name', true));
        $group_slug = trim($this->input->post('perm_group_slug', true));

        if (empty($group_slug)) {
            $group_slug = generate_slug($group_name);
        }

        // Validation
        if (empty($group_name)) {
            $this->session->set_flashdata('error', 'Group name is required');
            redirect('permissions/create_group');
        }

        // Check if slug exists
        if ($this->Permission_model->group_slug_exists($group_slug)) {
            $this->session->set_flashdata('error', 'A group with this slug already exists');
            redirect('permissions/create_group');
        }

        $this->Permission_model->create_group([
            'perm_group_name' => $group_name,
            'perm_group_slug' => $group_slug
        ]);

        $this->session->set_flashdata('success', 'Permission group created successfully');
        redirect('permissions/groups');
    }

    /**
     * Edit group form
     */
    public function edit_group($id)
    {
        $group = $this->Permission_model->get_group_by_id($id);

        if (!$group) {
            $this->session->set_flashdata('error', 'Group not found');
            redirect('permissions/groups');
        }

        $data = [
            'title' => 'Edit Group',
            'page' => 'permissions/edit_group',
            'page_title' => 'Edit Permission Group',
            'group' => $group,
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Permissions', 'url' => site_url('permissions/index')],
                ['title' => 'Permissions Groups', 'url' => site_url('permissions/groups')],
                ['title' => 'Edit Permission Group', 'url' => 'permissions/edit_group/' . $id]
            ]
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Update group
     */
    public function update_group($id)
    {
        $group = $this->Permission_model->get_group_by_id($id);

        if (!$group) {
            $this->session->set_flashdata('error', 'Group not found');
            redirect('permissions/groups');
        }

        $group_name = trim($this->input->post('perm_group_name', true));

        // Validation
        if (empty($group_name)) {
            $this->session->set_flashdata('error', 'Group name is required');
            redirect('permissions/edit_group/' . $id);
        }

        $this->Permission_model->update_group($id, [
            'perm_group_name' => $group_name
        ]);

        $this->session->set_flashdata('success', 'Permission group updated successfully');
        redirect('permissions/groups');
    }

    /**
     * Delete group
     */
    public function delete_group($id)
    {
        $group = $this->Permission_model->get_group_by_id($id);

        if (!$group) {
            $this->session->set_flashdata('error', 'Group not found');
            redirect('permissions/groups');
        }

        $perm_count = $this->Permission_model->count_permissions_in_group($id);
        if ($perm_count > 0) {
            $this->session->set_flashdata('error', 'Cannot delete group. ' . $perm_count . ' permission(s) belong to this group.');
            redirect('permissions/groups');
        }

        $this->Permission_model->delete_group($id);
        $this->session->set_flashdata('success', 'Permission group deleted successfully');
        redirect('permissions/groups');
    }
}
