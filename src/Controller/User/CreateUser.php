<?php

namespace WishgranterProject\Backend\Controller\User;

use WishgranterProject\Backend\Access\AccessResultInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
            : $this->accessUnauthorized('You are already logged in');
    }

    /**
     * Validates the data submitted to the controller.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateData(array $data)
    {
        $knownInputs =
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

        foreach (array_keys($data) as $key) {
            if (!in_array($key, $knownInputs)) {
                throw new \InvalidArgumentException('Unrecognized property ' . $key);
            }
        }

        if ($data['password'] != $data['confirmPassword']) {
            throw new \InvalidArgumentException('Passwords do not match.');
        }

        if (!$this->userManager->validateUsername($data['username'])) {
            throw new \InvalidArgumentException('Invalid username. Alpha-numerical characters only, please.');
        }

        if ($this->userManager->getUserByUsername($data['username'])) {
            throw new \InvalidArgumentException('Username is taken.');
        }

        if (!filter_var($data['email'], \FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Please provide a valid e-mail address.');
        }
    }
}
