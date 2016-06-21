<?php

namespace FreeradiusWeb\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use DB;
use Carbon\Carbon;

use FreeradiusWeb\Http\Controllers\Controller;
use FreeradiusWeb\Radacct;
use FreeradiusWeb\Http\Requests\RadacctRequest;

class RadacctController extends Controller
{
    const PREFIX = 'acct';
    const ERROR_NOREQUEST = 'No valid request has been set in the controller';
    const ERROR_NODATA = 'No data has been fetched';

    protected $request;
    protected $data;

    /**
     * Output the report.
     *
     * @param RadacctRequest $request
     * @return Response
     */
    public function show(RadacctRequest $request)
    {
        return response()->success(
            $this->setRequest($request)
                ->fetchData()
                ->formatData()
                ->get()
            );
    }

    /**
     * Get data of the report.
     *
     * @return Array
     */
    protected function get()
    {
        $this->checkDataOrFail();
        return $this->data;
    }

    /**
     * Set request to work with.
     *
     * @return RadacctController
     */
    protected function setRequest(RadacctRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Fetch data for the report.
     *
     * @throws \Exception If no request has been previously set
     * @return RadacctController
     */
    protected function fetchData()
    {
        $this->checkRequestOrFail();
        $request =& $this->request;

        // collect columns to retrieve
		$total = $request->granularity === 'total';
		$startColName = static::c('starttime');
        $stopColName = static::c('stoptime');

		$columns = [
            'starttime' => $total ? "MIN($startColName)" : $startColName,
            'stoptime' => $total ? "MAX($stopColName)" : $stopColName
		];

        $columns = array_merge(
			$columns,
			$this->parseMetrics($request)
		);

        if ($request->dimension) {
			$columns[$request->dimension] = $request->dimension;
		}

        // join columns for the SELECT string
        $select = array_reduce(
            array_keys($columns),
            function ($carry, $alias) use ($columns) {
                $sep = is_null($carry) ? '' : ', ';
                return $carry . "{$sep}{$columns[$alias]} as $alias";
            });

        // date conditions passed to UTC (guess DB is in UTC)
        $startDate = Carbon::parse($request->start_date, $request->timezone)
            ->setTimezone('Z');
        $endDate = Carbon::parse($request->end_date, $request->timezone)
            ->setTimezone('Z');

        $query = Radacct::select(DB::raw($select))
            ->where($startColName, '>=', $startDate)
            ->where($startColName, '<=', $endDate)
			->where($stopColName, '<>', '');

        // add extra filters in the request
        if ($request->filters) {
            foreach($this->parseFilters($request) as $filter) {
                $query->where($filter['column'], $filter['operator'], $filter['value']);
            }
        }

        // only do explicit groupBy when there is a dimension
        // if granularity==total metrics will be aggregates (implicit groupBy)
		if ($request->dimension && $total) {
    	    $query->groupBy($request->dimension);
		}

        $this->data = $query->get();

        return $this;
    }

    /**
     * Format data for the report.
     *
     * @throws  \Exception If no data has been previously fetched
     * @return RadacctController
     */
    protected function formatData()
    {
        // checks
        $this->checkRequestOrFail();
        $this->checkDataOrFail();

        // init
        $request =& $this->request;
        $options = [
            'metrics' => $this->parseMetrics($request),
            'keys' => array_merge(explode(',', $request->dimension), ['starttime']),
            'timezone' => $request->timezone,
            'granularity' => $request->granularity
        ];
        $data = [];

        // process
        foreach ($this->data as $session) {
            // session may be formatted to multiple records (depends on granularity)
            foreach ($this->granularize($session, $options) as $id => $granularized) {
                // if the record already exists, just add the metrics to it
                if (isset($data[$id])) {
                    foreach ($options['metrics'] as $metric => $column) {
                        $data[$id]->$metric += $granularized->$metric;
                    }
                } else {
                    $data[$id] = $granularized;
                }
            }
        }

        // clean up the $id associations
        $data = array_values($data);

        // group data by dimension and remove dimension from each record
		if ($request->dimension) {
			$dimension = $request->dimension;
			$data = collect($data)
				->groupBy($request->dimension)
				->map(function ($item, $key) use ($dimension) {
					return $item->map(function ($item, $key) use ($dimension) {
						return collect($item)->except($dimension);
					});
				});
		}

		$this->data = $data;

        return $this;
    }

    protected function granularize($session, $options)
    {
        $opt = (Object) $options;

        // convert to the requested timezone
        $starttime = $session->starttime =
            $session->starttime->setTimezone($opt->timezone);
        $stoptime = $session->stoptime =
            $session->stoptime->setTimezone($opt->timezone);

        if (in_array($opt->granularity, ['session', 'total'])) {
            $id = implode(':,', array_only($session->toArray(), $opt->keys));
            yield $id => $session;
            // do not yield anything else
            return;
        }

        // if (granularity === 'day'):
        // split/aggregate sessions into days
        $sessionduration = max(1, $stoptime->diffInSeconds($starttime));
        $upperlimit = $stoptime->copy()->modify('+1 day');
        $checkpoint = $starttime->copy()->modify('+1 day, midnight, -1 sec');
        
        while ($checkpoint->lt($upperlimit)) {
            $split = clone $session;
            $actualendtime = $stoptime->lt($checkpoint) ?
                $stoptime : $checkpoint;
            $splitduration = $actualendtime->diffInSeconds($starttime);
            $weight = $splitduration / $sessionduration;
            foreach ($opt->metrics as $metric => $column) {
                $split->$metric = round($split->$metric * $weight);
            }
            $split->starttime = $starttime =
                $checkpoint->copy()->modify('-1 day, +1 sec');
            $split->stoptime = $checkpoint;
            // prepare next round
            $starttime->modify('+1 day');
            $checkpoint->modify('+1 day');
            // yield the split as a session
            $id = implode(':,', array_only($split->toArray(), $opt->keys));
            yield $id => $split;
        }
    }

    protected function checkRequestOrFail()
    { 
        if (!$this->request instanceof RadacctRequest) {
            throw new \Exception(static::ERROR_NOREQUEST);
        }
    }

    protected function checkDataOrFail()
    {
        if (!isset($this->data)) {
            throw new \Exception(static::ERROR_NODATA);
        }
    }

    protected function parseFilters(RadacctRequest $request)
    {
        $filters = [];
        $operators = RadacctRequest::operators();
        $pattern = '/^
            (?<column>[[:alnum:]]+)
            (?<operator>(' . implode('|', array_keys($operators)) . '))
            (?<value>[[:alnum:]]+)
        $/x';

        foreach (explode(',', $request->filters) as $filter) {
            preg_match($pattern, $filter, $m); // validated by RadacctRequest
            $filters[] = [
                'column' => $m['column'],
                'operator' => $operators[$m['operator']],
                'value' => $m['value']
            ];
        }

        return $filters;
    }

    protected function parseMetrics(RadacctRequest $request)
    {
        $columns = [];
        $total = $request->granularity === 'total';

        foreach(explode(',', $request->metrics) as $metric) {
            $colName = static::c($metric);
            $columns[$metric] = $total ? "SUM($colName)" : $colName;
        }

        return $columns;
    }

	protected static function c($str)
	{
		return static::PREFIX . $str;
	}
}
