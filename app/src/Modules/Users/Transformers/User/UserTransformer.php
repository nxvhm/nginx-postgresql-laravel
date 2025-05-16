<?php

namespace App\Modules\Users\Transformers\User;

use App\Library\Resources\Transformers\Traits\WithPermissionsTransformerTrait;
use App\Library\Resources\Transformers\WithPermissionsTransformerInterface;
use App\Modules\Users\Entity\Role;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Services\Authorization;
use League\Fractal\TransformerAbstract;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
class UserTransformer extends TransformerAbstract implements WithPermissionsTransformerInterface {

	use WithPermissionsTransformerTrait;

	private Authorization $auth;

	public function __construct(Authorization $auth) {
		$this->auth = $auth;
	}

	protected array $availableIncludes = [
		'role',
		'createdBy',
		'owner',
		'userPermissions'
	];

	public function transform(User $user): array {
		return [
			'id' => $user->getId(),
			'name' => $user->getName(),
			'email' => $user->getEmail(),
			'phone' => $user->getPhone(),
			'passwordChangedOn' => $user->getPasswordChangedOn(),
			'type' => $user->getType()->value,
			'autoDeactivationDate' => $user->getAutoDeactivationDate(),
			'active' => $user->getActive()->value,
			'riskLiability' => $user->getRiskLiability(),
			'timezone' => $user->getTimezone()
		];
	}

	public function includeRole(User $user) {
		$role = $user->getRole();
		return $this->item($role, function(Role $role) {
			return [
				'id' => $role->getId(),
				'name' => $role->getName()
			];
		});
	}

	public function includeCreatedBy(User $user) {
		return $this->item($user->getCreatedBy(), function(User $createdBy) {
			return [
				'id' => $createdBy->getId(),
				'name' => $createdBy->getName()
			];
		});
	}

	public function includeOwner(User $user) {
		return $this->item($user->getOwner(), function(User $owner) {
			return [
				'id' => $owner->getId(),
				'name' => $owner->getName()
			];
		});
	}

	public function getUserPermissionsList(): array {
		return [
			'user-modify' => ['model' => 'user'],
			'user-permission-modify' => ['model' => 'user']
		];
	}

	public function getResourcePermissionsList(): array {
		return [];
	}


}
