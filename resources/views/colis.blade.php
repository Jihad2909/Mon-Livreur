@extends('app')

@section('content')
    <div class="container bg-white">
        <h1 class="mt-4">Colis</h1>
        <div class="row mt-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body"><h3>{{ $data['etat1'] }}</h3> En Attente</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('/etat=',[$id='1'])}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white mb-4">
                    <div class="card-body"><h3>{{ $data['etat2'] }}</h3> Accepter</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('/etat=',$id='2')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-dark mb-4">
                    <div class="card-body"><h3>{{ $data['etat3'] }}</h3> En cours</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-dark stretched-link" href="{{url('/etat=',$id='3')}}">View Details</a>
                        <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body"><h3>{{ $data['etat4'] }}</h3> Rejeté</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('/etat=',$id='4')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body"><h3>{{ $data['etat5'] }}</h3> Livrer</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('/etat=',$id='5')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-secondary text-white mb-4">
                    <div class="card-body"><h3>{{ $data['etat6'] }}</h3> etat 6</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('/etat=',$id='6')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
