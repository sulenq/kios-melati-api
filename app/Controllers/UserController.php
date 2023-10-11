<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format = 'json';

    public function index()
    {
        $response = [
            'message' => 'success',
            'usersData' => $this->model->orderBy('id', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    public function show($id = null)
    {
        //
    }

    public function create()
    {
        $valid = $this->validate([
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[users.email]|max_length[50]',
                'errors' => [
                    'valid_email' => '{field} invalid.',
                    'is_unique' => '{field} is already registered.'
                ]
            ],
            'username' => [
                'label' => 'Username',
                'rules' => 'required|is_unique[users.username]|max_length[100]',
                'errors' => [
                    'is_unique' => '{field} is not available.'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]|max_length[100]',
            ],
            'name' => [
                'label' => 'Name',
                'rules' => 'required|max_length[100]',
            ],
            'age' => [
                'label' => 'Age',
                'rules' => 'required|max_length[3]',
            ],
            'gender' => [
                'label' => 'Gender',
                'rules' => 'required|check_gender|max_length[10]',
                'errors' => [
                    'check_gender' => '{field} is invalid.'
                ]
            ],
            'address' => [
                'label' => 'Address',
                'rules' => 'required|max_length[200]',
            ],
            'phone' => [
                'label' => 'Phone',
                'rules' => 'required|max_length[20]',
            ],
        ]);

        if (!$valid) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $password = $this->request->getVar('password');
        $encrypted_password = password_hash($password, PASSWORD_BCRYPT);

        $this->model->insert([
            'email' => esc($this->request->getVar('email')),
            'username' => esc($this->request->getVar('username')),
            'password' => esc($encrypted_password),
            'name' => esc($this->request->getVar('name')),
            'age' => esc($this->request->getVar('age')),
            'gender' => esc($this->request->getVar('gender')),
            'address' => esc($this->request->getVar('address')),
            'phone' => esc($this->request->getVar('phone')),
        ]);

        $response = [
            'message' => 'User registered. Email : ' . $this->request->getVar('email') . ', Username : ' . $this->request->getVar('username') . ', Name : ' . $this->request->getVar('name')
        ];

        return $this->respondCreated($response);
    }

    public function edit($id = null)
    {
        //
    }

    public function update($id = null)
    {
        //
    }

    public function delete($id = null)
    {
        //
    }
}