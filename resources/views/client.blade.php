@extends('app')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        All Clients
    </div>
    <div class="card-body">
        <table id="example2" class="display table" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Adresse</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cl as $data)
                <tr>
                    <td>{{$data['id']}}</td>
                    <td>{{$data['name']}}</td>
                    <td>{{$data['email']}}</td>
                    <td>{{$data['phone']}}</td>
                    <td>{{$data['adresse']}}</td>
                    <td>
                        <button data-user-id="{{$data['id']}}" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                        </svg></button>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                        <input  class="form-check-input opencheck checkbox1" type="checkbox" value="{{$data['id']}}">
                        <input  type="hidden" value="{{$data['status']}}" class="status">
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Adresse</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>
    @include('clientInfoModal');
    @include('userConfirmationModal');
    @include('userUnconfirmationModal');

</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#example2').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5',
                'print',
            ]
        } );
    } );
    $(document).ready(function() {
        $('.table').on('click', '.btn-primary', function() {
            var userId = $(this).data('user-id');
            $.get('{{ route("users.show", ":id") }}'.replace(':id', userId), function(data) {
                $('#client-details-modal').find('.usertile').html(`
                <p>${data.name}</p>
                `);
                $('#client-details-modal').find('.card-text').html(`

                <p><strong>Email:</strong> ${data.email}</p>
                <hr class="bg-dark">
                <p><strong>Phone:</strong> ${data.phone}</p>
                <hr class="bg-dark">
                <p><strong>Location :</strong> ${data.place}</p>
                `);
                $('#client-details-modal').modal('show');
            });
        });
    });
    $(document).ready(function(){

        var check = document.querySelectorAll('.checkbox1');
        var checkmodal = document.querySelectorAll('.checkbox1');
        var status = document.querySelectorAll('.status');

        for (i = 0; i < status.length; ++i) {
            if(status[i].value == 1){
                check[i].checked = true;
            }else{
                check[i].checked = false;
            }
        }

        for (var checkbox of check){
            checkbox.addEventListener('click',function () {
                if (this.checked == true){
                    $(document).ready(function() {
                        $('#user-confirmation-modal').modal('show');
                    });
                    var iduser = this.value;
                    $('.check').on('click', function () {
                        $.get('{{ route("check.show", ":id") }}'.replace(':id', iduser), function (data) {
                        });
                    });
                }

                else {
                    $('#user-unconfirmation-modal').modal('show');
                    var iduser = this.value;
                    $('.uncheck').on('click', function() {
                        $.get('{{ route("uncheck.show", ":id") }}'.replace(':id', iduser), function(data) {
                        });
                    });
                }
            })
        }
    })


</script>
@endsection
