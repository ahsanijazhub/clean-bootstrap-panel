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
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth routes
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';

// Dashboard routes
$route['dashboard'] = 'dashboard/index';
$route['dashboard/get_customer_details'] = 'dashboard/get_customer_details';


$route['auth/login'] = 'auth/do_login';

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
