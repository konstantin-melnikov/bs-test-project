<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;

class HomeController extends Controller
{
    /**
     * Home page with currency list
     */
    public function index(Request $request)
    {
        $extremum = Currency::select(
            \DB::raw(
                'DATE_FORMAT(MIN(date), "%Y-%m-%d") AS minDate, DATE_FORMAT(MAX(date), "%Y-%m-%d") AS maxDate'
            )
        )->first();
        /**
         * @todo Check currency table on empty data
         */
        /**
         * @todo Validate request
         */
        $day = $request->input('day', date('Y-m-d'));
        $day = (strtotime($day) > strtotime($extremum->maxDate)) ? $extremum->maxDate : $day;

        $currencies = Currency::where('date', '=', $day)
            ->get();

        $data = [
            'dates' => [
                'min' => $extremum->minDate,
                'max' => $extremum->maxDate,
                'day' => $day
            ],
            'items' => $currencies->map(
                function ($item, $key) {
                    return [
                        'currency' => $item->par . ' ' . $item->name,
                        'rate'     => $item->value
                    ];
                }
            )
        ];
        return view('homepage', ['data' => $data]);
    }
}
