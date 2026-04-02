<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
*/
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth routes
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['auth/login'] = 'auth/do_login';

// Dashboard routes
$route['dashboard'] = 'dashboard/index';

// Roles
$route['roles'] = 'roles/index';
$route['roles/create'] = 'roles/create';
$route['roles/store'] = 'roles/store';
$route['roles/edit/(:num)'] = 'roles/edit/$1';
$route['roles/update/(:num)'] = 'roles/update/$1';
$route['roles/delete/(:num)'] = 'roles/delete/$1';

// Permissions
$route['permissions'] = 'permissions/index';
$route['permissions/create'] = 'permissions/create';
$route['permissions/store'] = 'permissions/store';
$route['permissions/edit/(:num)'] = 'permissions/edit/$1';
$route['permissions/update/(:num)'] = 'permissions/update/$1';
$route['permissions/delete/(:num)'] = 'permissions/delete/$1';

// Permission Groups
$route['permissions/groups'] = 'permissions/groups';
$route['permissions/create_group'] = 'permissions/create_group';
$route['permissions/store_group'] = 'permissions/store_group';
$route['permissions/edit_group/(:num)'] = 'permissions/edit_group/$1';
$route['permissions/update_group/(:num)'] = 'permissions/update_group/$1';
$route['permissions/delete_group/(:num)'] = 'permissions/delete_group/$1';

// Users
$route['users'] = 'users/index';
$route['users/create'] = 'users/create';
$route['users/store'] = 'users/store';
$route['users/edit/(:num)'] = 'users/edit/$1';
$route['users/update/(:num)'] = 'users/update/$1';
$route['users/delete/(:num)'] = 'users/delete/$1';

// Profile
$route['profile'] = 'profile/index';