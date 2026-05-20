<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Models\UserModel;

use Exception;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class AuthController extends BaseController
{
    public function __construct(
        private UserModel $user_model
    ) {
        parent::__construct();
    }

    public function register(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            $user_fname = trim($data['user_fname'] ?? '');
            $user_lname = trim($data['user_lname'] ?? '');
            $user_email = trim($data['user_email'] ?? '');
            $user_password = $data['user_password'] ?? '';

            if (
                empty($user_fname) ||
                empty($user_lname) ||
                empty($user_email) ||
                empty($user_password)
            ) {
                throw new HttpBadRequestException($request, 'All fields are required');
            }

            if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                throw new HttpBadRequestException($request, 'Invalid email format');
            }

            if (strlen($user_password) < 8) {
                throw new HttpBadRequestException($request, 'Password must be at least 8 characters');
            }

            $existing_user = $this->user_model->findUserByEmail($user_email);

            if ($existing_user) {
                throw new HttpBadRequestException($request, 'Email already exists');
            }

            $base_handle = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $user_fname . '.' . $user_lname));
            $user_handle = $base_handle;
            $suffix = 1;

            while (
                $this->user_model->findUserByHandle($user_handle)
            ) {
                $user_handle = $base_handle . $suffix;
                $suffix++;
            }

            $password_hash = password_hash($user_password, PASSWORD_DEFAULT);

            $user_id = $this->user_model->createUser([
                'user_handle' => $user_handle,
                'user_fname' => $user_fname,
                'user_lname' => $user_lname,
                'user_email' => $user_email,
                'user_password' => $password_hash,
                'user_role' => 'user'
            ]);

            return $this->renderJson($response, [
                'status' => 'success',
                'code' => 201,
                'message' => 'User registered successfully',
                'data' => [
                    'user_id' => $user_id,
                    'user_handle' => $user_handle
                ]
            ], 201);
        } catch (HttpBadRequestException $e) {
            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 400,
                'message' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 500,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            $user_email = trim(
                $data['user_email'] ?? ''
            );

            $user_password = $data['user_password'] ?? '';

            if (empty($user_email) || empty($user_password)) {
                throw new HttpBadRequestException(
                    $request,
                    'Email and password are required'
                );
            }

            $user = $this->user_model->findUserByEmail($user_email);

            if (!$user) {
                throw new HttpUnauthorizedException(
                    $request,
                    'Invalid credentials'
                );
            }

            if (!password_verify($user_password, $user['user_password'])) {
                throw new HttpUnauthorizedException(
                    $request,
                    'Invalid credentials'
                );
            }

            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'user_handle' => $user['user_handle'],
                'user_email' => $user['user_email'],
                'user_role' => $user['user_role']
            ];

            return $this->renderJson($response, [
                'status' => 'success',
                'code' => 200,
                'message' => 'Login successful',
                'data' => [
                    'user_id' => $user['user_id'],
                    'user_handle' => $user['user_handle'],
                    'user_role' => $user['user_role']
                ]
            ], 200);
        } catch (HttpUnauthorizedException $e) {
            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 401,
                'message' => $e->getMessage()
            ], 401);
        } catch (HttpBadRequestException $e) {
            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 400,
                'message' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 500,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request, Response $response): Response
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        return $this->renderJson($response, [
            'status' => 'success',
            'code' => 200,
            'message' => 'Logout successful'
        ], 200);
    }

    public function me(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])) {
            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        return $this->renderJson($response, [
            'status' => 'success',
            'code' => 200,
            'data' => $_SESSION['user']
        ], 200);
    }
}
