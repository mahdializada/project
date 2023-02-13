@extends('layout.master');
@section('content');


<div class="bdcmb-bg4 page-head parallax overlay">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-center">
                    <h3>Statutes</h3>
                </div>
            </div>
            <!-- /.colour-service-1-->
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li>।</li>
                    <li><a href="{{url('/')}}">home</a></li>
                    <li>sports news</li>
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

<div class="news-area pad60">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-9">
                <div class="sports-news mb60">
                    <div class="sports-img zoom mb40">
                        <img src="assets/images/sports-news/5.jpg" alt="news img">
                    </div>
                    <div class="sports-title mb20">
                        <h3>VOTE: 2018/18 Championship table</h3>
                        <p>By <span>Michael Bonose</span> / 15/12/2015 / 3 Comments / Categories: <span>Sports/Meeting/Corporate</span></p>
                    </div>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores ea id, voluptate, iusto, ullam quos maiores animi quo tempore laboriosam laborum magni aliquam molestias aperiam ut libero ipsam sequi harum! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores ipsum excepturi, ad unde laudantium voluptatum fugiat molestiae in tempore, deserunt hic quibusdam, non nam quaerat eligendi ipsam laboriosam. Facilis, excepturi.</p>
                    <div class="news-btn mt40">
                        <a href="#">read more</a>
                    </div>
                </div>
                <div class="sports-news mb60">
                    <div class="sports-img zoom mb40">
                        <img src="assets/images/sports-news/6.jpg" alt="news img">
                    </div>
                    <div class="sports-title mb20">
                        <h3>Watford set to sign Richarlison.</h3>
                        <p>By <span>Michael Bonose</span> / 15/12/2015 / 3 Comments / Categories: <span>Sports/Meeting/Corporate</span></p>
                    </div>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores ea id, voluptate, iusto, ullam quos maiores animi quo tempore laboriosam laborum magni aliquam molestias aperiam ut libero ipsam sequi harum! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores ipsum excepturi, ad unde laudantium voluptatum fugiat molestiae in tempore, deserunt hic quibusdam, non nam quaerat eligendi ipsam laboriosam. Facilis, excepturi.</p>
                    <div class="news-btn mt40">
                        <a href="#">read more</a>
                    </div>
                </div>
                <div class="sports-news mb60">
                    <div class="highlights-bg h-bg8 mini-bg overlay mb40">
                        <a class="mfp-iframe video-play-btn" href="https://www.youtube.com/watch?v=Kbg777rhMrA"><i class="fa fa-youtube-play"></i></a>
                    </div>

                    <div class="sports-title mb20">
                        <h3>Klopp stands firm on Coutinho.</h3>
                        <p>By <span>Michael Bonose</span> / 15/12/2015 / 3 Comments / Categories: <span>Sports/Meeting/Corporate</span></p>
                    </div>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores ea id, voluptate, iusto, ullam quos maiores animi quo tempore laboriosam laborum magni aliquam molestias aperiam ut libero ipsam sequi harum! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores ipsum excepturi, ad unde laudantium voluptatum fugiat molestiae in tempore, deserunt hic quibusdam, non nam quaerat eligendi ipsam laboriosam. Facilis, excepturi.</p>
                    <div class="news-btn mt40">
                        <a href="#">read more</a>
                    </div>
                </div>
                <nav aria-label="Page navigation mt60">
                    <ul class="pagination justify-content-left">
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">4</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-md-12 col-lg-3">
                <div class="news-box mb50 xs-mt-50">
                    <div class="news-search">
                        <form action="#">
                            <input type="text" placeholder="search">
                            <a href="#"><i class="fa fa-search"></i></a>
                        </form>
                    </div>
                </div>
                <div class="recent-news mt20">
                    <div class="blog-title">
                        <h3>recent news</h3>
                    </div>
                    <div class="comment-box mt30">
                        <img src="assets/images/sports-news/2.jpg" alt="sport img">
                        <div class="user-cmnt">
                            <a href="#"> <span>Lorem ipsum dolor sit amet, consectetur</span></a>
                            <p>Donec mi orci, vestibulum a auctor eu, tristique a diam.</p>
                        </div>
                    </div>
                    <div class="comment-box mt30">
                        <img src="assets/images/sports-news/4.jpg" alt="sport img">
                        <div class="user-cmnt">
                            <a href="#">Donec mi orci, vestibulum a auctor</a>
                            <p>Donec mi orci, vestibulum a auctor eu, tristique a diam.</p>
                        </div>
                    </div>
                    <div class="comment-box mt30">
                        <img src="assets/images/sports-news/7.jpg" alt="sport img">
                        <div class="user-cmnt">
                            <a href="#">Vestibulum cursus ut massa sed malesuada</a>
                            <p>Donec mi orci, vestibulum a auctor eu, tristique a diam.</p>
                        </div>
                    </div>
                    <div class="comment-box mt30">
                        <img src="assets/images/sports-news/1.jpg" alt="sport img">
                        <div class="user-cmnt">
                            <a href="#">Fusce neque felis, iaculis in pulvinar</a>
                            <p>Donec mi orci, vestibulum a auctor eu, tristique a diam.</p>
                        </div>
                    </div>
                </div>
                <div class="twit-area twit-post mt50">
                    <div class="section-title news-title">
                        <h4> <span>twitter post</span></h4>
                    </div>
                    <div class="twit-box">
                        <i class="fa fa-twitter"></i>
                        <div class="twitted">
                            <p><a href="#">@orchesTemplate</a> Lorem ipsum sit amet, consectetur adipiscing elit. Phasellus quis est sem. </p>
                            <p class="sml">5 minutes ago</p>
                        </div>
                    </div>
                    <div class="twit-box mt20">
                        <i class="fa fa-twitter"></i>
                        <div class="twitted">
                            <p><a href="#">@orchesTemplate</a> Lorem ipsum sit amet, consectetur adipiscing </p>
                            <a href="#">https://twitter.com/cigacmfcmr</a>
                            <p class="sml">8 minutes ago</p>
                        </div>
                    </div>
                    <div class="twit-box mt20">
                        <i class="fa fa-twitter"></i>
                        <div class="twitted">
                            <p><a href="#">@orchesTemplate</a> Donec mi orci, vestibulum a auctor eu, tristique a diam. Sed non lacinia metus.</p>
                            <a href="#">https://twitter.com/cigacmfcmr</a>
                            <p class="sml">15 minutes ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="footer-area bg2 parallax overlay2 pad60">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title ">
                        <h4> <span class="wht-txt">newsletter</span></h4>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="subscribe">
                        <form action="#">
                            <input class="name" type="text" placeholder="youremail@domain.com">
                        </form>
                        <a href="#"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="footer-box pt60">
                        <div class="footer-content add">
                            <div class="footer-logo footer-content">
                                <img src="assets/images/logo/logo.png" alt="footer logo">
                            </div>
                            <p class="pt30">Lorem ipsum dolor sit amet, ei ubique fastidii vim. Elitr feugait complectitur eu pro, sea audire ponderum eleifend cu. Vim at fuisset.</p>
                            <div class="add-info">
                                <p><a href="#"><i class="fa fa-map-marker"></i></a>23 New Design Street, Melbourne</p>
                                <p><a href="#"><i class="fa fa-location-arrow"></i></a>sportz@gmail.com</p>
                                <p class="mb-0"><a href="#"><i class="fa fa-phone" ></i></a>+880-123-456-7890</p>
                            </div>
                        </div>
                        <div class="footer-content">
                            <div class="ftr-title xs-mt-40">
                                <h4>partners</h4>
                            </div>
                            <div class="partners pt30">
                                <a href="#">fantasy football</a>
                                <a href="#">super 6</a>
                                <a href="#">sky Sports Pub Finder</a>
                                <a href="#">Living for Sport</a>
                                <a href="#">Planet Rugby</a>
                                <a href="#">Cricket365</a>
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
                                <a href="#">FIFA 2015</a>
                                <a href="#">Copa 2015</a>
                                <a href="#">UEFA Europa League</a>
                                <a href="#">La Liga League</a>
                                <a href="#">Uro 2014</a>
                                <a href="#">Africa 2015</a>
                            </div>
                        </div>
                        <div class="footer-content">
                            <div class="ftr-title xs-mt-40">
                                <h4>sportz channel</h4>
                            </div>
                            <div class="partners pt30">
                                <a href="#">Sports Main Event</a>
                                <a href="#">S. Sports Premier League</a>
                                <a href="#">Sky Sports Football</a>
                                <a href="#">Sky Sports Cricket</a>
                                <a href="#">Sky Sports Golf</a>
                                <a href="#">Sky Sports Action</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright pad30">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4>Copyright © 2018 designed by <span>iThemeslab.</span> All Rights Reserved</h4>
                </div>
            </div>
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






@endsection();
