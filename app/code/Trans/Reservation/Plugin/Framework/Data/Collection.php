<?php

namespace Trans\Reservation\Plugin\Framework\Data;

class Collection
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     */

    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    /**
     * Retrieve collection last page number
     *
     * @return int
     */
    public function aroundGetLastPageNumber(\Magento\Framework\Data\Collection $subject, callable $proceed)
    {
        if ($this->request->getFullActionName() == 'catalog_category_view' || $this->request->getFullActionName() == 'catalogsearch_result_index') {
            $collectionSize = $this->getSizeCustom($subject);

            if (0 === $collectionSize) {
                return 1;
            } elseif ($subject->getPageSize()) {
                return ceil($collectionSize / $subject->getPageSize());
            } else {
                return 1;
            }
        }

        return $proceed();
    }

    /**
     * get size custom for join collection on product list
     *
     * @return int
     */
    public function getSizeCustom($subject)
    {
        $countSelect = clone $subject->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $part = $countSelect->getPart(\Magento\Framework\DB\Select::GROUP);
        if (!is_array($part) || !count($part)) {
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
        } else {
            $group = $countSelect->getPart(\Magento\Framework\DB\Select::GROUP);
            $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
            $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT ".implode(", ", $group).")")));
        }

        $totalRecords = $subject->getConnection()->fetchOne($countSelect);

        return (int)$totalRecords;
    }
}
