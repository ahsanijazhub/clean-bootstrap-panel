<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // Simple auth check - just load library and require login
        $this->load->library('Auth_lib');
        $this->auth_lib->require_login();
    }

    /**
     * Dashboard index page
     */
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'page_title' => 'Dashboard',
            'breadcrumb' => [
                ['title' => 'Home', 'url' => site_url('dashboard')],
                ['title' => 'Dashboard', 'url' => '']
            ]
        ];

        // Get some stats for the dashboard
        $data['stats'] = $this->_get_dashboard_stats();
        
        // Get all vehicles with customer details
        $is_superadmin = is_superadmin();
        $current_user = current_user();
        $company_id = $is_superadmin ? null : ($current_user->company_id ?? null);
        
        $data['all_vehicles'] = $this->_get_all_vehicles_with_details($company_id, $is_superadmin);

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/dashboard/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }
    
    /**
     * Get all vehicles with customer details
     */
    private function _get_all_vehicles_with_details($company_id, $is_superadmin)
    {
        $this->db->select('v.*, c.company_name, 
            cu.id as customer_id, CONCAT(cu.first_name, " ", cu.last_name) as customer_name, cu.email as customer_email, cu.phone as customer_phone,
            cu.driving_license_front, cu.driving_license_back,
            ra.id as agreement_id, ra.agreement_number, ra.status as agreement_status, ra.signed_at,
            ra.customer_signature');
        $this->db->from('vehicles v');
        $this->db->join('companies c', 'c.id = v.company_id', 'left');
        $this->db->join('rental_agreements ra', 'ra.vehicle_id = v.id AND ra.deleted_at IS NULL', 'left');
        $this->db->join('customers cu', 'cu.id = ra.customer_id AND cu.deleted_at IS NULL', 'left');
        
        if (!$is_superadmin && $company_id) {
            $this->db->where('v.company_id', $company_id);
        }
        
        $this->db->where('v.deleted_at', NULL);
        $this->db->order_by('v.is_available', 'ASC');
        $this->db->order_by('c.company_name', 'ASC');
        $this->db->order_by('v.vehicle_name', 'ASC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get vehicles by company and availability status
     */
    private function _get_vehicles_by_status($company_id, $is_available)
    {
        $this->db->select('v.*, c.company_name, 
            cu.id as customer_id, CONCAT(cu.first_name, " ", cu.last_name) as customer_name, cu.email as customer_email, cu.phone as customer_phone,
            cu.driving_license_front, cu.driving_license_back,
            ra.id as agreement_id, ra.agreement_number, ra.status as agreement_status, ra.signed_at,
            ra.customer_signature');
        $this->db->from('vehicles v');
        $this->db->join('companies c', 'c.id = v.company_id', 'left');
        $this->db->join('rental_agreements ra', 'ra.vehicle_id = v.id AND ra.deleted_at IS NULL', 'left');
        $this->db->join('customers cu', 'cu.id = ra.customer_id AND cu.deleted_at IS NULL', 'left');
        $this->db->where('v.company_id', $company_id);
        $this->db->where('v.is_available', $is_available);
        $this->db->where('v.deleted_at', NULL);
        $this->db->order_by('v.vehicle_name', 'ASC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get all vehicles by availability status (for superadmin)
     */
    private function _get_all_vehicles($is_available)
    {
        $this->db->select('v.*, c.company_name, 
            cu.id as customer_id, CONCAT(cu.first_name, " ", cu.last_name) as customer_name, cu.email as customer_email, cu.phone as customer_phone,
            cu.driving_license_front, cu.driving_license_back,
            ra.id as agreement_id, ra.agreement_number, ra.status as agreement_status, ra.signed_at,
            ra.customer_signature');
        $this->db->from('vehicles v');
        $this->db->join('companies c', 'c.id = v.company_id', 'left');
        $this->db->join('rental_agreements ra', 'ra.vehicle_id = v.id AND ra.deleted_at IS NULL', 'left');
        $this->db->join('customers cu', 'cu.id = ra.customer_id AND cu.deleted_at IS NULL', 'left');
        $this->db->where('v.is_available', $is_available);
        $this->db->where('v.deleted_at', NULL);
        $this->db->order_by('c.company_name', 'ASC');
        $this->db->order_by('v.vehicle_name', 'ASC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get customer details via AJAX
     */
    public function get_customer_details()
    {
        $customer_id = $this->input->get('customer_id');
        
        if (!$customer_id) {
            echo json_encode(['success' => false, 'message' => 'Customer ID required']);
            return;
        }
        
        $this->db->select('c.*, co.company_name');
        $this->db->from('customers c');
        $this->db->join('companies co', 'co.id = c.company_id', 'left');
        $this->db->where('c.id', $customer_id);
        $this->db->where('c.deleted_at', NULL);
        $customer = $this->db->get()->row();
        
        if (!$customer) {
            echo json_encode(['success' => false, 'message' => 'Customer not found']);
            return;
        }
        
        // Add customer_name property for display
        $customer->customer_name = ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '');
        $customer->customer_name = trim($customer->customer_name);
        
        // Get rental agreements for this customer
        $this->db->select('ra.*, v.vehicle_name, v.license_plate, v.vehicle_make, v.vehicle_model');
        $this->db->from('rental_agreements ra');
        $this->db->join('vehicles v', 'v.id = ra.vehicle_id', 'left');
        $this->db->where('ra.customer_id', $customer_id);
        $this->db->where('ra.deleted_at', NULL);
        $agreements = $this->db->get()->result();
        
        echo json_encode([
            'success' => true,
            'customer' => $customer,
            'agreements' => $agreements
        ]);
    }

    /**
     * Get dashboard statistics
     */
    private function _get_dashboard_stats()
    {
        $is_superadmin = is_superadmin();
        $company_id = null;
        
        // Get company_id for non-superadmin users
        if (!$is_superadmin) {
            $current_user = current_user();
            $company_id = $current_user->company_id ?? null;
        }

        // Both Super Admin and Company Admin now use the same stats format
        $stats = [
            'total_customers' => 0,
            'rented_cars' => 0,
            'available_cars' => 0,
            'repair_accident_cars' => 0,
            'total_cars' => 0,
            'total_invoices' => 0,
            'outstanding_invoices' => 0
        ];

        // Count customers
        if ($this->db->table_exists('customers')) {
            if ($company_id) {
                $stats['total_customers'] = $this->db->where('company_id', $company_id)
                                                     ->where('deleted_at', NULL)
                                                     ->count_all_results('customers');
            } else {
                // Superadmin sees all customers
                $stats['total_customers'] = $this->db->where('deleted_at', NULL)
                                                     ->count_all_results('customers');
            }
        }

        // Count vehicles
        if ($this->db->table_exists('vehicles')) {
            if ($company_id) {
                $stats['total_cars'] = $this->db->where('company_id', $company_id)
                                               ->where('deleted_at', NULL)
                                               ->count_all_results('vehicles');
                
                // Rented cars (is_available = 0)
                $stats['rented_cars'] = $this->db->where('company_id', $company_id)
                                                ->where('is_available', 0)
                                                ->where('deleted_at', NULL)
                                                ->count_all_results('vehicles');
                
                // Available cars (is_available = 1)
                $stats['available_cars'] = $this->db->where('company_id', $company_id)
                                                   ->where('is_available', 1)
                                                   ->where('deleted_at', NULL)
                                                   ->count_all_results('vehicles');
            } else {
                // Superadmin sees all vehicles
                $stats['total_cars'] = $this->db->where('deleted_at', NULL)
                                                ->count_all_results('vehicles');
                
                $stats['rented_cars'] = $this->db->where('is_available', 0)
                                                ->where('deleted_at', NULL)
                                                ->count_all_results('vehicles');
                
                $stats['available_cars'] = $this->db->where('is_available', 1)
                                                    ->where('deleted_at', NULL)
                                                    ->count_all_results('vehicles');
            }
            
            // Repair/Accident cars - requires status field, set to 0 for now
            $stats['repair_accident_cars'] = 0;
        }

        // Count invoices (weekly payments)
        if ($this->db->table_exists('weekly_payments')) {
            // Get agreement IDs based on company
            $this->db->select('id');
            $this->db->from('rental_agreements');
            if ($company_id) {
                $this->db->where('company_id', $company_id);
            }
            $this->db->where('deleted_at', NULL);
            $agreement_ids = $this->db->get()->result_array();
            
            if (!empty($agreement_ids)) {
                $agg_ids = array_column($agreement_ids, 'id');
                
                // Total invoices
                $stats['total_invoices'] = $this->db->where_in('agreement_id', $agg_ids)
                                                   ->count_all_results('weekly_payments');
                
                // Outstanding invoices (pending)
                $stats['outstanding_invoices'] = $this->db->where_in('agreement_id', $agg_ids)
                                                         ->where('payment_status', 'pending')
                                                         ->count_all_results('weekly_payments');
            }
        }

        return $stats;
    }
}
