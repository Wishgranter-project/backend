<?php

namespace WishgranterProject\Backend\Controller\Collection\Item;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Helper\SearchResults;

/**
 * Searches for items within the entire collection.
 */
class SearchItems extends CollectionController
{
    use PaginationTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        list($currentPage, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $items            = $this->search($request);
        $resultsCount     = count($items);
        $pagesCount       = $this->countPages($resultsCount, $itemsPerPage);
        $slice            = array_slice($items, $offset, $limit);
        $currentPageCount = count($slice);

        $data = array_map([$this, 'dataTransferItem'], $slice);

        $searchResults = new SearchResults(
            $data,
            $currentPageCount,
            $currentPage,
            $pagesCount,
            $itemsPerPage,
            $resultsCount,
        );

        return $searchResults->renderResponse();
    }

    /**
     * Checks if a string is a valid field name.
     *
     * @param string $field
     *   The would be field name.
     *
     * @return bool
     *   True if it is.
     */
    public static function isValidFieldName(string $field): bool
    {
        if (in_array($field, ['uuid', 'title', 'artist', 'featuring', 'cover', 'album', 'soundtrack', 'genre'])) {
            return true;
        }

        if (preg_match('/xxx[\w]{1,100}$/', $field)) {
            return true;
        }

        /**
         * @todo Implements playlistId.
         *
         * if ($field == 'playlistId') {
         *    return true;
         * }
         */

        return false;
    }

    /**
     * Checks if a string is a logical operator.
     *
     * @param string $uriOperator
     *   An URI operator.
     *
     * @return bool
     *   True if it is.
     */
    public static function isLogicalOperator(string $uriOperator): bool
    {
        return in_array($uriOperator, array_keys(self::getLogicalOperators()));
    }

    /**
     * Checks if a string is a comparison operator.
     *
     * @param string $uriOperator
     *   An URI operator.
     *
     * @return bool
     *   True if it is.
     */
    public static function isComparisonOperator(string $uriOperator): bool
    {
        return in_array($uriOperator, array_keys(self::getComparisonOperators()));
    }

    /**
     * Given an URI operator, returns the search equivalent.
     *
     * @param string $uriOperator
     */
    public static function getComparisonOperatofFromUri(string $uriOperator): string
    {
        return self::getComparisonOperators()[$uriOperator];
    }

    /**
     * Returns the supported comparison logical operators.
     *
     * Indexed by the equivalent URI operator.
     *
     * @return array
     *   The operators.
     */
    public static function getLogicalOperators(): array
    {
        return [
            '$or'  => 'OR',
            '$and' => 'AND',
        ];
    }

    /**
     * Returns the supported comparison operators.
     *
     * Indexed by the equivalent URI operator.
     *
     * @return array
     *   The operators.
     */
    public static function getComparisonOperators(): array
    {
        return [
            '$eq'          => '=',
            '$ne'          => '!=',
            '$lt'          => '<',
            '$lte'         => '<=',
            '$gt'          => '>',
            '$gte'         => '>=',
            '$in'          => 'IN',
            '$notIn'       => 'NOT IN',
            '$contains'    => 'LIKE',
            '$notContains' => 'NOT LIKE',
            '$null'        => 'IS NULL',
            '$notNull'     => 'IS NOT NULL',
            '$between'     => 'BETWEEN',
            '$notBetween'  => 'NOT BETWEEN',
        ];
    }

    /**
     * Retrieves the filter parameters.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return array
     *   The filter parameters.
     */
    public static function getFilterParameters(ServerRequestInterface $request): array
    {
        $queryParams = $request->getQueryParams();
        $filters = (array) $queryParams['filters'] ?? [];

        // Simply being lenient with the filters, when added flat to the query string.
        foreach (['title', 'artist', 'album', 'featuring', 'soundtrack', 'genre'] as $property) {
            if (isset($filters[$property])) {
                continue;
            }

            if (!isset($queryParams[$property])) {
                continue;
            }

            // $contains ??
            $filters[$property] = is_array($queryParams[$property])
                ? ['$in' => $queryParams[$property]]
                : ['$eq' => $queryParams[$property]];

            unset($queryParams[$property]);
        }

        return $filters;
    }

    /**
     * Retrieves the sorting criteria.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return array
     *   The sorting criteria.
     */
    public static function getSortingCriteria(ServerRequestInterface $request): array
    {
        $queryParams = $request->getQueryParams();
        $criteria = !empty($queryParams['sort']) && is_array($queryParams['sort'])
            ? $queryParams['sort']
            : [];

        // Simply being lenient with the criteria, when added flat to the query string.
        $orderBy = $queryParams['orderBy'] ?? null;
        $sort = $queryParams['sort'] ?? null;

        if (!empty($orderBy) && !is_array($orderBy) && !is_array($sort)) {
            $criterion = $orderBy;
            $criterion .= $sort
                ? ':' . $sort
                : '';

            $criteria[] = $criterion;
        }

        return $criteria;
    }

    /**
     * Checks if a value is a sequential array.
     *
     * @param mixed $value
     *   The would be sequential array.
     *
     * @return bool
     *   True if it is a sequential array.
     */
    public static function isSequentialArray($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $keys = array_keys($value);
        $keys = array_filter($keys, 'is_numeric');

        return count($keys) == count($value);
    }

    /**
     * Searches for all matching items.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return WishgranterProject\DescriptivePlaylist\PlaylistItem[]
     *   Array of playlist items.
     */
    protected function search(ServerRequestInterface $request): array
    {
        $collection = $this->getCollection($request);
        $search     = $collection->search('AND');
        $filters    = self::getFilterParameters($request);
        $criteria   = self::getSortingCriteria($request);

        $this->addConditionsToSearch($search, $filters);
        $this->addSortingCriteria($search, $criteria);

        $results = $search->find();
        $all = array_values($results);

        return $all;
    }

    /**
     * Adds conditions to the search.
     *
     *  Example of an aggregated filter:
     *  ?filters[$and][0][$or][0][title][$contains]=noldor&filters[$and][0][$or][1][title][$contains]=hobbit&filters[$and][1][artist][$eq]=Blind+Guardian
     *
     *  It can be read better as:
     *  filters
     *      [$and]
     *          [0]
     *              [$or]
     *                  [0]
     *                      [title]
     *                          [$contains]=noldor
     *                  [1]
     *                      [title]
     *                          [$contains]=hobbit
     *          [1]
     *              [artist]
     *                  [$eq]=Blind+Guardian
     *  ------------------------------------------------
     *  Here a simpler example:
     *  ?filters[title][$contains]=noldor
     *
     * @param WishgranterProject\DescriptiveManager\Search\Search|
     *   WishgranterProject\DescriptiveManager\Search\ConditionGroup $search
     *   Search or condition group object.
     * @param array $filters
     *   Array of filters.
     * @param array $trail
     *   Trail of keys leading to the current filter.
     */
    protected function addConditionsToSearch($search, $filters, $trail = [])
    {
        foreach ($filters as $keyName => $keyValue) {
            $trail[] = $keyName;
            if (self::isLogicalOperator($keyName)) {
                $this->addConditionGroup($search, $keyName, $keyValue, $trail);
            } elseif (self::isComparisonOperator($keyName)) {
                $field = $trail[count($trail) - 2];
                $this->addCondition($search, $field, $keyName, $keyValue, $trail);
            } elseif (is_numeric($keyName)) {
                $this->addChildConditions($search, $keyValue, $trail);
            } elseif (self::isValidFieldName($keyName)) {
                $this->addFilterField($search, $keyName, $keyValue, $trail);
            } else {
                throw new \InvalidArgumentException('Invalid data at ' . $this->readableTrail($trail) . '.');
            }
        }
    }

    /**
     * Adds a condition to the search.
     *
     * @param WishgranterProject\DescriptiveManager\Search\Search|
     *   WishgranterProject\DescriptiveManager\Search\ConditionGroup $search
     *   Search or condition group object.
     * @param string $field
     *   The field.
     * @param string $uriOperator
     *   The comparison operator.
     * @param mixed $value
     *   Value to add to the condition.
     * @param array $trail
     *   Trail of keys leading to the current filter.
     */
    protected function addCondition($search, $field, $uriOperator, $value, $trail)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Empty value at ' . $this->readableTrail($trail) . '.');
        }

        $operator = self::getComparisonOperatofFromUri($uriOperator);
        $search->condition($field, $value, $operator);
    }

    /**
     * Adds a child conditions to the search.
     *
     * @param WishgranterProject\DescriptiveManager\Search\Search|
     *   WishgranterProject\DescriptiveManager\Search\ConditionGroup $search
     *   Search or condition group object.
     * @param mixed $child
     *   Child to add to the condition.
     * @param array $trail
     *   Trail of keys leading to the current filter.
     */
    protected function addChildConditions($search, $child, $trail)
    {
        if (!is_array($child)) {
            throw new \InvalidArgumentException('Invalid type at ' . $this->readableTrail($trail) . ', expected an array.');
        }

        $firstKey = array_keys($child)[0];
        $firstValue = array_values($child)[0];

        $conditionGroup  = self::isLogicalOperator($firstKey) && self::isSequentialArray($firstValue);
        $singleCondition = self::isValidFieldName($firstKey) && count($child) == 1;

        if (!$conditionGroup && !$singleCondition) {
            throw new \InvalidArgumentException('Invalid data at ' . $this->readableTrail($trail) .
              ', expected a condition or a condition group.');
        }

        $this->addConditionsToSearch($search, $child, $trail);
    }

    /**
     * Adds a condition group to the search.
     *
     * @param WishgranterProject\DescriptiveManager\Search\Search|
     *   WishgranterProject\DescriptiveManager\Search\ConditionGroup $search
     *   Search or condition group object.
     * @param mixed $childConditions
     *   Child conditions to add to the search.
     * @param array $trail
     *   Trail of keys leading to the current filter.
     */
    protected function addConditionGroup($search, $logicalOperator, $childConditions, $trail)
    {
        if (!is_array($childConditions) || !self::isSequentialArray($childConditions)) {
            throw new \InvalidArgumentException('Invalid type at ' . $this->readableTrail($trail) . ', expected a sequential array.');
        }

        $group = $logicalOperator == '$and'
            ? $search->andConditionGroup()
            : $search->orConditionGroup();

        $this->addConditionsToSearch($group, $childConditions, $trail);
    }

    /**
     * Adds a field to the search.
     *
     * @param WishgranterProject\DescriptiveManager\Search\Search|
     *   WishgranterProject\DescriptiveManager\Search\ConditionGroup $search
     *   Search or condition group object.
     * @param mixed $operatorAndValue
     *   Operator and value to add to the condition.
     * @param array $trail
     *   Trail of keys leading to the current filter.
     */
    protected function addFilterField($search, $field, $operatorAndValue, $trail = [])
    {
        if (!is_array($operatorAndValue)) {
            throw new \InvalidArgumentException('Invalid type at ' . $this->readableTrail($trail) . ', expected an array.');
        }

        $this->addConditionsToSearch($search, $operatorAndValue, $trail);
    }

    /**
     * Returns a human readable string of a filter trail.
     *
     * Just a helpful method to write error messages.
     *
     * @param array $trail
     *   Array of keys.
     *
     * @return string
     *   The trail as a string.
     */
    protected function readableTrail(array $trail)
    {
        return 'filters[' . implode('][', $trail) . ']';
    }

    /**
     * Adds sorting criteria to the search.
     *
     * Example: ?sort[0]=description:asc&sort[1]=name:desc&sort[2]=rand:token-goes-here
     *
     * @param WishgranterProject\DescriptiveManager\Search\Search $search
     *   Search object.
     * @param array $criteria
     *   Sorting criteria.
     */
    protected function addSortingCriteria($search, $criteria): void
    {
        foreach ($criteria as $key => $criterion) {
            $this->addSortingCriterion($search, $criterion, $key);
        }
    }

    /**
     * Adds a single sorting criterion to the search.
     *
     * @param WishgranterProject\DescriptiveManager\Search\Search $search
     *   Search object.
     * @param string $criterion
     *   Sorting criterion.
     * @param int $key
     *   Key of the current criterion.
     */
    protected function addSortingCriterion($search, $criterion, $key): void
    {
        if (is_array($criterion) || empty($criterion)) {
            throw new \InvalidArgumentException('Invalid criterion at sort[' . $key . '], expected a string.');
        }

        $selector = strtolower($criterion);
        $modifier = null;
        if (preg_match('/([A-Za-z\d]+):(.+)/', $criterion, $matches)) {
            $selector = $matches[1];
            $modifier = $matches[2] ?: $modifier;
        }

        if (in_array($selector, ['rand', 'random'])) {
            $search->orderRandomly($modifier);
            return;
        }

        $modifier = strtoupper($modifier ?: 'DESC');
        if (!$this->isValidFieldName($selector) || !in_array($modifier, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException('Unknown criterion at sort[' . $key . '].');
        }

        $search->orderBy($selector, $modifier);
    }
}
