<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email Configuration
 *
 * This file contains email settings for the application.
 * Update the SMTP settings below with your actual email server credentials.
 */

// Get the host to determine environment
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if ($host === 'localhost') {
    // Local Development - Use mail() function or configure local SMTP
    $config['protocol'] = 'mail'; // Use PHP's mail() function
    $config['mailtype'] = 'html';
    $config['charset'] = 'utf-8';
    $config['newline'] = "\r\n";
    $config['wordwrap'] = TRUE;

    // For local testing with SMTP (optional - configure if you have local SMTP)
    // $config['protocol'] = 'smtp';
    // $config['smtp_host'] = 'localhost';
    // $config['smtp_port'] = 25;
    // $config['smtp_timeout'] = 30;
} else {
    // Production - Use SMTP
    $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'mail.sozorentacar.com.au'; // Change to your SMTP host
    $config['smtp_port'] = 465; // 465 for SSL, 587 for TLS
    $config['smtp_user'] = 'no-reply@sozorentacar.com.au'; // Your email address
    $config['smtp_pass'] = 'sozoAU@1'; // Your email password or app password
    $config['smtp_timeout'] = 30;
    $config['smtp_crypto'] = 'ssl'; // 'ssl' or 'tls'

    $config['mailtype'] = 'html';
    $config['charset'] = 'utf-8';
    $config['newline'] = "\r\n";
    $config['wordwrap'] = FALSE; // Disabled to prevent breaking long URLs
    $config['validate'] = TRUE;
    $config['wrapchars'] = 0; // Don't wrap at any character limit
}

