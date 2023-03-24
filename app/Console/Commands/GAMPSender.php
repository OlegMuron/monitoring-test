<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GAMPSender extends Command
{
    private const NBU_URL = 'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?valcode=USD&date=%s&json';
    private const GA_MP_URL = 'https://www.google-analytics.com/mp/collect?measurement_id=%s&api_secret=%s';

    private const EVENT_NAME = 'uah_usd_rate';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gamp_sender:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send metric to the GA via GAMP';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        $usdRateDetailedRaw = Http::get(sprintf(self::NBU_URL, Carbon::now()->format('Ymd')))->body();
        $usdRate = json_decode($usdRateDetailedRaw, true)[0];

        $clientId = sprintf('%d.%d', random_int(100000000, 999999999), Carbon::now('UTC')->getTimestamp());

        Http::post(
            sprintf(
                self::GA_MP_URL,
                config('gamp.ga_measurement_id'),
                config('gamp.ga_api_secret')
            ),
            [
                'client_id' => $clientId,
                'events' => [
                    'name' => self::EVENT_NAME,
                    'params' => [
                        'value' => $usdRate['rate'],
                        'exchange_date' => $usdRate['exchangedate'],
                        'sent_time' => Carbon::now('UTC'),
                    ],
                ]
            ]
        );

        Log::info('Event sent with client_id = ' . $clientId);

        return Command::SUCCESS;
    }
}
