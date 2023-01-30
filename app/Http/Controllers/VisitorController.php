<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitorRequest;
use App\Models\Recharge;
use App\Models\Visitor;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class VisitorController extends Controller
{
    /**
     * @var Repository|Application|mixed
     */
    protected $token;
    public function __construct()
    {
        $this->token =config('app.token');
    }
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
        $word = Visitor::all()->count();
        $plural = Str::plural('wish', $word);
        $response =Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ',
        ])->get('https://www.airtimenigeria.com/api/v1/balance/get')->json();
       $balance =$response['universal_wallet']['balance'];
        return view('dashboard',compact('wishes', 'balance','plural','word'));
    }

    public function cardBonus(Request $request)
    {
        $regex = '/^234[0-9]{10}/';
        $token = $this->token;
        $request->validate([
            'number' => [
                'required',
                'string',
                'max:13',
                'regex:'.$regex
            ],
        ], [
            'number.required' => 'Your phone number is required',
            'number.regex' => 'Your phone number is invalid',
            'number.max' => 'Your phone number is not complete',
        ]);
        $ip = Recharge::query()->where('ip_address',request()->ip())->first();
        if ($ip){
            Alert::error('Sorry you have claim recharge card before come back Jan. 2nd 2024.','Thanks for your wish');
            return redirect(route('welcome'));
        };
        $network = self::detectNetwork($request->number);
        $number = $request->number;
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
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
    public static function detectNetwork($phone): ?string
    {
        $formated_phone = "0{$phone[3]}{$phone[4]}{$phone[5]}";
        $eti = ["0809", "0909", "0817", "0818", "0908"];
        $mtn = [
            "0806",
            "0803",
            "0816",
            "0813",
            "0810",
            "0814",
            "0903",
            "0906",
            "0703",
            "0706",
        ];
        $glo = ["0805", "0705", "0905", "0807", "0815", "0811"];
        $airtel = ["0802", "0902", "0701", "0808", "0708", "0812", "0907","0901"];

        if (in_array($formated_phone, $eti)){
            return '9mobile';
        }

        if (in_array($formated_phone, $mtn)){
            return 'mtn';
        }

        if (in_array($formated_phone, $glo)){
            return 'glo';
        }

        if (in_array($formated_phone, $airtel)){
            return 'airtel';
        }

        return null;
    }
    public function destroy(Request $request)
    {
        $details =Visitor::query()->where('id',$request->id)->first();
        $details->delete();
        toast('wishe deleted successfully', 'success');
        return redirect()->back();
    }
}
