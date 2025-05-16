<?php

namespace App\Modules\Users\Services;

use App\Config\Enum\YesNo;
use App\Modules\Users\Services\AuthorizationRules;
use App\Modules\Users\Entity\Permission;
use App\Modules\Users\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class Authorization {

	/**
	 * Authenticated user permissions
	 * @var array
	 */
	private array $userPermissions = [];

	public function __construct(
		private EntityManagerInterface $entityManager,
		private TokenStorageInterface $tokenStorageInterface,
		private Security $security,
		private AuthorizationRules $authorizationRules
	){}

	/**
	 * Get the currently authenticated user
	 * @return User
	 */
	public function getUser(): User {
		$user = $this->tokenStorageInterface->getToken()->getUser();
		if(empty($user) || !$user instanceof User)
			throw new Exception(sprintf('Expected App\\Entity\\User but received %s', get_class($user)));

		return $user;
	}
	/**
	 * Get current User Permissions
	 * @return array
	 */
	public function getUserPermissions(): array {
		return $this->userPermissions;
	}

	/**
	 * Get all permissions for a given user
	 * @param User|null $user
	 * @return array
	 */
	public function getUserPermissionsFromDB(?User $user = null): array {
		$user = $user ?? $this->getUser();
		$permissionRepository = $this->entityManager->getRepository(Permission::class);
		$roleIndexedPermissions = [];
		$userIndexedPermissions = [];

		foreach($permissionRepository->findUserExplicitPermissions($user) as $permission) {
			$userIndexedPermissions[$permission['name']] = $permission;
		}

		foreach($permissionRepository->findByRole($user->getRole()) as $permission) {
			$roleIndexedPermissions[$permission['name']] = $permission;
		}

		return array_merge($roleIndexedPermissions, $userIndexedPermissions);
	}
	/**
	 * Get only permissions where `use` == `Yes`
	 *
	 * @param array $permissionsList
	 * @return array
	 */
	public function getGrantedPermissions(array $permissionsList): array {
		return array_column(
			array_filter($permissionsList, fn($permissionItem) => !empty($permissionItem['use']) && $permissionItem['use']->value == YesNo::Yes->value),
			'name'
		);
	}
	/**
	 * Save user permissions into the service
	 * @param UserInterface $user
	 * @return void
	 */
	public function initUserPermissions(UserInterface $user): void {
		# @TODO: Implement Redis Permissions storage ?

		if(empty($this->getUserPermissions()))
			$this->userPermissions = $this->getUserPermissionsFromDB($user);
	}

	/**
	 * Check if permission exists and use is set to Yes in a given permissions array
	 * @param string $permissionName
	 * @param array $permissionsList
	 * @param array $params
	 * @return boolean
	 */
	private function checkFromProvidedPermissions(User $user, string $permissionName, array $permissionsList, array $params = []): bool {
		if(empty($permissionsList[$permissionName]))
			return false;

		if(empty($permissionsList[$permissionName]['use']))
			return false;

		if($permissionsList[$permissionName]['use']->value !== YesNo::Yes->value)
			return false;

		$rules = $this->authorizationRules::MAP;
		if(empty($rules[$permissionName]) || empty($params))
			return true;

		return $this->authorizationRules
			->makeRule($user, $this, $rules[$permissionName])
			->execute($permissionName, $params);
	}

	public function can(string $permissionName, array $params = []): bool {
		return $this->checkFromProvidedPermissions($this->getUser(), $permissionName, $this->userPermissions, $params);
	}

	public function checkAccess(User|int $user, string $permissionName, array $params = []) {
		$userRepository = $this->entityManager->getRepository(User::class);
		$user = is_numeric($user) ? $userRepository->findOneBy(['id' => $user]) : $user;
		if($user->getId() == $this->getUser()->getId())
			return $this->can($permissionName, $params);

		return $this->checkFromProvidedPermissions(
			$user,
			$permissionName,
			$this->getUserPermissionsFromDB($user),
			$params
		);
	}
}
