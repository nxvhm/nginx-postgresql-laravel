<?php

namespace App\Library\Resources\Serializers;

use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer as FractalArraySerializer;

class ArraySerializer extends FractalArraySerializer {

	public function paginator(PaginatorInterface $paginator): array
	{
		$currentPage = $paginator->getCurrentPage();
		$lastPage = $paginator->getLastPage();

		$pagination = [
				'total' => $paginator->getTotal(),
				'count' => $paginator->getCount(),
				'per_page' => $paginator->getPerPage(),
				'current_page' => $currentPage,
				'total_pages' => $lastPage,
		];

		$pagination['links'] = [];
		$pagination['links']['first'] = $paginator->getUrl(1);
		$pagination['links']['last'] = $paginator->getUrl($paginator->getLastPage());
		$pagination['links']['current'] = $paginator->getUrl($currentPage);

		if($currentPage > 1)
			$pagination['links']['previous'] = $paginator->getUrl($currentPage - 1);

		if($currentPage < $lastPage)
			$pagination['links']['next'] = $paginator->getUrl($currentPage + 1);

		if(empty($pagination['links']))
			$pagination['links'] = (object) [];

		return ['pagination' => $pagination];
	}

}
