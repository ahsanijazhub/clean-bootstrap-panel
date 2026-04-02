<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rental_agreement_model extends CI_Model {

    protected $table = 'rental_agreements';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all agreements with pagination and filters
     */
    public function get_all($limit = null, $offset = 0, $filters = [], $company_id = null)
    {
        $this->db->reset_query();
        
        $this->db->select('rental_agreements.*, CONCAT(customers.first_name, " ", customers.last_name) as customer_name, customers.email as customer_email, companies.company_name, vehicles.vehicle_name, vehicles.license_plate')
                ->from($this->table)
                ->join('customers', 'customers.id = rental_agreements.customer_id', 'left')
                ->join('companies', 'companies.id = rental_agreements.company_id', 'left')
                ->join('vehicles', 'vehicles.id = rental_agreements.vehicle_id', 'left')
                ->where('rental_agreements.deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('rental_agreements.company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('rental_agreements.company_id', $filters['company_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('agreement_number', $filters['search'])
                     ->or_like('customer_name', $filters['search'])
                     ->or_like('customers.email', $filters['search'])
                     ->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('rental_agreements.status', $filters['status']);
        }

        if (!empty($filters['customer_id'])) {
            $this->db->where('rental_agreements.customer_id', $filters['customer_id']);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->order_by('rental_agreements.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    /**
     * Count all agreements
     */
    public function count_all($filters = [], $company_id = null)
    {
        $this->db->from($this->table)
                 ->join('customers', 'customers.id = rental_agreements.customer_id', 'left')
                 ->where('rental_agreements.deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('rental_agreements.company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('rental_agreements.company_id', $filters['company_id']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('rental_agreements.status', $filters['status']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get agreement by ID with full details
     */
    public function get_by_id($id, $company_id = null)
    {
        $this->db->select('rental_agreements.*, CONCAT(customers.first_name, " ", customers.last_name) as customer_name, customers.email as customer_email, customers.phone as customer_phone, customers.driving_license_front, customers.driving_license_back, companies.company_name, companies.company_email, companies.company_phone, companies.company_address, companies.company_logo, vehicles.vehicle_name, vehicles.vehicle_make, vehicles.vehicle_model, vehicles.vehicle_year, vehicles.license_plate, vehicles.vehicle_color, vehicles.vin_number', FALSE)
                 ->from($this->table)
                 ->join('customers', 'customers.id = rental_agreements.customer_id', 'left')
                 ->join('companies', 'companies.id = rental_agreements.company_id', 'left')
                 ->join('vehicles', 'vehicles.id = rental_agreements.vehicle_id', 'left')
                 ->join('admin_users as reviewer', 'reviewer.id = rental_agreements.reviewed_by', 'left')
                 ->where('rental_agreements.id', $id)
                 ->where('rental_agreements.deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('rental_agreements.company_id', $company_id);
        }

        return $this->db->get()->row();
    }

    /**
     * Get agreement by customer ID
     */
    public function get_by_customer($customer_id)
    {
        $this->db->select('rental_agreements.*, vehicles.vehicle_name, vehicles.vehicle_make, vehicles.vehicle_model, vehicles.license_plate')
                 ->from($this->table)
                 ->join('vehicles', 'vehicles.id = rental_agreements.vehicle_id', 'left')
                 ->where('customer_id', $customer_id)
                 ->where('rental_agreements.deleted_at', NULL)
                 ->order_by('rental_agreements.created_at', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Get latest agreement by customer ID
     */
    public function get_latest_by_customer($customer_id)
    {
        return $this->db->where('customer_id', $customer_id)
                        ->where('deleted_at', NULL)
                        ->order_by('created_at', 'DESC')
                        ->get($this->table)
                        ->row();
    }

    /**
     * Check if agreement number exists
     */
    public function agreement_number_exists($agreement_number, $exclude_id = null)
    {
        $this->db->where('agreement_number', $agreement_number)
                 ->where('deleted_at', NULL);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Generate unique agreement number
     */
    public function generate_agreement_number($company_id)
    {
        do {
            $number = 'AGR-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while ($this->agreement_number_exists($number));

        return $number;
    }

    /**
     * Create agreement
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update agreement
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Soft delete agreement
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Approve agreement
     */
    public function approve($id, $reviewed_by, $notes = null)
    {
        $data = [
            'status' => 'approved',
            'reviewed_by' => $reviewed_by,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'admin_notes' => $notes
        ];

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Reject agreement
     */
    public function reject($id, $reviewed_by, $notes = null)
    {
        $data = [
            'status' => 'rejected',
            'reviewed_by' => $reviewed_by,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'admin_notes' => $notes
        ];

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Sign agreement
     */
    public function sign($id, $signature)
    {
        $data = [
            'status' => 'signed',
            'customer_signature' => $signature,
            'signed_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Assign vehicle to agreement
     */
    public function assign_vehicle($id, $vehicle_id)
    {
        $data = [
            'vehicle_id' => $vehicle_id,
            'vehicle_assigned_at' => date('Y-m-d H:i:s'),
            'status' => 'active'
        ];

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Get count by status
     */
    public function count_by_status($status, $company_id = null)
    {
        $this->db->where('status', $status)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        return $this->db->count_all_results($this->table);
    }

    /**
     * Get approved agreements without vehicle assigned
     */
    public function get_approved_without_vehicle($company_id = null)
    {
        $this->db->select('rental_agreements.*, CONCAT(customers.first_name, " ", customers.last_name) as customer_name, customers.email as customer_email, customers.phone as customer_phone')
                 ->from($this->table)
                 ->join('customers', 'customers.id = rental_agreements.customer_id', 'left')
                 ->where('rental_agreements.status', 'approved')
                 ->where('rental_agreements.vehicle_id', NULL)
                 ->where('rental_agreements.deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('rental_agreements.company_id', $company_id);
        }

        return $this->db->order_by('rental_agreements.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    /**
     * Get pending vehicle assignment count (approved agreements without vehicle)
     */
    public function count_pending_vehicle_assignment($company_id = null)
    {
        $this->db->where('status', 'approved')
                 ->where('vehicle_id', NULL)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        return $this->db->count_all_results($this->table);
    }

    /**
     * Get pending agreements count
     */
    public function count_pending($company_id = null)
    {
        return $this->count_by_status('pending', $company_id);
    }

    /**
     * Get approved agreements count
     */
    public function count_approved($company_id = null)
    {
        return $this->count_by_status('approved', $company_id);
    }

    /**
     * Get active agreements count
     */
    public function count_active($company_id = null)
    {
        return $this->count_by_status('active', $company_id);
    }

    /**
     * Get agreements with vehicles assigned (only approved/active agreements)
     */
    public function get_assigned_vehicles($company_id = null, $limit = null, $offset = 0)
    {
        $this->db->select('rental_agreements.*, CONCAT(customers.first_name, " ", customers.last_name) as customer_name, customers.email as customer_email, customers.phone as customer_phone, vehicles.vehicle_name, vehicles.vehicle_make, vehicles.vehicle_model, vehicles.vehicle_year, vehicles.license_plate, vehicles.vehicle_color')
                 ->from($this->table)
                 ->join('customers', 'customers.id = rental_agreements.customer_id', 'left')
                 ->join('vehicles', 'vehicles.id = rental_agreements.vehicle_id', 'left')
                 ->where('rental_agreements.vehicle_id IS NOT NULL', NULL, FALSE)
                 ->where('rental_agreements.deleted_at', NULL)
                 ->where_in('rental_agreements.status', ['approved', 'active']);

        if ($company_id !== null) {
            $this->db->where('rental_agreements.company_id', $company_id);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->order_by('rental_agreements.vehicle_assigned_at', 'DESC')
                        ->get()
                        ->result();
    }

    /**
     * Count agreements with vehicles assigned (only approved/active)
     */
    public function count_assigned_vehicles($company_id = null)
    {
        $this->db->where('vehicle_id IS NOT NULL', NULL, FALSE)
                 ->where('deleted_at', NULL)
                 ->where_in('status', ['approved', 'active']);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        return $this->db->count_all_results($this->table);
    }

    /**
     * Sign agreement by customer (customer action)
     */
    public function sign_by_customer($id, $customer_id, $signature = null)
    {
        $this->db->where('id', $id)
                 ->where('customer_id', $customer_id)
                 ->where('status', 'pending')
                 ->where('deleted_at', NULL);

        $update_data = [
            'status' => 'signed',
            'signed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Save customer signature if provided
        if (!empty($signature)) {
            $update_data['customer_signature'] = $signature;
        }

        return $this->db->update($this->table, $update_data);
    }

    /**
     * Reject agreement by customer (customer action)
     */
    public function reject_by_customer($id, $customer_id, $rejection_remarks)
    {
        $this->db->where('id', $id)
                 ->where('customer_id', $customer_id)
                 ->where('status', 'pending')
                 ->where('deleted_at', NULL);

        $update_data = [
            'status' => 'rejected',
            'rejected_at' => date('Y-m-d H:i:s'),
            'rejection_remarks' => $rejection_remarks,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->update($this->table, $update_data);
    }

    /**
     * Get agreement by agreement number
     */
    public function get_by_agreement_number($agreement_number)
    {
        return $this->db->where('agreement_number', $agreement_number)
                       ->where('deleted_at', NULL)
                       ->get($this->table)
                       ->row();
    }

    /**
     * Get agreements by vehicle ID
     */
    public function get_agreements_by_vehicle($vehicle_id)
    {
        $this->db->select('rental_agreements.*, CONCAT(customers.first_name, " ", customers.last_name) as customer_name, customers.email as customer_email, customers.phone as customer_phone')
                 ->from($this->table)
                 ->join('customers', 'customers.id = rental_agreements.customer_id', 'left')
                 ->where('rental_agreements.vehicle_id', $vehicle_id)
                 ->where('rental_agreements.deleted_at', NULL)
                 ->order_by('rental_agreements.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get agreement statistics by customer ID
     */
    public function get_customer_stats($customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('deleted_at', NULL);
        
        $total = $this->db->count_all_results($this->table);
        
        // Get count by status
        $statuses = ['pending', 'signed', 'checked_in', 'active', 'checked_out', 'completed', 'cancelled'];
        $stats = ['total' => $total];
        
        foreach ($statuses as $status) {
            $this->db->where('customer_id', $customer_id);
            $this->db->where('status', $status);
            $this->db->where('deleted_at', NULL);
            $stats[$status] = $this->db->count_all_results($this->table);
        }
        
        return (object) $stats;
    }

    /**
     * Get active/checked_in agreements with customer info for invoice generation
     */
    public function get_active_agreements($company_id = null)
    {
        $this->db->select('rental_agreements.id, rental_agreements.agreement_number, rental_agreements.status, CONCAT(customers.first_name, " ", customers.last_name) as customer_name, customers.email as customer_email, vehicles.vehicle_name, vehicles.license_plate')
                 ->from($this->table)
                 ->join('customers', 'customers.id = rental_agreements.customer_id', 'left')
                 ->join('vehicles', 'vehicles.id = rental_agreements.vehicle_id', 'left')
                 ->where_in('rental_agreements.status', ['active', 'checked_in'])
                 ->where('rental_agreements.deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('rental_agreements.company_id', $company_id);
        }

        return $this->db->order_by('rental_agreements.created_at', 'DESC')
                        ->get()
                        ->result();
    }
}
