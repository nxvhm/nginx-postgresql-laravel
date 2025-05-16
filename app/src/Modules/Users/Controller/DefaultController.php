<?php

namespace App\Modules\Users\Controller;

use App\Library\Resources\Pagination\PaginationData;
use App\Library\Resources\ResourceResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Services\Authorization;
use App\Modules\Users\Transformers\User\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use League\Fractal\Resource\Item;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController {

	public function __construct(
		private EntityManagerInterface $entityManager,
		private UserTransformer $userTransformer,
		private Authorization $auth
	){}

	public function list(#[CurrentUser] ?User $user, Request $request, PaginationData $pagination): Response {
		$userRepository = $this->entityManager->getRepository(User::class);

		// PAGINATION EXAMPLE
		$resource = ResourceResponse::withPagination(
			$userRepository->findUsers($request->query->all(), true), // @TODO: Use some sort of eager loading for the required relations.
			$pagination,
			$this->userTransformer
		);
		$resource->getManager()->parseIncludes(['role', 'createdBy', 'owner', 'userPermissions']);
		return $this->json($resource->getResponse()->toArray());
	}

	public function view(int $id): Response {
		try {
			$userRepository = $this->entityManager->getRepository(User::class);
			if(empty($user = $userRepository->findOneBy(['id' => $id])))
				throw new HttpException(404, 'User not found.');

			$resource = new ResourceResponse(new Item($user, $this->userTransformer));
			$resource->getManager()->parseIncludes(['role', 'createdBy', 'owner', 'userPermissions']);
			return $this->json($resource->getResponse()->toArray());
		} catch (HttpException $th) {
			return $this->json($th->getMessage(), $th->getStatusCode());
		} catch (\Throwable $th) {
			return $this->json('Something went wrong.', 500);
		}
	}

	// Example with using the built permission checks from symfony
	#[IsGranted('permission')]
	public function permissions(Authorization $auth): Response {
		$userPermissions = $auth->getUserPermissionsFromDB();
		return $this->json($userPermissions);
	}
}
