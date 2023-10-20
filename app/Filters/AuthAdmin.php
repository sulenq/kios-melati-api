<?php
namespace App\Filters;

use App\Models\EmployeeModel;
use App\Models\OutletModel;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthAdmin implements FilterInterface
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
        $outletId = $req->uri->getSegment(3);

        $outletModel = new OutletModel();
        $outlet = $outletModel->find($outletId);
        if (!$outlet) {
            $response = [
                'status' => 404,
                'message' => 'Outlet not found',
                'Store ID' => $outletId
            ];
            return $res->setJSON($response);
        }

        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('userId', $userId)
            ->where('outletId', $outletId)
            ->where('role', 'Admin')
            ->first();
        if (!$employee) {
            $response = [
                'status' => 403,
                'message' => 'You are not authorized to do this action',
                'userId' => $userId,
                'outletId' => $outletId
            ];
            return $res->setJSON($response);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}