<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;

trait PaginateTrait
{
    private function paginate (Request $request, $repository, int $maxResultPerPage = 10, string $order = null, mixed $property = null, array $criteria = null): array
    {
        $currentPage = $request->query->get('page') ?? 1;
        $items = $repository->findAllPaginatedBy(maxResult: $maxResultPerPage, page: $currentPage, order: $order, property: $property, criteria: $criteria);
        $pageNumber = ceil(($repository->countString($criteria ?? [])) / $maxResultPerPage);

        return ([
            'items' => $items,
            'maxResultPerPage' => $maxResultPerPage,
            'pageNumber' => $pageNumber,
            'currentPage' => $currentPage
        ]);
    }

    private function paginateWithSearch (Request $request, $repository, string $searchBy, int $maxResultPerPage = 10, string $order = null, mixed $property = null, array $criteria = null): array
    {

        $search = $request->query->get('q');
        if ($search) {
            $criteria[$searchBy] = $search;
        }

        $params = $this->paginate(
            request: $request,
            repository: $repository,
            maxResultPerPage: $maxResultPerPage,
            order: $order,
            property: $property,
            criteria: $criteria
        );

        $params['search'] = $search;

        return $params;
    }
}