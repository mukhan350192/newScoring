<?php

namespace App\Jobs;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendXData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle()
    {
        $data = $this->data;
        $iin = $data['iin'];
        $result['success'] = false;
        do {
            $url = 'https://secure2.1cb.kz/asource/v1/' . strval($iin) . '.xml';
            $username = '7015382439';
            $password = '7015382439';

            $http = new Client(['verify' => false]);
            try {
                $response = $http->get($url, [
                    'auth' => [
                        $username,
                        $password,
                    ],
                ]);

                $xml = simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA);

                if ($xml->TerrorList->Status->id[0] == 1) {
                    $result['message'] = 'Перечень организаций и лиц, связанных с финансированием терроризма и экстремизма. Не найден.';
                    $result['error'] = true;
                    break;
                }

                if ($xml->KgdWanted->Status->id[0] == 1) {
                    $result['message'] = 'Розыск Комитетом государственных доходов Министерства Финансов РК. Найден';
                    $result['error'] = true;
                    break;
                }

                if ($xml->QamqorAlimony->Status->id[0] == 1) {
                    $result['message'] = 'Розыск алиментщиков Комитетом по правовой статистике и специальным учетам ГП РК. Найден';
                    $result['error'] = true;
                    break;
                }

                if ($xml->QamqorList->Status->id[0] == 1) {
                    $result['message'] = 'Розыск преступников, должников, без вести пропавших лиц Комитетом по правовой статистике и специальным учетам ГП РК. Найден.';
                    $result['error'] = true;
                    break;
                }
                if ($xml->Dynamics->Dynamic->status->id == 1) {
                    $result['message'] = 'Информационный сервис. Комитет по правовой статистике и специальным учетам Генеральной прокуратуры Республики Казахстан. Найден';
                    $result['error'] = true;
                    break;
                }

                $n = (array)$xml->DebtorBan->Status;

                if (isset($n['@attributes']) && $n['@attributes']['id'] == 3) {
                    $result['access'] = true;

                }
                if (isset($n['@attributes']) && $n['@attributes']['id'] == 1) {
                    $result['error'] = true;
                    $result['message'] = 'Актуальные сведения из единого реестра должников и временно ограниченных на выезд должников';
                    break;
                }


            } catch (BadResponseException $e) {
                info($e);
            }
            $result['success'] = true;
        } while (false);
        $leadID = $data['leadID'];
        $phone = $data['phone'];
        $responseUrl = 'https://icredit-crm.kz/api/webhock/cronResponseTest.php?leadID=' . $leadID . '&phone=' . $phone.'&iin=' . $iin . '&';
        if (isset($result['error']) && $result['error'] == true) {
            $responseUrl .= 'otkazId=10411&message=2';
        }

        if (isset($result['access']) && $result['access'] == true){
            $responseUrl .= 'access=true&message=1';
        }
        $s = $http->get($responseUrl);
        $s = $s->getBody()->getContents();
        $s = json_encode($s);
        DB::table('test')->insertGetId([
           'response' => $s,
        ]);
        return response()->json($result);
    }
}
