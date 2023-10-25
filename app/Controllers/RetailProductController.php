<?php

namespace App\Controllers;

use App\Libraries\JwtPayload;
use App\Models\OutletModel;
use CodeIgniter\RESTful\ResourceController;

class RetailProductController extends ResourceController
{
    protected $modelName = 'App\Models\RetailProductModel';

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
            'data' => $product
        ];
        return $this->respond($response);
    }

    public function readAllByOutlet($outletId = null)
    {
        $outletModel = new OutletModel();
        $outlet = $outletModel->find($outletId);
        if (!$outlet) {
            $response = [
                'status' => 404,
                'message' => 'Outlet not found',
                'outletId' => $outletId
            ];
            return $this->respond($response);
        }

        $products = $this->model->where('outletId', $outletId)->findAll();
        if (!$products) {
            $response = [
                'status' => 404,
                'message' => 'No Products',
                'outletId' => $outletId
            ];
            return $this->respond($response);
        }
        $response = [
            'status' => 200,
            'message' => 'Products found',
            'data' => $products
        ];
        return $this->respond($response);
    }

    public function create($outletId = null)
    {
        $jwt = new JwtPayload($this->request);
        $payload = (array) $jwt->getPayload();
        $userId = $payload['id'];

        $outletModel = new OutletModel();
        $outlet = $outletModel->find($outletId);
        if (!$outlet) {
            $response = [
                'status' => 404,
                'message' => 'Outlet not found'
            ];
            $this->respond($response);
        }

        $valid = $this->validate([
            'code' => [
                'label' => 'Code',
                'rules' => "required|max_length[100]|is_code_unique_by_outlet[$outletId]",
                'errors' => [
                    'required' => 'Code is required',
                    'is_unique' => 'Code is already registered',
                    'is_code_unique_by_outlet' => 'Code is already registered in this outlet'
                ]
            ],
            "name" => [
                'label' => 'Name',
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Name is required',
                ]
            ],
            'price' => [
                'label' => 'Price',
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Price is required',
                ]
            ],
            'stock' => [
                'label' => 'Stock',
                'rules' => 'required|max_length[11]',
                'errors' => [
                    'required' => 'Stock is required'
                ]
            ],
            'category' => [
                'label' => 'Category',
                'rules' => 'required|max_length[100]|check_category',
                'errors' => [
                    'required' => 'Category is required',
                    'check_category' => '{field} invalid'
                ]
            ],
        ]);
        if (!$valid) {
            $response = [
                'status' => 400,
                'message' => 'Add Product Failed',
                'invalid' => $this->validator->getErrors()
            ];
            return $this->respond($response);
        }

        $productData = [
            'outletId' => $outletId,
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
            'message' => 'Product added',
            'productName' => $productData['name']
        ];
        return $this->respond($response);
    }

    public function update($outletId = null, $productId = null)
    {
        $outletModel = new OutletModel();
        $outlet = $outletModel->find($outletId);
        if (!$outlet) {
            $response = [
                'status' => 404,
                'message' => 'Outlet not found',
                'outletId' => $outletId
            ];
            return $this->respond($response);
        }

        $product = $this->model->find($productId);
        if (!$product) {
            $response = [
                'status' => 404,
                'message' => 'Product not found',
                'productId' => $productId
            ];
            return $this->respond($response);
        }

        $codeValid = true;
        if ($product['code'] !== esc($this->request->getVar('code'))) {
            $codeValid = $this->validate([
                'code' => [
                    'label' => 'Code',
                    'rules' => "required|max_length[100]|is_code_unique_by_outlet[$outletId]",
                    'errors' => [
                        'is_unique' => 'Code is already registered',
                        'is_code_unique_by_outlet' => 'Code is already registered in this outlet'
                    ]
                ],
            ]);
        }

        $valid = $this->validate([
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
        if (!$codeValid) {
            $response = [
                'status' => 400,
                'message' => 'Update product failed',
                'invalid' => $this->validator->getErrors()
            ];
            return $this->respond($response);
        }

        if (!$valid) {
            $response = [
                'status' => 400,
                'message' => 'Update product failed',
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
            'status' => 200,
            'message' => 'Product updated',
            'productId' => $productId
        ];
        return $this->respond($response);
    }

    public function delete($outletId = null, $productId = null)
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
            'message' => 'Product deleted',
            'productId' => $productId,
        ];

        return $this->respond($response);
    }
}