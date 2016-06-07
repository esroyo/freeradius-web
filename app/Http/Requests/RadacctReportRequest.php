<?php

namespace Freeradius\Http\Requests;

use Schema;
use Illuminate\Http\JsonResponse;
use Freeradius\Http\Requests\Request;

class RadacctReportRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'timezone' => 'required|in:' .
                implode(',', timezone_identifiers_list()),
            'metrics' => [
                'required',
                'regex:/^((' . implode('|', static::metrics()) . '),?)+$/'
            ],
            'granularity' => 'required|in:' .
                implode(',', static::granularities()),
            'dimension' => 'in:' .
                implode(',', static::dimensions()),
            'filters' => [
                'regex:/^(
                    [[:alnum:]]+
                    (' . implode('|', array_keys(static::operators())) . ')
                    [[:alnum:]]+,?
                )+$/x'
            ],
        ];
    }

	/**
	 * {@inheritdoc}
     */
	public function response(array $errors)
    {
    	return new JsonResponse($errors, 422);
	}

    public static function dimensions()
    {
        return [
            'username',
            'groupname',
            'realm',
            'nasipaddress',
            'nasportid',
            'nasportype',
            'calledstationid',
            'callingstationid',
            'servicetype',
            'framedprotocol',
            'framedipaddress'
        ];
    }

    public static function metrics()
    {
        return [
            'sessiontime',
            'inputoctets',
            'outputoctets'
        ];
    }

    public static function granularities()
    {
        return  [
            'session',
            'day',
            'total'
        ];
    }

    public static function operators()
    {
        return [
            // filter => MySQL operator
            '=='    =>  '=',
            '!='    =>  '<>',
        ];
    }
}
