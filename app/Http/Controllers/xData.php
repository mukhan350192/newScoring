<?php

namespace App\Http\Controllers;

use App\Jobs\SendXData;
use App\Jobs\test;
use App\Models\c;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class xData extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $leadID = $request->input('leadID');
        $phone = $request->input('phone');
        $lastName = $request->input('lastName');
        $fatherName = $request->input('fatherName');
        $name = $request->input('name');
        $iin = $request->input('iin');
        $result['success'] = false;
        do {
            if (!$leadID) {
                $result['message'] = 'Не передан лид айди';
                break;
            }
            if (!$phone) {
                break;
            }
            if (!$iin) {
                break;
            }
            /* $data = [
                 'leadID' => $leadID,
                 'iin' => $iin,
                 'phone' => $phone,
                 'lastName' => $lastName,
                 'name' => $name,
                 'fatherName' => $fatherName,
             ];*/
            $url = 'https://secure2.1cb.kz/asource/v1/' . strval($iin) . '.xml';
            $username = '7015382439';
            $password = '7015382439';
            $result['success'] = false;


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


                $result['error'] = false;
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
                if ($xml->Dynamics->Dynamic->status->id == 1){
                    $result['message'] = 'Информационный сервис. Комитет по правовой статистике и специальным учетам Генеральной прокуратуры Республики Казахстан. Найден';
                    $result['error'] = true;
                    break;
                }
              /*  if ($xml->Pedophile->Status->id[0] == 1) {
                    $result['message5'] = 'Сведения о лицах, привлеченные к уголовной отвественности за совершение уголовных правонарушений против половой неприкосновенности несовершеннолетних. Найден.';
                    $result['error'] = true;
                }*/


              /*  $n = (array)$xml->DebtorBan->Status;
                if ($n['@attributes']['id'][0] == 3) {
                    $result['access'] = true;

                }
                if ($n['@attributes']['id'][0] == 1) {
                    $amount = [];
                    $newAmount = [];
                    for ($i = 1; $i < sizeof($xml->DebtorBan->Companies->Company); $i++) {
                        $a = (array)$xml->DebtorBan->Companies->Company[$i]->RecoveryAmount;
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
                }*/


            } catch (BadResponseException $e) {
                info($e);
                print_r($e);
            }
            // SendXData::dispatch($data)->delay(now()->addSecond(10));
            $result['success'] = true;

        } while (false);

        return response()->json($result);
    }


    public function standard(Request $request)
    {
        $url = "http://secure2.1cb.kz/susn_status/api/v1/login";
        $username = env('xdata_username');
        $password = env('xdata_password');
        $result['success'] = false;
        do {
            $http = new Client(['verify' => false]);
            $response = $http->get($url, [
                'auth' => [
                    $username,
                    $password,
                ],
            ]);
            var_dump($response->getBody()->getContents());
        } while (false);
        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];
        test::dispatch($data);
        $result['success'] = true;
        return response()->json($result);
    }

    public function susn(Request $request){
        $iin = $request->input('iin');
        $url = "https://secure2.1cb.kz/susn-status/api/v1/login";
        $username = 7471656497;
        $password = 970908350192;
        $result['success'] = false;
        $result['error'] = false;
        do {
            if (!$iin) {
                $result['message'] = 'Не передан параметры';
                break;
            }
            if (strlen($iin) != 12) {
                $result['message'] = 'Длина ИИН должен быть 12';
                break;
            }
            $http = new Client(['verify' => false]);
            $response = $http->get($url, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode('7471656497:970908350192'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $hash = $response['access']['hash'];
            $url = "https://secure2.1cb.kz/susn-status/api/v1/subject/$iin";
            $headers = [
                'Content-Type' => 'application/json',
                'Consent-Confirmed' => 1,
            ];
            $body = [
                'token_hash' => $hash,
            ];

            $res = $http->get($url, [
                'headers' => $headers,
                'body' => json_encode($body),
            ]);
            $res = $res->getBody()->getContents();
            $res = json_decode($res,true);
            foreach ($res['status'] as $status){
                if ($status['statusCode'] == 10000){
                    $result['error'] = true;
                    $result['message'] = 'Многодетные матери, награжденные подвесками «Алтын алќа», «Кїміс алќа» или получившие ранее звание «Мать-героиня», а также награжденные орденами «Материнская слава» I и II степени';
                    break;
                }
                if ($status['statusCode'] == 39000){
                    $result['error'] = true;
                    $result['message'] = 'Многодетные семьи';
                    break;
                }
                if ($status['statusCode'] == 17005){
                    $result['error'] = true;
                    $result['message'] = 'Лица, осуществляющие уход за ребенком-инвалидом';
                    break;
                }
                if ($status['statusCode'] == 11100){
                    $result['error'] = true;
                    $result['message'] = 'Инвалиды первой группы';
                    break;
                }
            }
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }
}
