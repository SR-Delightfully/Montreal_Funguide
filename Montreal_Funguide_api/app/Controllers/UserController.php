<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Models\UserModel;
use App\Exceptions\HttpInvalidInputException;

use Exception;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class UserController extends BaseController
{
    public function __construct(
        private UserModel $users_model
    ) {
        parent::__construct();
    }

    public function index(Request $request, Response $response, array $args): Response
    {
        try {
            $users = $this->users_model->getAllUsers();
            return $this->renderJson(
                $response,
                [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $users
                ],
                200
            );
        } catch (Exception $e) {
            return $this->renderJson(
                $response,
                [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Failed to fetch users',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $user_id = (int) ($args['id'] ?? 0);
            if ($user_id <= 0) {
                throw new HttpBadRequestException(
                    $request,
                    'Invalid user ID'
                );
            }

            $user = $this->users_model->getUserById($user_id);
            if (!$user) {
                throw new HttpNotFoundException(
                    $request,
                    'User not found'
                );
            }

            return $this->renderJson($response, [
                'status' => 'success',
                'code' => 200,
                'data' => $user
            ], 200);
        } catch (HttpNotFoundException $e) {

            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 404,
                'message' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {

            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to fetch user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();

            $user_handle = trim($data['user_handle'] ?? '');
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
                throw new HttpInvalidInputException(
                    $request,
                    'All inputs are required.'
                );
            }

            if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                throw new HttpInvalidInputException(
                    $request,
                    'Invalid email format'
                );
            }

            if (strlen($user_password) < 8) {
                throw new HttpInvalidInputException(
                    $request,
                    'Password must be at least 8 characters'
                );
            }

            $existing_user = $this->users_model->findUserByEmail($user_email);

            if ($existing_user) {
                throw new HttpInvalidInputException(
                    $request,
                    'Email already exists in the system'
                );
            }

            $password_hash = password_hash(
                $user_password,
                PASSWORD_DEFAULT
            );

            $new_user_id = $this->users_model->createUser([
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
                'message' => 'User created successfully',
                'user_id' => $new_user_id
            ], 201);
        } catch (HttpInvalidInputException $e) {

            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 400,
                'message' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {

            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to create new user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $user_id = (int) ($args['id'] ?? 0);

            if ($user_id <= 0) {
                throw new HttpBadRequestException(
                    $request,
                    'User with the provided ID does not exist'
                );
            }

            $existing_user = $this->users_model
                ->getUserById($user_id);

            if (!$existing_user) {
                throw new HttpNotFoundException(
                    $request,
                    'User not found'
                );
            }

            $data = $request->getParsedBody();

            $updated = $this->users_model->updateUser(
                $user_id,
                $data
            );

            return $this->renderJson($response, [
                'status' => 'success',
                'code' => 200,
                'message' => 'User updated successfully',
                'updated' => $updated
            ], 200);
        } catch (HttpNotFoundException $e) {

            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 404,
                'message' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {

            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request, response $response, array $args): Response
    {
        try {
            $user_id = (int) ($args['id'] ?? 0);

            if ($user_id <= 0) {
                throw new HttpBadRequestException(
                    $request,
                    'Invalid user ID'
                );
            }

            $existing_user = $this->users_model->getUserById($user_id);

            if (!$existing_user) {
                throw new HttpNotFoundException(
                    $request,
                    'User not found'
                );
            }

            $this->users_model->deleteUser($user_id);

            return $this->renderJson($response, [
                'status' => 'success',
                'code' => 200,
                'message' => 'User deleted successfully'
            ], 200);
        } catch (HttpNotFoundException $e) {

            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 404,
                'message' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {

            return $this->renderJson($response, [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
