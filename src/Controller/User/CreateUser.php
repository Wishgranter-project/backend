<?php

namespace WishgranterProject\Backend\Controller\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Access\AccessResultInterface;

class CreateUser extends GetUser
{
    /**
     * Invoke the controler.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response object.
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data = $this->getPostData($request);
        $this->validateData($data);

        $userId = $this->userManager->getAvailableUserId($data['username']);

        $user = $this->userManager->getUser($userId);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setHash($this->userManager->generateHash($data['password']));

        $data = $this->dataTransferUser($user);

        return $this->jsonResource($data, 201)
            ->addSuccess(201, 'User registered', 'User ' . $data['username'] . ' registered')
            ->renderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user /** and $anonymous_registration_enabled */) {
            return $this->accessGranted();
        }

        return $user->hasRole('admin')
            ? $this->accessGranted()
            : $this->accessUnauthorized('You are already logged in.');
    }

    /**
     * Validates the data submitted to the controller.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateData(array $data)
    {
        $this->validateKnownProperties($data);

        $this->validateRequiredProperties($data);

        $this->validateDataFormats($data);

        if ($data['password'] != $data['confirmPassword']) {
            throw new \InvalidArgumentException('Passwords do not match.');
        }

        if ($this->userManager->getUserByUsername($data['username'])) {
            throw new \InvalidArgumentException('Username is taken.');
        }

        if ($this->userManager->getUserByEmail($data['email'])) {
            throw new \InvalidArgumentException('This e-mail is in use.');
        }
    }

    protected function validateDataFormats(array $data)
    {
        if (!$this->userManager->validateUsername($data['username'])) {
            throw new \InvalidArgumentException('Invalid username. Alpha-numerical characters only, please.');
        }

        if (!filter_var($data['email'], \FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Please provide a valid e-mail address.');
        }
    }

    protected function validateKnownProperties(array $data)
    {
        $knownInputs = [
            'password',
            'confirmPassword',
            'username',
            'email',
        ];

        foreach (array_keys($data) as $key) {
            if (!in_array($key, $knownInputs)) {
                throw new \InvalidArgumentException('Unrecognized property ' . $key);
            }
        }
    }

    protected function validateRequiredProperties(array $data)
    {
        $requiredInput = [
            'password',
            'confirmPassword',
            'username',
            'email',
        ];

        foreach ($requiredInput as $key) {
            if (!isset($data[$key])) {
                throw new \InvalidArgumentException('Missing property ' . $key);
            }
        }
    }
}
