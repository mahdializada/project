@extends('layout.master');
@section('content');

<div class="bdcmb-bg1 page-head parallax overlay">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-center">
                    <h3>LA LIGA FIXTURE</h3>
                </div>
            </div>
            <!-- /.colour-service-1-->
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li><a href="index.html">home</a></li>
                    <li>।</li>
                    <li>club list</li>
                    <li>।</li>
                    <li>barcelona</li>
                </ol>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</div>
<!-- /.page-header -->
<!-- page title & breadcrumbs end -->

<!--fixtures area start-->
<div class="laliga-box pad60">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4 class="text-center">la liga</h4>
                <div class="fixture-box bb-1 ">
                    <div class="club-fixture">
                        <div class="club-img">
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/barcelona.png')}}" alt="club logo">
                            <span>barcelona</span>
                        </div>
                        <div class="club-schedule text-center">
                            <h5>2018-june-20 12.00 am</h5>
                            <p>barcelona</p>
                        </div>
                        <div class="club-img">
                            <span>real madrid</span>
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/realmadrid.png')}}" alt="club logo">
                        </div>
                    </div>
                </div>
                <div class="fixture-box bb-1">
                    <div class="club-fixture  active">
                        <div class="club-img">
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/atletico.png')}}" alt="club logo">
                            <span>Atlético Madrid</span>
                        </div>
                        <div class="club-schedule text-center">
                            <h5>2018-june-20 12.00 am</h5>
                            <p>Atlético Madrid</p>
                        </div>
                        <div class="club-img">
                            <span>villarreal</span>
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/villarreal.png')}}" alt="club logo">
                        </div>
                    </div>
                </div>
                <div class="fixture-box bb-1">
                    <div class="club-fixture">
                        <div class="club-img">
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/sevilla.png')}}" alt="club logo">
                            <span>sevilla</span>
                        </div>
                        <div class="club-schedule text-center">
                            <h5>2018-june-20 12.00 am</h5>
                            <p>barcelona</p>
                        </div>
                        <div class="club-img">
                            <span>real betis</span>
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/realbetis.png')}}" alt="club logo">
                        </div>
                    </div>
                </div>
                <div class="fixture-box bb-1">
                    <div class="club-fixture">
                        <div class="club-img">
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/alves.png')}}" alt="club logo">
                            <span>alves</span>
                        </div>
                        <div class="club-schedule text-center">
                            <h5>2018-june-20 12.00 am</h5>
                            <p>real madrid</p>
                        </div>
                        <div class="club-img">
                            <span>barcelona</span>
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/barcelona.png')}}" alt="club logo">
                        </div>
                    </div>
                </div>
                <div class="fixture-box bb-1">
                    <div class="club-fixture">
                        <div class="club-img">
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/realmadrid.png')}}" alt="club logo">
                            <span>real madrid</span>
                        </div>
                        <div class="club-schedule text-center">
                            <h5>2018-june-20 12.00 am</h5>
                            <p>real madrid</p>
                        </div>
                        <div class="club-img">
                            <span>malaga</span>
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/malaga.png')}}" alt="club logo">
                        </div>
                    </div>
                </div>
                <div class="fixture-box bb-1">
                    <div class="club-fixture">
                        <div class="club-img">
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/barcelona.png')}}" alt="club logo">
                            <span>barcelona</span>
                        </div>
                        <div class="club-schedule text-center">
                            <h5>2018-june-20 12.00 am</h5>
                            <p>barcelona</p>
                        </div>
                        <div class="club-img">
                            <span>real madrid</span>
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/realmadrid.png')}}" alt="club logo">
                        </div>
                    </div>
                </div>
                <div class="fixture-box">
                    <div class="club-fixture">
                        <div class="club-img">
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/villarreal.png')}}" alt="club logo">
                            <span>villarreal</span>
                        </div>
                        <div class="club-schedule text-center">
                            <h5>2018-june-20 12.00 am</h5>
                            <p>barcelona</p>
                        </div>
                        <div class="club-img">
                            <span>sevilla</span>
                            <img class="sm-img" src="{{asset('assets/images/club-logo/logo/sevilla.png')}}" alt="club logo">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--fixtures area end-->

@endsection()
