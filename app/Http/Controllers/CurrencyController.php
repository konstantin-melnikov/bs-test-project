<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Currency;

class CurrencyController extends Controller
{
    const DEFAULT_PERIOD = 30;

    public function show(Request $request, $currencyID, $from = null, $to = null)
    {
        if (!$request->wantsJson()) {
            return response()->json(
                ['errors' => ['Must have header `Accept: application/json`']],
                400
            );
        }
        $params = ['currencyID' => $currencyID, 'from' => $from, 'to' => $to];
        foreach ($params as $key => $value) {
            if (empty($value)) {
                unset($params[$key]);
            }
        }
        $validator = Validator::make(
            $params,
            [
                'currencyID' => 'alpha_num|max:10',
                'from'       => 'date_format:Y-m-d|before_or_equal:' . $request->input('to', date('Y-m-d')),
                'to'         => 'date_format:Y-m-d|before_or_equal:' . date('Y-m-d')
            ]
        );
        if (!$validator->passes()) {
            return response()->json(
                ['errors' => $validator->errors()->all()],
                400
            );
        }
        $hasCurrency = Currency::where('currencyID', $currencyID)->exists();
        if (!$hasCurrency) {
            return response()->json(
                ['errors' => ["Not found currency with id: {$currencyID}"]],
                404
            );
        }
        $from = ($from) ? $from : date('Y-m-d', strtotime('-'.self::DEFAULT_PERIOD.' days'));
        $to = ($to) ? $to : date('Y-m-d');
        $rates = Currency::where('currencyID', $currencyID)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->get();

        if (count($rates) == 0) {
            $currency = Currency::where('currencyID', $currencyID)->first();
            return response()->json(
                [
                    'data' => [
                        'currencyID' => $currency->currencyID,
                        'numCode'    => $currency->numCode,
                        'сharCode'   => $currency->сharCode,
                        'par'        => $currency->par,
                        'name'       => $currency->name,
                        'rates'      => []
                    ]
                ],
                200
            );
        }
        return response()->json(
            [
                'data' => [
                    'currencyID' => $rates->first()->currencyID,
                    'numCode'    => $rates->first()->numCode,
                    'сharCode'   => $rates->first()->сharCode,
                    'par'        => $rates->first()->par,
                    'name'       => $rates->first()->name,
                    'rates'      => $rates
                ]
            ],
            200
        );
    }
}
