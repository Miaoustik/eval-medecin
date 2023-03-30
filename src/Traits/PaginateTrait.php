<?php

namespace App\Traits;

trait PaginateTrait
{
    private function paginate ($request, $repository, $paginationPath, $maxResultPerPage = 10, $order = null, $property = [])
    {
        $maxResultPerPage = 10;
        $currentPage = $request->query->get('page') ?? 1;
        $items = $repository->findAllPaginatedBy(maxResult: $maxResultPerPage, page: $currentPage, order: $order, property: $property);
        $pageNumber = ceil(($repository->count([])) / $maxResultPerPage);

        return ([
            'items' => $items,
            'maxResultPerPage' => $maxResultPerPage,
            'pageNumber' => $pageNumber,
            'currentPage' => $currentPage,
            'paginationPath' => $paginationPath
        ]);
    }
}