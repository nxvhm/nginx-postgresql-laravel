<?php

namespace App\Modules\Users\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManagerInterface;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use App\Library\Resources\ResourceResponse;
use App\Modules\Users\Entity\Role;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Services\Authorization;
use App\Modules\Users\Repository\PermissionRepository;
use App\Modules\Users\Transformers\Roles\RoleItemTransformer;

class RoleController extends AbstractController {

	public function __construct(private EntityManagerInterface $entityManager){}

	public function list(#[CurrentUser] ?User $user, Authorization $auth): Response {
		$roles = $this->entityManager
			->getRepository(Role::class)
			->getBaseRoleQuery($user, true)
			->getQuery()
			->getResult()
		;
		$resource = new ResourceResponse(new Collection($roles, new RoleItemTransformer));
		return $this->json($resource->getResponse()->toArray());
	}

	public function role(int $id): Response {
		$role = $this->entityManager->getRepository(Role::class)->find($id);
		if(empty($role))
			throw new NotFoundHttpException(sprintf('Role with id %s not found.', $id));

		$resource = new ResourceResponse(new Item($role, new RoleItemTransformer));
		return $this->json($resource->getResponse()->toArray());
	}

	public function getPermissions(int $id, PermissionRepository $permissionRepository): Response {
		$role = $this->entityManager->getRepository(Role::class)->find($id);
		if(empty($role))
			throw new NotFoundHttpException(sprintf('Role with id %s not found.', $id));

		$permissions = $permissionRepository->findByRole($role);
		return $this->json($permissions);
	}

}
