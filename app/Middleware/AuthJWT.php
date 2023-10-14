<?php
namespace App\Middleware;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthJWT implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtKey = getenv('JWT_SECRET');
        $jwtAlg = getenv('JWT_ALG');
        $authHeader = $request->getHeader('Authorization');
        $rc = service('response');

        if (!$authHeader) {
            $response = [
                'status' => 401,
                'message' => 'Unauthorized'
            ];
            return $rc->setJSON($response);
        }

        $jwt = substr($authHeader->getValue(), 7); // Menghapus 'Bearer '

        try {
            JWT::decode($jwt, new Key($jwtKey, $jwtAlg));
        } catch (\Exception $e) {
            $response = [
                'status' => 401,
                'message' => 'Unauthorized'
            ];
            return $rc->setJSON($response);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}