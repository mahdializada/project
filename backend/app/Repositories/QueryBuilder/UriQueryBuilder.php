<?php

namespace App\Repositories\QueryBuilder;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class UriQueryBuilder
{


    private Model $model;

    protected UriParser $uriParser;

    protected array $orderBy = [];

    protected string $sortByKey = "sortBy";

    protected string $sortDescKey = "sortDesc";

    public int $itemsPerPage = 10;

    protected string $itemsPerPageKey = "itemsPerPage";

    protected int    $page      = 1;

    protected string $pageKey   = "page";

    protected array  $columns   = ['*'];

    protected array  $relations = [];

    public string $default_order_by = "created_at";

    public $query;


    public function __construct(Model $model, Request $request, bool $onlyTrashed = false)
    {
        $this->uriParser = new UriParser($request);
        $this->model = $model;
        if ($onlyTrashed) {
            $this->query = $model::onlyTrashed();
        } else {
            $this->query = $this->model->newQuery();
        }
    }

    public function setWithTrashed()
    {
        $this->query = $this->model->newQuery()->withTrashed();
    }

    public function setTableName($table_name)
    {
        $this->query = $this->model->setTable($table_name);
    }
    public function returnQuery()
    {
        return $this->query;
    }
    /**
     * Take URL query parameters and parse it to array
     *
     */
    private function setOrderBy()
    {
        $sortBy = $this->uriParser->queryParameter($this->sortByKey);
        $sortDesc = $this->uriParser->queryParameter($this->sortDescKey);
        $sortBy = $sortBy["values"];
        $sortDesc = $sortDesc["values"];
        $orderBy = [];
        $length = count($sortBy);
        if ($length > 0) {
            for ($index = 0; $index < $length; $index++) {
                $order = [
                    "column" => $sortBy[$index],
                    "direction" => $sortDesc[$index] == "true" ? "desc" : "asc",
                ];
                array_push($orderBy, $order);
            }
        }
        $this->orderBy = $orderBy;
    }

    /**
     * Assign relations to query
     *
     */
    private function withRelations()
    {
        if (count($this->relations) > 0) {
            $this->query = $this->query->with($this->relations);
        }
    }


    /**
     * Build Pagination Settings for resource
     * @return UriQueryBuilder
     * @throws Exception
     */
    public function build(): UriQueryBuilder
    {
        $this->withRelations();

        if ($this->uriParser->hasQueryParameter($this->sortByKey)) {
            $this->orderBy();
        }

        if ($this->uriParser->hasQueryParameter($this->pageKey)) {
            $this->setPage();
        }

        if ($this->uriParser->hasQueryParameter($this->itemsPerPageKey)) {
            $this->setItemsPerPage();
        }

        return $this;
    }


    /**
     * Set page value
     */
    private function setPage()
    {
        $page = $this->uriParser->queryParameter($this->pageKey);
        $this->page = $page["value"];
    }


    /**
     * Set items per page value
     */
    private function setItemsPerPage()
    {
        $itemsPerPage = $this->uriParser->queryParameter($this->itemsPerPageKey);
        $this->itemsPerPage = $itemsPerPage["value"];
    }


    /**
     * Set order action
     *
     * @throws Exception
     */
    protected function orderBy()
    {
        $this->setOrderBy();

        foreach ($this->orderBy as $order) {
            $column = $order["column"];
            if (!$this->hasTableColumn($column)) {
                // throw new Exception("Unknown column [{$column}] in [{$this->model->getTable()}] table");
                return  $this->query;
            }
            $this->query = $this->query->orderBy($order["column"], $order["direction"]);
        }
    }


    /**
     * Return all resources
     *
     * @return array
     */
    public function get(): array
    {
        $this->withRelations();
        $items = $this->query->get();
        return ["result" => true, "total" => $items->count(), "data" => $items->all()];
    }


    public function paginate(): JsonResponse
    {
        if ($this->itemsPerPage == -1) {
            $items = $this->query->latest($this->default_order_by)->get();
            $result = ["result" => true, "total" => $items->count(), "data" => $items->all()];
            return response()->json($result, Response::HTTP_OK);
        }
        $result = $this->query->latest($this->default_order_by)->paginate($this->itemsPerPage);
        $data = $result->items();
        $total = $result->total();
        $result = [
            "data" => $data,
            "total" => $total,
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Check model has the specified column
     *
     * @param $column
     * @return bool
     */
    private function hasTableColumn($column): bool
    {
        return (Schema::hasColumn($this->model->getTable(), $column));
    }

    /**
     * Set Model Relations if has
     * @param array $relations
     * @return UriQueryBuilder
     */
    public function setRelations(array $relations): UriQueryBuilder
    {
        $this->relations = $relations;
        return $this;
    }

    public function filterWithAuthCompany($hasMany = true)
    {
        $authCompanies =  json_decode(Auth::user()->selected_companies);
        if ($authCompanies) {
            if ($hasMany) {
                $this->query = $this->query->whereHas("companies", function ($builder) use ($authCompanies) {
                    $builder->whereIn("companies.id", $authCompanies);
                });
            } else {
                $this->query = $this->query->whereIn("company_id", $authCompanies);
            }
        }
    }
}
