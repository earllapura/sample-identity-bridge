@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Authorization</div>

                <div class="card-body">
                    <form method="POST">
                        @csrf
                        <div class="box-header with-border">
                            <img alt="X" height="20" src="assets/img/icon.png" width="20">
                                Sign in with My.IIT
                        </div>
                        <div class="box-body">
                            <h2>
                                <a data-target="#clientDetailModal" data-toggle="modal" href="javascript:void(0)">
                                    {{ $application_name }}
                                </a>
                                wants to access your My.IIT account
                            </h2>
                            <p>
                                This will allow
                                <a data-target="#clientDetailModal" data-toggle="modal" href="javascript:void(0)">
                                    {{ $application_name }}
                                </a>
                                to:
                            </p>
                            <ul>
                                @forelse ($scopes as $key => $scope)
                                <li>
                                    {{ $key }}
                                    <a class="pull-right" data-scope="{{ $scope }}" data-target="#scopeDetailModal" data-toggle="modal" href="javascript:void(0)" role="button">
                                        <i class="fa fa-info-circle">
                                        </i>
                                    </a>
                                </li>
                                @empty
                                <li>
                                    Access all your data.
                                </li>
                                @endforelse
                            </ul>
                            <h3>
                                Allow {{ $application_name }} to do this?
                            </h3>
                            <p>
                                By clicking Allow, you allow this app to use your information in accordance to their terms of service and privacy policies.
                            </p>
                            <input type="hidden" name="client_id" value="{{ $client_id }}">
                            <input type="hidden" name="response_type" value="{{ $response_type }}">
                            <input type="hidden" name="scope" value="{{ $scope }}">
                            <input class="btn btn-primary btn-flat pull-right" name="authorized" type="submit" value="Allow"/>
                            <a href="/" class="btn btn-danger btn-flat pull-right">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
