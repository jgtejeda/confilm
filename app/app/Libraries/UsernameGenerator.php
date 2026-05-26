<?php

namespace App\Libraries;

use App\Models\UserModel;

class UsernameGenerator
{
    private UserModel $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function generate(string $nombres, string $apellidoPat): string
    {
        $firstLetter = mb_substr($nombres, 0, 1);
        $normalizedLastName = $this->normalize($apellidoPat);
        $randomChars = substr(bin2hex(random_bytes(4)), 0, 4);

        $candidate = strtolower($firstLetter . $normalizedLastName . '_' . $randomChars);

        $maxAttempts = 10;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $existing = $this->userModel->where('username', $candidate)->first();
            if ($existing === null) {
                return $candidate;
            }
            $attempt++;
            $candidate = strtolower($firstLetter . $normalizedLastName . '_' . substr(bin2hex(random_bytes(4)), 0, 4));
        }

        throw new \RuntimeException('No se pudo generar un username único después de ' . $maxAttempts . ' intentos');
    }

    private function normalize(string $str): string
    {
        $from = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'];
        $to = ['a', 'e', 'i', 'o', 'u', 'n', 'u', 'A', 'E', 'I', 'O', 'U', 'N', 'U'];
        $str = str_replace($from, $to, $str);
        return preg_replace('/[^a-z]/', '', $str);
    }
}