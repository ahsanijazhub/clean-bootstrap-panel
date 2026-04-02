<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_user_model extends CI_Model {

    protected $table = 'admin_users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get user by email
     */
    public function get_by_email($email)
    {
        return $this->db->where('email', $email)
                        ->where('deleted_at', NULL)
                        ->get($this->table)
                        ->row();
    }

    /**
     * Get user by ID with role info
     */
    public function get_by_id($id)
    {
        return $this->db->select('admin_users.*, roles.role_name, roles.role_slug, companies.company_name')
                        ->from($this->table)
                        ->join('roles', 'roles.id = admin_users.role_id', 'left')
                        ->join('companies', 'companies.id = admin_users.company_id', 'left')
                        ->where('admin_users.id', $id)
                        ->where('admin_users.deleted_at', NULL)
                        ->get()
                        ->row();
    }

    /**
     * Verify login credentials
     */
    public function verify_login($email, $password)
    {
        $user = $this->get_by_email($email);

        if ($user && password_verify($password, $user->password)) {
            if ($user->is_active != 1) {
                return ['success' => false, 'message' => 'Your account is inactive.'];
            }

            // Update last login
            $this->update_last_login($user->id);

            return ['success' => true, 'user' => $user];
        }

        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    /**
     * Update last login timestamp
     */
    public function update_last_login($user_id)
    {
        return $this->db->where('id', $user_id)
                        ->update($this->table, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user permissions
     */
    public function get_user_permissions($user_id)
    {
        $user = $this->get_by_id($user_id);

        if (!$user) {
            return [];
        }

        // Superadmin has all permissions
        if ($user->is_superadmin == 1) {
            return $this->db->select('perm_slug')
                            ->get('permissions')
                            ->result_array();
        }

        return $this->db->select('permissions.perm_slug')
                        ->from('role_permissions')
                        ->join('permissions', 'permissions.id = role_permissions.permission_id')
                        ->where('role_permissions.role_id', $user->role_id)
                        ->get()
                        ->result_array();
    }

    /**
     * Check if user has permission
     */
    public function has_permission($user_id, $permission_slug)
    {
        $user = $this->get_by_id($user_id);

        if (!$user) {
            return false;
        }

        // Superadmin has all permissions
        if ($user->is_superadmin == 1) {
            return true;
        }

        $result = $this->db->select('role_permissions.id')
                           ->from('role_permissions')
                           ->join('permissions', 'permissions.id = role_permissions.permission_id')
                           ->where('role_permissions.role_id', $user->role_id)
                           ->where('permissions.perm_slug', $permission_slug)
                           ->get()
                           ->row();

        return $result ? true : false;
    }

    /**
     * Get all users with pagination
     */
    public function get_all($limit = 10, $offset = 0, $filters = [])
    {
        $this->db->select('admin_users.*, roles.role_name, companies.company_name')
                 ->from($this->table)
                 ->join('roles', 'roles.id = admin_users.role_id', 'left')
                 ->join('companies', 'companies.id = admin_users.company_id', 'left')
                 ->where('admin_users.deleted_at', NULL);

        if (!empty($filters['company_id'])) {
            $this->db->where('admin_users.company_id', $filters['company_id']);
        }

        if (!empty($filters['role_id'])) {
            $this->db->where('admin_users.role_id', $filters['role_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('admin_users.first_name', $filters['search'])
                     ->or_like('admin_users.last_name', $filters['search'])
                     ->or_like('admin_users.email', $filters['search'])
                     ->group_end();
        }

        return $this->db->order_by('admin_users.created_at', 'DESC')
                        ->limit($limit, $offset)
                        ->get()
                        ->result();
    }

    /**
     * Count all users
     */
    public function count_all($filters = [])
    {
        $this->db->from($this->table)
                 ->where('deleted_at', NULL);

        if (!empty($filters['company_id'])) {
            $this->db->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['role_id'])) {
            $this->db->where('role_id', $filters['role_id']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Create user
     */
    public function create($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update user
     */
    public function update($id, $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Soft delete user
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}
