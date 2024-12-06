@extends('app')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Colis
    </div>
    <div class="card-body">
        <table id="example" class="display" style="width:100%">
            <thead>
            <tr>
                <th>ID coli</th>
                <th>Description</th>
                <th>Reference</th>
                <th>Publier par</th>
                <th>Livrer par</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $d)
                <tr>
                    <td>{{$d['id']}}</td>
                    <td>{{$d['description']}}</td>
                    <td>{{$d['reference']}}</td>
                    <td><b>nom:</b> {{$d['namecl']}} | <b>email:</b> {{$d['emailcl']}}</td>
                    <td><b>nom:</b> {{$d['nameliv']}} | <b>email:</b> {{$d['emailliv']}}</td>
                    <td>
                        <svg id="eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                        </svg>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>ID coli</th>
                <th>Description</th>
                <th>Reference</th>
                <th>Publier par</th>
                <th>Livrer par</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable( {
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
</script>
@endsection
