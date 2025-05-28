<?php

namespace Config;

use Utils\Env;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{

    public static function getSignedJWTForUser(string $email)
    {
        $issuedAtTime = time();
        $tokenTimeToLive = Env::getEnv('JWT_TIME_TO_LIVE');
        $tokenExpiration = $issuedAtTime + $tokenTimeToLive;

        $payload = [
            'data' => [
                'email' => $email
            ],
            'iat' => $issuedAtTime,
            'exp' => $tokenExpiration,
        ];

        $jwt = JWT::encode($payload, Env::getEnv('JWT_SECRET_KEY'), 'HS256');

        return $jwt;
    }

    public static function validateJWTFromRequest(string $encodedToken)
    {
        try {
            $key = Env::getEnv('JWT_SECRET_KEY');
            $data = JWT::decode($encodedToken, new Key($key, 'HS256'));

            return $data;
        } catch (\Throwable $th) {
        }
    }
}
