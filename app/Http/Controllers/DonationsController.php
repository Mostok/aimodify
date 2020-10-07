<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Messages;
use App\Settings;
use App\User;

class DonationsController extends Controller
{
    
    var $view = [];

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['getDonate', 'postDonate', 'updateMessage']]);
    }

    public function getHome()
    {
        $this->view['title'] = trans('donations.home.title');
        return view('donations.home', $this->view);
    }
    
    public function getData()
    {
        return Datatables::eloquent(Messages::select(['updated_at', 'status', 'billing_system', 'name', 'amount', 'commission', 'message', 'id'])->where('user_id', Auth::user()->id)->whereIn('status', ['success', 'user', 'refund']))
            ->editColumn('updated_at', function ($data) {
                return $data->updated_at ? with(new Carbon($data->updated_at))->setTimezone(Auth::user()->timezone) : '';
            })->editColumn('amount', function ($data) {
                return number_format($data->amount, 2, '.', '');
            })->editColumn('commission', function ($data) {
                return number_format($data->commission, 2, '.', '');
            })->editColumn('message', function ($data) {
                if (Auth::user()->smiles == 'true')
                    return Messages::smileys(htmlentities(e($data->message)));
                else
                    return $data->message;
            })->orderBy('updated_at', 'DESC')->make(true);
    }
    
    public function postRemove(Request $request)
    {
        $this->validate($request, [
            'id' => [ 'required', 'integer' ],
        ]);
        if (Messages::where('user_id', Auth::user()->id)->where('id', $request->id)->delete())
            return response()->json(['success'=> trans('donations.remove.success')]);
        return response()->json(['error'=> trans('donations.remove.error')]);
    }
    
    public function postCreate(Request $request)
    {   
        $rules = [
            'amount' => [ 'required', 'numeric', 'min:0.01' ],
            'name' => [ 'required' ],
            'message' => [ 'nullable', 'max:512' ],
            'updated_at' => [ 'date' ]
        ];
        if($request->video_file){
            $rules['video_file'] = ['mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'];
        }
        $this->validate($request, $rules);

        $data = $request->only(['amount', 'name', 'message', 'updated_at']);
        $data['user_id'] = Auth::user()->id;
        $data['status'] = 'user';

        $data['video'] = '';
        if($request->video_file){
            $video = $request->video_file;
            $time = time();
            $videoName = $time.'.'.$video->getClientOriginalExtension();
            $destinationPath = public_path('screenshots/'.Auth::user()->id);
            $video->move($destinationPath, $videoName);

            $data['video'] = "/screenshots/".Auth::user()->id."/".$videoName;
        }

        
        $data['updated_at'] = (new Carbon($data['updated_at'], Auth::user()->timezone))->setTimezone(config('app.timezone'));

        if (Messages::create($data))
            return response()->json(['success'=> trans('donations.create.success')]);
        return response()->json(['error'=> trans('donations.create.error')]);
    }
    
    /**
     *  Donate
     */
    public function getDonate(Request $request, $source, $id)
    { 
        $this->view['user'] = User::where('token', $source . '::' . $id)->first();
        if (!$this->view['user'])
            abort(404);
        $this->view['settings'] = Settings::where('user_id', $this->view['user']->id)->first();
        if (!$this->view['user'])
            abort(403);
        $this->view['emotes'] = Storage::disk('public')->allFiles('emotes');
        $this->view['title'] = trans('donations.donate.title');
        $this->view['conditions'] = Storage::get("pages/" . \Lang::locale() . "/terms-and-conditions.html");
        return view('donations.donate', $this->view);
    }
    public function postDonate(Request $request, $source, $id)
    { 
        
        $user = User::where('token', $source . '::' . $id)->first();
        if (!$user)
            abort(404);
        $settings = Settings::where('user_id', $user->id)->first();
        if (!$settings)
            abort(403);

        $rules = [
            'amount' => [ 'required', 'numeric', 'min:' . $settings->amount_minimum, 'max:1000000' ],
            'name' => [ 'required', 'max:32' ],
            'message' => [ 'nullable', 'max:' . $settings->max_message_length ]
        ];

        if($request->video_file){
            $rules['video_file'] = ['mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'];
        }

        $this->validate($request, $rules);


        $data = $request->only(['amount', 'name', 'message', 'live']);
        if($request->live == "on"){
            $data['status'] = 'wait';
            $data['live'] = "1";
        }else{
            $data['status'] = 'user';
            $data['live'] = "0";
        }
        
        $data['video'] = '';
        if($request->video_file){
            $video = $request->video_file;
            $time = time();
            $videoName = $time.'.'.$video->getClientOriginalExtension();
            $destinationPath = public_path('screenshots/'.Auth::user()->id);
            $video->move($destinationPath, $videoName);

            $data['video'] = "/screenshots/".Auth::user()->id."/".$videoName;
        }
        
        

        
        // $data['video'] = "/public/1.mp4";
        $data['user_id'] = $user->id;
        
        $result = Messages::create($data);
        return $result;
    }


    public function updateMessage(Request $request){
        $data = $request->all();
        $message = Messages::where("id", $data['id'])->first();
        $message->roomid = $data['roomid'];
        $message->status = "user";
        $message->save();
        return $data;
    }
    
}