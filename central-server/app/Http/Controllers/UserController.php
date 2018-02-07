<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use App\User;
use App\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{


	function index()
	{
		dd($this->allOnline());
	}

		function registerMAC(Request $request)
		{
			$user = Auth::user();
			$data = $request->all();

			if($user == null)
				abort(401, 'Unautorized');

			$validator = Validator::make($data, [
				'mac_address' => 'required|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
				'image' => 'required|image'
		]);

		if($validator->fails()){
			dd($validator);
			return view('dashboard', ['user'=> $user, 'error' => 'Incorrect Format', 'message'=>'']);
		}	
			$user->mac_address = $data['mac_address'];
			$file = Input::file('image');
			$user->profile_pic = $file->getClientOriginalName();
		$file->move(public_path().'/uploads', $file->getClientOriginalName());
			$user->save();

			return view('dashboard', ['user'=> $user, 'error' => '', 'message'=>'Successful']);
		}

		function barGraph()
		{
			$user = Auth::user();

			if($user == null)
				abort(401,'Unautorized');

			$all = json_decode($this->parseGraph($user->id));
			$data = $all[0];
			$date = $all[1];
			$total = $all[2];

			return view('dashboard_bar', ['total' => $total, 'data' => $data, 'date' => $date, 'message' => '', 'error' => '']);
		} 

		function lineGraph()
		{
			$user = Auth::user();

			if($user == null)
				abort(401,'Unautorized');

			$all = json_decode($this->parseGraph($user->id));
			$data = $all[0];
			$date = $all[1];
			$total = $all[2];

			return view('dashboard_line', ['total' => $total, 'data' => $data, 'date' => $date, 'message' => '', 'error' => '']);
		} 

		function pieGraph()
		{
			$user = Auth::user();

			if($user == null)
				abort(401,'Unautorized');

			$all = json_decode($this->parseGraph($user->id));
			$data = $all[0];
			$date = $all[1];
			$total = $all[2];

			return view('dashboard_pie', ['total' => $total, 'data' => $data, 'date' => $date, 'message' => '', 'error' => '']);
		} 

		function image(Request $request)
		{
			$data = $request->all();
			$email = $data['email'];

			$user = User::where('email',$email)->first();

			return json_encode(['image' => $user->profile_pic]);
		}

		function leaderboard()
		{
			$orders = DB::table('logs')
                ->select('logs.mac_address', DB::raw('COUNT(*) as minutes'))
                ->groupBy('mac_address')
								->limit(10)
                ->get()
                ->sortBy(function($log) {
                	return $log->minutes;
									})->reverse();	

      return json_encode($this->getUsernames($orders));
      
		}

		function allOnline()
		{
			$recent = Carbon::now('Asia/Kolkata')->subMinutes(5);
			$online = Log::where('timestamp','>', $recent)->get();

			return json_encode($this->getUsernames($online));
		}

		function getUsernames($orders)
		{

			$macs = array();

      foreach($orders as $order)
      {
      	array_push($macs,$order->mac_address);
      }

      $userNames = User::whereIn('mac_address', $macs)->get();

			foreach($orders as $order)
     	{
     		foreach($userNames as $user)
     		{
		     	$userNames = User::whereIn('mac_address',$macs)->get();
     			
     			if($user->mac_address == $order->mac_address)
     			{
     				$order->username = $user->username;
     				$order->name = $user->name;
     				break;
     			}
     			else
     				$order->username = "Not Registered";

     		}
     	}

     	return ($orders);	
		}

		function parseGraph($id)
		{
			$user = User::where('id',$id)->first();
			$data = $user->hoursActiveInAWeek(Carbon::today('Asia/Kolkata'));
			$date = $user->daysOfAWeek();

			$total = 0;
			foreach( $data as $time){
				$total += $time;
			}

			return json_encode([$data,$date,$total]);
		}

}
