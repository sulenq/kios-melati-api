<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EmployeeModel;
use App\Libraries\JwtPayload;


class StoreController extends ResourceController
{
    protected $modelName = 'App\Models\StoreModel';
    protected $format = 'json';

    public function readAll()
    {
        $response = [
            'status' => 200,
            'message' => 'Get all stores success',
            'stores' => $this->model->orderBy('id', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    public function read($storeId = null)
    {
        $store = $this->model->find($storeId);

        if ($store) {
            $response = [
                'status' => 200,
                'message' => 'Store found',
                'storeData' => $store
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => 404,
                'message' => 'Store not found',
                'storeId' => $storeId
            ];
            return $this->respond($response);
        }
    }

    public function readByAdminUser($storeId = null)
    {
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('userId', $storeId)
            ->where('role', 'Admin')
            ->findAll();

        if ($employee) {
            $response = [
                'status' => 200,
                'message' => 'Store found',
                'storeData' => $employee
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => 404,
                'message' => 'Store not found',
                'userId' => $storeId
            ];
            return $this->respond($response);
        }
    }

    public function readByCashierUser($storeId = null)
    {
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('userId', $storeId)
            ->where('role', 'Cashier')
            ->findAll();

        if ($employee) {
            $response = [
                'status' => 200,
                'message' => 'Store found',
                'storeData' => $employee
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => 404,
                'message' => 'Store not found',
                'userId' => $storeId
            ];
            return $this->respond($response);
        }
    }

    public function create()
    {
        $jwt = new JwtPayload($this->request);
        $payload = (array) $jwt->getPayload();
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
                'status' => 400,
                'invalid' => $this->validator->getErrors()
            ];
            return $this->respond($response);
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
            'role' => 'Admin',
            'status' => 'Owner',
            'salary' => 0,
        ]);
        // $employeeModel->insert([
        //     'userId' => $userId,
        //     'storeId' => $storeId,
        //     'role' => 'Cashier',
        //     'status' => 'Owner',
        //     'salary => 0'
        // ]);

        $response = [
            'status' => 201,
            'message' => 'Store registered. Store Name : ' . $this->request->getVar('storeName')
        ];

        return $this->respondCreated($response, 201);
    }

    public function update($storeId = null)
    {
        $jwt = new JwtPayload($this->request);
        $payload = (array) $jwt->getPayload();
        $userId = $payload['id'];

        $store = $this->model->find($storeId);
        if (!$store) {
            $response = [
                'status' => 404,
                'message' => 'Store not found',
                'Store ID' => $storeId
            ];
            return $this->respond($response);
        }

        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('userId', $userId)
            ->where('storeId', $storeId)
            ->where('status', 'Owner')
            ->first();
        if (!$employee) {
            $response = [
                'status' => 403,
                'message' => 'You are not authorized to do this action',
                'userId' => $userId,
                'storeId' => $storeId
            ];
            return $this->respond($response);
        }

        $emailRules = "required|valid_email|max_length[100]|is_unique[store.email,id,$storeId]";
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
                'rules' => $emailRules,
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
                'status' => 400,
                'invalid' => $this->validator->getErrors()
            ];
            return $this->respond($response);
        }

        $updateData = [
            'storeName' => esc($this->request->getVar('storeName')),
            'address' => esc($this->request->getVar('address')),
            'phone' => esc($this->request->getVar('phone')),
            'email' => esc($this->request->getVar('email')),
            'category' => esc($this->request->getVar('category')),
        ];

        $this->model->update($storeId, $updateData);
        $response = [
            'status' => 200,
            'message' => 'Store updated',
        ];
        return $this->respond($response);
    }

    public function delete($storeId = null)
    {
        $store = $this->model->find($storeId);
        if (!$store) {
            return $this->respond(['message' => 'Store not found', 'Store ID' => $storeId], 409);
        }

        $this->model->delete($storeId);

        $employeeModel = new EmployeeModel();
        $employeeModel->where('storeId', $storeId)->delete();

        $response = [
            'status' => 200,
            'message' => 'Store deleted',
            'storeId' => $storeId,
            'storeName' => $store['storeName']
        ];

        return $this->respond($response);
    }
}