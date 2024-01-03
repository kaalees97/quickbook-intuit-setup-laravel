@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Customer List</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered">
                                        <thead>
                                            <tr>
                                            <th class="size-font">S.no</th>
                                            <th>Name</th>
                                            <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="overall-size">
                                            @php
                                            $sno = 0;
                                            @endphp
                                            @foreach ($customer_details as $data_customer_lists)
                                                <tr>
                                                    <td>{{ ++$sno }}</td>

                                                    <td class="coin-name" data-th="Coin Name">
                                                        <span class="bt-content">
                                                            {{ $data_customer_lists->GivenName }}
                                                        </span>
                                                    </td>
                                        
                                                    <td data-th="Balance">
                                                        <a href="{{ route('editcustomerform',$data_customer_lists->Id) }}" class="bt-content">Edit</a>
                                                        <a href="{{ route('deletecust',$data_customer_lists->Id) }}" class="bt-content">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
