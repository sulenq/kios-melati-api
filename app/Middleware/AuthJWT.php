<?php
namespace App\Middleware;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use CodeIgniter\Config\Services;

class AuthJWT
{
    public function handle($request, \Closure $next)
    {
        $jwtKey = getenv('JWT_SECRET');
        $jwtAlg = getenv('JWT_ALG');
        $headers = $request->getHeaders();
        if (!isset($headers['Authorization'])) {
            return Services::response()
                ->setJSON(['message' => 'Token not provided'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $jwt = $headers['Authorization'];
        try {
            JWT::decode($jwt, new Key($jwtKey, $jwtAlg));
        } catch (\Exception $e) {
            return Services::response()
                ->setJSON(['message' => 'Invalid token'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}