<?php

namespace App\Service;

use DateTimeImmutable;

class JWTservice
{
    //Generation du token
    public function generateToken(
        array $header,
        array $payload,
        string $secret,
        int $validity = 10800): string
    {
        if ($validity > 0){
            $now = new DateTimeImmutable();
            $expiration = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $expiration;
        }



        //Encodage du JWT
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        //Nettoyage des valeurs encodé ("+", "/" et "=") car les encodage génère des symbole que les fichier jwt n'accepte pas
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        //Génération de la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header .  '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        //Creation du token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    //Verifier que le token est valide
    public function isValide(string $token): bool
    {
        return preg_match(
          pattern: '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
                subject: $token
        ) === 1;
    }

    //Récupèrer le header
    public function getHeader(string $token): array
    {
        //récurèrer le token
        $array = explode('.', $token);
        //Décoder le header
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    //Récupèrer le payload
    public function getPayload(string $token): array
    {
        //récurèrer le token
        $array = explode('.', $token);
        //Décoder le payload
        $plaload = json_decode(base64_decode($array[1]), true);

        return $plaload;
    }

    //Verifier si le token à expirer
    public  function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    //Verifier la signature du token
    public function check(string $token, string $secret)
    {
        //Recuperation du header et du payload
        $header = $this->getHeader($token);
        $paylaod = $this->getPayload($token);

        //Régéneration d'un nouveau token
        $verifToken = $this->generateToken($header, $paylaod, $secret, 0);



        return $token === $verifToken;
    }

}