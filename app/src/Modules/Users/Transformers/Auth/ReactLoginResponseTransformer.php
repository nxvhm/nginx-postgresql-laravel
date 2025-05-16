<?php

namespace App\Modules\Users\Transformers\Auth;

use App\Config\Enum\YesNo;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Services\Authorization;
use Exception;
use League\Fractal\TransformerAbstract;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
class ReactLoginResponseTransformer extends TransformerAbstract {

	private $auth;
	public function __construct(Authorization $auth) {
		$this->auth = $auth;
	}

	public function transform(JWTPostAuthenticationToken $token): array {
		$user = $token->getUser();

		if(!$user instanceof User)
			throw new Exception('Authenticated resource is not User Entity');

		return [
			'token' => $token->getCredentials(),
			'user' => [
				'id' => $user->getId(),
				'name' => $user->getName(),
				'email' => $user->getEmail(),
				'role' => [
					'name' => $user->getRole()->getName(),
					'id' => $user->getRole()->getId()
				],
				'permissions' => $this->getGrantedSimplePermissions()
			]
		];
	}

	private function getGrantedSimplePermissions(): array {
		$grantedSimplePermissions = [];
		foreach($this->auth->getUserPermissions() as $permission) {
			if(!empty($permission['rule_id']))
				continue;

			if($permission['use']->value == YesNo::Yes->value)
				$grantedSimplePermissions[] = $permission['name'];
		}

		return $grantedSimplePermissions;
	}
}
