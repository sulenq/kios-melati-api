<?php

namespace App\Controllers;

use App\Libraries\JwtPayload;
use App\Models\EmployeeModel;
use CodeIgniter\RESTful\ResourceController;

class EmployeeController extends ResourceController
{
    protected $modelName = 'App\Models\EmployeeModel';

    public function readAll()
    {
        $response = [
            'status' => 200,
            'message' => 'Get all employees success',
            'employees' => $this->model->orderBy('id', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    public function read($employeeId = null)
    {
        $employee = $this->model->find($employeeId);

        if (!$employee) {
            $response = [
                'status' => 404,
                'message' => 'Employee not found',
                'employeeId' => $employeeId
            ];
            return $this->respond($response);
        }

        $response = [
            'status' => 200,
            'message' => 'Employee found',
            'employee' => $employee
        ];
        return $this->respond($response);
    }

    public function readByStore($outletId = null)
    {
        $employees = $this->model->where('outletId', $outletId)
            ->findAll();

        if (!$employees) {
            $response = [
                'status' => 404,
                'message' => 'Employees not found',
                'outletId' => $outletId
            ];
            return $this->respond($response);
        }

        $response = [
            'status' => 200,
            'message' => 'Employees found',
            'employees' => $employees
        ];
        return $this->respond($response);

    }

    public function create($outletId = null)
    {
        $employee = $this->model->where('userId', esc($this->request->getVar('userId')))
            ->where('outletId', $outletId)
            ->where('role', esc($this->request->getVar('role')))
            ->first();
        if ($employee) {
            $response = [
                'status' => 400,
                'invalid' => ['userId' => 'Employee is already registered, User ID : ' . esc($this->request->getVar('userId'))]
            ];
            return $this->respond($response);
        }

        $valid = $this->validate([
            'userId' => [
                'label' => 'User ID',
                'rules' => 'required|max_length[100]|check_user',
                'errors' => [
                    'check_user' => 'User not found'
                ]
            ],
            "role" => [
                'label' => 'Role',
                'rules' => 'required|max_length[100]|check_role',
                'errors' => [
                    "check_role" => "{field} invalid"
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|max_length[100]|check_status',
                'errors' => [
                    "check_status" => "{field} invalid"
                ]
            ],
            'salary' => [
                'label' => 'Salary',
                'rules' => 'required|max_length[11]',
            ],
        ]);
        if (!$valid) {
            $response = [
                'status' => 400,
                'invalid' => $this->validator->getErrors()
            ];
            return $this->respond($response);
        }

        $employeeData = [
            'userId' => esc($this->request->getVar('userId')),
            'outletId' => $outletId,
            'role' => esc($this->request->getVar('role')),
            'status' => esc($this->request->getVar('status')),
            'salary' => esc($this->request->getVar('salary'))
        ];
        $this->model->insert($employeeData);

        $response = [
            'status' => 201,
            'message' => 'Cashier added, User ID : ' . $this->request->getVar('userId'),
            'userId' => $this->request->getVar('userId')
        ];
        return $this->respond($response);
    }

    public function update($outletId = null, $employeeId = null)
    {
        $employee = $this->model->find($employeeId);
        if (!$employee) {
            $response = [
                'status' => 404,
                'message' => 'Employee not found',
                'Employee ID' => $employeeId
            ];
            return $this->respond($response);
        }

        $valid = $this->validate([
            "role" => [
                'label' => 'Role',
                'rules' => 'max_length[100]|check_role',
                'errors' => [
                    "check_role" => "{field} invalid"
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'max_length[100]|check_status',
                'errors' => [
                    "check_status" => "{field} invalid"
                ]
            ],
            'salary' => [
                'label' => 'Salary',
                'rules' => 'required|max_length[11]',
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
            'role' => esc($this->request->getVar('role')),
            'status' => esc($this->request->getVar('status')),
            'salary' => esc($this->request->getVar('salary'))
        ];

        $this->model->update($employeeId, $updateData);
        $response = [
            'status' => 200,
            'message' => 'Employee updated, Employee ID : ' . $employeeId,
            'employeeId' => $employeeId
        ];
        return $this->respond($response);
    }

    public function delete($outletId = null, $employeeId = null)
    {
        $employee = $this->model->find($employeeId);

        if (!$employee) {
            $response = [
                'status' => 404,
                'message' => 'Employee not found',
                'employeeId' => $employeeId,
            ];

            return $this->respond($response);
        }

        if ($employee['status'] === 'Owner') {
            $response = [
                'status' => 400,
                'message' => 'Owner cannot be deleted',
            ];
            return $this->respond($response);
        }

        $this->model->delete($employeeId);
        $response = [
            'status' => 200,
            'message' => 'Employee deleted, User ID : ' . $employeeId,
            'employeeId' => $employeeId,
        ];

        return $this->respond($response);
    }
}