<?php

namespace WishgranterProject\Backend\Controller\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Access\AccessResultInterface;
use WishgranterProject\Backend\Exception\NotFound;

class UpdateUser extends CreateUser
{
    protected $user;

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
        $userId = $request->getAttribute('userId');
        if (!$this->userManager->userExists($userId)) {
            throw new NotFound('User ' . $userId . ' not found');
        }
        $this->user = $this->userManager->getUser($userId);

        $data = $this->getPostData($request);
        $this->validateData($data);

        //--------------------------
        $this->user->setUsername($data['username']);
        if (!empty($data['password'])) {
            $this->user->setHash($this->userManager->generateHash($data['password']));
        }
        //--------------------------

        $data = $this->dataTransferUser($this->user);

        return $this->jsonResource($data, 201)
            ->addSuccess(200, 'User updated', 'User ' . $data['username'] . ' updated.')
            ->renderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return $this->accessUnauthenticated();
        }

        $sameUser = $user->getId() == $request->getAttribute('userId');
        $isAdmin = $user->hasRole('admin');

        return $sameUser || $isAdmin
            ? $this->accessGranted()
            : $this->accessDenied('You are unauthorized to edit this user\'s account.');
    }

    protected function validateDataPassword(array $data)
    {
        if (empty($data['password'])) {
            return;
        }

        if ($data['password'] != $data['confirmPassword']) {
            throw new \InvalidArgumentException('Passwords do not match.');
        }

        if (!$this->userManager->validatePassword($data['existingPassword'], $this->user->getHash())) {
            throw new \InvalidArgumentException('Password incorrect.');
        }
    }

    protected function validateDataUsername(array $data)
    {
        $usingUsername = $this->userManager->getUserByUsername($data['username']);
        if (!$usingUsername) {
            return;
        }

        if ($usingUsername->getId() != $this->user->getId()) {
            throw new \InvalidArgumentException('Username is taken.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getKnownProperties(array $data): array
    {
        return [
            'existingPassword',
            'password',
            'confirmPassword',
            'username',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredProperties(array $data): array
    {
        $required = ['username'];

        if (!empty($data['existingPassword']) || !empty($data['password']) || !empty($data['confirmPassword'])) {
            $required = array_merge($required, ['existingPassword', 'password', 'confirmPassword']);
        }

        return $required;
    }
}
