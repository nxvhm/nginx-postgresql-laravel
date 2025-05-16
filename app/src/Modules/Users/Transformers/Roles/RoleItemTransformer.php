<?php

namespace App\Modules\Users\Transformers\Roles;

use App\Modules\Users\Entity\Role;
use League\Fractal\TransformerAbstract;

class RoleItemTransformer extends TransformerAbstract {
	public function transform(Role $role): array {
		return [
			'id' => $role->getId(),
			'name' => $role->getName(),
			'defaultPage' => $role->getDefaultPage(),
			'system' => $role->getSystem(),
			'type' => $role->getType()
		];
	}
}
