<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_model extends CI_Model {

    protected $table = 'permissions';
    protected $groups_table = 'permission_groups';

    // ==================== PERMISSIONS ====================

    /**
     * Get all permissions with group info
     */
    public function get_all()
    {
        return $this->db->select('permissions.*, permission_groups.perm_group_name, permission_groups.perm_group_slug')
                        ->from($this->table)
                        ->join('permission_groups', 'permission_groups.id = permissions.permission_group_id', 'left')
                        ->order_by('permission_groups.perm_group_name', 'ASC')
                        ->order_by('permissions.perm_name', 'ASC')
                        ->get()
                        ->result();
    }

    /**
     * Get permissions grouped by their group
     */
    public function get_all_grouped()
    {
        $permissions = $this->get_all();
        $grouped = [];

        foreach ($permissions as $perm) {
            $group_name = $perm->perm_group_name ?? 'Uncategorized';
            if (!isset($grouped[$group_name])) {
                $grouped[$group_name] = [];
            }
            $grouped[$group_name][] = $perm;
        }

        return $grouped;
    }

    /**
     * Get permission by ID
     */
    public function get_by_id($id)
    {
        return $this->db->select('permissions.*, permission_groups.perm_group_name')
                        ->from($this->table)
                        ->join('permission_groups', 'permission_groups.id = permissions.permission_group_id', 'left')
                        ->where('permissions.id', $id)
                        ->get()
                        ->row();
    }

    /**
     * Create permission
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update permission
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Delete permission
     */
    public function delete($id)
    {
        // Also removes from role_permissions due to CASCADE
        return $this->db->where('id', $id)->delete($this->table);
    }

    /**
     * Check if slug exists
     */
    public function slug_exists($slug, $exclude_id = null)
    {
        $this->db->where('perm_slug', $slug);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    // ==================== PERMISSION GROUPS ====================

    /**
     * Get all groups
     */
    public function get_all_groups()
    {
        return $this->db->order_by('perm_group_name', 'ASC')
                        ->get($this->groups_table)
                        ->result();
    }

    /**
     * Get group by ID
     */
    public function get_group_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->get($this->groups_table)
                        ->row();
    }

    /**
     * Create group
     */
    public function create_group($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->groups_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update group
     */
    public function update_group($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->groups_table, $data);
    }

    /**
     * Delete group (only if no permissions use it)
     */
    public function delete_group($id)
    {
        // Check if group has permissions
        $count = $this->db->where('permission_group_id', $id)
                          ->count_all_results($this->table);

        if ($count > 0) {
            return false;
        }

        return $this->db->where('id', $id)->delete($this->groups_table);
    }

    /**
     * Count permissions in a group
     */
    public function count_permissions_in_group($group_id)
    {
        return $this->db->where('permission_group_id', $group_id)
                        ->count_all_results($this->table);
    }

    /**
     * Check if group slug exists
     */
    public function group_slug_exists($slug, $exclude_id = null)
    {
        $this->db->where('perm_group_slug', $slug);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->groups_table) > 0;
    }
}
