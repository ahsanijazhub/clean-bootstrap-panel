<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model {

    protected $table = 'customers';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all customers with pagination and filters
     */
    public function get_all($limit = null, $offset = 0, $filters = [], $company_id = null)
    {
        // Select only the columns we need
        $this->db->select('
            customers.id,
            customers.company_id,
            customers.selected_agreement_id,
            customers.selected_vehicle_id,
            customers.email,
            customers.phone,
            customers.first_name,
            customers.last_name,
            customers.date_of_birth,
            customers.license_number,
            customers.license_issue_date,
            customers.license_expiry_date,
            customers.license_issuing_state,
            customers.license_type,
            customers.address,
            customers.postal_code,
            customers.customer_state,
            customers.driving_license_front,
            customers.driving_license_back,
            customers.is_profile_completed,
            customers.is_active,
            customers.last_login,
            customers.created_at,
            customers.updated_at,
            companies.company_name
        ');
        $this->db->from('customers');
        $this->db->join('companies', 'companies.id = customers.company_id', 'left');
        $this->db->where('customers.deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('first_name', $filters['search'])
                     ->or_like('last_name', $filters['search'])
                     ->or_like('email', $filters['search'])
                     ->or_like('phone', $filters['search'])
                     ->or_like('license_number', $filters['search'])
                     ->group_end();
        }

        if (isset($filters['is_profile_completed']) && $filters['is_profile_completed'] !== '') {
            $this->db->where('is_profile_completed', $filters['is_profile_completed']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', $filters['is_active']);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        $results = $this->db->order_by('created_at', 'DESC')
                        ->get()
                        ->result();
        
        // Compute name field from first_name and last_name for views
        foreach ($results as $customer) {
            $customer->name = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
        }
        
        return $results;
    }

    /**
     * Count all customers
     */
    public function count_all($filters = [], $company_id = null)
    {
        $this->db->from($this->table)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('first_name', $filters['search'])
                     ->or_like('last_name', $filters['search'])
                     ->or_like('email', $filters['search'])
                     ->or_like('phone', $filters['search'])
                     ->or_like('license_number', $filters['search'])
                     ->group_end();
        }

        if (isset($filters['is_profile_completed']) && $filters['is_profile_completed'] !== '') {
            $this->db->where('is_profile_completed', $filters['is_profile_completed']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', $filters['is_active']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get customer by ID
     */
    public function get_by_id($id, $company_id = null)
    {
        // Select only the columns we need
        $this->db->select('
            customers.id,
            customers.company_id,
            customers.selected_agreement_id,
            customers.selected_vehicle_id,
            customers.email,
            customers.phone,
            customers.password,
            customers.first_name,
            customers.last_name,
            customers.date_of_birth,
            customers.license_number,
            customers.license_issue_date,
            customers.license_expiry_date,
            customers.license_issuing_state,
            customers.license_type,
            customers.address,
            customers.postal_code,
            customers.customer_state,
            customers.driving_license_front,
            customers.driving_license_back,
            customers.token,
            customers.token_expires_at,
            customers.is_profile_completed,
            customers.is_active,
            customers.last_login,
            customers.created_at,
            customers.updated_at
        ');
        $this->db->where('id', $id)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        return $this->db->get($this->table)->row();
    }

    /**
     * Get customer by email
     */
    public function get_by_email($email, $company_id = null)
    {
        $this->db->select('
            customers.id,
            customers.company_id,
            customers.email,
            customers.phone,
            customers.password,
            customers.first_name,
            customers.last_name,
            customers.is_active,
            customers.is_profile_completed,
            customers.created_at
        ');
        $this->db->where('email', $email)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        return $this->db->get($this->table)->row();
    }

    /**
     * Get customer by token
     */
    public function get_by_token($token)
    {
        // Clean the token (remove any spaces or special characters)
        if (empty($token)) {
            return null;
        }
        $token = trim(preg_replace('/\s+/', '', $token));

        $this->db->select('
            customers.id,
            customers.company_id,
            customers.email,
            customers.phone,
            customers.first_name,
            customers.last_name,
            customers.is_active,
            customers.is_profile_completed,
            customers.token,
            customers.token_expires_at
        ');
        return $this->db->where('token', $token)
                        ->where('deleted_at', NULL)
                        ->where('token_expires_at >', date('Y-m-d H:i:s'))
                        ->get($this->table)
                        ->row();
    }

    /**
     * Get customer by token (ignore expiry - for debugging)
     */
    public function get_by_token_any($token)
    {
        $token = trim(preg_replace('/\s+/', '', $token));

        $this->db->select('
            customers.id,
            customers.company_id,
            customers.email,
            customers.phone,
            customers.first_name,
            customers.last_name,
            customers.is_active,
            customers.is_profile_completed,
            customers.token,
            customers.token_expires_at
        ');
        return $this->db->where('token', $token)
                        ->where('deleted_at', NULL)
                        ->get($this->table)
                        ->row();
    }

    /**
     * Check if email exists
     */
    public function email_exists($email, $company_id, $exclude_id = null)
    {
        $this->db->where('email', $email)
                 ->where('company_id', $company_id)
                 ->where('deleted_at', NULL);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Check if email exists globally (across all companies)
     */
    public function email_exists_global($email, $exclude_id = null)
    {
        $this->db->where('email', $email)
                 ->where('deleted_at', NULL);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Create customer and wallet
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $this->db->insert($this->table, $data);
        $customer_id = $this->db->insert_id();

        // Auto-create wallet for customer
        $this->load->model('Customer_wallet_model');
        $this->Customer_wallet_model->create($customer_id);

        $this->db->trans_complete();

        return $customer_id;
    }

    /**
     * Update customer
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Soft delete customer
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Generate unique token for email verification
     */
    public function generate_token()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Set token for customer
     */
    public function set_token($id, $token)
    {
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

        return $this->update($id, [
            'token' => $token,
            'token_expires_at' => $expires_at
        ]);
    }

    /**
     * Clear token after use
     */
    public function clear_token($id)
    {
        return $this->update($id, [
            'token' => null,
            'token_expires_at' => null
        ]);
    }

    /**
     * Mark profile as completed
     */
    public function complete_profile($id, $data)
    {
        $data['is_profile_completed'] = 1;
        // Don't clear the token - customer needs it to set password
        // Token will only be cleared after password is set

        return $this->update($id, $data);
    }

    /**
     * Get customers by company ID
     */
    public function get_by_company($company_id)
    {
        $this->db->select('
            customers.id,
            customers.company_id,
            customers.email,
            customers.phone,
            customers.first_name,
            customers.last_name,
            customers.is_active,
            customers.is_profile_completed,
            customers.created_at
        ');
        return $this->db->where('company_id', $company_id)
                        ->where('deleted_at', NULL)
                        ->order_by('first_name', 'ASC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Count customers by company
     */
    public function count_by_company($company_id)
    {
        return $this->db->where('company_id', $company_id)
                        ->where('deleted_at', NULL)
                        ->count_all_results($this->table);
    }

    /**
     * Count pending profiles (not completed)
     */
    public function count_pending($company_id = null)
    {
        $this->db->where('is_profile_completed', 0)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        return $this->db->count_all_results($this->table);
    }

    /**
     * Update password
     */
    public function update_password($id, $password)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        return $this->update($id, ['password' => $hashed_password]);
    }

    /**
     * Check if customer has password
     */
    public function has_password($id)
    {
        $customer = $this->get_by_id($id);
        return !empty($customer->password);
    }

    /**
     * Get customer wallet balance
     */
    public function get_wallet_balance($id)
    {
        $this->load->model('Customer_wallet_model');
        return $this->Customer_wallet_model->get_balance($id);
    }

    /**
     * Get customer wallet
     */
    public function get_wallet($id)
    {
        $this->load->model('Customer_wallet_model');
        return $this->Customer_wallet_model->get_by_customer($id);
    }
}
