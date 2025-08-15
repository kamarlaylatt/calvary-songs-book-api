<?php

use App\Models\Admin;
use App\Models\Role;
use App\Http\Controllers\Api\Admin\AdminController;
use Illuminate\Http\Request;

require_once 'vendor/autoload.php';

// Create a new admin with roles
echo "Testing store method...\n";
$request = new Request();
$request->setMethod('POST');
$request->replace([
    'name' => 'Test Admin 2',
    'email' => 'admin2@example.com',
    'password' => 'password',
    'password_confirmation' => 'password',
    'status' => 'active',
    'roles' => [1]
]);

$controller = new AdminController();
$response = $controller->store($request);
echo "Store response: ";
print_r($response->getData());

echo "\nTesting update method...\n";
$admin = Admin::where('email', 'admin2@example.com')->first();
$request = new Request();
$request->setMethod('PUT');
$request->replace([
    'name' => 'Updated Test Admin 2',
    'email' => 'admin2@example.com',
    'status' => 'active',
    'roles' => [1]
]);

$response = $controller->update($request, $admin);
echo "Update response: ";
print_r($response->getData());

echo "\nTesting delete method...\n";
$response = $controller->destroy($admin);
echo "Delete response status code: " . $response->getStatusCode() . "\n";

echo "\nAll tests completed.\n";
