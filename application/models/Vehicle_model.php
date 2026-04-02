<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_model extends CI_Model {

    protected $table = 'vehicles';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all vehicles with pagination and filters
     */
    public function get_all($limit = null, $offset = 0, $filters = [], $company_id = null)
    {
        $this->db->select('v.*, c.company_name');
        $this->db->from('vehicles v');
        $this->db->join('companies c', 'c.id = v.company_id', 'left');
        $this->db->where('v.deleted_at', NULL);

        // If company_id is provided, filter by company (for non-superadmin)
        if ($company_id !== null) {
            $this->db->where('v.company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('v.company_id', $filters['company_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('v.vehicle_name', $filters['search'])
                     ->or_like('v.license_plate', $filters['search'])
                     ->or_like('v.vehicle_make', $filters['search'])
                     ->or_like('v.vehicle_model', $filters['search'])
                     ->or_like('v.vin_number', $filters['search'])
                     ->group_end();
        }

        if (!empty($filters['vehicle_type'])) {
            $this->db->where('v.vehicle_type', $filters['vehicle_type']);
        }

        if (!empty($filters['is_active'])) {
            $this->db->where('v.is_active', $filters['is_active']);
        }

        if (!empty($filters['is_available'])) {
            $this->db->where('v.is_available', $filters['is_available']);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->order_by('v.vehicle_name', 'ASC')
                        ->get()
                        ->result();
    }

    /**
     * Count all vehicles
     */
    public function count_all($filters = [], $company_id = null)
    {
        $this->db->from($this->table)
                 ->where('deleted_at', NULL);

        // If company_id is provided, filter by company (for non-superadmin)
        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('vehicle_name', $filters['search'])
                     ->or_like('license_plate', $filters['search'])
                     ->or_like('vehicle_make', $filters['search'])
                     ->or_like('vehicle_model', $filters['search'])
                     ->or_like('vin_number', $filters['search'])
                     ->group_end();
        }

        if (!empty($filters['vehicle_type'])) {
            $this->db->where('vehicle_type', $filters['vehicle_type']);
        }

        if (!empty($filters['is_active'])) {
            $this->db->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['is_available'])) {
            $this->db->where('is_available', $filters['is_available']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get vehicle by ID
     */
    public function get_by_id($id, $company_id = null)
    {
        $this->db->where('id', $id)
                 ->where('deleted_at', NULL);

        // If company_id is provided, filter by company (for non-superadmin)
        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        return $this->db->get($this->table)->row();
    }

    /**
     * Check if license plate exists
     */
    public function license_plate_exists($license_plate, $company_id = null, $exclude_id = null)
    {
        $this->db->where('license_plate', $license_plate)
                 ->where('deleted_at', NULL);

        // Filter by company if provided
        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Check if VIN number exists
     */
    public function vin_exists($vin_number, $company_id = null, $exclude_id = null)
    {
        if (empty($vin_number)) {
            return false;
        }

        $this->db->where('vin_number', $vin_number)
                 ->where('deleted_at', NULL);

        // Filter by company if provided
        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Create vehicle
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update vehicle
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Soft delete vehicle
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Update vehicle availability
     */
    public function update_availability($id, $is_available)
    {
        $data = [
            'is_available' => $is_available,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Get vehicles by company ID
     */
    public function get_by_company($company_id)
    {
        $this->db->select('v.*, c.company_name, c.company_slug');
        $this->db->from('vehicles v');
        $this->db->join('companies c', 'c.id = v.company_id', 'left');
        $this->db->where('v.deleted_at', NULL);
        
        // If company_id is provided, filter by company
        if ($company_id !== null && $company_id !== '') {
            $this->db->where('v.company_id', $company_id);
        }
        
        return $this->db->order_by('v.vehicle_name', 'ASC')
                        ->get()
                        ->result();
    }

    /**
     * Get available vehicles by company ID
     */
    public function get_available_by_company($company_id)
    {
        $this->db->select('v.*, c.company_name, c.company_slug');
        $this->db->from('vehicles v');
        $this->db->join('companies c', 'c.id = v.company_id', 'left');
        $this->db->where('v.is_available', 1);
        $this->db->where('v.is_active', 1);
        $this->db->where('v.deleted_at', NULL);

        // If company_id is provided, filter by company
        if ($company_id !== null && $company_id !== '') {
            $this->db->where('v.company_id', $company_id);
        }

        return $this->db->order_by('v.vehicle_name', 'ASC')
                        ->get()
                        ->result();
    }

    /**
     * Get vehicle types
     */
    public function get_vehicle_types()
    {
        return ['Sedan', 'SUV', 'Truck', 'Van', 'Minivan', 'Coupe', 'Hatchback', 'Wagon', 'Convertible', 'Pickup', 'Luxury', 'Electric', 'Hybrid'];
    }

    /**
     * Get engine types
     */
    public function get_engine_types()
    {
        return ['Petrol', 'Diesel', 'Electric', 'Hybrid', 'CNG', 'LPG'];
    }

    /**
     * Get transmission types
     */
    public function get_transmission_types()
    {
        return ['Automatic', 'Manual', 'CVT', 'Semi-Automatic', 'Dual-Clutch'];
    }

    /**
     * Bulk insert vehicles (for import)
     */
    public function insert_batch($data)
    {
        if (!empty($data)) {
            // Add timestamps to all records
            $timestamp = date('Y-m-d H:i:s');
            foreach ($data as &$record) {
                $record['created_at'] = $timestamp;
                $record['updated_at'] = $timestamp;
            }
            return $this->db->insert_batch($this->table, $data);
        }
        return false;
    }

    /**
     * Get all vehicles for export
     */
    public function get_all_for_export($company_id = null, $filters = [])
    {
        $this->db->where('deleted_at', NULL);

        // If company_id is provided, filter by company (for non-superadmin)
        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('company_id', $filters['company_id']);
        }

        return $this->db->order_by('id', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get weekly rate
     */
    public function get_weekly_rate($vehicle_id, $company_id = null)
    {
        $this->db->select('weekly_rate')
                 ->where('id', $vehicle_id)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        $result = $this->db->get($this->table)->row();
        return $result ? (float) $result->weekly_rate : 0.00;
    }

    /**
     * Get daily amount from weekly rate
     */
    public function get_daily_amount($vehicle_id, $company_id = null)
    {
        $weekly_rate = $this->get_weekly_rate($vehicle_id, $company_id);
        return round($weekly_rate / 7, 2);
    }

    /**
     * Get rate info with weekly and daily breakdown
     */
    public function get_rate_info($vehicle_id, $company_id = null)
    {
        $this->db->select('weekly_rate')
                 ->where('id', $vehicle_id)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        $result = $this->db->get($this->table)->row();

        if (!$result) {
            return ['weekly' => 0, 'daily' => 0];
        }

        $weekly = (float) $result->weekly_rate;
        $daily = round($weekly / 7, 2);

        return [
            'weekly' => $weekly,
            'daily' => $daily
        ];
    }
}
