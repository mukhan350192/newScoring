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
            $username = env('xdata_username');
            $password = env('xdata_password');
            $result['success'] = false;
            var_dump($username);

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
                print_r($xml);


                $result['error'] = false;
                if ($xml->TerrorList->Status->id[0] == 1) {
                    $result['message1'] = 'Перечень организаций и лиц, связанных с финансированием терроризма и экстремизма. Не найден.';
                    $result['error'] = true;
                }

                if ($xml->KgdWanted->Status->id[0] == 1) {
                    $result['message2'] = 'Розыск Комитетом государственных доходов Министерства Финансов РК. Найден';
                    $result['error'] = true;
                }

                if ($xml->QamqorList->Status->id[0] == 1) {
                    $result['message3'] = 'Розыск преступников, должников, без вести пропавших лиц Комитетом по правовой статистике и специальным учетам ГП РК. Найден.';
                    $result['error'] = true;
                }

                if ($xml->Pedophile->Status->id[0] == 1) {
                    $result['message5'] = 'Сведения о лицах, привлеченные к уголовной отвественности за совершение уголовных правонарушений против половой неприкосновенности несовершеннолетних. Найден.';
                    $result['error'] = true;
                }


                $n = (array)$xml->DebtorBan->Status;
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
                }


            } catch (BadResponseException $e) {
                info($e);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\c $c
     * @return \Illuminate\Http\Response
     */
    public function show(c $c)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\c $c
     * @return \Illuminate\Http\Response
     */
    public function edit(c $c)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\c $c
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, c $c)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\c $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(c $c)
    {
        //
    }
}
