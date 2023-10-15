<?php
namespace App\Libraries;

use CodeIgniter\HTTP\IncomingRequest;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JwtPayload
{
    private $jwtKey;
    private $jwtAlg;
    private $request;

    public function __construct(IncomingRequest $request)
    {
        $this->jwtKey = getenv('JWT_SECRET');
        $this->jwtAlg = getenv('JWT_ALG');
        $this->request = $request;
    }

    public function getPayload()
    {
        $authHeader = $this->request->getHeader('Authorization');
        $jwt = substr($authHeader->getValue(), 7); // Menghapus 'Bearer '

        if (!$jwt) {
            return false;
        }
        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtKey, $this->jwtAlg));
            return $decoded;
        } catch (\Exception $e) {
            // Menangani pengecualian jika terjadi kesalahan saat mendekode JWT
            return false;
        }
    }
}