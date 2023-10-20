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

    public function read($outletId = null)
    {
        $outlet = $this->model->find($outletId);

        if ($outlet) {
            $response = [
                'status' => 200,
                'message' => 'Outlet found',
                'outlet' => $outlet
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => 404,
                'message' => 'Outlet not found',
                'outletId' => $outletId
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
                'message' => 'Employee not found',
                'userId' => $userId
            ];
            return $this->respond($response);
        }

        // Inisialisasi array untuk menyimpan daftar toko
        $outlets = [];

        // Loop melalui entitas Employee untuk mendapatkan outletId
        foreach ($employees as $employee) {
            $outletId = $employee['outletId'];

            // Ambil entitas Store berdasarkan outletId
            $outlet = $this->model->find($outletId);

            if ($outlet) {
                $outlets[] = ['outlet' => $outlet, 'employee' => $employee];
            }
        }

        // Periksa apakah ada toko yang ditemukan
        if (empty($outlets)) {
            $response = [
                'status' => 404,
                'message' => 'Outlet not found',
                'userId' => $userId
            ];
            return $this->respond($response);
        }

        $response = [
            'status' => 200,
            'message' => 'Outlet found',
            'data' => $outlets
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

        $outletId = $this->model->insert([
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
            'outletId' => $outletId,
            'role' => 'Admin',
            'roleColor' => 'purple',
            'status' => 'Owner',
            'salary' => 0,
        ]);
        // $employeeModel->insert([
        //     'userId' => $userId,
        //     'outletId' => $outletId,
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

    public function update($outletId = null)
    {
        $jwt = new JwtPayload($this->request);
        $payload = (array) $jwt->getPayload();
        $userId = $payload['id'];

        $outlet = $this->model->find($outletId);
        if (!$outlet) {
            $response = [
                'status' => 404,
                'message' => 'Store not found',
                'Store ID' => $outletId
            ];
            return $this->respond($response);
        }

        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('userId', $userId)
            ->where('outletId', $outletId)
            ->where('status', 'Owner')
            ->first();
        if (!$employee) {
            $response = [
                'status' => 403,
                'message' => 'You are not authorized to do this action',
                'userId' => $userId,
                'outletId' => $outletId
            ];
            return $this->respond($response);
        }

        $emailRules = "required|valid_email|max_length[100]|is_unique[outlet.email,id,$outletId]";
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

        $this->model->update($outletId, $updateData);
        $response = [
            'status' => 200,
            'message' => 'Store updated',
        ];
        return $this->respond($response);
    }

    public function delete($outletId = null)
    {
        $outlet = $this->model->find($outletId);
        if (!$outlet) {
            return $this->respond(['message' => 'Store not found', 'Store ID' => $outletId], 409);
        }

        $this->model->delete($outletId);

        $employeeModel = new EmployeeModel();
        $employeeModel->where('outletId', $outletId)->delete();

        $response = [
            'status' => 200,
            'message' => 'Store deleted',
            'outletId' => $outletId,
            'outletName' => $outlet['outletName']
        ];

        return $this->respond($response);
    }
}