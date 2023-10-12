<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format = 'json';


    public function generate_jwt($user) // return jwt token
    {
        $jwtKey = getenv('JWT_SECRET');
        $jwtAlg = getenv('JWT_ALG');
        $payload = [
            'username' => $user['username'],
            'name' => $user['name'],
            'age' => $user['age'],
            'gender' => $user['gender'],
            'address' => $user['address'],
            'phone' => $user['phone'],
            // Waktu token dibuat
            'iat' => time(),
            // Waktu kedaluwarsa token (12 jam)
            'exp' => time() + 43200,
        ];

        return JWT::encode($payload, $jwtKey, $jwtAlg);
    }

    public function parseToken($jwt) // return payload
    {
        $jwtKey = getenv('JWT_SECRET');
        $jwtAlg = getenv('JWT_ALG');
        $decoded = JWT::decode($jwt, new Key($jwtKey, $jwtAlg));
        return $decoded;
    }

    public function verifyToken() // return boolean
    {
        $authHeader = $this->request->getHeader('Authorization');
        $jwt = substr($authHeader->getValue(), 7); // Menghapus 'Bearer '

        try {
            $decoded = $this->parseToken($jwt);
            return $this->respond(['message' => 'Token valid', 'payload' => (array) $decoded], 200);
        } catch (\Exception $e) {
            return $this->respond(['message' => 'Token invalid', 'jwt' => $jwt], 401);
        }
    }

    public function signin()
    {
        $valid = $this->validate([
            'emailOrUsername' => [
                'label' => 'Email or Username',
                'rules' => 'required',
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]',
            ],
        ]);

        if (!$valid) {
            return $this->respond(['message' => $this->validator->getErrors()]);
        }

        $emailOrUsername = $this->request->getVar('emailOrUsername');
        $password = $this->request->getVar('password');

        // Cari user berdasarkan email atau username
        $user = $this->model->where('email', $emailOrUsername)
            ->orWhere('username', $emailOrUsername)
            ->first();

        if ($user) {
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil
                $jwt = $this->generate_jwt($user);
                // $jwt = new JWTCI4;
                return $this->respond(['message' => 'Signed In', 'jwt' => $jwt], 200);
            } else {
                // Password salah
                return $this->respond(['message' => 'Password is not match'], 401);
            }
        } else {
            // User tidak ditemukan
            return $this->respond(['message' => 'User not found'], 409);
        }
    }
}