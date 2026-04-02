<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accident_model extends CI_Model {

    protected $table = 'accidents';
    protected $third_party_table = 'accident_third_parties';
    protected $third_party_vehicle_table = 'accident_third_party_vehicles';
    protected $images_table = 'accident_images';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all accidents with pagination and filters
     */
    public function get_all($limit = null, $offset = 0, $filters = [], $company_id = null)
    {
        $this->db->select('a.*, c.company_name, v.vehicle_name as vehicle_name, v.license_plate as license_plate');
        $this->db->from('accidents a');
        $this->db->join('companies c', 'c.id = a.company_id', 'left');
        $this->db->join('vehicles v', 'v.id = a.vehicle_id', 'left');
        $this->db->where('a.deleted_at', NULL);

        // If company_id is provided, filter by company (for non-superadmin)
        if ($company_id !== null) {
            $this->db->where('a.company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('a.company_id', $filters['company_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('a.location', $filters['search'])
                     ->or_like('a.description', $filters['search'])
                     ->or_like('v.vehicle_name', $filters['search'])
                     ->or_like('v.license_plate', $filters['search'])
                     ->group_end();
        }

        if (!empty($filters['is_paid'])) {
            $this->db->where('a.is_paid', $filters['is_paid']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('a.accident_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('a.accident_date <=', $filters['date_to']);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->order_by('a.accident_date DESC, a.accident_time DESC')
                        ->get()
                        ->result();
    }

    /**
     * Count all accidents
     */
    public function count_all($filters = [], $company_id = null)
    {
        $this->db->from($this->table . ' a')
                 ->where('a.deleted_at', NULL);

        // If company_id is provided, filter by company (for non-superadmin)
        if ($company_id !== null) {
            $this->db->where('a.company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('a.company_id', $filters['company_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('a.location', $filters['search'])
                     ->or_like('a.description', $filters['search'])
                     ->group_end();
        }

        if (!empty($filters['is_paid'])) {
            $this->db->where('a.is_paid', $filters['is_paid']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get accident by ID
     */
    public function get_by_id($id)
    {
        return $this->db->select('a.*, c.company_name, v.vehicle_name as vehicle_name, v.license_plate as license_plate, v.vehicle_make, v.vehicle_model')
                       ->from('accidents a')
                       ->join('companies c', 'c.id = a.company_id', 'left')
                       ->join('vehicles v', 'v.id = a.vehicle_id', 'left')
                       ->where('a.id', $id)
                       ->where('a.deleted_at', NULL)
                       ->get()
                       ->row();
    }

    /**
     * Create new accident
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update accident
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete accident (soft delete)
     */
    public function delete($id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Get all third parties for an accident
     */
    public function get_third_parties($accident_id)
    {
        return $this->db->select('tp.*, tpv.id as vehicle_id, tpv.registration_number, tpv.vehicle_make, tpv.vehicle_model, tpv.year_of_manufacture, tpv.damage_description, tpv.vehicle_description')
                      ->from($this->third_party_table . ' tp')
                      ->join($this->third_party_vehicle_table . ' tpv', 'tpv.third_party_id = tp.id', 'left')
                      ->where('tp.accident_id', $accident_id)
                      ->where('tp.deleted_at', NULL)
                      ->order_by('tp.id', 'ASC')
                      ->get()
                      ->result();
    }

    /**
     * Get third party by ID
     */
    public function get_third_party_by_id($id)
    {
        return $this->db->from($this->third_party_table)
                       ->where('id', $id)
                       ->where('deleted_at', NULL)
                       ->get()
                       ->row();
    }

    /**
     * Get third party vehicle by ID
     */
    public function get_third_party_vehicle_by_id($id)
    {
        return $this->db->from($this->third_party_vehicle_table)
                       ->where('id', $id)
                       ->where('deleted_at', NULL)
                       ->get()
                       ->row();
    }

    /**
     * Create third party
     */
    public function create_third_party($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->third_party_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update third party
     */
    public function update_third_party($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->third_party_table, $data);
    }

    /**
     * Delete third party (soft delete)
     */
    public function delete_third_party($id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $id);
        return $this->db->update($this->third_party_table, $data);
    }

    /**
     * Create third party vehicle
     */
    public function create_third_party_vehicle($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->third_party_vehicle_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update third party vehicle
     */
    public function update_third_party_vehicle($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->third_party_vehicle_table, $data);
    }

    /**
     * Delete third party vehicle (soft delete)
     */
    public function delete_third_party_vehicle($id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $id);
        return $this->db->update($this->third_party_vehicle_table, $data);
    }

    /**
     * Get all images for an accident
     */
    public function get_images($accident_id)
    {
        return $this->db->from($this->images_table)
                       ->where('accident_id', $accident_id)
                       ->where('deleted_at', NULL)
                       ->order_by('id', 'ASC')
                       ->get()
                       ->result();
    }

    /**
     * Add image to accident
     */
    public function add_image($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->images_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Delete image (soft delete)
     */
    public function delete_image($id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $id);
        return $this->db->update($this->images_table, $data);
    }

    /**
     * Get image by ID
     */
    public function get_image_by_id($id)
    {
        return $this->db->from($this->images_table)
                       ->where('id', $id)
                       ->where('deleted_at', NULL)
                       ->get()
                       ->row();
    }

    /**
     * Get vehicles for a company (for dropdown)
     */
    public function get_vehicles($company_id)
    {
        return $this->db->select('id, vehicle_name, license_plate, vehicle_make, vehicle_model')
                       ->from('vehicles')
                       ->where('company_id', $company_id)
                       ->where('deleted_at', NULL)
                       ->where('is_active', 1)
                       ->order_by('vehicle_name', 'ASC')
                       ->get()
                       ->result();
    }

    /**
     * Get count of third parties for an accident
     */
    public function count_third_parties($accident_id)
    {
        return $this->db->from($this->third_party_table)
                       ->where('accident_id', $accident_id)
                       ->where('deleted_at', NULL)
                       ->count_all_results();
    }

    /**
     * Get count of images for an accident
     */
    public function count_images($accident_id)
    {
        return $this->db->from($this->images_table)
                       ->where('accident_id', $accident_id)
                       ->where('deleted_at', NULL)
                       ->count_all_results();
    }
}