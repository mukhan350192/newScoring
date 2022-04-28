<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        $iin = $data['iin'];
        $url = 'https://secure2.1cb.kz/asource/v1/' . strval($iin) . '.xml';
        $username = env('xdata_username');
        $password = env('xdata_password');
        $result['success'] = false;
        do {
            $http = new Client(['verify' => false]);
            try {
                $response = $http->get($url, [
                    'auth' => [
                        $username,
                        $password,
                    ],
                ]);
                //$response = $response->getBody()->getContents();
                $xml = simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA);
                var_dump($xml);
                $full = ($xml['ROOT']['Header']['Subject']['Name']['value'][0]);

                $lastName = $data['lastName'];
                $name = $data['name'];
                $fatherName = $data['name'];

                $original = mb_strtoupper($lastName) . ' ' . mb_strtoupper($name) . ' ' . mb_strtoupper($fatherName);
                if (strcmp($full, $original) == 0) {
                    $result['fio'] = 'ФИО совпадают';
                } else if (strcmp($full, $original) !== 0) {
                    $result['fio'] = "ФИО не совпадают $full";
                }
                $result['error'] = false;
                if ($xml['TerrorList']['Status']['id'][0] == 1) {
                    $result['message1'] = 'Перечень организаций и лиц, связанных с финансированием терроризма и экстремизма. Не найден.';
                    $result['error'] = true;
                }

                if ($xml['KgdWanted']['Status']['id'][0] == 1) {
                    $result['message2'] = 'Розыск Комитетом государственных доходов Министерства Финансов РК. Найден';
                    $result['error'] = true;
                }

                if ($xml['QamqorList']['Status']['id'][0] == 1) {
                    $result['message3'] = 'Розыск преступников, должников, без вести пропавших лиц Комитетом по правовой статистике и специальным учетам ГП РК. Найден.';
                    $result['error'] = true;
                }

                if ($xml['FalseBusi']['Status']['id'][0] == 1) {
                    $result['message4'] = 'Перечень налогоплательщиков, осуществивших лжепредпринимательскую деятельность. Найден.';
                    $result['error'] = true;
                }

                if ($xml['Pedophile']['Status']['id'][0] == 1) {
                    $result['message5'] = 'Сведения о лицах, привлеченные к уголовной отвественности за совершение уголовных правонарушений против половой неприкосновенности несовершеннолетних. Найден.';
                    $result['error'] = true;
                }


                $n = (array)$xml['DebtorBan']['Status'];
                if ($n['@attributes']['id'][0] == 3) {
                    $result['access'] = true;

                }
                if ($n['@attributes']['id'][0] == 1) {
                    $amount = [];
                    $newAmount = [];
                    for ($i = 1; $i < sizeof($xml['DebtorBan']['Companies']['Company']); $i++) {
                        $a = (array)$xml['DebtorBan']['Companies']['Company'][$i]['RecoveryAmount'];
                        array_push($amount, $a['@attributes']['value']);
                        $newAmount = array_unique($amount);
                        array_push($newAmount);
                    }
                    $sum = 0;
                    foreach ($newAmount as $key) {
                        $sum += $key;
                    }
                    if ($sum > 90000) {
                        $result['error'] = true;
                        $result['message6'] = 'Сумма взыскании ' . $sum . ' тенге.';
                    }
                }


            } catch (BadResponseException $e) {
                info($e);
            }
        } while (false);
        $leadID = $data['leadID'];
        $phone = $data['phone'];
        $responseUrl = 'https://icredit-crm.kz/api/test/test.php?leadID=' . $leadID . '&phone=' . $phone.'&iin=' . $iin . '&';
        if (isset($result['error']) && $result['error'] == true) {
            $responseUrl .= 'otkazId=10411&message=2';
        }

        if (isset($result['access']) && $result['access'] == true){
            $responseUrl .= 'access=true&message=1';
        }
        $http->get($responseUrl);
    }
}
