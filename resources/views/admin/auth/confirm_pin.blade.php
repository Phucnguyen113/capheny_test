<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/set1.css" />
    <script src='https://code.jquery.com/jquery-3.5.0.min.js'></script>
    <script >
                    /*!
            * classie - class helper functions
            * from bonzo https://github.com/ded/bonzo
            * 
            * classie.has( elem, 'my-class' ) -> true/false
            * classie.add( elem, 'my-new-class' )
            * classie.remove( elem, 'my-unwanted-class' )
            * classie.toggle( elem, 'my-class' )
            */

            /*jshint browser: true, strict: true, undef: true */
            /*global define: false */

            ( function( window ) {

            'use strict';

            // class helper functions from bonzo https://github.com/ded/bonzo

            function classReg( className ) {
            return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
            }

            // classList support for class management
            // altho to be fair, the api sucks because it won't accept multiple classes at once
            var hasClass, addClass, removeClass;

            if ( 'classList' in document.documentElement ) {
            hasClass = function( elem, c ) {
                return elem.classList.contains( c );
            };
            addClass = function( elem, c ) {
                elem.classList.add( c );
            };
            removeClass = function( elem, c ) {
                elem.classList.remove( c );
            };
            }
            else {
            hasClass = function( elem, c ) {
                return classReg( c ).test( elem.className );
            };
            addClass = function( elem, c ) {
                if ( !hasClass( elem, c ) ) {
                elem.className = elem.className + ' ' + c;
                }
            };
            removeClass = function( elem, c ) {
                elem.className = elem.className.replace( classReg( c ), ' ' );
            };
            }

            function toggleClass( elem, c ) {
            var fn = hasClass( elem, c ) ? removeClass : addClass;
            fn( elem, c );
            }

            var classie = {
            // full names
            hasClass: hasClass,
            addClass: addClass,
            removeClass: removeClass,
            toggleClass: toggleClass,
            // short names
            has: hasClass,
            add: addClass,
            remove: removeClass,
            toggle: toggleClass
            };

            // transport
            if ( typeof define === 'function' && define.amd ) {
            // AMD
            define( classie );
            } else {
            // browser global
            window.classie = classie;
            }

            })( window );
    </script>
    <style>

            @import url(http://fonts.googleapis.com/css?family=Raleway:200,500,700,800);

            @font-face {
                font-weight: normal;
                font-style: normal;
                font-family: 'codropsicons';
                src:url('../fonts/codropsicons/codropsicons.eot');
                src:url('../fonts/codropsicons/codropsicons.eot?#iefix') format('embedded-opentype'),
                    url('../fonts/codropsicons/codropsicons.woff') format('woff'),
                    url('../fonts/codropsicons/codropsicons.ttf') format('truetype'),
                    url('../fonts/codropsicons/codropsicons.svg#codropsicons') format('svg');
            }

            *, *:after, *:before { -webkit-box-sizing: border-box; box-sizing: border-box;}
            .clearfix:before, .clearfix:after { content: ''; display: table; }
            .clearfix:after { clear: both; }

            body {
                background: #f9f7f6;
                color: #404d5b;
                font-weight: 500;
                font-size: 1.05em;
                font-family: 'Raleway', Arial, sans-serif;
            }
            .forget-text {
                font-size: 14px;
                margin-left: 18em;
                color:#388fc2;
                cursor: pointer;
            }
            a {
                color: #2fa0ec;
                text-decoration: none;
                outline: none;
            }

            a:hover, a:focus {
                color: #404d5b;
            }

            .container {
                margin: 0 auto;
                text-align: center;
                overflow: hidden;
            }

            .content {
                font-size: 150%;
                padding: 3em 0;
                text-align: center;
            }
            .submit-button {
                cursor: pointer;
            }
            .content h2 {
                margin: 0 0 2em;
                opacity: 0.1;
            }

            .content p {
                margin: 1em 0;
                padding: 5em 0 0 0;
                font-size: 0.65em;
            }

            .bgcolor-1 { background: #f0efee; }
            .bgcolor-2 { background: #f9f9f9; }
            .bgcolor-3 { background: #e8e8e8; }
            .bgcolor-4 { background: #244254b5; color: #fff; height: 100vh!important;}
            .bgcolor-5 { background: #df6659; color: #521e18; }
            .bgcolor-6 { background: #2fa8ec; color: #fff;}
            .bgcolor-7 { background: #d0d6d6; }
            .bgcolor-8 { background: #3d4444; color: #fff; }
            .bgcolor-9 { background: #8781bd; color: #fff; }
            .bgcolor-10 { background: #6C6C6C; }

            body .nomargin-bottom {
                margin-bottom: 0;
            }

            /* Header */
            .codrops-header {
                padding: 3em 190px 4em;
                letter-spacing: -1px;
            }

            .codrops-header h1 {
                font-weight: 800;
                font-size: 4em;
                line-height: 1;
                margin: 0.25em 0 0;
            }

            .codrops-header h1 span {
                display: block;
                font-size: 50%;
                font-weight: 400;
                padding: 0.325em 0 1em 0;
                color: #c3c8cd;
            }

            /* Demos nav */
            .codrops-demos a {
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: bold;
                font-size: 0.85em;
                display: inline-block;
                margin: 0 1em;
                font-family: "Avenir", "Helvetica Neue", Helvetica, Arial, sans-serif;
            }

            .codrops-demos a.current-demo {
                border-bottom: 2px solid;
                color: #404d5b;
            }

            /* Top Navigation Style */
            .codrops-links {
                position: relative;
                display: inline-block;
                white-space: nowrap;
                font-size: 1.25em;
                text-align: center;
            }

            .codrops-links::after {
                position: absolute;
                top: 0;
                left: 50%;
                margin-left: -1px;
                width: 2px;
                height: 100%;
                background: #dbdbdb;
                content: '';
                -webkit-transform: rotate3d(0,0,1,22.5deg);
                transform: rotate3d(0,0,1,22.5deg);
            }

            .codrops-icon {
                display: inline-block;
                margin: 0.5em;
                padding: 0em 0;
                width: 1.5em;
                text-decoration: none;
            }

            .codrops-icon span {
                display: none;
            }

            .codrops-icon:before {
                margin: 0 5px;
                text-transform: none;
                font-weight: normal;
                font-style: normal;
                font-variant: normal;
                font-family: 'codropsicons';
                line-height: 1;
                speak: none;
                -webkit-font-smoothing: antialiased;
            }

            .codrops-icon--drop:before {
                content: "\e001";
            }

            .codrops-icon--prev:before {
                content: "\e004";
            }

            /* Related demos */
            .content--related {
                text-align: center;
                color: #D8DADB;
                font-weight: bold;
            }

            .media-item {
                display: inline-block;
                padding: 1em;
                vertical-align: top;
                -webkit-transition: color 0.3s;
                transition: color 0.3s;
            }

            .media-item__img {
                opacity: 0.8;
                -webkit-transition: opacity 0.3s;
                transition: opacity 0.3s;
            }

            .media-item:hover .media-item__img,
            .media-item:focus .media-item__img {
                opacity: 1;
            }

            .media-item__title {
                font-size: 0.75em;
                margin: 0;
                padding: 0.5em;
            }

            @media screen and (max-width: 50em) {
                .codrops-header {
                    padding: 3em 10% 4em;
                }
            }

            @media screen and (max-width: 40em) {
                .codrops-header h1 {
                    font-size: 2.8em;
                }
            }
            article,aside,details,figcaption,figure,footer,header,hgroup,main,nav,section,summary{display:block;}audio,canvas,video{display:inline-block;}audio:not([controls]){display:none;height:0;}[hidden]{display:none;}html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;}body{margin:0;}a:focus{outline:thin dotted;}a:active,a:hover{outline:0;}h1{font-size:2em;margin:0.67em 0;}abbr[title]{border-bottom:1px dotted;}b,strong{font-weight:bold;}dfn{font-style:italic;}hr{-moz-box-sizing:content-box;box-sizing:content-box;height:0;}mark{background:#ff0;color:#000;}code,kbd,pre,samp{font-family:monospace,serif;font-size:1em;}pre{white-space:pre-wrap;}q{quotes:"\201C" "\201D" "\2018" "\2019";}small{font-size:80%;}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline;}sup{top:-0.5em;}sub{bottom:-0.25em;}img{border:0;}svg:not(:root){overflow:hidden;}figure{margin:0;}fieldset{border:1px solid #c0c0c0;margin:0 2px;padding:0.35em 0.625em 0.75em;}legend{border:0;padding:0;}button,input,select,textarea{font-family:inherit;font-size:100%;margin:0;}button,input{line-height:normal;}button,select{text-transform:none;}button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer;}button[disabled],html input[disabled]{cursor:default;}input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0;}input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;}input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none;}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0;}textarea{overflow:auto;vertical-align:top;}table{border-collapse:collapse;border-spacing:0;}
            .login {
                text-align: center;
                color: white!important;
                font-size: 40px;
            }
            .input {
                position: relative;
                z-index: 1;
                display: inline-block;
                margin: 1em;
                max-width: 350px;
                width: calc(100% - 2em);
                vertical-align: top;
            }

            .input__field {
                position: relative;
                display: block;
                float: right;
                padding: 0.8em;
                width: 60%;
                border: none;
                border-radius: 0;
                background: #f0f0f0;
                color: #aaa;
                font-weight: bold;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                -webkit-appearance: none; /* for box shadows to show on iOS */
            }

            .input__field:focus {
                outline: none;
            }

            .input__label {
                display: inline-block;
                float: right;
                padding: 0 1em;
                width: 40%;
                color: #6a7989;
                font-weight: bold;
                font-size: 70.25%;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            .input__label-content {
                position: relative;
                display: block;
                padding: 1.6em 0;
                width: 100%;
            }

            .graphic {
                position: absolute;
                top: 0;
                left: 0;
                fill: none;
            }

            .icon {
                color: #ddd;
                font-size: 150%;
            }
            /* Madoka */
            .input--madoka {
                margin: 1.1em;
                cursor: pointer!important;
            }

            .input__field--madoka {
                width: 100%;
                background: transparent;
                color: #d1d1d1;
                cursor: pointer!important;
                font-size: 16px;
                padding: 1.6em;
            }

            .input__label--madoka {
                position: absolute;
                width: 100%;
                height: 100%;
                color: #7A7593;
                text-align: left;
                cursor: text;
            }

            .input__label-content--madoka {
                -webkit-transform-origin: 0% 50%;
                transform-origin: 0% 50%;
                -webkit-transition: -webkit-transform 0.3s;
                transition: transform 0.3s;
            }

            .graphic--madoka {
                -webkit-transform: scale3d(1, -1, 1);
                transform: scale3d(1, -1, 1);
                -webkit-transition: stroke-dashoffset 0.3s;
                transition: stroke-dashoffset 0.3s;
                pointer-events: none;

                stroke: #d1d1d1;
                stroke-width: 4px;
                stroke-dasharray: 962;
                stroke-dashoffset: 558;
            }

            .input__field--madoka:focus + .input__label--madoka,
            .input--filled .input__label--madoka {
                cursor: default;
                pointer-events: none;
            }

            .input__field--madoka:focus + .input__label--madoka .graphic--madoka,
            .input--filled .graphic--madoka {
                stroke-dashoffset: 0;
            }

            .input__field--madoka:focus + .input__label--madoka .input__label-content--madoka,
            .input--filled .input__label-content--madoka {
                -webkit-transform: scale3d(0.81, 0.81, 1) translate3d(0, 4em, 0);
                transform: scale3d(0.81, 0.81, 1) translate3d(0, 4em, 0);
            }
    </style>
</head>
<body>
    <section class="content bgcolor-4">
        <h1 class="login">NHẬP MÃ XÁC NHẬN CỦA BẠN</h1>
        <div class="input-email">
        <span class="input input--madoka">
            @csrf
            <input type="hidden" name="refify_token" value="{{$token}}">
            <input class="input__field input__field--madoka" type="text" id="input-31" name="user_email"/>
            <label class="input__label input__label--madoka" for="input-31">
                <svg class="graphic graphic--madoka" width="100%" height="100%" viewBox="0 0 404 77" preserveAspectRatio="none">
                    <path d="m0,0l404,0l0,77l-404,0l0,-77z"/>
                </svg>
                <span class="input__label-content input__label-content--madoka" style="color: rgb(221, 221, 221)">Nhập mã xác nhận</span>
            </label>
           
        </span>
        <div id="verify_pin_error" class="p_error" style="color:red"></div>
    </div>
    <div class="submit-button">
        <span class="input input--madoka">
            <input class="input__field input__field--madoka" type="submit" value="Tiếp tục" id="input-33" />
            <label class="input__label input__label--madoka" for="input-33">
                <svg class="graphic graphic--madoka" width="100%" height="100%" viewBox="0 0 404 77" preserveAspectRatio="none">
                    <path d="m0,0l404,0l0,77l-404,0l0,-77z"/>
                </svg>
            </label>
        </span>
    </div>
    </section>
    <script>
        (function() {
            // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
            if (!String.prototype.trim) {
                (function() {
                    // Make sure we trim BOM and NBSP
                    var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
                    String.prototype.trim = function() {
                        return this.replace(rtrim, '');
                    };
                })();
            }

            [].slice.call( document.querySelectorAll( 'input.input__field' ) ).forEach( function( inputEl ) {
                // in case the input is already filled..
                if( inputEl.value.trim() !== '' ) {
                    classie.add( inputEl.parentNode, 'input--filled' );
                }

                // events:
                inputEl.addEventListener( 'focus', onInputFocus );
                inputEl.addEventListener( 'blur', onInputBlur );
            } );

            function onInputFocus( ev ) {
                classie.add( ev.target.parentNode, 'input--filled' );
            }

            function onInputBlur( ev ) {
                if( ev.target.value.trim() === '' ) {
                    classie.remove( ev.target.parentNode, 'input--filled' );
                }
            }
        })();
    </script>
</body>
</html>
<script>
    $('#input-33').on('click',function(){
        
        var verify_pin=$('#input-31').val();
        var verify_token=$('input[name="refify_token"]').val();
        var _token=$('input[name="_token"]').val();
        if(verify_pin.lenght==0){
            $('#verify_pin_error').html('Mã xác nhận không được trống');
        }else{
            $.ajax({
                type: "POST",
                url: "{{url('admin/auth/confirm_pin')}}/{{$token}}",
                data: {verify_pin:verify_pin,_token:_token,verify_token:verify_token},
                dataType: "json",
                success: function (response) {
                    if(!$.isEmptyObject(response.error)){
                        $('.p_error').html('');
                        $.each(response.error,function(index,item){
                            $(`#${index}_error`).html(item);
                        })
                    }else{
                       window.location.replace('{{url("admin/auth/change_password")}}/'+response.token)
                    }
                }
            });
        }

    })
</script>