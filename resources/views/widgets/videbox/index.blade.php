@extends('layouts.app')

@section('content')
    <h1>All Streamers</h1>  
    <div class="videobox-games">
        <div>
            @foreach($games as $game)
                <a href="/widgets/videobox/?game={{ $game->name }}" class="videobox-game @if(isset($_GET['game']) && $_GET['game'] == $game->name) active @endif">
                    {{ $game->name }}
                </a>
            @endforeach
            <a href="/widgets/videobox">All games</a>
        </div> 
    </div>  
    <form action="/widgets/videobox" class="video-box-search">
        <input name="s" @isset($_GET['s']) value="{{ $_GET['s'] }}" @endisset/>
        @isset($_GET['game'])
            <input name="game" value="{{ $_GET['game'] }}" type="hidden" />
        @endisset
        <button>Search streamer</button>     
    </form>
    <div class="all-streamers">
        @foreach($streamers as $streamer)
        <div class="video-box-streamer">
            <span>{{ $streamer->name }} - {{ $streamer->email }}</span>

            <button>Submit a request</button>
         </div>
        @endforeach
    </div> 
    <div class="pagination">
        {{ $streamers->appends(request()->input())->links() }}
    </div>
    <style>
        .videobox-games{
            background: #dedede;
            padding: 15px;
            margin-top: 15px;
        }
        .videobox-games>div{
            display: flex;
            flex-wrap: wrap;
            margin-bottom: -15px;
        }
        .videobox-games a{
            width: 20%;
            margin-bottom: 15px;
        }
        .videobox-games a.active{
            text-decoration: underline;
        }
        .videobox-games a:hover{
            text-decoration: underline;
        }
        .video-box-search{
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            display: flex;
            flex-wrap: wrap;
        }
        .video-box-search input{
            flex: 1;
            border: 1px solid #ccc;
            border-right: 0;
            min-height: 45px;
            padding: 5px 15px;
        }
        .video-box-search button, .video-box-streamer button{
            border-radius: 0;
            border: 0;
            background: #59438c;
            color: #fff;
            transition: .2s all;
            padding: 5px 15px;
            cursor: pointer;
        }
        .video-box-search button:hover, .video-box-streamer button:hover{
            box-shadow: 0 0 10px 0 #59438c;
        }
        .video-box-streamer{
            background: #e0e0e0;
            padding: 5px 10px;
            margin: 10px 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
        .video-box-streamer span{
            flex: 1;
        }
        .pagination{
            display: flex;
            flex-wrap: wrap;
        }
        .pagination span{
            position: relative;
            float: left;
            padding: 0.5rem 0.75rem;
            margin-left: -1px;
            color: #0275d8;
            text-decoration: none;
            background-color: #ccc;
            border: 1px solid #ddd;
            color: #fff;
        }
        .pagination .disabled{
            display: none;
        }
    </style>
@endsection