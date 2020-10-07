@extends('layouts.blank')

@section('css')
    @parent
    <link href="{{ asset('assets/css/auth.css') }}" rel="stylesheet">
@endsection

@section('content')
    {{-- Content --}}
    <div class="auth" id="content">
        <div class="auth-container">
            <div class="card">
                <img src="{{ asset('assets/img/logo-clean.png') }}" class="logo">
                <div class="auth-content">
                    <h3>
                        @lang('auth.home.title')
                    </h3><br>
                    @include('auth.links')

                    <div class="auth-btns">
                        <div class="auth-btn active">Streamer</div>
                        <div class="auth-btn">Advertiser</div>
                    </div>
                </div>
            </div>
            <div class="text-xs-center footer-copy">
                &copy; {{ date('Y') }} {{ config('app.title') }}
            </div>
        </div>
    </div>
    <script>
        function setCookie(name,value,days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        }
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
        function eraseCookie(name) {   
            document.cookie = name+'=; Max-Age=-99999999;';  
        }
        setCookie("type","Streamer");

        var btns = document.querySelectorAll(".auth-btn");
        btns.forEach(function(el){
            el.addEventListener("click", function(e){
                e.preventDefault();
                btns.forEach(function(btn){
                    btn.classList.remove("active");
                });
                el.classList.add("active");
                console.log(el.innerHTML);
                setCookie("type",el.innerHTML);
            });
        });
    </script>
@endsection
