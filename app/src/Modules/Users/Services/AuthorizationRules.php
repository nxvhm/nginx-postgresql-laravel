<?php

namespace App\Modules\Users\Services;

use Exception;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Rules\AuthorizationRuleAbstract;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuthorizationRules {
	const MAP = [
		'user-modify' => \App\Modules\Users\Rules\UserModifyRule::class,
		'user-permission-modify' => \App\Modules\Users\Rules\UserPermissionsModifyRule::class
	];

	public function __construct(private ContainerInterface $container){
	}

	public function makeRule(User $user, Authorization $auth, string $ruleClassName): AuthorizationRuleAbstract {
		if(!in_array($ruleClassName, AuthorizationRules::MAP))
			throw new Exception('Invalid authorization rule requested.');

		if(!is_subclass_of($ruleClassName, AuthorizationRuleAbstract::class))
			throw new Exception('Invalid authorization rule requested.');

		$rule = $this->container->get($ruleClassName);
		$rule->setAuthorization($auth);
		$rule->setUser($user);

		return $rule;
	}
}
