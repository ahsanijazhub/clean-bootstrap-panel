<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_wallet_model extends CI_Model {

    protected $table = 'customer_wallets';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get wallet by customer ID
     */
    public function get_by_customer($customer_id)
    {
        return $this->db->where('customer_id', $customer_id)
                        ->get($this->table)
                        ->row();
    }

    /**
     * Create wallet for customer
     */
    public function create($customer_id)
    {
        $data = [
            'customer_id' => $customer_id,
            'balance' => 0.00,
            'total_earned' => 0.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert($this->table, $data);
    }

    /**
     * Add amount to wallet balance (for weekly payments)
     */
    public function add_earnings($customer_id, $amount)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->set('balance', 'balance + ' . floatval($amount), FALSE);
        $this->db->set('total_earned', 'total_earned + ' . floatval($amount), FALSE);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        return $this->db->update($this->table);
    }

    /**
     * Get wallet balance
     */
    public function get_balance($customer_id)
    {
        $wallet = $this->get_by_customer($customer_id);
        return $wallet ? $wallet->balance : 0.00;
    }

    /**
     * Get total earned
     */
    public function get_total_earned($customer_id)
    {
        $wallet = $this->get_by_customer($customer_id);
        return $wallet ? $wallet->total_earned : 0.00;
    }
}
