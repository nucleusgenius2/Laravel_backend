<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JwtTokenService
{
    public static $secret = 'HiPbwrFNoQt2W5Di1BmZfaZ2EWGzV9fwctwY1M80HamnYCCkuVAG4xPR5lcSJgwS';

    public function generatePublicJWT(int $hours): string
    {
        $payload = [
            'id' => GenerateUniqueString::generate(0,10),
            'name' => GenerateUniqueString::generateName(10),
            'iat' => now()->timestamp,
            'exp' => now()->addHours($hours)->timestamp, // Время истечения токена
        ];


        return $this->generateToken($payload, $hours);
    }

    public function generateAuthJWT(int $hours): string
    {
        $user = Auth::user();

        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'iat' => now()->timestamp,
            'exp' => now()->addHours($hours)->timestamp,
        ];

        return $this->generateToken($payload, $hours);
    }

    public static function generateToken(array $payload, int $hours = 1): string
    {
        // 1. Заголовок (Header)
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        // 2. Полезная нагрузка (Payload)
        $payload['iat'] = time();
        $payload['exp'] = time() + ($hours * 3600);

        // 3. Кодирование Base64
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        // 4. Формирование подписи
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);


        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    /**
     * Проверка валидности JWT токена
     */
    public static function verifyToken(string $token): bool|array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false; // Неверный формат токена
        }

        [$headerEncoded, $payloadEncoded, $signatureProvided] = $parts;

        // Пересчитать подпись
        $signatureExpected = self::base64UrlEncode(
            hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secret, true)
        );

        if (!hash_equals($signatureExpected, $signatureProvided)) {
            return false; // Подпись токена не совпадает
        }

        // Декодировать полезную нагрузку
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);

        // Проверить истечение срока действия токена
        if ($payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    /**
     * Base64 URL Encoding
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL Decoding
     */
    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

}
