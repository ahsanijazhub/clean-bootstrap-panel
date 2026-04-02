<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agreement_template_model extends CI_Model {

    protected $table = 'agreement_templates';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all templates with pagination and filters
     */
    public function get_all($limit = null, $offset = 0, $filters = [], $company_id = null)
    {
        $this->db->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        if (!empty($filters['company_id'])) {
            $this->db->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->like('template_name', $filters['search']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', $filters['is_active']);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->order_by('created_at', 'DESC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Count all templates
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
            $this->db->like('template_name', $filters['search']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', $filters['is_active']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get template by ID
     */
    public function get_by_id($id, $company_id = null)
    {
        $this->db->where('id', $id)
                 ->where('deleted_at', NULL);

        if ($company_id !== null) {
            $this->db->where('company_id', $company_id);
        }

        return $this->db->get($this->table)->row();
    }

    /**
     * Get default template for company
     * First checks for company-specific default, then falls back to global default (NULL company_id)
     */
    public function get_default($company_id)
    {
        // First try to get company-specific default
        $template = $this->db->where('company_id', $company_id)
                        ->where('is_default', 1)
                        ->where('is_active', 1)
                        ->where('deleted_at', NULL)
                        ->get($this->table)
                        ->row();
        
        // If no company-specific default, try global default (NULL company_id)
        if (!$template) {
            $template = $this->db->where('company_id', NULL)
                            ->where('is_default', 1)
                            ->where('is_active', 1)
                            ->where('deleted_at', NULL)
                            ->get($this->table)
                            ->row();
        }
        
        return $template;
    }

    /**
     * Get active templates for company
     */
    public function get_active_by_company($company_id)
    {
        return $this->db->where('company_id', $company_id)
                        ->where('is_active', 1)
                        ->where('deleted_at', NULL)
                        ->order_by('template_name', 'ASC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Create template
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Convert empty string to NULL for company_id (global template)
        if (isset($data['company_id']) && $data['company_id'] === '') {
            $data['company_id'] = NULL;
        }

        // If setting as default, unset other defaults first
        if (!empty($data['is_default']) && $data['is_default'] == 1) {
            $this->unset_default($data['company_id']);
        }

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update template
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        // If setting as default, unset other defaults first
        if (!empty($data['is_default']) && $data['is_default'] == 1) {
            $template = $this->get_by_id($id);
            if ($template) {
                $this->unset_default($template->company_id, $id);
            }
        }

        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Soft delete template
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Unset default for all templates of a company
     */
    public function unset_default($company_id, $exclude_id = null)
    {
        $this->db->where('company_id', $company_id);

        if ($exclude_id !== null) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->update($this->table, ['is_default' => 0]);
    }

    /**
     * Set template as default
     */
    public function set_as_default($id, $company_id)
    {
        // Unset other defaults
        $this->unset_default($company_id);

        // Set this as default
        return $this->db->where('id', $id)
                        ->update($this->table, ['is_default' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get available placeholders
     */
    public function get_placeholders()
    {
        return [
            // Customer placeholders
            '{{customer_name}}' => 'Customer full name',
            '{{customer_email}}' => 'Customer email address',
            '{{customer_phone}}' => 'Customer phone number',
            
            // Company placeholders
            '{{company_name}}' => 'Company name',
            '{{company_email}}' => 'Company email',
            '{{company_phone}}' => 'Company phone',
            '{{company_address}}' => 'Company address',
            
            // Agreement placeholders
            '{{date}}' => 'Current date',
            '{{agreement_number}}' => 'Unique agreement number',
            
            // Vehicle placeholders
            '{{vehicle_name}}' => 'Vehicle name',
            '{{vehicle_make}}' => 'Vehicle make',
            '{{vehicle_model}}' => 'Vehicle model',
            '{{vehicle_year}}' => 'Vehicle year',
            '{{vehicle_color}}' => 'Vehicle color',
            '{{vehicle_license_plate}}' => 'License plate number',
            '{{vehicle_vin}}' => 'VIN number',
            '{{vehicle_type}}' => 'Vehicle type (Sedan, SUV, etc.)',
            '{{vehicle_transmission}}' => 'Transmission (Automatic/Manual)',
            '{{vehicle_engine}}' => 'Engine type',
            '{{vehicle_fuel}}' => 'Fuel type',
            '{{vehicle_mileage}}' => 'Current mileage',
            '{{weekly_rent_price}}' => 'Weekly rental price',
        ];
    }

    /**
     * Render template with customer, company and vehicle data
     */
    public function render_template($template_content, $customer, $company, $agreement_number, $vehicle = null, $agreement = null)
    {
        $placeholders = [
            '{{customer_name}}' => (($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
            '{{customer_email}}' => $customer->email ?? '',
            '{{customer_phone}}' => $customer->phone ?? '',
            '{{company_name}}' => $company->company_name ?? '',
            '{{company_email}}' => $company->company_email ?? '',
            '{{company_phone}}' => $company->company_phone ?? '',
            '{{company_address}}' => $company->company_address ?? '',
            '{{date}}' => date('F d, Y'),
            '{{agreement_number}}' => $agreement_number,
            
            // Vehicle placeholders
            '{{vehicle_name}}' => $vehicle->vehicle_name ?? '',
            '{{vehicle_make}}' => $vehicle->vehicle_make ?? '',
            '{{vehicle_model}}' => $vehicle->vehicle_model ?? '',
            '{{vehicle_year}}' => $vehicle->vehicle_year ?? '',
            '{{vehicle_color}}' => $vehicle->vehicle_color ?? '',
            '{{vehicle_license_plate}}' => $vehicle->license_plate ?? '',
            '{{vehicle_vin}}' => $vehicle->vin_number ?? '',
            '{{vehicle_type}}' => $vehicle->vehicle_type ?? '',
            '{{vehicle_transmission}}' => $vehicle->transmission ?? '',
            '{{vehicle_engine}}' => $vehicle->engine_type ?? '',
            '{{vehicle_fuel}}' => $vehicle->fuel_type ?? '',
            '{{vehicle_mileage}}' => $vehicle->mileage ?? '',
            '{{weekly_rent_price}}' => $agreement->rental_price_weekly ?? ($vehicle->weekly_rate ?? ''),
            
            // Rental Details placeholders
            '{{rental_price_weekly}}' => $agreement->rental_price_weekly ?? '',
            '{{rental_price_daily}}' => $agreement->rental_price_daily ?? '',
            '{{security_bond}}' => $agreement->security_bond ?? '',
            '{{insurance_arranged_by}}' => $agreement->insurance_arranged_by ?? '',
            '{{description_terms}}' => $agreement->agreement_description ?? '',
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $template_content);
    }

    /**
     * Count templates by company
     */
    public function count_by_company($company_id)
    {
        return $this->db->where('company_id', $company_id)
                        ->where('deleted_at', NULL)
                        ->count_all_results($this->table);
    }

    /**
     * Get templates for customer dropdown
     * - Generic templates (customer_id = NULL) show for all customers
     * - Customer-specific templates (customer_id = X) show ONLY for that customer
     */
    public function get_templates_for_customer($customer_id, $company_id)
    {
        $this->db->reset_query();
        
        // Get all generic templates for this company (customer_id = NULL)
        $generic_templates = $this->db->where('company_id', $company_id)
                                     ->where('customer_id', NULL)
                                     ->where('is_active', 1)
                                     ->where('deleted_at', NULL)
                                     ->order_by('template_name', 'ASC')
                                     ->get($this->table)
                                     ->result();
        
        // Check if this customer has any specific template
        $customer_template = $this->db->where('customer_id', $customer_id)
                                     ->where('is_active', 1)
                                     ->where('deleted_at', NULL)
                                     ->get($this->table)
                                     ->row();
        
        // If customer has a specific template, add it at the beginning
        if ($customer_template) {
            // Add customer-specific template at the beginning
            array_unshift($generic_templates, $customer_template);
        }
        
        return $generic_templates;
    }

    /**
     * Get template by customer (customer-specific or default)
     */
    public function get_by_customer($customer_id, $company_id)
    {
        // Try to get customer-specific template first
        $template = $this->db->where('customer_id', $customer_id)
                             ->where('is_active', 1)
                             ->where('deleted_at', NULL)
                             ->get($this->table)
                             ->row();

        if ($template) {
            return $template;
        }

        // Fall back to company default
        return $this->get_default($company_id);
    }
}
