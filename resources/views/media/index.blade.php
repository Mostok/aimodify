@extends('layouts.app')

@section('content')
    <style>
        .media-library-top{
            display: flex;
            flex-wrap: wrap;
            position: relative;
            max-width: 150px;
            padding: 5px 10px;
            justify-content: center;
            background: #59438c;
            color: #fff;
            align-items: center;
            font-weight: 700;
        }
        .media-library-top input[type="file"]{
            cursor: pointer;
        }

        .media-file-add input[type="file"]{
            cursor: pointer;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
        }
        .media-file-add i{
            margin-right: 5px;
        }
        .media-file.media-file-add>div{
            position: relative;
            border: 2px solid #ccc;
            text-align: center;
            font-size: 25px;
            padding: 80px 0;
            transition: .2s all;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .media-file{
            width: 100%;
            display: block;
        }
        .media-file p{
            margin-bottom: 0;
            margin-top: 10px;
            text-align: center;
            padding: 0 15px;
    		word-break: break-all;
        }
        .media-file .fa-play{
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%,-50%);
            font-size: 60px;
        }
        .media-file>div{
            height: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            background: #d9cafb;
            font-weight: bold;
            font-size: 22px;
            border: 2px solid #59438c;
            position: relative;
            min-height: 204px;
        }
        .media-file>div:hover{
            background: #ccc;
            color: #fff;
        }
        .media-file img,.media-file video{
            max-width: 100%;
            max-height: 200px;
            min-height: 200px;
            display: block;
        }
        .media-library{
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
            margin-top: 20px;
        }
        .media-library>div{
        	width: 20%;
            padding: 0 10px;
            position: relative;
            margin-bottom: 20px;
        }
        .media-file-delete{
        	position: absolute;
		    right: 12px;
		    top: 0px;
		    font-size: 34px;
		    background: #59438cbf;
		    padding: 0 10px;
		    color: #fff;
		    transition: .2s all;
		    cursor: pointer;
        }
        .media-file-delete:hover{
        	background: #59438c;
        }
        .media-file-delete form{
        	display: none;
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
    <form action="/media/add" class="media-library-top media-file-add" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="file" name="file" />
        <i class="fa fa-plus"></i>
        Add media
    </form>
    <div class="media-library">
        @foreach($files as $file)
        	<div>
	            <a data-fancybox="gallery" href="{{ $file->getUrl() }}" class="media-file">
	                <div title="{{ $file->file_name }}">
	                @if($file->mime_type == "image/jpeg" || $file->mime_type == "image/png" || $file->mime_type == "image/gif" || strpos($file->file_name, ".svg") !== FALSE)
	                    <img src="{{ $file->getUrl() }}" alt="{{ $file->name }}"> 
	                @elseif($file->mime_type == "video/mp4")
	                    <video src="{{ $file->getUrl() }}"></video>
	                    <i class="fa fa-play"></i>
	                @else
	                    <span>{{ $file->file_name }}</span>
	                @endif
	                </div>
                    <p>{{ $file->file_name }}</p> 
	            </a>
	            <div class="media-file-delete">
	            	<i class="fa fa-trash"></i>
	            	<form action="/media/remove" method="POST">
	            		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
	            		<input type="hidden" name="media_id" value="{{ $file->id }}">
	            	</form>
	            </div>
            </div>
        @endforeach
        <div>
        	<form action="/media/add" class="media-file media-file-add" method="POST" enctype="multipart/form-data">
	            <div>
	                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
	                <input type="file" name="file" />
	                <i class="fa fa-plus"></i>
	                Add media
	            </div>
	        </form>
        </div>
        
    </div>
    <div class="pagination">
        {{ $files->links() }}
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script type="text/javascript">
        document.querySelectorAll("input[type='file']").forEach(function(input){
            input.addEventListener("change", function(){
                if(input.parentElement.tagName == "FORM"){
                    input.parentElement.submit();
                }else{
                    input.parentElement.parentElement.submit();
                }
                
            });
        });

        document.querySelectorAll(".media-file-delete").forEach(function(btn){
            btn.addEventListener("click", function(){
            	var result = confirm("Delete media?");
            	if(result){
            		btn.querySelector("form").submit();
            	}
            });
        });

        
    </script>
@endsection