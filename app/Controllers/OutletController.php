<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use App\Models\EmployeeModel;
use App\Libraries\JwtPayload;


class OutletController extends ResourceController
{
    protected $modelName = 'App\Models\OutletModel';

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
        $outlet = $this->model->find($storeId);

        if ($outlet) {
            $response = [
                'status' => 200,
                'message' => 'Store found',
                'storeData' => $outlet
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

    public function readAllByUser()
    {
        $jwt = new JwtPayload($this->request);
        $payload = (array) $jwt->getPayload();
        $userId = $payload['id'];

        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            $response = [
                'status' => 404,
                'message' => 'User not found',
                'userId' => $userId
            ];
            return $this->respond($response);
        }

        $employeeModel = new EmployeeModel();
        $employees = $employeeModel->where('userId', $userId)
            ->findAll();
        if (!$employees) {
            $response = [
                'status' => 404,
                'message' => 'Outlet not found',
                'userId' => $userId
            ];
            return $this->respond($response);
        }

        // Inisialisasi array untuk menyimpan daftar toko
        $stores = [];

        // Loop melalui entitas Employee untuk mendapatkan storeId
        foreach ($employees as $employee) {
            $storeId = $employee['storeId'];

            // Ambil entitas Store berdasarkan storeId
            $outlet = $this->model->find($storeId);

            if ($outlet) {
                $stores[] = $outlet;
            }
        }

        // Periksa apakah ada toko yang ditemukan
        if (empty($stores)) {
            $response = [
                'status' => 404,
                'message' => 'Store not found',
                'userId' => $userId
            ];
            return $this->respond($response);
        }

        $response = [
            'status' => 200,
            'message' => 'Stores found',
            'stores' => $stores
        ];
        return $this->respond($response);
    }
    public function create()
    {
        $jwt = new JwtPayload($this->request);
        $payload = (array) $jwt->getPayload();
        $userId = $payload['id'];

        $valid = $this->validate([
            'outletName' => [
                'label' => 'Outlet Name',
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
                'rules' => 'required|valid_email|is_unique[outlet.email]|max_length[100]',
                'errors' => [
                    'valid_email' => '{field} invalid',
                    'is_unique' => '{field} is already registered'
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
                'message' => 'Request invalid',
                'invalid' => $this->validator->getErrors()
            ];
            return $this->respond($response);
        }

        $storeId = $this->model->insert([
            'createdBy' => $userId,
            'outletName' => esc($this->request->getVar('outletName')),
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
            'message' => 'Store registered. Store Name : ' . $this->request->getVar('outletName')
        ];

        return $this->respondCreated($response, 201);
    }

    public function update($storeId = null)
    {
        $jwt = new JwtPayload($this->request);
        $payload = (array) $jwt->getPayload();
        $userId = $payload['id'];

        $outlet = $this->model->find($storeId);
        if (!$outlet) {
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

        $emailRules = "required|valid_email|max_length[100]|is_unique[outlet.email,id,$storeId]";
        $valid = $this->validate([
            'outletName' => [
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
            'outletName' => esc($this->request->getVar('outletName')),
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
        $outlet = $this->model->find($storeId);
        if (!$outlet) {
            return $this->respond(['message' => 'Store not found', 'Store ID' => $storeId], 409);
        }

        $this->model->delete($storeId);

        $employeeModel = new EmployeeModel();
        $employeeModel->where('storeId', $storeId)->delete();

        $response = [
            'status' => 200,
            'message' => 'Store deleted',
            'storeId' => $storeId,
            'outletName' => $outlet['outletName']
        ];

        return $this->respond($response);
    }
}