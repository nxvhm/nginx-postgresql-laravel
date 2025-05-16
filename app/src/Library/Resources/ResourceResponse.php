<?php

namespace App\Library\Resources;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Scope;
use App\Library\Resources\Serializers\ArraySerializer;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use App\Library\Resources\Pagination\PaginationData;
use App\Library\Resources\Pagination\ResourcePaginator;

class ResourceResponse {

	public Manager $manager;
	public ?ResourcePaginator $resourcePaginator = null;

	public function __construct(
		private Item|Collection $resource,
		private ?SerializerAbstract $serializer = null){
			$this->manager = new Manager();
	}

	public function setResourcePaginator(ResourcePaginator $paginator): self {
		$this->resourcePaginator = $paginator;
		return $this;
	}

	public function getResourcePaginator(): ?ResourcePaginator {
		return $this->resourcePaginator;
	}
	/**
	 * When pagination is required, use this method to get an instance of the ResourceResponse Class
	 * @param Query $query
	 * @param PaginationData $pagination
	 * @param TransformerAbstract $transformer
	 * @param SerializerAbstract|null $serializer
	 * @return ResourceResponse
	 */
	public static function withPagination(
		Query $query,
		PaginationData $pagination,
		TransformerAbstract $transformer,
		?SerializerAbstract $serializer = null
	): ResourceResponse {
		$query
			->setFirstResult($pagination->getOffset())
			->setMaxResults($pagination->perPage);

		$paginatedResults = new Paginator($query);
		$paginator = (new ResourcePaginator($pagination))
			->setTotal($paginatedResults->count())
			->setCount(count($paginatedResults->getIterator()));

		return (new self(new Collection($paginatedResults, $transformer, 'items'), $serializer))->setResourcePaginator($paginator);
	}

	public function getManager(): Manager {
		return $this->manager;
	}

	public function setManager(Manager $manager): self {
		$this->manager = $manager;
		return $this;
	}

	public function getResponse(): Scope {
		$paginator = $this->getResourcePaginator();
		// dd($paginator->getUrl($paginator->getCurrentPage()));
		if(!empty($paginator))
			$this->resource->setPaginator($paginator);

		return $this->getManager()
			->setSerializer($this->serializer ?? new ArraySerializer)
			->createData($this->resource);
	}


}
