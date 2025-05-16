<?php

namespace App\Library\Resources\Pagination;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginationData {

	public int $page;
	public int $perPage;
	public int $total;

	public function __construct(protected RequestStack $requestStack, protected UrlGeneratorInterface $urlGenerator){
		$request = $requestStack->getCurrentRequest();
		$this->page = intval($request->get('page', 1));
		$this->perPage = intval($request->get('perPage', 10));
	}

	public function getOffset(): int {
		if($this->page <= 1)
			return 0;

		return ($this->page - 1)*$this->perPage;
	}

	public function getPageUrl(int $page): string {
		$request = $this->requestStack->getCurrentRequest();
		$query = $request->query->all();
		return $this->urlGenerator->generate($request->attributes->get('_route'), [...$query, 'page' => $page]);
	}
}
