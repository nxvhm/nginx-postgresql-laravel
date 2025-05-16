<?php

namespace App\Modules\Users\Rules;

use App\Modules\Users\Config\Enum\Type;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Repository\UserRepository;
use App\Modules\Users\Services\RoleService;
use App\Modules\Users\Services\UserService;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
class UserModifyRule extends AuthorizationRuleAbstract {

	public function __construct(
		private UserRepository $userRepository,
		private UserService $userService,
		private RoleService $roleService){
	}

	public function execute(string $permission, array $params = []): bool {
		if(empty($params['user']))
			return true;

		try {
			$result = false;
			$subject = $params['user'];
			$user = $this->getUser();
			$authorization = $this->getAuthorization();
			if(!$subject instanceof User)
				$subject = $this->userRepository->findOneBy(['id' => $params['user']]);

			if($subject->getType() == Type::AppConnect->value)
				throw new Exception('Invalid user type');

			if($subject->getId() == $user->getId())
				return $result;

			$userRole = $this->roleService->getRepository()->getRole($user->getRole(), true);
			$subjectRole = $this->roleService->getRepository()->getRole($subject->getRole(), true);

			if($authorization->checkAccess($user, 'user-view-all')) {
				$result = $subject->getOwnerId() == $user->getId() || $this->roleService->isParent($userRole, $subjectRole);
			} else {
				if($this->userService->isUserManagerTo($user->getId(), $subject->getId()))
					$result = $subject->getOwnerId() == $user->getId() || $this->roleService->isParent($userRole, $subjectRole, true);
			}

			return $result;
		} catch (\Throwable $th) {
			return false;
		}
	}
}
