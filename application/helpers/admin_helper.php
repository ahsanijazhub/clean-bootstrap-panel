<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Helper Functions
 */

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in()
    {
        $CI =& get_instance();
        return $CI->session->userdata('is_logged_in') === true;
    }
}

if (!function_exists('current_user')) {
    /**
     * Get current logged in user
     */
    function current_user()
    {
        $CI =& get_instance();
        if (!is_logged_in()) {
            return null;
        }

        $CI->load->model('Admin_user_model');
        return $CI->Admin_user_model->get_by_id($CI->session->userdata('user_id'));
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if current user has a permission
     */
    function has_permission($permission_slug)
    {
        $CI =& get_instance();
        if (!is_logged_in()) {
            return false;
        }

        $user_id = $CI->session->userdata('user_id');

        // Check if superadmin
        if ($CI->session->userdata('is_superadmin') == 1) {
            return true;
        }

        $CI->load->model('Admin_user_model');
        return $CI->Admin_user_model->has_permission($user_id, $permission_slug);
    }
}

if (!function_exists('is_superadmin')) {
    /**
     * Check if current user is superadmin
     */
    function is_superadmin()
    {
        $CI =& get_instance();
        return $CI->session->userdata('is_superadmin') == 1;
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date for display
     */
    function format_date($date, $format = 'M d, Y')
    {
        if (empty($date)) {
            return 'N/A';
        }
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime for display
     */
    function format_datetime($datetime, $format = 'M d, Y H:i')
    {
        if (empty($datetime)) {
            return 'N/A';
        }
        return date($format, strtotime($datetime));
    }
}

if (!function_exists('str_limit')) {
    /**
     * Limit string length with ellipsis
     */
    function str_limit($string, $limit = 100, $end = '...')
    {
        if (strlen($string) <= $limit) {
            return $string;
        }
        return substr($string, 0, $limit) . $end;
    }
}

if (!function_exists('generate_slug')) {
    /**
     * Generate URL-friendly slug from string
     */
    function generate_slug($string)
    {
        $slug = strtolower(trim($string));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
}

if (!function_exists('active_menu')) {
    /**
     * Check if menu item is active
     */
    function active_menu($segment, $value, $class = 'active')
    {
        $CI =& get_instance();
        return ($CI->uri->segment($segment) == $value) ? $class : '';
    }
}

if (!function_exists('flash_message')) {
    /**
     * Get flash message HTML
     */
    function flash_message()
    {
        $CI =& get_instance();
        $html = '';

        if ($CI->session->flashdata('success')) {
            $html .= '<div class="alert alert-success">' . $CI->session->flashdata('success') . '</div>';
        }

        if ($CI->session->flashdata('error')) {
            $html .= '<div class="alert alert-danger">' . $CI->session->flashdata('error') . '</div>';
        }

        if ($CI->session->flashdata('warning')) {
            $html .= '<div class="alert alert-warning">' . $CI->session->flashdata('warning') . '</div>';
        }

        if ($CI->session->flashdata('info')) {
            $html .= '<div class="alert alert-info">' . $CI->session->flashdata('info') . '</div>';
        }

        return $html;
    }
}

if (!function_exists('get_initials')) {
    /**
     * Get initials from name
     */
    function get_initials($name)
    {
        $words = explode(' ', trim($name));
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        return substr($initials, 0, 2);
    }
}

if (!function_exists('status_badge')) {
    /**
     * Generate status badge HTML
     */
    function status_badge($status, $type = 'default')
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'default' => 'secondary'
        ];

        $color = $colors[$status] ?? $colors[$type] ?? $colors['default'];
        $label = ucfirst(str_replace('_', ' ', $status));

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }
}

if (!function_exists('form_error')) {
    /**
     * Get form validation error message
     */
    function form_error($field)
    {
        $CI =& get_instance();
        $error = $CI->session->flashdata('form_errors');
        
        if (is_array($error) && isset($error[$field])) {
            return $error[$field];
        }
        
        // Check validation errors from form_validation library
        if (isset($CI->form_validation) && method_exists($CI->form_validation, 'error')) {
            return $CI->form_validation->error($field, '<div class="invalid-feedback">', '</div>');
        }
        
        return '';
    }
}

if (!function_exists('set_value')) {
    /**
     * Get form field value with old input support
     */
    function set_value($field, $default = '')
    {
        $CI =& get_instance();
        
        // Check for old input in flashdata
        $old_input = $CI->session->flashdata('old_input');
        if (is_array($old_input) && isset($old_input[$field])) {
            return htmlspecialchars($old_input[$field]);
        }
        
        // Check for old input in post data
        $post_data = $CI->session->flashdata('post_data');
        if (is_array($post_data) && isset($post_data[$field])) {
            return htmlspecialchars($post_data[$field]);
        }
        
        return $default;
    }
}

if (!function_exists('set_select')) {
    /**
     * Set select option as selected
     */
    function set_select($field, $value, $default = false)
    {
        $CI =& get_instance();
        
        // Check for old input in flashdata
        $old_input = $CI->session->flashdata('old_input');
        if (is_array($old_input) && isset($old_input[$field]) && $old_input[$field] == $value) {
            return ' selected';
        }
        
        // Check for old input in post data
        $post_data = $CI->session->flashdata('post_data');
        if (is_array($post_data) && isset($post_data[$field]) && $post_data[$field] == $value) {
            return ' selected';
        }
        
        return $default ? ' selected' : '';
    }
}

if (!function_exists('set_checkbox')) {
    /**
     * Set checkbox as checked
     */
    function set_checkbox($field, $value, $default = false)
    {
        $CI =& get_instance();
        
        // Check for old input in flashdata
        $old_input = $CI->session->flashdata('old_input');
        if (is_array($old_input) && isset($old_input[$field]) && $old_input[$field] == $value) {
            return ' checked';
        }
        
        // Check for old input in post data
        $post_data = $CI->session->flashdata('post_data');
        if (is_array($post_data) && isset($post_data[$field]) && $post_data[$field] == $value) {
            return ' checked';
        }
        
        return $default ? ' checked' : '';
    }
}
