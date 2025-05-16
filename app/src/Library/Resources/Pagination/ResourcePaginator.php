<?php

namespace App\Library\Resources\Pagination;

use League\Fractal\Pagination\PaginatorInterface;

class ResourcePaginator implements PaginatorInterface {

	private PaginationData $data;
	private int $total;
	private int $count;

	public function __construct(PaginationData $paginationData) {
		$this->setPaginationData($paginationData);
	}

	public function setPaginationData(PaginationData $paginationData): self {
		$this->data = $paginationData;
		return $this;
	}

	public function getData(): PaginationData {
		return $this->data;
	}

	public function setTotal(int $total = 0): self {
		$this->total = $total;
		return $this;
	}

	public function setCount(int $count = 0): self {
		$this->count = $count;
		return $this;
	}

	public function getCurrentPage(): int {
		return $this->data->page;
	}

	public function getLastPage(): int {
		return (int) ceil($this->getTotal() / $this->getCount());
	}

	public function getTotal(): int {
		return $this->total;
	}

	public function getCount(): int {
		return $this->count;
	}

	public function getPerPage(): int {
		return $this->data->perPage;
	}

  public function getUrl(int $page): string {
		return $this->data->getPageUrl($page);
	}
}
