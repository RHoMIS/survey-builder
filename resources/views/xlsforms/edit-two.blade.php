@extends('layouts.app')

@section('content')
    <div id="app">
        <b-card no-body class="border-dark border-top-0 rounded-top rounded-lg" header-bg-variant="dark">
            <template #header>
                <b-nav card-header tabs class="bg-dark rounded-top rounded-sm flex-nowrap">
                    <b-nav-item href="{{ url('xlsform/'.$xlsform->name.'/edit-one') }}">Stage 1 - Create</b-nav-item>
                    <b-nav-item active>Stage 2 - Build</b-nav-item>
                    <b-nav-item href="{{ url('xlsform/'.$xlsform->name.'/edit-three') }}">Stage 3 - Customise
                    </b-nav-item>
                    <b-nav-item href="{{ url('xlsform/'.$xlsform->name.'/edit-four') }}">Stage 4 - Review</b-nav-item>
                </b-nav>
            </template>
            <b-card-body>
                <form-builder-stage-two
                    :countries="{{$countries->toJson() }}"
                    :languages="{{ $languages->toJson() }}"
                    :projects="{{ $projects->toJson() }}"
                    :modules="{{ $modules->toJson() }}"
                    user-id="{{ Auth::id() }}"
                    :xlsform-original="{{ $xlsform->toJson() }}"
                    rhomis-app-url="{{ config('auth.rhomis_url') }}"
                >
                    <template v-slot:csrf>@csrf</template>
                </form-builder-stage-two>
            </b-card-body>
        </b-card>

        <form-key-details-view :xlsform="{{ $xlsform->toJson() }}"
                               rhomis-app-url="{{ config('auth.rhomis_url') }}"></form-key-details-view>

    </div>

@endsection

@section('after_scripts')
    <script src="{{ asset('js/xlsforms.js') }}"></script>
@endsection


@section('after_styles')
    {{--    <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/form.css') }}"/>--}}
@endsection
