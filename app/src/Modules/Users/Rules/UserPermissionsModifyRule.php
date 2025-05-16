<?php

namespace App\Modules\Users\Rules;

use Throwable;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Repository\UserRepository;
use App\Modules\Users\Services\RoleService;
use App\Modules\Users\Services\UserService;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[Autoconfigure(public: true)]
class UserPermissionsModifyRule extends AuthorizationRuleAbstract {

	public function __construct(
		private UserRepository $userRepository,
		private UserService $userService,
		private RoleService $roleService){
	}

	public function execute(string $permission, array $params = []): bool {
		if(empty($params))
			return true;

		if(empty($params['user']))
			return false;

		try {
			$user = $this->getUser();
			$subject = $params['user'];

			if(!$subject instanceof User)
				$subject = $this->userRepository->findOneBy(['id' => $subject]);

			$authorization = $this->getAuthorization();
			if(!$authorization->checkAccess($user, 'user-modify', ['user' => $subject]))
				throw new HttpException(403, 'You are not allowed to perform this action.');

			if(empty($subject->getOwnerId())) {
				if(!$authorization->checkAccess($user, 'user-permission-modify-root-users'))
					throw new HttpException(403, 'You are not allowed to perform this action.');

			} elseif($subject->getOwnerId() != $user->getId()) {
				if(!$authorization->checkAccess($user, 'user-permission-modify-foreign-owner'))
					throw new HttpException(403, 'You are not allowed to perform this action.');
			}
			return true;
		} catch (Throwable $th) {
			return false;
		}
	}


}
