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
            : $this->accessDenied('You are already logged in.');
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

        $this->validateDataPassword($data);

        $this->validateDataUsername($data);

        $this->validateDataEmail($data);
    }

    protected function validateDataPassword(array $data)
    {
        if (isset($data['password']) && $data['password'] != $data['confirmPassword']) {
            throw new \InvalidArgumentException('Passwords do not match.');
        }
    }

    protected function validateDataUsername(array $data)
    {
        if ($this->userManager->getUserByUsername($data['username'])) {
            throw new \InvalidArgumentException('Username is taken.');
        }
    }

    protected function validateDataEmail(array $data)
    {
        if (!empty($data['email']) && $this->userManager->getUserByEmail($data['email'])) {
            throw new \InvalidArgumentException('This e-mail is in use.');
        }
    }

    protected function validateDataFormats(array $data)
    {
        $this->validateDataFormatsUsername($data);
        $this->validateDataFormatsEmail($data);
    }

    protected function validateDataFormatsUsername($data)
    {
        if (isset($data['username']) && !$this->userManager->validateUsername($data['username'])) {
            throw new \InvalidArgumentException('Invalid username. Alpha-numerical characters only, please.');
        }
    }

    protected function validateDataFormatsEmail($data)
    {
        if (isset($data['email']) && !filter_var($data['email'], \FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Please provide a valid e-mail address.');
        }
    }

    protected function getKnownProperties(array $data): array
    {
        return [
            'password',
            'confirmPassword',
            'username',
            'email',
        ];
    }

    protected function getRequiredProperties(array $data): array
    {
        return $this->getKnownProperties($data);
    }

    protected function validateKnownProperties(array $data)
    {
        $knownInputs = $this->getKnownProperties($data);

        foreach (array_keys($data) as $key) {
            if (in_array($key, $knownInputs)) {
                continue;
            }

            throw new \InvalidArgumentException('Unrecognized property ' . $key);
        }
    }

    protected function validateRequiredProperties(array $data)
    {
        $requiredInput = $this->getRequiredProperties($data);

        foreach ($requiredInput as $key) {
            if (isset($data[$key])) {
                continue;
            }

            throw new \InvalidArgumentException('Missing property ' . $key);
        }
    }
}
