<?php

namespace App\Modules\Users\Rules;

use App\Modules\Users\Entity\User;
use App\Modules\Users\Services\Authorization;
use Doctrine\ORM\EntityManagerInterface;

abstract class AuthorizationRuleAbstract {

	private User $user;
	private Authorization $auth;

	public function setUser(User $user): self {
		$this->user = $user;
		return $this;
	}

	public function getUser() {
		return $this->user;
	}

	public function setAuthorization(Authorization $auth): self {
		$this->auth = $auth;
		return $this;
	}

	public function getAuthorization(): Authorization {
		return $this->auth;
	}

	abstract function execute(string $permissionName, array $params): bool;

}
