<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    protected $table = 'roles';

    /**
     * Get all roles (exclude super-admin role by default)
     */
    public function get_all($include_superadmin = false)
    {
        $this->db->where('deleted_at', NULL);

        if (!$include_superadmin) {
            $this->db->where('role_slug !=', 'super-admin');
        }

        return $this->db->order_by('role_name', 'ASC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Get role by ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->where('deleted_at', NULL)
                        ->get($this->table)
                        ->row();
    }

    /**
     * Check if role is superadmin
     */
    public function is_superadmin_role($id)
    {
        $role = $this->get_by_id($id);
        return $role && $role->role_slug === 'super-admin';
    }

    /**
     * Create role
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update role
     */
    public function update($id, $data)
    {
        // Prevent updating superadmin role
        if ($this->is_superadmin_role($id)) {
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Soft delete role
     */
    public function delete($id)
    {
        // Prevent deleting superadmin role
        if ($this->is_superadmin_role($id)) {
            return false;
        }

        return $this->db->where('id', $id)
                        ->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get permission IDs for a role
     */
    public function get_permission_ids($role_id)
    {
        $result = $this->db->select('permission_id')
                           ->where('role_id', $role_id)
                           ->get('role_permissions')
                           ->result_array();

        return array_column($result, 'permission_id');
    }

    /**
     * Sync permissions for a role
     */
    public function sync_permissions($role_id, $permission_ids = [])
    {
        // Don't allow modifying superadmin permissions
        if ($this->is_superadmin_role($role_id)) {
            return false;
        }

        // Remove existing permissions
        $this->db->where('role_id', $role_id)->delete('role_permissions');

        // Add new permissions
        if (!empty($permission_ids)) {
            $batch = [];
            foreach ($permission_ids as $perm_id) {
                $batch[] = [
                    'role_id' => $role_id,
                    'permission_id' => (int) $perm_id
                ];
            }
            $this->db->insert_batch('role_permissions', $batch);
        }

        return true;
    }

    /**
     * Count users with this role
     */
    public function count_users($role_id)
    {
        return $this->db->where('role_id', $role_id)
                        ->where('deleted_at', NULL)
                        ->count_all_results('admin_users');
    }

    /**
     * Check if slug exists
     */
    public function slug_exists($slug, $exclude_id = null)
    {
        $this->db->where('role_slug', $slug)
                 ->where('deleted_at', NULL);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }
}
