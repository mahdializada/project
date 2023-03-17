<?php


namespace App\Repositories\QueryBuilder;

use Illuminate\Http\Request;

class UriParser
{

    const PATTERN = '/!=|=|<=|<|>=|>/';

    const ARRAY_QUERY_PATTERN = '/(.*)\[\]/';

    protected Request $request;

    protected array $constantParameters = [
        'sortBy',
        'sortDesc',
        'itemsPerPage',
        'page',
        'multiSort',
    ];

    protected string $uri;

    protected $queryUri;

    protected array $queryParameters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->uri = $request->getRequestUri();

        $this->setQueryUri($this->uri);

        if ($this->hasQueryUri()) {
            $this->setQueryParameters($this->queryUri);
        }
    }

    public static function getPattern(): string
    {
        return self::PATTERN;
    }

    public static function getArrayQueryPattern(): string
    {
        return self::ARRAY_QUERY_PATTERN;
    }

    public function queryParameter($key)
    {

        $parameters = collect($this->queryParameters);
        $plucked = $parameters->pluck("key");
        $keys = $plucked->all();

        $queryParameters = array_combine($keys, $this->queryParameters);

        return $queryParameters[$key];
    }


    public function constantParameters(): array
    {
        return $this->constantParameters;
    }

    public function whereParameters(): array
    {
        return array_filter(
            $this->queryParameters,
            function ($queryParameter) {
                $key = $queryParameter['key'];
                return (!in_array($key, $this->constantParameters));
            }
        );
    }

    private function setQueryUri($uri)
    {
        $explode = explode('?', $uri);

        $this->queryUri = (isset($explode[1])) ? rawurldecode($explode[1]) : null;
    }

    private function setQueryParameters($queryUri)
    {
        $queryParameters = array_filter(explode('&', $queryUri));

        array_map([$this, 'appendQueryParameter'], $queryParameters);
    }

    public function itemsPerPage()
    {
        $itemsPerPage = $this->hasQueryParameter("itemsPerPage");
        $itemPerPage = $this->hasQueryParameter("itemPerPage");
        $items_Per_Page = $this->hasQueryParameter("items_per_page");
        $item_Per_Page = $this->hasQueryParameter("item_per_page");
        $perPage = $this->hasQueryParameter("perPage");
        $per_page = $this->hasQueryParameter("per_page");

        if ($itemsPerPage) {
            return $this->queryParameter("itemsPerPage");
        } else if ($itemPerPage) {
            return $this->queryParameter("itemPerPage");
        } else if ($items_Per_Page) {
            return $this->queryParameter("items_per_page");
        } else if ($item_Per_Page) {
            return $this->queryParameter("item_per_page");
        } else if ($perPage) {
            return $this->queryParameter("perPage");
        } else if ($per_page) {
            return $this->queryParameter("per_page");
        }
        return 10;
    }

    public function queryValue(string $key)
    {
        return $this->request->query->get($key);
    }

    private function appendQueryParameter($parameter)
    {
        // whereIn expression
        preg_match(self::ARRAY_QUERY_PATTERN, $parameter, $arrayMatches);
        if (count($arrayMatches) > 0) {
            $this->appendQueryParameterAsWhereIn($parameter, $arrayMatches[1]);
            return;
        }

        // basic where expression
        $this->appendQueryParameterAsBasicWhere($parameter);
    }

    private function appendQueryParameterAsBasicWhere($parameter)
    {
        preg_match(self::PATTERN, $parameter, $matches);

        $operator = $matches[0];

        list($key, $value) = explode($operator, $parameter);

        if (!$this->isConstantParameter($key) && $this->isLikeQuery($value)) {
            $operator = 'like';
            $value = str_replace('*', '%', $value);
        }

        $this->queryParameters[] = [
            'type' => 'Basic',
            'key' => $key,
            'operator' => $operator,
            'value' => $value
        ];
    }

    private function appendQueryParameterAsWhereIn($parameter, $key)
    {
        if (str_contains($parameter, '!=')) {
            $type = 'NotIn';
            $seperator = '!=';
        } else {
            $type = 'In';
            $seperator = '=';
        }

        $index = null;
        foreach ($this->queryParameters as $_index => $queryParameter) {
            if ($queryParameter['type'] == $type && $queryParameter['key'] == $key) {
                $index = $_index;
                break;
            }
        }

        if ($index !== null) {
            $this->queryParameters[$index]['values'][] = explode($seperator, $parameter)[1];
        } else {
            $this->queryParameters[] = [
                'type' => $type,
                'key' => $key,
                'values' => [explode($seperator, $parameter)[1]]
            ];
        }
    }

    public function hasQueryUri()
    {
        return ($this->queryUri);
    }

    public function getQueryUri()
    {
        return $this->queryUri;
    }

    public function hasQueryParameters(): bool
    {
        return (count($this->queryParameters) > 0);
    }

    public function hasQueryParameter($key): bool
    {
        $parameters = collect($this->queryParameters);
        $plucked = $parameters->pluck("key");
        $keys = $plucked->all();

        return (in_array($key, $keys));
    }

    private function isLikeQuery($query)
    {
        $pattern = "/^\*|\*$/";

        return (preg_match($pattern, $query, $matches));
    }

    private function isConstantParameter($key): bool
    {
        return (in_array($key, $this->constantParameters));
    }

}
