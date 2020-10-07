@extends('layouts.blank')

@section('css')
    <link href="{{ asset('assets/css/donate.css') }}" rel="stylesheet">
    <style>
        body {
            background: rgba(0, 0, 0, 0) url("{{ asset(Storage::url('backgrounds/' . $settings['background'])) }}") repeat scroll center center;
        }
        .container .banner {
            background: rgba(0, 0, 0, 0) url("{{ asset(Storage::url('banners/' . $settings['banner'])) }}") repeat scroll center center;
        }
        @if ($user->smiles == 'false') 
            .maxlength {
                right: 14px;
            }
            .kappa {
                display: none;
            }
        @endif
        #submit {
            background-color: {{ $settings->button_color }} !important;
        }
        .btn-share-camera{
            display: none;
        }

        .btn-send-to-camera{
            margin-top: 20px;
            display: none;
        }

        .media-box h2{
            display: none !important;
        }

        .media-container{
            width: 100% !important;
        }
        .media-controls{
            display: none !important;
        }
        .status-no-stream{
            display: none;
        }
        .status-no-stream span{
            color: #dcdc2a;
        }

        .status-on-stream{
            display: none;
        }
        .status-on-stream span{
            color: green;
        }
        .status-on-stream, .status-no-stream{
            margin-top: 15px;
            text-align: center;
        }

        #videos-container video{
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="container">
  
        <div class="banner">
        
            <img src="{{ $user->avatar }}" class="avatar">
     
            <h1 class="name">{{ $user->name }}</h1>
     
            @if ($user->service() == 'twitch')
                <a href="https://twitch.tv/{{ $user->name }}" class="link" target="_blank"><i class="fa fa-twitch"></i> /{{ $user->name }}</a>
            @elseif ($user->service() == 'youtube')
                <a href="https://youtube.com/channel/{{ $user->service_id() }}" class="link" target="_blank"><i class="fa fa-youtube-play"></i> /channel/{{ $user->name }}</a>
            @elseif ($user->service() == 'mixer')
                <a href="https://mixer.com/{{ $user->name }}" class="link" target="_blank"><i class="fa fa-xing"></i> /id{{ $user->service_id() }}</a>
            @endif
        </div>

        <div class="content">
            
      
            {!! Form::open(['route' => ['donate.post', $user->service(), $user->service_id()], 'id' => 'postDonate', 'autocomplete' => 'off']) !!}
              
                {!! nl2br(e($settings->memo)) !!}<hr>
          
                <div class="form-group">
                    {!! Form::text('name', '', ['class' => 'form-control', 'id' => 'name', 'placeholder' => trans('donations.donate.name.title')]) !!}
                </div>
             
                <div class="input-group">
                    <span class="input-group-addon">{!! config('app.currency_icon') !!}</span>
                    {!! Form::text('amount', $settings['amount_placeholder'], ['class' => 'form-control', 'id' => 'amount', 'placeholder' => trans('donations.donate.amount.title')]) !!}
                </div>
                <p class="help-block">@lang('donations.donate.amount.info', ['amount_minimum' => $settings->amount_minimum . ' ' . config('app.currency_icon')])</p>
               
                <div style="position: relative">
                    {!! Form::textarea('message', '', ['class' => 'form-control', 'rows' => '4', 'maxlength' => $settings->max_message_length, 'id' => 'message', 'placeholder' => trans('donations.donate.message.title')]) !!}
                    <span class="maxlength">{{ $settings->max_message_length }}</span>
                    <a href="#" class="kappa"><img src="{{ asset(Storage::url('emotes/Kappa.png')) }}"></a>
                </div>
                
                <div class="form-group" style="margin-top: 15px;">
                    {!! Form::label('background', trans('donations.home.create.video')) !!}
                    <div id="soundBlock">
                        <div class="input-group">
                     
                            <div class="input-group">
                                <label class="input-group-btn">
                                    <span class="btn btn-secondary" style="border-radius: 0px;">
                                        @lang('global.browse') {!! Form::file('video_file', ['accept' => 'video/*', 'id' => 'video_file', 'style' => 'display: none;']) !!}
                                    </span>
                                </label>
                                <input type="text" class="form-control" readonly>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <label class="input-group">
                    <input name="live" type="checkbox" id="live" />
                    {!! trans('donations.donate.checkbox') !!}
                </label>



           
                <div class="form-footer">
                    {!! Form::button('<i class="fa fa-gift" aria-hidden="true"></i> ' . trans('donations.donate.submit'), ['class' => 'btn btn-primary btn-lg', 'type' => 'submit', 'id' => 'submit']) !!}
                </div>
            {!! Form::close() !!}
            
        
            <div id="payForm">
             
                <button class="back">
                    <i aria-hidden="true" class="fa fa-long-arrow-left"></i> 
                    @lang('donations.donate.back')
                </button>
           
                <div class="total"><span class="total-span"></span> {!! config('app.currency_icon') !!}</div>
                <div class="total-label">@lang('donations.donate.subtotal')</div>
            
                <div class="list-group payment-methods">
                  
                    @if(config('paypal.status') == 'enabled' && $settings->paypal != '')
                        @include('donations.elements.paypal')
                    @endif
                </div>


                <button class="btn btn-share-camera">Share camera</button>
                <div id="videos-container">

                </div>
                <button class="btn btn-send-to-camera">Send to stream</button>
                <div class="status-no-stream">Status: <span>Sending...</span></div>
                <div class="status-on-stream">Status: <span>On stream</span></div>
            </div>
        </div>
    </div>
    <div class="donation-footer">
        @lang('donations.donate.terms', ['modal' => 'termsAndConditionsModal'])
         <br><br>
        &copy; <a href="{{ route('home') }}">{{ config('app.title') }}</a> {{ date('Y') }}
    </div>
@endsection

@section('modals')

   
    <div class="modal fade" id="termsAndConditionsModal" tabindex="-1" role="dialog" aria-labelledby="termsAndConditionsModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="termsAndConditionsModalLabel">@lang('donations.donate.conditions')</h4>
          </div>
          <div class="modal-body">
            <div class="element-select">
                {!! $conditions !!}
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">@lang('global.close')</button>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('scripts')
    <script src="https://kudzi1.herokuapp.com/socket.io/socket.io.js"></script>
    <script>
        var videoElement;
        var audioSelect;
        var videoSelect;
        const peerConnections = {};
        function makeid(){
            return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        }
        var roomid = makeid();

        const config1 = {
            iceServers: [
                { 
                    "urls": "stun:stun.l.google.com:19302",
                },
                { 
                    "urls": "turn:numb.viagenie.ca?transport=tcp",
                    "username": "kalini.art@gmail.com",
                    "credential": "Knopka91019254"
                }
            ]
        };

        const socket = io.connect("https://kudzi1.herokuapp.com/");

        socket.on("answer", (id, description) => {
            peerConnections[id].setRemoteDescription(description);
        });

        socket.on("watcher", id => {
            const peerConnection = new RTCPeerConnection(config1);
            peerConnections[id] = peerConnection;

            let stream = videoElement.srcObject;
            stream.getTracks().forEach(track => peerConnection.addTrack(track, stream));

            peerConnection.onicecandidate = event => {
                if (event.candidate) {
                socket.emit("candidate", id, event.candidate);
                }
            };

            peerConnection
                .createOffer()
                .then(sdp => peerConnection.setLocalDescription(sdp))
                .then(() => {
                    socket.emit("offer", id, peerConnection.localDescription);
                });
        });

        socket.on("candidate", (id, candidate) => {
            peerConnections[id].addIceCandidate(new RTCIceCandidate(candidate));
            $(".status-no-stream").hide();
            $(".status-on-stream").show();
        });

        socket.on("disconnectPeer", id => {
            peerConnections[id].close();
            delete peerConnections[id];
            videoElement.srcObject.getVideoTracks().forEach(track => {
                track.stop();
                videoElement.srcObject.removeTrack(track);
            });

            videoElement.srcObject.getAudioTracks().forEach(track => {
                track.stop();
                videoElement.srcObject.removeTrack(track);
            });
            $('.back').click();
            
        });

        window.onunload = window.onbeforeunload = () => {
            socket.close();
        };

        

        function getDevices() {
            return navigator.mediaDevices.enumerateDevices();
        }

        function gotDevices(deviceInfos) {
            window.deviceInfos = deviceInfos;
            for (const deviceInfo of deviceInfos) {
                const option = document.createElement("option");
                option.value = deviceInfo.deviceId;
                if (deviceInfo.kind === "audioinput") {
                    option.text = deviceInfo.label || `Microphone ${audioSelect.length + 1}`;
                    audioSelect.appendChild(option);
                } else if (deviceInfo.kind === "videoinput") {
                    option.text = deviceInfo.label || `Camera ${videoSelect.length + 1}`;
                    videoSelect.appendChild(option);
                }
            }
        }

        function getStream() {
            if (window.stream) {
                window.stream.getTracks().forEach(track => {
                	track.stop();
                });
            }
            const audioSource = audioSelect.value;
            const videoSource = videoSelect.value;
            const constraints = {
                audio: { deviceId: audioSource ? { exact: audioSource } : undefined },
                video: { 
                    deviceId: videoSource ? { exact: videoSource } : undefined,
                    frameRate: {
                        max: 15
                    },
                    minBitrate: 600,
                    maxBitrate: 600
                    // mandatory: {
                    //    maxWidth: 320,
                    //    maxHeight: 240,
                    //    maxAspectRatio:4/3,
                    //    maxFrameRate:1
                    // }
                }
            };
            return navigator.mediaDevices
                .getUserMedia(constraints)
                .then(gotStream)
                .catch(handleError);
        }

        function gotStream(stream) {
            window.stream = stream;
            audioSelect.selectedIndex = [...audioSelect.options].findIndex(
                option => option.text === stream.getAudioTracks()[0].label
            );
            videoSelect.selectedIndex = [...videoSelect.options].findIndex(
                option => option.text === stream.getVideoTracks()[0].label
            );
            videoElement.srcObject = stream;
            socket.emit("broadcaster", roomid);
        }

        function handleError(error) {
            console.error("Error: ", error);
        }



        var _length = {{ $settings->max_message_length }};
        $(function() {
      
            var message_trigger = function() {
                _length = {{ $settings->max_message_length }} - $(this).val().length;
                $('.maxlength').text(_length);
            }
            $('#message').on('keypress', message_trigger);
            $('#message').on('keyup', message_trigger);
            $('#message').on('change', message_trigger);
      
            $('.kappa').popover({
                html: true,
                placement: 'top',
                trigger: 'click',
                content: `
                    <div class="emotes">
                        @foreach ($emotes as $emotion)
                            <img src="{{ asset(Storage::url($emotion)) }}" onclick="emotion('{{ basename($emotion, '.png') }}')">
                        @endforeach
                    </div>`
            });

            $('#postDonate').ajaxForm({
                dataType: 'json', 
                success: function(data) { 
                    returnedData = data;
                    auto_notify(data); 
                    if (typeof data.id != 'undefined') {
                        $('#postDonate').fadeOut('slow');
                        $('#payForm').fadeIn('slow');

                        if(data.live == "1"){
                            document.querySelector(".btn-share-camera").style = "display: block;";
                            $(".btn-send-to-camera").hide();
                        }

                        $('.total-span').text(number_format(data.amount, 2, '.', ''));
                        $('.payment-methods a').each(function() {
                            $(this).attr('href', $(this).attr('default-href').replace('0000', data.id));
                        });

                        $(".btn-share-camera").click();
                        // $(".btn-send-to-camera").click();
                    }
                },
                error: function(data) { error_notify(data.responseJSON); }
            }); 
    
            $('.back').on('click', function() {
                    $('#postDonate').fadeIn('slow');
                    $('#payForm').fadeOut('slow');
                    document.querySelector(".btn-share-camera").style = "";
                    $(".status-no-stream").hide();
                    $(".status-on-stream").hide();

                    document.querySelector("#videos-container").innerHTML = "";
            });
            
            
            $(".btn-share-camera").on("click", function(e){
                e.preventDefault();
                $(".btn-share-camera").hide();
                
           
                document.querySelector("#videos-container").innerHTML = `
                    <section class="select">
                      <label for="audioSource">Audio source: </label>
                      <select id="audioSource"></select>
                    </section>

                    <section class="select">
                      <label for="videoSource">Video source: </label>
                      <select id="videoSource"></select>
                    </section>

                    <video playsinline autoplay muted></video>
                `;
                $(".btn-send-to-camera").show();
                videoElement = document.querySelector("video");
                audioSelect = document.querySelector("select#audioSource");
                videoSelect = document.querySelector("select#videoSource");

                audioSelect.onchange = getStream;
                videoSelect.onchange = getStream;

                getStream().then(getDevices).then(gotDevices);

            });


            $(".btn-send-to-camera").on("click", function(e){
                e.preventDefault();
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4){
                        $(".btn-send-to-camera").hide();
                        $(".status-no-stream").show();
                    }
                };
                xhr.open('POST', '/message/update?id='+returnedData.id+'&roomid='+roomid+'&_token={{ csrf_token() }}');
                xhr.send();
            });
        });
        function emotion(emotion) {
            if (_length < emotion.length) {
                $.notify({ message: `@lang('donations.donate.emotion_error')` },{ type: 'danger' });
                return;
            }
            $('#message').val(`${$('#message').val()} ${emotion}`);
            $('#message').trigger('change');
        }
    </script>


    <script>
    
    </script>  
@endsection