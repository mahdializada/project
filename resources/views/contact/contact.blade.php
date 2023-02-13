@extends('layout.master')
@section('content')

<div class="bdcmb-bg3 page-head parallax overlay">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-center">
                </div>
            </div>
            <!-- /.colour-service-1-->
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li><a href="{{url('/')}}">home</a></li>
                    <li>।</li>
                    <li>Contact Us </li>
                </ol>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</div>
<!-- /.page-header -->
<br><br>
<div class="container" style="color:black">
    <form action="#" method="post" enctype="multipart/form-data">
        <div class="form-group col-md-6">
          <label>Name <i class="text-red">*</i></label>
          <input type="text" name="name"  class="form-control">
        </div>
        <div class="form-group col-md-6">
          <label>Email*</label>
          <input type="text" name="email"  class="form-control">
        </div>
        <div class="form-group col-md-6">
          <label>Discription*</label>
          <input type="textarea" name="discription" class="form-control">
        </div>
        <div class="form-group col-md-6">
            <input type="submit" name="submit" value="Comment" class="btn btn-success"  style="border-radius:0px">
        </div>
      </form>
</div>

@endsection
