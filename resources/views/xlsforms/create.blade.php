@extends('layouts.app')

@section('content')
    <h2>RHoMIS XLS Form Builder</h2>
    <div class="card border border-dark">
        <div class="card-header bg-dark text-light"><h3>Survey Builder</h3></div>
        <div class="card-body">
            This tool will help you build your own, customised RHoMIS ODK Survey form. The pages below will guide you
            through each stage in the process. Please read each instruction carefully.<br/><br/>
            The Process is divided into 4 stages:
            <ol>
                <li>First, you give some basic information about your survey project.</li>
                <li>Second, you get to build the survey using the complete set of RHoMIS Core and Optional Modules.</li>
                <li>Next, you can customise the questions and available responses to your specific context.</li>
                <li>Finally, you can review the complete form and decide on the next steps.</li>
            </ol>

            Your progress is saved after each stage, and you may go back to change any entry until you mark the form as
            'finalised'. At that point, the form is locked and ready for live data collection.<br/>

        </div>
    </div>
    <div id="app">
        <b-card no-body class="border-dark border-top-0 rounded-top rounded-lg">
            <b-tabs card nav-class="bg-dark rounded-top rounded-sm flex-nowrap" nav-wrapper-class="px-2" lazy>
                <b-tab title="Stage 1 - Create" active>
                    <form-builder-stage-one
                        :countries="{{$countries->toJson() }}"
                        :languages="{{ $languages->toJson() }}"
                        :projects-start="{{ $projects->toJson() }}"
                        :modules="{{ $modules->toJson() }}"
                        :themes="{{ $themes->toJson() }}"
                        user-id="{{ Auth::id() }}"
                    >
                        <template v-slot:csrf>@csrf</template>
                    </form-builder-stage-one>
                </b-tab>
                <b-tab title="Stage 2 - Build" lazy>
                    <b-card class="secondary">Please save your form before continuing</b-card>
                </b-tab>
                <b-tab title="Stage 3 - Customise" lazy>
                    <b-card class="secondary">Please save your form before continuing</b-card>
                </b-tab>
                <b-tab title="Stage 4 - Review" lazy>
                    <b-card class="secondary">Please save your form before continuing</b-card>
                </b-tab>
            </b-tabs>
        </b-card>
    </div>

@endsection

@section('after_scripts')
    <script src="{{ asset('js/xlsforms.js') }}"></script>
@endsection


@section('after_styles')
    <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/form.css') }}"/>
@endsection
