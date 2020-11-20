<?php

namespace App\Dto;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class PaginatedDto.
 */
class PaginatedDto implements DtoInterface
{
    /**
     * @var int
     *
     * @Groups({"all"})
     */
    public int $currentPage;

    /**
     * @var int
     *
     * @Groups({"all"})
     */
    public int $totalPages;

    /**
     * @var int
     *
     * @Groups({"all"})
     */
    public int $totalItems;

    /**
     * @var array
     *
     * @Groups({"all"})
     */
    public array $items;

    /**
     * PaginatedDto constructor.
     *
     * @param Pagerfanta $paginator
     * @param array      $items
     */
    public function __construct(Pagerfanta $paginator, array $items)
    {
        $this->items       = $items;
        $this->totalItems  = $paginator->getNbResults() ?? 0;
        $this->currentPage = $paginator->getCurrentPage();
        $this->totalPages  = $paginator->getNbPages();
    }
}
