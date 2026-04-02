<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weekly_payment_model extends CI_Model {

    protected $table = 'weekly_payments';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate unique invoice number
     */
    public function generate_invoice_number()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        // Get last invoice number for this month
        $this->db->like('invoice_number', $prefix . $year . $month, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get($this->table)->row();
        
        if ($result) {
            $last_num = intval(substr($result->invoice_number, -4));
            $new_num = str_pad($last_num + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new_num = '0001';
        }
        
        return $prefix . $year . $month . $new_num;
    }

    /**
     * Generate invoices for a new agreement
     * Creates weekly invoices from check-in date until end_date
     * If end_date is NULL (active agreement), generates invoices up to current date
     * 
     * IMPORTANT: Invoice periods are calculated from the check-in date (checked_in_at)
     * not from the agreement from_date, since billing starts from when customer picks up the vehicle
     */
    public function generate_agreement_invoices($agreement, $vehicle = null)
    {
        $invoices = [];
        
        // Check if we have a valid rental price
        if (empty($agreement->rental_price_weekly)) {
            return $invoices;
        }
        
        // Determine the start date for billing:
        // Use checked_in_at if available, otherwise fall back to from_date
        // Billing starts from when customer actually picks up the vehicle
        if (!empty($agreement->checked_in_at)) {
            // Extract just the date part from datetime
            $start_date = date('Y-m-d', strtotime($agreement->checked_in_at));
        } elseif (!empty($agreement->from_date)) {
            $start_date = $agreement->from_date;
        } else {
            // No start date available, cannot generate invoices
            return $invoices;
        }
        
        // If end_date is NULL (active agreement), generate invoices up to current date
        // Otherwise use the specified end_date
        if (empty($agreement->end_date)) {
            // For active agreements, generate invoices up to today (or last completed week)
            $today = date('Y-m-d');
            
            // If start_date is in the future, don't generate any invoices yet
            if (strtotime($start_date) > strtotime($today)) {
                return $invoices;
            }
            
            // End date is today for generating past/current invoices
            $end_date = $today;
        } else {
            $end_date = $agreement->end_date;
        }
        
        $weekly_rate = floatval($agreement->rental_price_weekly);
        $daily_rate = round($weekly_rate / 7, 2);
        
        $current_date = $start_date;
        $week_number = 1;
        
        while (strtotime($current_date) <= strtotime($end_date)) {
            // Find Sunday of the current week
            $day_of_week = date('w', strtotime($current_date));
            $days_until_sunday = $day_of_week == 0 ? 0 : 7 - $day_of_week;
            
            $period_end = date('Y-m-d', strtotime("+{$days_until_sunday} days", strtotime($current_date)));
            
            // Don't exceed end date
            if (strtotime($period_end) > strtotime($end_date)) {
                $period_end = $end_date;
            }
            
            $days_count = (strtotime($period_end) - strtotime($current_date)) / 86400 + 1;
            $total_amount = round($daily_rate * $days_count, 2);
            
            // Determine if partial week (first week or last partial week)
            $payment_type = ($week_number == 1 && $days_count < 7) ? 'partial_week' : 'full_week';
            
            // Create invoice record
            $invoice_data = [
                'invoice_number' => $this->generate_invoice_number(),
                'agreement_id' => $agreement->id,
                'customer_id' => $agreement->customer_id,
                'vehicle_id' => $vehicle ? $vehicle->id : null,
                'payment_type' => $payment_type,
                'start_date' => $current_date,
                'end_date' => $period_end,
                'days_count' => $days_count,
                'weekly_rent' => $weekly_rate,
                'daily_amount' => $daily_rate,
                'total_amount' => $total_amount,
                'payment_status' => 'pending',
                'due_date' => $period_end,
                'notes' => 'Week ' . $week_number . ' rental invoice (from check-in: ' . $start_date . ')'
            ];
            
            $invoice_id = $this->create($invoice_data);
            if ($invoice_id) {
                $invoice_data['id'] = $invoice_id;
                $invoices[] = (object) $invoice_data;
            }
            
            // Move to next week (day after period end)
            $current_date = date('Y-m-d', strtotime('+1 day', strtotime($period_end)));
            $week_number++;
            
            // Safety limit
            if ($week_number > 52) {
                break;
            }
        }
        
        return $invoices;
    }

    /**
     * Generate final invoice when agreement ends early (checkout before end date)
     */
    public function generate_final_invoice($agreement, $checkout_date, $vehicle = null)
    {
        // Get the last invoice
        $last_invoice = $this->db->where('agreement_id', $agreement->id)
            ->order_by('end_date', 'DESC')
            ->limit(1)
            ->get($this->table)
            ->row();
        
        if (!$last_invoice) {
            return null;
        }
        
        // If checkout is on or after the last invoice end date, no final invoice needed
        if (strtotime($checkout_date) >= strtotime($last_invoice->end_date)) {
            return null;
        }
        
        // Calculate final period
        $start_date = date('Y-m-d', strtotime('+1 day', strtotime($last_invoice->end_date)));
        $end_date = $checkout_date;
        
        if (strtotime($start_date) > strtotime($end_date)) {
            return null;
        }
        
        $days_count = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
        $daily_rate = floatval($last_invoice->daily_amount);
        $total_amount = round($daily_rate * $days_count, 2);
        
        $invoice_data = [
            'invoice_number' => $this->generate_invoice_number(),
            'agreement_id' => $agreement->id,
            'customer_id' => $agreement->customer_id,
            'vehicle_id' => $vehicle ? $vehicle->id : null,
            'payment_type' => 'final',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'days_count' => $days_count,
            'weekly_rent' => $last_invoice->weekly_rent,
            'daily_amount' => $daily_rate,
            'total_amount' => $total_amount,
            'payment_status' => 'pending',
            'due_date' => $end_date,
            'notes' => 'Final invoice - early checkout'
        ];
        
        return $this->create($invoice_data) ? (object) $invoice_data : null;
    }

    /**
     * Get all payments for an agreement
     */
    public function get_by_agreement($agreement_id)
    {
        return $this->db->where('agreement_id', $agreement_id)
                        ->order_by('start_date', 'ASC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Get all payments for a customer
     */
    public function get_by_customer($customer_id, $limit = null, $offset = 0)
    {
        $this->db->where('customer_id', $customer_id)
                 ->order_by('start_date', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get($this->table)->result();
    }

    /**
     * Count payments for a customer
     */
    public function count_by_customer($customer_id)
    {
        return $this->db->where('customer_id', $customer_id)
                        ->count_all_results($this->table);
    }

    /**
     * Get payment summary for a customer (actual totals from database)
     * Returns total pending and total paid amounts from invoices
     */
    public function get_payment_summary($customer_id)
    {
        // Get total pending (payment_status = 'pending')
        $this->db->select_sum('total_amount');
        $this->db->where('customer_id', $customer_id);
        $this->db->where('payment_status', 'pending');
        $pending_result = $this->db->get($this->table)->row();
        $total_pending = floatval($pending_result->total_amount ?? 0);

        // Get total paid (payment_status = 'paid')
        $this->db->reset_query();
        $this->db->select_sum('total_amount');
        $this->db->where('customer_id', $customer_id);
        $this->db->where('payment_status', 'paid');
        $paid_result = $this->db->get($this->table)->row();
        $total_paid = floatval($paid_result->total_amount ?? 0);

        // Get overdue total
        $this->db->reset_query();
        $this->db->select_sum('total_amount');
        $this->db->where('customer_id', $customer_id);
        $this->db->where('payment_status', 'overdue');
        $overdue_result = $this->db->get($this->table)->row();
        $total_overdue = floatval($overdue_result->total_amount ?? 0);

        return (object) [
            'total_pending' => $total_pending,
            'total_paid' => $total_paid,
            'total_overdue' => $total_overdue,
            'total_amount' => $total_pending + $total_paid + $total_overdue
        ];
    }

    /**
     * Get payment by ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->get($this->table)
                        ->row();
    }

    /**
     * Get invoice with full details (customer, vehicle, agreement)
     */
    public function get_with_details($id)
    {
        $this->db->select('
            wp.*,
            c.first_name as customer_first_name,
            c.last_name as customer_last_name,
            c.email as customer_email,
            c.phone as customer_phone,
            v.vehicle_name,
            v.license_plate,
            v.vehicle_make,
            v.vehicle_model,
            v.vehicle_year,
            ra.agreement_number
        ');
        $this->db->from('weekly_payments wp');
        $this->db->join('customers c', 'c.id = wp.customer_id', 'left');
        $this->db->join('vehicles v', 'v.id = wp.vehicle_id', 'left');
        $this->db->join('rental_agreements ra', 'ra.id = wp.agreement_id', 'left');
        $this->db->where('wp.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Get all invoices with pagination
     */
    public function get_all($limit = null, $offset = 0, $filters = [])
    {
        $this->db->select('
            wp.*,
            c.first_name as customer_first_name,
            c.last_name as customer_last_name,
            c.email as customer_email,
            c.company_id as customer_company_id,
            v.vehicle_name,
            v.license_plate,
            ra.agreement_number
        ');
        $this->db->from('weekly_payments wp');
        $this->db->join('customers c', 'c.id = wp.customer_id', 'left');
        $this->db->join('vehicles v', 'v.id = wp.vehicle_id', 'left');
        $this->db->join('rental_agreements ra', 'ra.id = wp.agreement_id', 'left');

        if (!empty($filters['customer_id'])) {
            $this->db->where('wp.customer_id', $filters['customer_id']);
        }

        if (!empty($filters['agreement_id'])) {
            $this->db->where('wp.agreement_id', $filters['agreement_id']);
        }

        if (!empty($filters['vehicle_id'])) {
            $this->db->where('wp.vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['payment_status'])) {
            $this->db->where('wp.payment_status', $filters['payment_status']);
        }

        // Filter by company - join through customer
        if (!empty($filters['company_id'])) {
            $this->db->where('c.company_id', $filters['company_id']);
        }

        // Customer search (name or customer ID)
        if (!empty($filters['customer_search'])) {
            $customer_search = $filters['customer_search'];
            // Check if it's a numeric customer ID
            if (is_numeric($customer_search)) {
                $this->db->where('wp.customer_id', $customer_search);
            } else {
                // Search by name
                $this->db->group_start();
                $this->db->like('c.first_name', $customer_search);
                $this->db->or_like('c.last_name', $customer_search);
                $this->db->or_like('c.email', $customer_search);
                $this->db->group_end();
            }
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('wp.invoice_number', $filters['search']);
            $this->db->or_like('c.first_name', $filters['search']);
            $this->db->or_like('c.last_name', $filters['search']);
            $this->db->or_like('c.email', $filters['search']);
            $this->db->or_like('ra.agreement_number', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('wp.start_date', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Count all invoices
     */
    public function count_all($filters = [])
    {
        $this->db->from('weekly_payments wp');
        $this->db->join('customers c', 'c.id = wp.customer_id', 'left');
        $this->db->join('rental_agreements ra', 'ra.id = wp.agreement_id', 'left');

        if (!empty($filters['customer_id'])) {
            $this->db->where('wp.customer_id', $filters['customer_id']);
        }

        if (!empty($filters['agreement_id'])) {
            $this->db->where('wp.agreement_id', $filters['agreement_id']);
        }

        if (!empty($filters['payment_status'])) {
            $this->db->where('wp.payment_status', $filters['payment_status']);
        }

        // Filter by company - join through customer
        if (!empty($filters['company_id'])) {
            $this->db->where('c.company_id', $filters['company_id']);
        }

        // Customer search (name or customer ID)
        if (!empty($filters['customer_search'])) {
            $customer_search = $filters['customer_search'];
            // Check if it's a numeric customer ID
            if (is_numeric($customer_search)) {
                $this->db->where('wp.customer_id', $customer_search);
            } else {
                // Search by name
                $this->db->group_start();
                $this->db->like('c.first_name', $customer_search);
                $this->db->or_like('c.last_name', $customer_search);
                $this->db->or_like('c.email', $customer_search);
                $this->db->group_end();
            }
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('wp.invoice_number', $filters['search']);
            $this->db->or_like('c.first_name', $filters['search']);
            $this->db->or_like('c.email', $filters['search']);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * Update invoice payment status
     */
    public function update_status($id, $status)
    {
        $data = [
            'payment_status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'paid') {
            $data['paid_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Get pending payments for a customer
     */
    public function get_pending($customer_id)
    {
        return $this->db->where('customer_id', $customer_id)
                        ->where('payment_status', 'pending')
                        ->order_by('start_date', 'ASC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Get completed payments for a customer
     */
    public function get_completed($customer_id)
    {
        return $this->db->where('customer_id', $customer_id)
                        ->where('payment_status', 'completed')
                        ->order_by('completed_at', 'DESC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Create a weekly payment record
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Mark payment as completed and add to wallet
     */
    public function mark_completed($payment_id, $customer_id, $amount)
    {
        // Start transaction
        $this->db->trans_start();

        // Update payment status
        $this->db->where('id', $payment_id);
        $this->db->update($this->table, [
            'payment_status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Add to wallet
        $this->db->where('customer_id', $customer_id);
        $this->db->set('balance', 'balance + ' . floatval($amount), FALSE);
        $this->db->set('total_earned', 'total_earned + ' . floatval($amount), FALSE);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->update('customer_wallets');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Calculate payments for a period
     * Returns array of payment data ready for insertion
     */
    public function calculate_payments($agreement_id, $customer_id, $vehicle_weekly_rent, $start_date, $end_date)
    {
        $daily_amount = round($vehicle_weekly_rent / 7, 2);
        $payments = [];
        $current_date = $start_date;

        while ($current_date <= $end_date) {
            // Find the Sunday of the current week
            $days_until_sunday = 7 - date('w', strtotime($current_date));
            if ($days_until_sunday == 7) {
                $days_until_sunday = 0; // If it's Sunday
            }

            $period_end = date('Y-m-d', strtotime("+{$days_until_sunday} days", strtotime($current_date)));

            // Don't exceed the overall end date
            if (strtotime($period_end) > strtotime($end_date)) {
                $period_end = $end_date;
            }

            $days_count = (strtotime($period_end) - strtotime($current_date)) / 86400 + 1;

            // Calculate total for this period
            $total_amount = round($daily_amount * $days_count, 2);

            // Determine if this is a partial week (first week might not start on Monday)
            $is_partial = ($current_date != date('Y-m-d', strtotime('monday this week', strtotime($current_date)))) ? 'partial_week' : 'full_week';

            // If the period is less than 7 days and it's the first period, mark as partial
            if (count($payments) == 0 && $days_count < 7) {
                $is_partial = 'partial_week';
            } else {
                $is_partial = 'full_week';
            }

            $payments[] = [
                'agreement_id' => $agreement_id,
                'customer_id' => $customer_id,
                'payment_type' => $is_partial,
                'start_date' => $current_date,
                'end_date' => $period_end,
                'days_count' => $days_count,
                'weekly_rent' => $vehicle_weekly_rent,
                'daily_amount' => $daily_amount,
                'total_amount' => $total_amount,
                'payment_status' => 'pending'
            ];

            // Move to next period (next day after period end)
            $current_date = date('Y-m-d', strtotime('+1 day', strtotime($period_end)));
        }

        return $payments;
    }

    /**
     * Calculate partial week payment (Tuesday to Sunday example)
     */
    public function calculate_partial_week($agreement_id, $customer_id, $vehicle_weekly_rent, $start_date, $end_date)
    {
        $daily_amount = round($vehicle_weekly_rent / 7, 2);
        $days_count = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
        $total_amount = round($daily_amount * $days_count, 2);

        return [
            'agreement_id' => $agreement_id,
            'customer_id' => $customer_id,
            'payment_type' => 'partial_week',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'days_count' => $days_count,
            'weekly_rent' => $vehicle_weekly_rent,
            'daily_amount' => $daily_amount,
            'total_amount' => $total_amount,
            'payment_status' => 'pending'
        ];
    }

    /**
     * Calculate full week payment (Monday to Sunday)
     */
    public function calculate_full_week($agreement_id, $customer_id, $vehicle_weekly_rent, $week_start_date)
    {
        $daily_amount = round($vehicle_weekly_rent / 7, 2);
        $week_end_date = date('Y-m-d', strtotime('+6 days', strtotime($week_start_date)));

        return [
            'agreement_id' => $agreement_id,
            'customer_id' => $customer_id,
            'payment_type' => 'full_week',
            'start_date' => $week_start_date,
            'end_date' => $week_end_date,
            'days_count' => 7,
            'weekly_rent' => $vehicle_weekly_rent,
            'daily_amount' => $daily_amount,
            'total_amount' => $vehicle_weekly_rent,
            'payment_status' => 'pending'
        ];
    }

    /**
     * Get pending payments summary for admin
     */
    public function get_pending_summary($company_id = null)
    {
        $this->db->select('wp.*, CONCAT(c.first_name, " ", c.last_name) as customer_name, c.email, ra.agreement_number, v.vehicle_name, v.license_plate');
        $this->db->from('weekly_payments wp');
        $this->db->join('customers c', 'c.id = wp.customer_id');
        $this->db->join('rental_agreements ra', 'ra.id = wp.agreement_id');
        $this->db->join('vehicles v', 'v.id = ra.vehicle_id');
        $this->db->where('wp.payment_status', 'pending');

        if ($company_id !== null) {
            $this->db->where('ra.company_id', $company_id);
        }

        return $this->db->order_by('wp.start_date', 'ASC')
                        ->get()
                        ->result();
    }
}
