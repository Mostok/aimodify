<?php

namespace App\Http\Controllers\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use App\User;
use App\Game;

class VideoboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','can:advertise']);
    }

    public function getHome()
    {
    	$posts_per_page = 1;
        if(isset($_GET['s'])){
            $streamers = User::where('name','LIKE','%'.$_GET['s'].'%')->where("type", "Streamer")->paginate($posts_per_page);
        }else if(isset($_GET['game'])){
        	$streamers = User::where("type", "Streamer")->whereHas('games', function($q){
			    $q->where('name', '=', $_GET['game']);
			})->paginate($posts_per_page);
        }else if(isset($_GET['game']) && isset($_GET['s'])){
        	$streamers = User::where('name','LIKE','%'.$_GET['s'].'%')->where("type", "Streamer")->whereHas('games', function($q){
			    $q->where('name', '=', $_GET['game']);
			})->paginate($posts_per_page);
        }else{
            $streamers = User::where("type", "Streamer")->paginate($posts_per_page);
        }
        
        $this->view = [
            'title' => "Video Box",
            "games" => Game::all(),
            "streamers" => $streamers
        ];
        return view('widgets.videbox.index', $this->view);
    }
}