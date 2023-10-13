<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EmployeeModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class StoreController extends ResourceController
{
    protected $modelName = 'App\Models\StoreModel';
    protected $format = 'json';
    public function index()
    {
        $response = [
            'message' => 'Get all stores',
            'usersData' => $this->model->orderBy('id', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    public function read($id = null)
    {
        $store = $this->model->find($id);

        if ($store) {
            $response = [
                'message' => 'Store found',
                'storeData' => $store
            ];
            return $this->respond($response, 200);
        } else {
            return $this->respond(['message' => 'Store not found'], 409);
        }
    }

    public function create()
    {
        $authHeader = $this->request->getHeader('Authorization');

        if (!$authHeader) {
            return $this->respond(['message' => 'Sign in required'], 401);
        }

        $jwt = substr($authHeader->getValue(), 7); // Menghapus 'Bearer '
        $employeeModel = new EmployeeModel();

        try {
            $jwtKey = getenv('JWT_SECRET');
            $jwtAlg = getenv('JWT_ALG');
            $decoded = JWT::decode($jwt, new Key($jwtKey, $jwtAlg));

            if (!$decoded) {
                return $this->respond(['message' => 'Token invalid', 'jwt' => $jwt], 401);
            }
        } catch (\Exception $e) {
            return $this->respond(['message' => 'Token invalid', 'jwt' => $jwt], 401);
        }

        if (!$decoded) {
            return $this->respond(['message' => 'Token invalid', 'jwt' => $jwt], 401);
        }

        $payload = (array) $decoded;
        $userId = $payload['id'];

        $valid = $this->validate([
            'storeName' => [
                'label' => 'Store Name',
                'rules' => 'required|max_length[100]',
            ],
            'address' => [
                'label' => 'Address',
                'rules' => 'required|max_length[200]',
            ],
            'phone' => [
                'label' => 'Phone',
                'rules' => 'required|max_length[100]',
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[store.email]|max_length[100]',
                'errors' => [
                    'valid_email' => '{field} invalid.',
                    'is_unique' => '{field} is already registered.'
                ]
            ],
            'category' => [
                'label' => 'Category',
                'rules' => 'required|max_length[100]',
            ],
        ]);

        if (!$valid) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $storeId = $this->model->insert([
            'createdBy' => $userId,
            'storeName' => esc($this->request->getVar('storeName')),
            'address' => esc($this->request->getVar('address')),
            'phone' => esc($this->request->getVar('phone')),
            'email' => esc($this->request->getVar('email')),
            'category' => esc($this->request->getVar('category')),
        ]);


        $employeeModel = new EmployeeModel();
        $employeeModel->insert([
            'userId' => $userId,
            'storeId' => $storeId,
            'role' => 'Admin'
        ]);

        $response = [
            'message' => 'Store registered. Store Name : ' . $this->request->getVar('storeName')
        ];

        return $this->respondCreated($response);
    }

    public function update($id = null)
    {
        $authHeader = $this->request->getHeader('Authorization');

        if (!$authHeader) {
            return $this->respond(['message' => 'Sign in required'], 401);
        }

        $jwt = substr($authHeader->getValue(), 7); // Menghapus 'Bearer '
        $employeeModel = new EmployeeModel();

        try {
            $jwtKey = getenv('JWT_SECRET');
            $jwtAlg = getenv('JWT_ALG');
            $decoded = JWT::decode($jwt, new Key($jwtKey, $jwtAlg));
        } catch (\Exception $e) {
            return $this->respond(['message' => 'Token invalid', 'jwt' => $jwt], 401);
        }


        if (!$decoded) {
            return $this->respond(['message' => 'Token invalid', 'jwt' => $jwt], 401);
        }

        $payload = (array) $decoded;
        $userId = $payload['id'];
        $storeId = $id;

        $employee = $employeeModel->where('userId', $userId)
            ->where('storeId', $storeId)
            ->first();

        $store = $this->model->find($id);

        if (!$store) {
            return $this->respond(['message' => 'Store not found', 'Store ID' => $storeId], 409);
        }

        if (!$employee) {
            return $this->respond(['message' => 'You are not authorized to do this action, User ID :' . $userId . ", Store ID :" . $storeId], 405);
        }

        $data = $this->request->getJSON();
        $updateData = $data->updateData;

        $this->model->update($storeId, $updateData);
        return $this->respond(['message' => 'Store updated'], 200);
    }

    public function delete($id = null)
    {
        $authHeader = $this->request->getHeader('Authorization');

        if (!$authHeader) {
            return $this->respond(['message' => 'Sign in required'], 401);
        }

        $jwt = substr($authHeader->getValue(), 7); // Menghapus 'Bearer '
        $employeeModel = new EmployeeModel();

        try {
            $jwtKey = getenv('JWT_SECRET');
            $jwtAlg = getenv('JWT_ALG');
            $decoded = JWT::decode($jwt, new Key($jwtKey, $jwtAlg));
        } catch (\Exception $e) {
            return $this->respond(['message' => 'Token invalid', 'jwt' => $jwt], 401);
        }

        if (!$decoded) {
            return $this->respond(['message' => 'Token invalid', 'jwt' => $jwt], 401);
        }

        $payload = (array) $decoded;
        $userId = $payload['id'];
        $storeId = $id;

        $employee = $employeeModel->where('userId', $userId)
            ->where('storeId', $storeId)
            ->first();

        $store = $this->model->find($id);

        if (!$employee) {
            return $this->respond(['message' => 'You are not authorized to do this action, User ID :' . $userId . ", Store ID :" . $storeId], 405);
        }

        if (!$store) {
            return $this->respond(['message' => 'Store not found', 'Store ID' => $storeId], 409);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Store deleted']);
    }
}