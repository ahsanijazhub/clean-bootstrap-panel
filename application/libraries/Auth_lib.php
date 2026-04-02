<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_lib {

    protected $CI;
    public $user = null;
    public $permissions = [];

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Admin_user_model');
    }

    /**
     * Check if logged in, redirect if not
     */
    public function require_login()
    {
        if (!$this->is_logged_in()) {
            $this->CI->session->set_userdata('redirect_url', current_url());
            redirect('login');
        }

        $this->user = $this->CI->Admin_user_model->get_by_id(
            $this->CI->session->userdata('user_id')
        );

        if (!$this->user) {
            $this->CI->session->sess_destroy();
            redirect('login');
        }

        $perms = $this->CI->Admin_user_model->get_user_permissions($this->user->id);
        $this->permissions = array_column($perms, 'perm_slug');

        $this->CI->load->vars([
            'current_user' => $this->user,
            'user_permissions' => $this->permissions
        ]);
    }

    /**
     * Check if user is logged in
     */
    public function is_logged_in()
    {
        return $this->CI->session->userdata('is_logged_in') === true;
    }

    /**
     * Check permission
     */
    public function has_permission($slug)
    {
        if ($this->user && $this->user->is_superadmin == 1) {
            return true;
        }
        return in_array($slug, $this->permissions);
    }

    /**
     * Check if superadmin
     */
    public function is_superadmin()
    {
        return $this->user && $this->user->is_superadmin == 1;
    }
}