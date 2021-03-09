<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Currency as CurrencyModel;

class Currency extends Command
{
    /**
     * @example http://www.cbr.ru/scripts/XML_daily.asp?date_req=02/03/2002
     */
    const DAILY_URL = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=%s';
    /**
     * @example http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=02/03/2001&date_req2=14/03/2001&VAL_NM_RQ=R01235
     */
    const DYNAMIC_URL = 'http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=%s&date_req2=%s&VAL_NM_RQ=%s';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update {--M|method=daily : Use daily or dynamic} {--D|days=30 : Minimum 30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency for each day from http://www.cbr.ru.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $method = $this->option('method');
        $days = (is_numeric($this->option('days'))) ? (int) $this->option('days') : 30;
        if (!in_array($method, ['daily', 'dynamic'], true)) {
            $this->error("Wrong method '{$method}'");
            return false;
        }
        if ($method == 'daily') {
            $this->_updateCurrencyByDaily($days);
        } else {
            $this->_updateCurrencyByDynamic($days);
        }
    }

    private function _updateCurrencyByDaily($days)
    {
        $start = new \DateTime(date('Y-m-d', strtotime("-{$days} days")));
        $end = new \DateTime(date('Y-m-d'));
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($start, $interval, $end);
        foreach ($period as $day) {
            $arr = $this->_getData(self::DAILY_URL, [$day->format("d/m/Y")]);
            if (empty($arr['Valute'])) {
                $this->error("No currency for day {$day->format("d/m/Y")}");
                continue;
            }
            foreach ($arr['Valute'] as $currency) {
                try {
                    $model = CurrencyModel::updateOrCreate(
                        [
                            'currencyID' => $currency['@attributes']['ID'],
                            'date'       => $day->format('Y-m-d')
                        ],
                        [
                            'numCode'  => $currency['NumCode'],
                            'сharCode' => $currency['CharCode'],
                            'name'     => $currency['Name'],
                            'par'      => (int) $currency['Nominal'],
                            'value'    => (float) str_replace(',', '.', $currency['Value']),
                        ]
                    );
                    /**
                     * @todo updateOrCreate work incorrect wite `date` field
                     * use hardcode (:
                     */
                    $model->date = $day->format('Y-m-d');
                    $model->save();
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        $this->info("Currency was update from {$start->format('Y-m-d')} to {$end->format('Y-m-d')} by daily method");
    }

    private function _updateCurrencyByDynamic($days)
    {
        $start = new \DateTime(date('Y-m-d', strtotime("-{$days} days")));
        $end = new \DateTime(date('Y-m-d'));
        $daily = $this->_getData(self::DAILY_URL, [$end->format("d/m/Y")]);
        if (empty($daily['Valute'])) {
            $this->error("No currency for day {$end->format("d/m/Y")}");
            return false;
        }
        foreach ($daily['Valute'] as $currency) {
            $currencyID = $currency['@attributes']['ID'];
            $dynamic = $this->_getData(self::DYNAMIC_URL, [$start->format("d/m/Y"), $end->format("d/m/Y"), $currencyID]);
            if (empty($dynamic['Record'])) {
                $this->error("No currency for day {$day->format("d/m/Y")}");
                continue;
            }
            foreach ($dynamic['Record'] as $rate) {
                try {
                    $day = date_create_from_format('d.m.Y', $rate['@attributes']['Date']);
                    $model = CurrencyModel::updateOrCreate(
                        [
                            'currencyID' => $currencyID,
                            'date'       => $day->format('Y-m-d')
                        ],
                        [
                            'numCode'  => $currency['NumCode'],
                            'сharCode' => $currency['CharCode'],
                            'name'     => $currency['Name'],
                            'par'      => (int) $rate['Nominal'],
                            'value'    => (float) str_replace(',', '.', $rate['Value']),
                        ]
                    );
                    /**
                     * @todo updateOrCreate work incorrect wite `date` field
                     * use hardcode (:
                     */
                    $model->date = $day->format('Y-m-d');
                    $model->save();
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        $this->info("Currency was update from {$start->format('Y-m-d')} to {$end->format('Y-m-d')} by dynamic method");
    }

    private function _getData($url, $options)
    {
        try {
            $url = vsprintf($url, $options);
            $response = Http::retry(5, 1000)->get($url);
        } catch (\Exception $e) {
            $this->error(
                "Get xml falied for url {$url}"
            );
            return false;
        }
        $xml = simplexml_load_string(
            $response->body()
        );
        if (!$xml) {
            $this->error("Parse xml falied url {$url}");
            return false;
        }
        return json_decode(json_encode($xml), true);
    }
}
