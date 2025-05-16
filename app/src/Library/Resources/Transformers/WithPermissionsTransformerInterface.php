<?php

namespace App\Library\Resources\Transformers;

interface WithPermissionsTransformerInterface {
	/**
	 * Get a list of permissions that should be checked
	 * for the authenticated user against the transformer resource
	 *
	 * @return array
	 */
	public function getUserPermissionsList(): array;

	/**
	 * Get a list of permissions that should be checked for the given transformer resource
	 *
	 * @return array
	 */
	public function getResourcePermissionsList(): array;
}
