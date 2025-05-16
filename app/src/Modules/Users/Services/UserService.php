<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Entity\User;
use App\Modules\Users\Entity\UserAcl;
use App\Modules\Users\Repository\UserAclRepository;
use App\Modules\Users\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService {

	private UserRepository $userRepository;
	private UserAclRepository $userAclRepository;
	public function __construct(private EntityManagerInterface $entityManager){
		$this->userRepository = $entityManager->getRepository(User::class);
		$this->userAclRepository = $entityManager->getRepository(UserAcl::class);
	}

	public function isUserManagerTo(User|int $user, User|int $subject): bool {
		return $this->userAclRepository->isManagerTo(
			$user instanceof User ? $user->getId() : $user,
			$subject instanceof User ? $subject->getId() : $subject
		);
	}

}
