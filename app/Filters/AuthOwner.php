<?php
namespace App\Filters;

use App\Models\EmployeeModel;
use App\Models\OutletModel;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthOwner implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtKey = getenv('JWT_SECRET');
        $jwtAlg = getenv('JWT_ALG');
        $authHeader = $request->getHeader('Authorization');
        $res = service('response');
        $req = service('request');

        if (!$authHeader) {
            $response = [
                'status' => 401,
                'message' => 'Unauthorized'
            ];
            return $res->setJSON($response);
        }

        $jwt = substr($authHeader->getValue(), 7); // Menghapus 'Bearer '

        try {
            $decode = JWT::decode($jwt, new Key($jwtKey, $jwtAlg));
        } catch (\Exception $e) {
            $response = [
                'status' => 401,
                'message' => 'Unauthorized'
            ];
            return $res->setJSON($response);
        }

        $payload = (array) $decode;
        $userId = $payload['id'];
        $storeId = $req->uri->getSegment(2);

        $outletModel = new OutletModel();
        $outlet = $outletModel->find($storeId);
        if (!$outlet) {
            $response = [
                'status' => 404,
                'message' => 'Store not found',
                'Store ID' => $storeId
            ];
            return $res->setJSON($response);
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
            return $res->setJSON($response);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}