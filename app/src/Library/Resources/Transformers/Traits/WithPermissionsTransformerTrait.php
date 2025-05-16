<?php

namespace App\Library\Resources\Transformers\Traits;

trait WithPermissionsTransformerTrait {

	public function includeUserPermissions($resource) {
		return $this->item($resource, function($resource) {
			$result = [];
			foreach($this->getUserPermissionsList() as $permission => $permissionParams) {
				if(empty($permissionParams) || empty($permissionParams['model'])) {
					$result[$permission] = $this->auth->can($permission);
					continue;
				}

				$params = [$permissionParams['model'] => $resource];
				if(!empty($permissionParams['modelValue'])) {
					$params[$permissionParams['model']] = $permissionParams['modelValue'] == 'resource'
						? $resource->{'get'.ucfirst($permissionParams['modelValue']['property'])}
						: $this->auth->getUser()->{'get'.ucfirst($permissionParams['modelValue']['property'])};
				}

				$result[$permission] = $this->auth->can($permission, $params);
			}

			return $result;
		});
	}
}
