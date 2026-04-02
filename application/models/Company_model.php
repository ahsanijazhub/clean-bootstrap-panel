<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_model extends CI_Model {

    protected $table = 'companies';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all companies with pagination and filters
     */
    public function get_all($limit = null, $offset = 0, $filters = [])
    {
        $this->db->where('deleted_at', NULL);

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('company_name', $filters['search'])
                     ->or_like('company_email', $filters['search'])
                     ->or_like('company_phone', $filters['search'])
                     ->group_end();
        }

        if (!empty($filters['is_active'])) {
            $this->db->where('is_active', $filters['is_active']);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->order_by('company_name', 'ASC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Count all companies
     */
    public function count_all($filters = [])
    {
        $this->db->from($this->table)
                 ->where('deleted_at', NULL);

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('company_name', $filters['search'])
                     ->or_like('company_email', $filters['search'])
                     ->or_like('company_phone', $filters['search'])
                     ->group_end();
        }

        if (!empty($filters['is_active'])) {
            $this->db->where('is_active', $filters['is_active']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get company by ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->where('deleted_at', NULL)
                        ->get($this->table)
                        ->row();
    }

    /**
     * Get company by slug
     */
    public function get_by_slug($slug)
    {
        return $this->db->where('company_slug', $slug)
                        ->where('deleted_at', NULL)
                        ->get($this->table)
                        ->row();
    }

    /**
     * Check if slug exists
     */
    public function slug_exists($slug, $exclude_id = null)
    {
        $this->db->where('company_slug', $slug)
                 ->where('deleted_at', NULL);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Create company
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update company
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Soft delete company
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Count users in company
     */
    public function count_users($company_id)
    {
        return $this->db->where('company_id', $company_id)
                        ->where('deleted_at', NULL)
                        ->count_all_results('admin_users');
    }
}