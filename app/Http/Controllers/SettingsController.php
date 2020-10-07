<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Settings;
use App\Game;

class SettingsController extends Controller
{
    
    var $view = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getAccount()
    {
        $userGames = Auth::user()->games()->get();
        $userGamesIDs = [];
        foreach($userGames as $game){
            $userGamesIDs[] = $game->id;
        }
        $this->view['title'] = trans('settings.account.title');
        return view('settings.account', $this->view)->with([
            "games" => Game::whereNotIn('id', $userGamesIDs)->paginate(30)
        ]);
    }

    public function search(Request $request)
    {
        $data = $request->all();
        $games = [];
        $userGames = Auth::user()->games()->get();
        $userGamesIDs = [];
        foreach($userGames as $game){
            $userGamesIDs[] = $game->id;
        }
        if($data['s'] && !empty($data['s'])){
            $games = Game::where('name','LIKE','%'.$data['s'].'%')->whereNotIn('id', $userGamesIDs)->take(30)->get();
            $games[] = array("name"=>$data['s']);
        }else{
            $games = Game::whereNotIn('id', $userGamesIDs)->take(30)->get();
        }
        echo json_encode($games);
        
    }

    public function addgame(Request $request){
        $data = $request->all();
        if($data['game'] && !empty($data['game'])){
            $games = Game::where("name", $data['game'])->get();
            if(count($games) == 0){
                $game = Game::create(["name"=>$data['game']]);
            }else{
                $game = $games[0];
            }
            Auth::user()->games()->attach([$game->id]);
            echo json_encode(Auth::user()->games()->get());
        }
    }

    public function removegame(Request $request){
        $data = $request->all();
        if($data['game'] && !empty($data['game'])){
            Auth::user()->games()->detach([$data['game']]);
        }
    }
    
    public function postAccount(Request $request)
    {
        $this->validate($request, [
            'name' => ['required','max:255'],
            'phone' => ['max:30'],
            'nickname' => Rule::unique('users')->ignore(Auth::user()->id),
            'email' => ['required','email'],
            'timezone' => [ 'required', 'timezone' ],
            'smiles' => ['required', 'in:true,false'],
            'links' => ['required', 'in:true,false'],
            'black_list_words' => [],
            'nickname' => ['max:255'],
            'subscribers' => ['max:255'],
            'twitch' => ['max:255'],
            'youtube' => ['max:255'],
            'avonline' => ['max:255'],
            'streamviews' => ['max:255'],
            'videoviews' => ['max:255'],
            'bizdesc' => ['max:255'],
            'products_for_advertisment' => ['max:255'],
            'connection1' => ['max:255'],
            'connection2' => ['max:255'],
            'connection3' => ['max:255'],
            'typeadver' => ['max:255']
        ]);
        
        $data = $request->only(['name', 'email', 'nickname','subscribers', 'about', 'youtube', 'twitch', 'phone', 'timezone', 'smiles', 'links', 'black_list_words', 'avonline', 'streamviews', 'videoviews', 'bizdesc', 'products_for_advertisment', 'connection1', 'connection1', 'connection2', 'connection3', 'typeadver']);
        Auth::user()->update($data);
        return response()->json(['success'=> trans('settings.account.success')]);
    }
    
    public function getDonation()
    {
        $this->view['settings'] = Settings::user(Auth::user()->id);
        $this->view['patterns'] = Storage::disk('public')->allFiles('backgrounds/patterns');
        $this->view['title'] = trans('settings.donation.title');
        return view('settings.donation', $this->view);
    }
    
    public function postDonation(Request $request)
    {
        $this->validate($request, [
            'amount_minimum' => [ 'required', 'integer', 'min:0.01', 'max:1000000' ],
            'max_message_length' => [ 'required', 'integer', 'min:1', 'max:512' ],
            'amount_placeholder' => [ 'required', 'integer', 'min:0.01', 'max:1000000' ],
            'button_color' => [ 'required', 'color' ],
            'memo' => [ ],
            'donation_banner' => [ 'nullable', 'image' ],
            'paypal' => [ 'nullable', 'email' ],
            'background_file' => [ 'nullable', 'image' ]
        ]);
        $settings = Settings::user(Auth::user()->id);
        $data = $request->only(['paypal', 'amount_minimum', 'max_message_length', 'amount_placeholder', 'button_color', 'memo']);
        
        // Background
        if ($request->hasFile('background_file')) {
            if (stristr($settings['background'], 'patterns/') === FALSE)
                Storage::disk('public')->delete('backgrounds/' . $settings['background']);
            $filename = str_random(30) . '.' . $request->background_file->extension();
            $request->background_file->storeAs('backgrounds', $filename, 'public'); 
            $data['background'] = $filename;
        } else if (stristr($request->background, 'patterns/')) {
            if (Storage::disk('public')->exists('backgrounds/patterns/' . basename($request->background))) {
                if (stristr($settings['background'], 'patterns/') === FALSE)
                    Storage::disk('public')->delete('backgrounds/' . $settings['background']);
                $data['background'] = $request->background;
            } else
                return response()->json(['danger' => ['background' => [trans('settings.donation.background_error')]]]);
        }
        // Banner
        if ($request->hasFile('donation_banner')) {
            if ($settings['banner'] != 'banners/default.jpg')
                Storage::disk('public')->delete('banners/' . $settings['banner']);
            $filename = str_random(30) . '.' . $request->donation_banner->extension();
            $request->donation_banner->storeAs('banners', $filename, 'public'); 
            $data['banner'] = $filename;
        }
        
        Auth::user()->settings()->where('user_id', Auth::user()->id)->update($data);
        return response()->json(['success'=> trans('settings.donation.success')]);
    }

    public function screenshot(Request $request)
    {
        $data = substr(request()->data, strpos(request()->data, ",") + 1);
        $decodedData = base64_decode($data);
        $time = time();
        $imageName = $time.'.png';
        // \File::put(public_path(). '/screenshots/'.Auth::user()->id."/" . $imageName, $decodedData);
        if (!file_exists(public_path().'/screenshots/'.Auth::user()->id)) {
            mkdir(public_path().'/screenshots/'.Auth::user()->id, 0777);
        }
        file_put_contents(public_path().'/screenshots/'.Auth::user()->id."/".$imageName, $decodedData);
        return public_path(). '/screenshots/'.Auth::user()->id."/" . $imageName;
    }
    
}