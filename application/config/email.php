<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email Configuration
 *
 * This file contains email settings for the application.
 * Update the SMTP settings below with your actual email server credentials.
 */

$config['protocol'] = 'mail';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['wordwrap'] = TRUE;

// For SMTP configuration (uncomment and configure for production):
// $config['protocol'] = 'smtp';
// $config['smtp_host'] = 'smtp.example.com';
// $config['smtp_port'] = 465;
// $config['smtp_user'] = 'no-reply@example.com';
// $config['smtp_pass'] = 'your_password';
// $config['smtp_timeout'] = 30;
// $config['smtp_crypto'] = 'ssl';
// $config['validate'] = TRUE;