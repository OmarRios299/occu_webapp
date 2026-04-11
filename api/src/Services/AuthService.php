<?php

declare(strict_types=1);

namespace Occu\Api\Services;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AuthService
{
    private const LEGACY_CRYPT_SALT = '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$';

    private JsonResponder $json;

    public function __construct(
        private readonly UserRepository $users,
        private readonly JwtService $jwt,
        private readonly RefreshTokenService $refresh,
        private readonly PinMailerService $mailer,
        private readonly GoogleIdTokenService $google,
        ?JsonResponder $json = null,
    ) {
        $this->json = $json ?? new JsonResponder();
    }

    public function register(Request $request, Response $response): Response
    {
        $b = $this->body($request);

        $nombre = trim((string)($b['nombre'] ?? ''));
        $apellido = trim((string)($b['apellido'] ?? ''));
        $correo = trim((string)($b['correo'] ?? ''));
        $contrasena = (string)($b['contrasena'] ?? '');
        $nivel = trim((string)($b['nivel'] ?? ''));
        $ciudad = (int)($b['ciudad'] ?? 0);

        if ($nombre === '' || $apellido === '' || $correo === '' || $contrasena === '' || $nivel === '' || $ciudad <= 0) {
            return $this->json->error($response, 422, 'validation_error', 'Campos obligatorios incompletos');
        }

        if ($this->users->emailExists($correo)) {
            return $this->json->error($response, 409, 'email_exists', 'Este correo ya se encuentra registrado');
        }

        $pin = (string)random_int(100000, 999999);
        $hash = crypt($contrasena, self::LEGACY_CRYPT_SALT);

        $userId = $this->users->createInternalUser([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'id_ciudad' => $ciudad,
            'correo' => $correo,
            'contrasena' => $hash,
            'nivel' => $nivel,
            'imagen' => 'views/assets/img/usuario_default.png',
            'id_alta' => 0,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'pin' => $pin,
        ]);

        $this->mailer->sendPin($correo, $pin);

        return $this->json->created($response, [
            'user_id' => $userId,
            'message' => 'Registro creado. Revisa tu correo para el PIN.',
        ]);
    }

    public function verifyPin(Request $request, Response $response): Response
    {
        $b = $this->body($request);

        $correo = trim((string)($b['correo'] ?? ''));
        $contrasena = (string)($b['contrasena'] ?? '');
        $pin = trim((string)($b['pin'] ?? ''));

        if ($correo === '' || $contrasena === '' || $pin === '') {
            return $this->json->error($response, 422, 'validation_error', 'Campos obligatorios incompletos');
        }

        $user = $this->users->findByEmail($correo);
        if (!$user) {
            return $this->json->error($response, 401, 'invalido', 'Usuario o contraseña incorrectos');
        }

        if ((int)($user['estado'] ?? 0) !== 0) {
            return $this->json->error($response, 403, 'desactivado', 'El usuario está desactivado');
        }

        $hash = crypt($contrasena, self::LEGACY_CRYPT_SALT);
        if (strtoupper((string)$user['correo_electronico']) !== strtoupper($correo) || (string)($user['contrasena'] ?? '') !== $hash) {
            return $this->json->error($response, 401, 'invalido', 'Usuario o contraseña incorrectos');
        }

        if ((string)($user['verificado'] ?? 'No') === 'Si') {
            // Ya verificado: permitir emitir tokens igualmente
            return $this->issueTokens($response, (int)$user['id'], (string)$user['nivel'], $request);
        }

        if ((string)($user['pin'] ?? '') !== $pin) {
            return $this->json->error($response, 400, 'pin_invalido', 'PIN inválido');
        }

        $this->users->markVerified((int)$user['id']);

        return $this->issueTokens($response, (int)$user['id'], (string)$user['nivel'], $request);
    }

    public function resendPin(Request $request, Response $response): Response
    {
        $b = $this->body($request);
        $correo = trim((string)($b['correo'] ?? ''));

        if ($correo === '') {
            return $this->json->error($response, 422, 'validation_error', 'Correo obligatorio');
        }

        $user = $this->users->findByEmail($correo);
        if (!$user) {
            return $this->json->error($response, 404, 'invalido', 'Este correo no ha sido registrado anteriormente');
        }

        if ((string)($user['verificado'] ?? 'No') === 'Si') {
            return $this->json->error($response, 409, 'verificado', 'Este correo ya ha sido verificado anteriormente');
        }

        $pin = (string)random_int(100000, 999999);
        $this->users->updatePin($correo, $pin);
        $this->mailer->sendPin($correo, $pin);

        return $this->json->ok($response, ['message' => 'Reenviado: revisa tu correo']);
    }

    public function login(Request $request, Response $response): Response
    {
        $b = $this->body($request);
        $correo = trim((string)($b['correo'] ?? ''));
        $contrasena = (string)($b['contrasena'] ?? '');

        if ($correo === '' || $contrasena === '') {
            return $this->json->error($response, 422, 'validation_error', 'Correo y contraseña son obligatorios');
        }

        $user = $this->users->findByEmail($correo);
        if (!$user) {
            return $this->json->error($response, 401, 'invalido', 'Usuario o contraseña incorrectos');
        }

        if ((int)($user['estado'] ?? 0) !== 0) {
            return $this->json->error($response, 403, 'desactivado', 'El usuario está desactivado');
        }

        $hash = crypt($contrasena, self::LEGACY_CRYPT_SALT);
        if ((string)($user['contrasena'] ?? '') !== $hash) {
            return $this->json->error($response, 401, 'invalido', 'Usuario o contraseña incorrectos');
        }

        if ((string)($user['verificado'] ?? 'No') !== 'Si') {
            return $this->json->error($response, 403, 'verificacion', 'Esta cuenta no está verificada');
        }

        return $this->issueTokens($response, (int)$user['id'], (string)$user['nivel'], $request);
    }

    public function refresh(Request $request, Response $response): Response
    {
        $b = $this->body($request);
        $refreshToken = trim((string)($b['refresh_token'] ?? ''));
        if ($refreshToken === '') {
            return $this->json->error($response, 422, 'validation_error', 'refresh_token es obligatorio');
        }

        $rotated = $this->refresh->rotate($refreshToken, $this->ip($request), $this->ua($request));
        if (!$rotated) {
            return $this->json->error($response, 401, 'unauthorized', 'Refresh token inválido');
        }

        $user = $this->users->findById($rotated['user_id']);
        if (!$user || (int)($user['estado'] ?? 0) !== 0) {
            return $this->json->error($response, 401, 'unauthorized', 'No autorizado');
        }

        $access = $this->jwt->issueAccessToken((int)$user['id'], (string)$user['nivel']);

        return $this->json->ok($response, [
            'access_token' => $access,
            'access_token_expires_in' => 900,
            'refresh_token' => $rotated['new_refresh_token'],
            'refresh_token_expires_at' => $rotated['new_refresh_token_expires_at'],
        ]);
    }

    public function googleLogin(Request $request, Response $response): Response
    {
        $b = $this->body($request);
        $idToken = trim((string)($b['id_token'] ?? ''));
        if ($idToken === '') {
            return $this->json->error($response, 422, 'validation_error', 'id_token es obligatorio');
        }

        $claims = $this->google->verify($idToken);
        if (!$claims) {
            return $this->json->error($response, 401, 'token_invalido', 'Token de Google inválido');
        }

        $user = $this->users->findByProvider('google', $claims['sub']);
        if (!$user) {
            // Crear usuario base (pendiente completar info)
            $name = trim($claims['name'] ?? '');
            $parts = $name !== '' ? explode(' ', $name, 2) : [];
            $nombre = $parts[0] ?? 'Usuario';
            $apellido = $parts[1] ?? 'Google';

            $newId = $this->users->createGoogleUser([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'id_ciudad' => 0,
                'correo' => $claims['email'] ?: '',
                'nivel' => 'Cliente',
                'imagen' => $claims['picture'] ?: 'views/assets/img/usuario_default.png',
                'id_usuario_proveedor' => $claims['sub'],
                'id_alta' => 0,
                'fecha_alta' => date('Y-m-d H:i:s'),
            ]);

            $user = $this->users->findById($newId);
        }

        if (!$user) {
            return $this->json->error($response, 500, 'internal_error', 'No se pudo obtener el usuario');
        }

        if ((int)($user['estado'] ?? 0) !== 0) {
            return $this->json->error($response, 403, 'desactivado', 'El usuario está desactivado');
        }

        // Si aún no está verificado, pedir completar información
        if ((string)($user['verificado'] ?? 'No') !== 'Si') {
            $pending = $this->jwt->issueGooglePendingToken((int)$user['id']);
            return $this->json->ok($response, [
                'needs_profile' => true,
                'google_pending_token' => $pending,
            ]);
        }

        return $this->issueTokens($response, (int)$user['id'], (string)$user['nivel'], $request);
    }

    public function googleComplete(Request $request, Response $response): Response
    {
        $b = $this->body($request);
        $pending = trim((string)($b['google_pending_token'] ?? ''));
        $nivel = trim((string)($b['nivel'] ?? ''));
        $idCiudad = (int)($b['id_ciudad'] ?? 0);

        if ($pending === '' || $nivel === '') {
            return $this->json->error($response, 422, 'validation_error', 'google_pending_token y nivel son obligatorios');
        }

        $payload = $this->jwt->verifyGooglePendingToken($pending);
        if (!$payload || empty($payload['sub'])) {
            return $this->json->error($response, 401, 'unauthorized', 'Token inválido');
        }

        $userId = (int)$payload['sub'];
        $this->users->completeGoogleInfo($userId, $idCiudad > 0 ? $idCiudad : 0, $nivel);

        $user = $this->users->findById($userId);
        if (!$user) {
            return $this->json->error($response, 500, 'internal_error', 'No se pudo obtener el usuario');
        }

        return $this->issueTokens($response, $userId, (string)$user['nivel'], $request);
    }

    public function logout(Request $request, Response $response): Response
    {
        $b = $this->body($request);
        $refreshToken = trim((string)($b['refresh_token'] ?? ''));
        if ($refreshToken !== '') {
            $this->refresh->revoke($refreshToken);
        }
        return $this->json->ok($response, ['message' => 'logout_ok']);
    }

    private function issueTokens(Response $response, int $userId, string $nivel, Request $request): Response
    {
        $access = $this->jwt->issueAccessToken($userId, $nivel);
        $refresh = $this->refresh->createForUser($userId, $this->ip($request), $this->ua($request));

        return $this->json->ok($response, [
            'access_token' => $access,
            'access_token_expires_in' => 900,
            'refresh_token' => $refresh['refresh_token'],
            'refresh_token_expires_at' => $refresh['refresh_token_expires_at'],
            'user' => [
                'id' => $userId,
                'nivel' => $nivel,
            ],
        ]);
    }

    private function body(Request $request): array
    {
        $parsed = $request->getParsedBody();
        if (is_array($parsed)) return $parsed;
        if (is_object($parsed)) return (array)$parsed;
        return [];
    }

    private function ip(Request $request): ?string
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? null;
        return is_string($ip) && $ip !== '' ? $ip : null;
    }

    private function ua(Request $request): ?string
    {
        $ua = $request->getServerParams()['HTTP_USER_AGENT'] ?? null;
        return is_string($ua) && $ua !== '' ? $ua : null;
    }
}

