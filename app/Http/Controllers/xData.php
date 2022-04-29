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
        do{
            if (!$leadID){
                $result['message'] = 'Не передан лид айди';
                break;
            }
            if (!$phone){
                break;
            }
            if (!$iin) {
                break;
            }
            $data = [
                'leadID' => $leadID,
                'iin' => $iin,
                'phone' => $phone,
                'lastName' => $lastName,
                'name' => $name,
                'fatherName' => $fatherName,
            ];
            SendXData::dispatch($data)->delay(now()->addSecond(10));
            $result['success'] = true;

        }while(false);

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function show(c $c)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function edit(c $c)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, c $c)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(c $c)
    {
        //
    }
}
