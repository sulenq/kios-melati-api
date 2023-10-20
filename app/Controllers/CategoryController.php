<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class CategoryController extends ResourceController
{
    protected $modelName = 'App\Models\CategoryModel';


    public function readAll()
    {
        $response = [
            'status' => 200,
            'message' => 'Get all categories success',
            'employees' => $this->model->orderBy('id', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    public function read($categoryId = null)
    {
        $category = $this->model->find($categoryId);

        if (!$category) {
            $response = [
                'status' => 404,
                'message' => 'Category not found',
                'categoryId' => $categoryId
            ];
            return $this->respond($response);
        }

        $response = [
            'status' => 200,
            'message' => 'Category found',
            'employee' => $category
        ];
        return $this->respond($response);
    }

    public function create($outletId = null)
    {
        $valid = $this->validate(
            [
                "category" => [
                    'label' => 'Category',
                    'rules' => 'required|max_length[100]'
                ]
            ],
        );
        if (!$valid) {
            $response = [
                'status' => 400,
                'invalid' => $this->validator->getErrors()
            ];
            return $this->respond($response);
        }

        $categoryData = ['category' => esc($this->request->getVar('category'))];
        $this->model->insert($categoryData);
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