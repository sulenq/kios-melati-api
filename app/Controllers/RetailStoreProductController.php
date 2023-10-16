<?php

namespace App\Controllers;

use App\Libraries\JwtPayload;
use App\Models\StoreModel;
use CodeIgniter\RESTful\ResourceController;

class RetailStoreProductController extends ResourceController
{
    protected $modelName = 'App\Models\RetailStoreProductModel';

    public function readAll()
    {
        $response = [
            'status' => 200,
            'message' => 'Get all products success',
            'employees' => $this->model->orderBy('id', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    public function read($productId = null)
    {
        $product = $this->model->find($productId);

        if (!$product) {
            $response = [
                'status' => 404,
                'message' => 'Product not found',
                'productId' => $productId
            ];
            return $this->respond($response);
        }

        $response = [
            'status' => 200,
            'message' => 'Product found',
            'employee' => $product
        ];
        return $this->respond($response);
    }

    public function create($storeId = null)
    {
        $jwt = new JwtPayload($this->request);
        $payload = (array) $jwt->getPayload();
        $userId = $payload['id'];

        $storeModel = new StoreModel();
        $store = $storeModel->find($storeId);
        if (!$store) {
            $response = [
                'status' => 404,
                'message' => 'Store not found'
            ];
            $this->respond($response);
        }

        $valid = $this->validate([
            'code' => [
                'label' => 'Code',
                'rules' => 'required|max_length[100]|is_unique[retail_store_product.code]',
                'errors' => [
                    'is_unique' => 'Code is already registered'
                ]
            ],
            "name" => [
                'label' => 'Name',
                'rules' => 'required|max_length[100]',
            ],
            'price' => [
                'label' => 'Price',
                'rules' => 'required|max_length[100]',
            ],
            'stock' => [
                'label' => 'Stock',
                'rules' => 'required|max_length[11]',
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

        $productData = [
            'storeId' => $storeId,
            'createdBy' => $userId,
            'code' => esc($this->request->getVar('code')),
            'name' => esc($this->request->getVar('name')),
            'price' => esc($this->request->getVar('price')),
            'stock' => esc($this->request->getVar('stock')),
            'category' => esc($this->request->getVar('category'))
        ];
        $this->model->insert($productData);
        $response = [
            'status' => 201,
            'invalid' => 'Product added'
        ];
        return $this->respond($response);
    }

    public function update($storeId = null, $productId = null)
    {
        $storeModel = new StoreModel();
        $store = $storeModel->find($storeId);
        if (!$store) {
            $response = [
                'status' => 404,
                'message' => 'Store not found'
            ];
            $this->respond($response);
        }

        $codeValidation = "required|max_length[100]|is_unique[retail_store_product.code,id,$productId]";
        $valid = $this->validate([
            'code' => [
                'label' => 'Code',
                'rules' => $codeValidation,
                'errors' => [
                    'is_unique' => 'Code is already registered'
                ]
            ],
            "name" => [
                'label' => 'Name',
                'rules' => 'required|max_length[100]',
            ],
            'price' => [
                'label' => 'Price',
                'rules' => 'required|max_length[100]',
            ],
            'stock' => [
                'label' => 'Stock',
                'rules' => 'required|max_length[11]',
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

        $productData = [
            'code' => esc($this->request->getVar('code')),
            'name' => esc($this->request->getVar('name')),
            'price' => esc($this->request->getVar('price')),
            'stock' => esc($this->request->getVar('stock')),
            'category' => esc($this->request->getVar('category'))
        ];
        $this->model->update($productId, $productData);
        $response = [
            'status' => 201,
            'invalid' => 'Product updated',
            'productId' => $productId
        ];
        return $this->respond($response);
    }

    public function delete($storeId = null, $productId = null)
    {
        $product = $this->model->find($productId);

        if (!$product) {
            $response = [
                'status' => 404,
                'message' => 'Product not found',
                'productId' => $productId,
            ];

            return $this->respond($response);
        }

        $this->model->delete($productId);
        $response = [
            'status' => 200,
            'message' => 'Product deleted, Product ID : ' . $productId,
            'productId' => $productId,
        ];

        return $this->respond($response);
    }
}