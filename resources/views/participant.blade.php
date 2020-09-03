@extends('flyerless-club-management::layouts.app')

@section('title', 'Flyerless Club Management')

@section('module-content')
    <div id="p-main">
        <div id="p-title"> Flyerless Club Management</div>
        <div id="p-description"> From here you can manage your club details that are used on the flyerless system </div>
    </div>

    <club-management-form
        :can-update="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('flyerless-club-management.club.update')?'true':'false')}}"
        query-string="{{url()->getAuthQueryString()}}"
    >

    </club-management-form>

@endsection

