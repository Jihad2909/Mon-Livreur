@extends('app')

@section('body')

<div class="" style="position: relative;">
        <div class="col-sm-2 bg-dark" style="position: fixed;height: 700px; top: 0px">
            <ul class="nav flex-column">
                <li class="nav-item mt-2 text-white">
                    <p style="text-align: center"><img class="bg-white" src="https://fonts.gstatic.com/s/i/materialicons/account_circle/v20/24px.svg">&nbsp&nbspAdmin</p>
                </li>
                <li class="nav-item mt-2 text-white">
                    <hr class="bg-white">
                </li>
                <li class="nav-item mt-2 text-white">
                    <p><img style="width: 30px;height: 30px" class="rounded-circle shadow-4-strong" alt="avatar2" src="https://mdbcdn.b-cdn.net/img/new/avatars/1.webp" >&nbsp&nbspJihad Mazrani</p>
                </li>
                <li class="nav-item mt-2 text-white">
                    <hr class="bg-white">
                </li>
                <li class="nav-item py-2 text-white">
                    <i class="fas fa-database fa-fw"></i>&nbsp&nbsp
                    <a id="colis" class="nav-menu text-white" >Dashboard</a>
                </li>
                <li class="nav-item text-white pt-3">
                    <i class="fas fa-users fa-fw"></i>&nbsp&nbsp
                    <a class="nav-menu text-white" data-toggle="collapse" href="#submenu1" >Users
                        <i class="fas fa-angle-down pl-5"></i>
                    </a>
                    <div id="submenu1" class="collapse">
                        <ul class="nav flex-column">
                            <li class="nav-link">
                                <a id="client" href="{{url('/client')}}" class="nav-link text-white-50" >Clients</a>
                            </li>
                            <li class="nav-link">
                                <a id="livreur" href="{{url('/livreur')}}" class="nav-link text-white-50">Livreurs</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    <div style="position: absolute;top: 15px;left: 250px; width: 80% ">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-light ">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3 text-dark" href="">Home</a>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="#!">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <main id="mydiv" class="mt-5">
        </main>

    </div>

    </div>
@endsection
@section('script')
    <script type="text/javascript">
       /* $(document).ready(function() {
            $('#client').on('click',function() {
                $('#mydiv').load('/client');
            });
        });
        $(document).ready(function() {
            $('#livreur').on('click',function() {
                $('#mydiv').load('/livreur');
            });
        });
        $(document).ready(function() {
            $('#colis').on('click',function() {
                $('#mydiv').load('/colisinfo');
            });
        });*/
        $(document).ready(function() {
                $('#mydiv').load('/colisinfo');
        });
    </script>
@endsection
