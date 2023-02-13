<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Afghanistan Football Federation "AFF" </title>
    <meta name="author" content="iThemesLab">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons -->
    <link rel="shortcut icon" href="{{asset('assets/favicon/favicon.ico')}}">
    <link rel="apple-touch-icon" href="{{asset('assets/favicon/apple-icon-57x57.png')}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{asset('assets/favicon/apple-icon-72x72.png')}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{asset('assets/favicon/apple-icon-114x114.png')}}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{asset('assets/favicon/apple-icon-144x144.png')}}">

    <!--All Css Here-->

    <!--Bootstrap Css-->
    <link rel="stylesheet" href="{{asset('assets/css/vendor/bootstrap.min.css')}}">
    <!--Font-Awesome Css-->
    <link rel="stylesheet" href="{{asset('assets/css/vendor/font-awesome.min.css')}}">
    <!--Owl-Carousel Css-->
    <link rel="stylesheet" href="{{asset('assets/css/vendor/owl.carousel.min.css')}}">
    <!--Animate Css-->
    <link rel="stylesheet" href="{{asset('assets/css/vendor/animate.css')}}">
    <!--magnific popup Css-->
    <link rel="stylesheet" href="{{asset('assets/css/vendor/magnific-popup.css')}}">
    <!--Jquery Ui Css-->
    <link rel="stylesheet" href="{{asset('assets/css/vendor/jquery-ui.min.css')}}">
    <!--Style Css-->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <!--Responsive Css-->
    <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}">


    <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- preloader -->
    <div class="preloader">
        <div class='loader'>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--text'></div>
        </div>
    </div>
    <!-- preloader -->

    <!--main-container-->
    <div class="main-container">

        <!-- /.header start -->
        <header>
            <div class="nav-menu" id="sticky-menu">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="navbar-header">
                                <a class="navbar-brand" href="{{url('/')}}"><img src="{{url('assets/images/logo/logo.png')}}"  alt="BEFIT logo"></a>
                                <button class="navbar-toggler d-md-inlline d-xl-none" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="fa fa-bars"></span>
                                </button>
                            </div>
                        </div>
                        <!--col end-->
                        <div class="col-lg-8">
                            <nav class="navbar navbar-expand-lg">
                                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                                    <ul class="navbar-nav ml-auto">
                                        <li class="nav-item active">
                                            <a class="nav-link" href="{{url('/')}}">HOME</a>
                                        </li>
                                        <li class="nav-item dropdown ">
                                          <a class="nav-link dropdown-toggle"  data-toggle="dropdown">aboutus</a>
                                          <div class="dropdown-menu animation  slideUpIn">
                                            <a class="dropdown-item" href="{{url('history')}}">HISTORY</a>
                                          <a class="dropdown-item" href="{{url('who_we')}}" >WHO WE ARE</a>
                                          <a class="dropdown-item" href="{{url('couching')}}">COUCHING</a>
                                            <a class="dropdown-item" href="{{url('refereeing')}}">REFEREEING</a>
                                            <a class="dropdown-item" href="{{url('award')}}">AWARDS</a>
                                            <a class="dropdown-item" href="{{url('partener')}}">OUR PARTENAR</a>
                                            <a class="dropdown-item" href="{{url('jobs_team')}}">JOBS</a>
                                            <a class="dropdown-item" href="{{url('statutes')}}">STATUTES AND REGULATIONS</a>
                                            <a class="dropdown-item" href="{{url('tribunal')}}">TRIBUNAL DECISIONS</a>
                                            <a class="dropdown-item" href="home3.html">AFF ANNUAL REVIEW</a>
                                            <a class="dropdown-item" href="home3.html">FINANCAIL REPORTS</a>
                                            <a class="dropdown-item" href="{{url('contact')}}">CONTACT</a>
                                          </div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " href="{{url('fixture')}}">FUTURE</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{url('result')}}">RESULT</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="index.html" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">COMPITITIONS</a>
                                            <div class="dropdown-menu animation  slideUpIn">
                                                <a class="dropdown-item" href="{{url('cups')}}">ASIA CUP</a>
                                                <a class="dropdown-item" href="{{url('friendly')}}">FRIENDLY MATCHES</a>
                                                <a class="dropdown-item" href="{{url('kabul_legure')}}">KABUL LEAGURE</a>
                                                <a class="dropdown-item" href="{{url('women')}}">WOMEN CHAMPIONSSHIP</a>
                                                <a class="dropdown-item" href="futsal">FUTSAL LEAGUE</a>
                                            </div>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="index.html" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">NATIONALTEAMS</a>
                                            <div class="dropdown-menu animation  slideUpIn">
                                                <a class="dropdown-item" href="{{url('mens')}}">MEN'S NATIONAL TEAMS</a>
                                                <a class="dropdown-item" href="{{url('women')}}">WOMEN'S NATIONAL TEAMS</a>
                                            </div>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="index.html" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">STORE</a>
                                            <div class="dropdown-menu animation  slideUpIn">
                                                <a class="dropdown-item" href="{{url('store')}}">STORE HOME</a>
                                                <a class="dropdown-item" href="{{url('mens')}}">MEN'S JERSEY</a>
                                            <a class="dropdown-item" href="{{url('women')}}">WOMEN'S JERSEY</a>
                                                <a class="dropdown-item" href="{{url('accessories')}}">ACCESSORIES</a>
                                                <a class="dropdown-item" href="{{url('exculusive')}}">EXCULUSIVE</a>
                                                <a class="dropdown-item" href="{{url('wallpaper')}}">WALLPAPER</a>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </nav>
                        </div>
                        <!--col end-->
                        <div class="col-lg-1">
                            <ul class="navbar-nav">
                                <li class="nav-item d-none d-lg-inline">
                                    <div class="icon-menu">
                                        <ul>
                                            <li><a href="#" class="search-btn search-box-btn"><i class="fa fa-search"></i></a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                                </div>
                            </nav>
                        </div>
                        <!--col end-->

                        <!--col end-->
                    </div>
                    <!--row end-->
                </div>
            </div>

            @if(URL::current() == url('/'))
            @include('layout.slider')
            @endif

        </header>
        <!--header end-->

        @yield('content')




        <footer>
            <div class="footer-area bg2 parallax overlay2 pad60">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="section-title ">
                                <h4> <span class="wht-txt">AFGHANISTAN FOOTBALL FEDERATION</span></h4>
                            </div>
                        </div>
                        <!--col end-->
                        <div class="col-md-12">
                            <div class="subscribe">
                                <form action="#">
                                    <input class="name" type="text" placeholder="media@aff.org.af">
                                </form>
                                <a href="#"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <!--col end-->
                    </div>
                    <!--row end-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="footer-box pt60">
                                <div class="footer-content add">
                                    <div class="footer-logo footer-content">
                                        <img src="assets/images/logo/logo.png" alt="footer logo">
                                    </div>
                                    <p class="pt30">The Afghanistan Football Federation is the governing body of football in Afghanistan, controlling the men's and women's national teams. It was founded in 1933 and has been a member of FIFA since 1948 and the Asian Football Confederation since 1954.</p>
                                    <div class="add-info">
                                        <p><a href="#"><i class="fa fa-map-marker"></i></a>P.O. Box 128 - KABUL</p>
                                        <p><a href="#"><i class="fa fa-location-arrow"></i></a>info@aff.org.af</p>
                                        <p class="mb-0"><a href="#"><i class="fa fa-phone" ></i></a>+93-75/2023 770</p>
                                    </div>
                                </div>
                                <div class="footer-content">
                                    <div class="ftr-title xs-mt-40">
                                        <h4>partners</h4>
                                    </div>
                                    <div class="partners pt30">
                                        <a href="#">AGC</a>
                                        <a href="#">hummel</a>
                                        <a href="#">Emirates</a>
                                        <a href="#">Alokozay Magic</a>
                                        <a href="#">Alokozay Cola</a>
                                        <a href="#">Alokozay Brezz</a>
                                    </div>
                                </div>
                                <div class="footer-content">
                                    <div class="ftr-title xs-mt-40">
                                        <h4>latest post</h4>
                                    </div>
                                    <div class="news-info pt30">
                                        <div class="news-detail nws-bar zoom">
                                            <img src="assets/images/footer/1.jpg" alt="footer img">
                                            <p>Set yourself the challenge of doing the bare minimum.</p>
                                        </div>
                                        <div class="news-detail zoom">
                                            <img src="assets/images/footer/2.jpg" alt="footer img">
                                            <p>Body fat percentage: what does it really mean?</p>
                                        </div>
                                        <div class="news-detail zoom">
                                            <img src="assets/images/footer/3.jpg" alt="footer img">
                                            <p class="mb-0">This treatment sounded just what I was looking for.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer-content">
                                    <div class="ftr-title xs-mt-40">
                                        <h4>football</h4>
                                    </div>
                                    <div class="partners pt30">
                                        <a href="#">FIFA World Cup 2022</a>
                                        <a href="#">AFC Asian Cup 2023</a>
                                        <a href="#">APL 2020</a>
                                        <a href="#">ACL 2020</a>
                                        <a href="#">CAFA 2020</a>
                                        <a href="#">Kabul Futsal League 2020</a>
                                    </div>
                                </div>
                                <div class="footer-content">
                                    <div class="ftr-title xs-mt-40">
                                        <h4>TV Partners</h4>
                                    </div>
                                    <div class="partners pt30">
                                        <a href="#">Tolo TV</a>
                                        <a href="#">Lemar TV</a>
                                        <a href="#">Ariana TV</a>
                                        <a href="#">Sky Sports </a>
                                        <a href="#">Sky Sports HD</a>
                                        <a href="#">BenSports HD</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--col end-->
                    </div>
                    <!--row end-->
                </div>
            </div>
            <div class="copyright pad30">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Copyright © 2020 <span>Afghanistan Football Federation.</span> All Rights Reserved</h4>
                        </div>
                        <!--col end-->
                    </div>
                    <!--row end-->
                </div>
            </div>
        </footer>

    </div>
    <!--main-container-->

    <!--Search Popup-->
    <div id="search-popup" class="search-popup">
        <div class="close-search theme-btn"><span class="fa fa-close"></span></div>
        <div class="popup-inner">

            <div class="search-form">
                <form method="post" action="index.html">
                    <div class="form-group">
                        <fieldset>
                            <input type="search" class="form-control" name="search-input" value="" placeholder="Search Here" required>
                            <input type="submit" value="Search" class="theme-btn">
                        </fieldset>
                    </div>
                </form>

                <br>
                <h3>Recent Search Keywords</h3>
            </div>
        </div>
    </div>
    <!--End Search Popup-->

    <!-- back to to btn start -->
    <div id="back-to-top"></div>
    <!-- back to to btn end -->


    <!--All Js Here-->
    <!-- jquery latest version -->
    <script src="{{asset('assets/js/vendor/jquery-3.2.1.min.js')}}"></script>
    <!--Migrate Js-->
    <script src="{{asset('assets/js/vendor/jquery-migrate.js')}}"></script>
    <!--Popper Js-->
    <script src="{{asset('assets/js/vendor/popper-1.12.3.min.js')}}"></script>
    <!--Bootstrap Js-->
    <script src="{{asset('assets/js/vendor/bootstrap.min.js')}}"></script>
    <!--Owl-Carousel Js-->
    <script src="{{asset('assets/js/vendor/owl.carousel.min.js')}}"></script>
    <!--magnific-popup js-->
    <script src="{{asset('assets/js/vendor/jquery.magnific-popup.min.js')}}"></script>
    <!--countdown Js-->
    <script src="{{asset('assets/js/vendor/countdown.js')}}"></script>
    <!--counter Js-->
    <script src="{{asset('assets/js/vendor/jquery.counterup.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/waypoints-jquery.js')}}"></script>
    <!--Jquery Ui Js-->
    <script src="{{asset('assets/js/vendor/jquery-ui.min.js')}}"></script>
    <!--Wow Js-->
    <script src="{{asset('assets/js/vendor/wow.min.js')}}"></script>

    <!-- template main js file -->
    <script src="{{asset('assets/js/main.js')}}"></script>
</body>

</html>
