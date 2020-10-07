@extends('layouts.app')

@section('content')
    <style>
        .games-popular{
            padding: 10px;
            border: 1px solid #ccc;
            position: absolute;
            width: 100%;
            top: 41px;
            background: #fff;
            z-index: 2;
            display: flex;
            flex-wrap: wrap;
            transition: .2s all;
            visibility: hidden;
            opacity: 0;
        }
        .games-popular.active{
            visibility: visible;
            opacity: 1;
        }
        .games-popular h2{
            font-size: 15px;
            width: 100%;
            font-weight: bold;
        }
        .game-popular,.game{
            background: #e4e0e0;
            padding: 5px 8px;
            margin-right: 5px;
            transition: .2s all;
            position: relative;
        }
        .game{
            padding-right: 24px;
        }
        .game span{
            position: absolute;
            right: 3px;
            top: 50%;
            transform: translateY(-50%);
            padding: 0 5px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .game-popular:hover{
            background: #dad3d3;
            cursor: pointer;
        }
        .games-list{
            display: flex;
            flex-wrap: wrap;
        }
    </style>
    {!! Form::open(['route' => 'settings.account.save', 'id' => 'settingsAccountForm']) !!}
        {{-- Name --}}
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            <input type="text" name="name" value="{{ Auth::user()->name }}" required class="form-control"/>
        </div>

        {{-- E-mail --}}
        <div class="form-group">
            {!! Form::label('email', 'E-mail') !!}
            <input type="email" name="email" value="{{ Auth::user()->email }}" required class="form-control"/>
        </div>

        {{-- Nickname --}}
        <div class="form-group">
            {!! Form::label('nickname', 'Nickname') !!}
            <input type="text" name="nickname" value="{{ Auth::user()->nickname }}" required class="form-control"/>
        </div>

        {{-- Phone --}}
        <div class="form-group">
            {!! Form::label('phone', 'Phone') !!}
            <input type="tel" name="phone" value="{{ Auth::user()->phone }}" class="form-control"/>
        </div>

        {{-- Timezone --}}
        <div class="form-group">
            {!! Form::label('timezone', trans('settings.account.timezone')) !!}
            {!! Form::select('timezone', Auth::user()->timezone_list(), Auth::user()->timezone_get(), ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'id' => 'timezone']) !!}
        </div>
        {{-- Smileys --}}
        <div class="form-group">
            {!! Form::label('smiles', trans('settings.account.smiles')) !!}
            {!! Form::select('smiles', ['true' => trans('settings.account.yes'), 'false' => trans('settings.account.no')], Auth::user()->smiles, ['class' => 'form-control selectpicker', 'id' => 'smiles']) !!}
        </div>
        {{-- Links --}}
        <div class="form-group">
            {!! Form::label('links', trans('settings.account.links')) !!}
            {!! Form::select('links', ['true' => trans('settings.account.yes'), 'false' => trans('settings.account.no')], Auth::user()->links, ['class' => 'form-control selectpicker', 'id' => 'links']) !!}
        </div>
        {{-- Black List Words --}}
        <div class="form-group">
            {!! Form::label('black_list_words', trans('settings.account.black_list_words.title')) !!}
            {!! Form::textarea('black_list_words', Auth::user()->black_list_words, [ 'class' => 'form-control' ]) !!}
            <p class="help-block">@lang('settings.account.black_list_words.info')</p>
        </div>

        


        @if(Auth::user()->type == "Streamer")

        {{-- About Channels --}}
        <div class="form-group">
            {!! Form::label('about', 'About Channels') !!}
            {!! Form::textarea('about', Auth::user()->about, [ 'class' => 'form-control' ]) !!}
        </div>
        

        <div class="form-group">
            {!! Form::label('youtube', 'Link to youtube channel') !!}
            <input type="text" name="youtube" value="{{ Auth::user()->youtube }}" class="form-control"/>
        </div>

        <div class="form-group">
            {!! Form::label('twitch', 'Link to twitch channel') !!}
            <input type="text" name="twitch" value="{{ Auth::user()->twitch }}" class="form-control"/>
        </div>

        <div class="form-group">
            {!! Form::label('subscribers', 'Number of subscribers') !!}
            <input type="text" name="subscribers" value="{{ Auth::user()->subscribers }}" class="form-control"/>
        </div>

        <div class="form-group">
            {!! Form::label('avonline', 'Average online on streams') !!}
            <input type="text" name="avonline" value="{{ Auth::user()->avonline }}" class="form-control"/>
        </div>
        
        <div class="form-group">
            {!! Form::label('streamviews', 'Average Stream Views') !!}
            <input type="text" name="streamviews" value="{{ Auth::user()->streamviews }}" class="form-control"/>
        </div>

        <div class="form-group">
            {!! Form::label('streamviews', 'Average video views') !!}
            <input type="text" name="videoviews" value="{{ Auth::user()->videoviews }}" class="form-control"/>
        </div>

        <div class="form-group">
            {!! Form::label('connection1', 'Connection info') !!}
            <input type="text" name="connection1" value="{{ Auth::user()->connection1 }}" class="form-control" style="margin-bottom: 10px;"/>
            <input type="text" name="connection2" value="{{ Auth::user()->connection2 }}" class="form-control" style="margin-bottom: 10px;"/>
            <input type="text" name="connection3" value="{{ Auth::user()->connection3 }}" class="form-control" />
        </div>

        <div class="form-group">
            {!! Form::label('typeadver', 'Type of Advertising which are cancelled') !!}
            <input type="text" name="typeadver" value="{{ Auth::user()->typeadver }}" class="form-control"/>
        </div>
        
        








        



        <div class="form-group">
            <label>Games</label>
            <div class="games-list">
                @foreach(Auth::user()->games()->get() as $game)
                <div class="game" >
                    {{ $game->name }}
                    <span data-id="{{ $game->id }}" class="game-remove">x</span>
                </div>
                @endforeach
            </div>
            <div style="position: relative;margin-top: 10px;">   
                <input type="text" name="game" placeholder="Type the game..." class="form-control"/>
                <div class="games-popular">
                    <h2>Popular games</h2>
                    @foreach($games as $game)
                    <div class="game-popular">{{ $game->name }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="form-group">
            {!! Form::label('bizdesc', 'Description of Business') !!}
            {!! Form::textarea('bizdesc', Auth::user()->bizdesc, [ 'class' => 'form-control' ]) !!}
        </div>
        <div class="form-group">
            {!! Form::label('products_for_advertisment', 'Products for Advertisment') !!}
            <input type="text" name="products_for_advertisment" value="{{ Auth::user()->products_for_advertisment }}" class="form-control"/>
        </div> 
        @endif
        {{-- Submit --}}
        {!! Form::submit(trans('settings.account.save'), ['class' => 'btn btn-primary']) !!}
    {!! Form::close() !!}

        <button id="screenshot" class="btn btn-primary">Screenshot</button>
        <video id="video" autoplay style="display: none"></video>
        <canvas id="canvas" style="display: none;"></canvas>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#settingsAccountForm').ajaxForm({ 
                dataType: 'json', 
                success: function(data) {
                    auto_notify(data);
                },
                error: function(data) {
                    error_notify(data.responseJSON);
                }
            }); 
        });


        //games

        

        var timeout;
        var token = document.querySelector("[name='_token']").value;
        var input_game = document.querySelector("[name='game']");
        var gamesList = document.querySelector(".games-list");
        var gamesPopular = document.querySelector(".games-popular");
        function addGame(game){
            input_game.value = "";
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/settings/account/addgame?game=' + game +"&_token="+token);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var games = JSON.parse(xhr.responseText);
                    document.querySelectorAll(".game").forEach(function(game){
                        game.remove();
                    });
                    games.forEach(function(game){
                        gamesList.innerHTML += '<div class="game">'+game.name+'<span data-id="'+game.id+'" class="game-remove">x</span></div>'
                    });
                }
                else {
                    console.log('Request failed.  Returned status of ' + xhr.status);
                }
            };
            xhr.send();
        }
        input_game.addEventListener("keydown", function(e){
            if(e.key == "Enter"){
                e.preventDefault();
                addGame(input_game.value);
                document.querySelector(".game-popular:last-child").remove();
            }else{
                clearTimeout(timeout);
                timeout = setTimeout(function(){
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '/settings/account/search?s=' + input_game.value +"&_token="+token);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var games = JSON.parse(xhr.responseText);
                            if(games.length > 0){
                                document.querySelectorAll(".game-popular").forEach(function(game){
                                    game.remove();
                                });
                                
                                games.forEach(function(game){
                                    gamesPopular.innerHTML += '<div class="game-popular">'+game.name+'</div>'
                                });
                            }
                        }
                        else {
                            console.log('Request failed.  Returned status of ' + xhr.status);
                        }
                    };
                    xhr.send();
                }, 1000);
            }
        });

        document.querySelector("[name='game']").addEventListener("focus", function(e){
            document.querySelector(".games-popular").classList.add("active");
        });

        document.querySelector("[name='game']").addEventListener("blur", function(e){
            document.querySelector(".games-popular").classList.remove("active");
        });

        

        document.addEventListener("click", function(e){
            var path = e.path || (e.composedPath && e.composedPath());
            path.forEach(function(el, i){
                if(el.classList && el.classList.contains("game-popular")){
                    e.preventDefault();
                    addGame(el.innerHTML);
                    el.remove();
                }

                if(el.classList && el.classList.contains("game-remove")){
                    e.preventDefault();
                    var id = el.getAttribute("data-id");
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '/settings/account/removegame?game=' + id +"&_token="+token);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var parent = el.parentElement;
                            parent.querySelector("span").remove();
                            gamesPopular.innerHTML += '<div class="game-popular">'+parent.innerText.trim()+'</div>';
                            parent.remove();
                        }
                        else {
                            console.log('Request failed.  Returned status of ' + xhr.status);
                        }
                    };
                    xhr.send();
                }
            });
        });


        //screenshots
        var canvas = document.querySelector("#canvas");
        var video = document.querySelector("#video");
        var ctx = canvas.getContext('2d');
        var array = [];
        var currentTime = 0;

        async function startCapture() {
          try {
            video.srcObject = await navigator.mediaDevices.getDisplayMedia({
              video: {
                cursor: "always"
              },
              audio: false
            });
          } catch(err) {
          	document.querySelector("#screenshot").innerHTML = "Screenshot";
          	document.querySelector("#screenshot").classList.remove("started");
            console.error("Error: " + err);
          }
        }

        function stopCapture(evt) {
		  var tracks = video.srcObject.getTracks();

		  tracks.forEach(function(track) { track.stop() });
		  video.srcObject = null;
		}

        document.querySelector("#screenshot").addEventListener("click", function(e){
            e.preventDefault();
            console.log("work");
            if(this.classList.contains("started")){
            	stopCapture();
            	this.classList.remove("started");
            	this.innerHTML = "Screenshot";
            }else{
            	startCapture();
            	this.classList.add("started");
            	this.innerHTML = "Stop screenshot";
            }
            
            
        });


        function initCanvas(e) {
          canvas.width = this.videoWidth;
          canvas.height = this.videoHeight;
        }

        function drawFrame(e) {
            this.pause();
            ctx.drawImage(this, 0, 0);
            /* 
            this will save as a Blob, less memory consumptive than toDataURL
            a polyfill can be found at
            https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/toBlob#Polyfill
            */
            if(parseInt(this.currentTime)%5 == 0 && currentTime != parseInt(this.currentTime)){
            	canvas.toBlob(saveFrame, 'image/jpeg');
            	currentTime = parseInt(this.currentTime);
            }

            
            if (this.currentTime < this.duration) {
              this.play();
            }
          }

        function saveFrame(blob) {
            var url = URL.createObjectURL(blob);
            console.log(url);
            array.push(blob);


			var reader = new FileReader();
			reader.onload = function(event){
				var formData = new FormData();
				formData.append('data', event.target.result);
				formData.append('_token', document.querySelector("[name='_token']").value);
	
			  	var xhr = new XMLHttpRequest();
		        // Add any event handlers here...
		        xhr.open('POST', '/settings/screenshot', true);
		        xhr.onload = function() {
		            if (xhr.status === 200) {
		                console.log(xhr.responseText);
		            }
		            else {
		                alert('Request failed.  Returned status of ' + xhr.status);
		            }
		        };
		        xhr.send(formData);
			};
			reader.readAsDataURL(blob);


        }

        video.addEventListener('loadedmetadata', initCanvas, false);
        video.addEventListener('timeupdate', drawFrame, false);
        
    </script>
@endsection