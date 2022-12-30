<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitorRequest;
use App\Models\Recharge;
use App\Models\Visitor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class VisitorController extends Controller
{
    public function submit(StoreVisitorRequest $request)
    {
        $ip = Visitor::query()->where('ip_address',request()->ip())->first();
        if ($ip){
            Alert::error('Sorry you have wish him, come back Jan. 2nd 2024.','Thanks for your wish');
            return redirect(route('welcome'));
        };
       Visitor::query()->create([
            'name' =>$request->name,
            'number' =>$request->number,
            'message' =>$request->messages,
            'ip_address'=>request()->ip(),
        ]);
        Alert::success('I really appreciate the time you took to wish me on my special day.','Thanks for your wish');
        return redirect(route('visitor'));
    }

    public function dashboard(): Factory|View|Application
    {
        $wishes = Visitor::all();
        $response =Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer 211|CrkBdJxubacIpFKePn2geZlyotxKgUiP0zTKY0NB',
        ])->get('https://www.airtimenigeria.com/api/v1/balance/get')->json();
       $balance =$response['universal_wallet']['balance'];
        return view('dashboard',compact('wishes', 'balance'));
    }

    public function cardBonus(Request $request)
    {
        $ip = Recharge::query()->where('ip_address',request()->ip())->first();
        if ($ip){
            Alert::error('Sorry you have claim recharge card before come back Jan. 2nd 2024.','Thanks for your wish');
            return redirect(route('welcome'));
        };
        $network = $request->network;
        $number = $request->number;
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer 211|CrkBdJxubacIpFKePn2geZlyotxKgUiP0zTKY0NB',
        ])->post('https://www.airtimenigeria.com/api/v1/airtime/purchase',[
            'network_operator' => $network,
            'phone' => $number,
            'amount' => 100,
        ])->json();
       if ($response['status'] === 'failed'){
           Recharge::query()->create([
               'network' => $network,
               'number' => $number,
               'ip_address'=>request()->ip(),
               'status'=>$response['status']
           ]);
           Alert::error('Sorry we are unable to recharge you line please try next year Jan. 2nd, 2022. Do come early');
           return redirect(route('welcome'));
       }
        Recharge::query()->create([
            'network' => $network,
            'number' => $number,
            'ip_address'=>request()->ip(),
            'status'=>$response['status']
        ]);
        Visitor::query()->where('number', $number)->update(array('fund' => 'yes'));
        Alert::success('Congratulations your line have been recharge. Enjoy your card');
        return redirect(route('welcome'));
    }
}
