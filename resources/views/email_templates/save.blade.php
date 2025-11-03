@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                @if($id=='')
                    <h3 class="text-themecolor">{{trans('lang.create_email_templates')}}</h3>
                @else
                    <h3 class="text-themecolor">{{trans('lang.edit_email_templates')}}</h3>
                @endif
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a
                                href="{{ url('email-templates') }}">{{trans('lang.email_templates')}}</a></li>
                    @if($id=='')
                        <li class="breadcrumb-item active">{{trans('lang.create_email_templates')}}</li>
                    @else
                        <li class="breadcrumb-item active">{{trans('lang.edit_email_templates')}}</li>
                    @endif
                </ol>
            </div>
        </div>
        <div>
            <div class="card-body">
                <div class="error_top" style="display:none"></div>
                <div class="success_top" style="display:none"></div>
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.notification')}}</legend>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.type')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control" id="type" readonly>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.subject')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control" id="subject">
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.message')}}</label>
                                <div class="col-7"><textarea class="form-control col-7" name="message" id="message"></textarea></div>
                            </div>
                            <div class="form-group row width-100">
                                <div class="form-check width-100">
                                    <input type="checkbox" id="is_send_to_admin">
                                    <label class="col-3 control-label"
                                           for="is_send_to_admin">{{trans('lang.is_send_to_admin')}}</label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary edit-setting-btn"><i class="fa fa-save"></i> {{
                trans('lang.save')}}
                </button>
                <a href="{{url('email-templates')}}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                trans('lang.cancel')}}</a>
            </div>
        </div>
        @endsection
        @section('scripts')
            <script>
                var requestId = "<?php echo $id; ?>";
                var id = requestId;
                $('#message').summernote({
                    height: 400,
                    width: 1000,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['forecolor', ['forecolor']],
                        ['backcolor', ['backcolor']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                    ]
                });
                $(document).ready(function () {
                    if (requestId != '') {
                        jQuery("#data-table_processing").show();
                        $.get('{{ route('email-templates.json', ['id'=>':id']) }}'.replace(':id', id), function (data) {
                            $("#subject").val(data.subject || '');
                            $('#message').summernote("code", data.message || '');
                            if (data.isSendToAdmin) { $("#is_send_to_admin").prop('checked', true); }
                            var type = '';
                            if (data.type == "new_order_placed") {
                                type = "{{trans('lang.new_order_placed')}}";
                            } else if (data.type == "new_vendor_signup") {
                                type = "{{trans('lang.new_vendor_signup')}}";
                            } else if (data.type == "payout_request") {
                                type = "{{trans('lang.payout_request')}}";
                            } else if (data.type == "payout_request_status") {
                                type = "{{trans('lang.payout_request_status')}}";
                            } else if (data.type == "wallet_topup") {
                                type = "{{trans('lang.wallet_topup')}}";
                            }
                            $('#type').val(type);
                        }).always(function(){ jQuery("#data-table_processing").hide(); });
                    }
                });
                $(".edit-setting-btn").click(async function () {
                    $(".success_top").hide();
                    $(".error_top").hide();
                    var subject = $("#subject").val();
                    var message = $('#message').summernote('code');
                    var isSendToAdmin = $("#is_send_to_admin").is(":checked");
                    if (subject == "") {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{trans('lang.please_enter_subject')}}</p>");
                        window.scrollTo(0, 0);
                        return false;
                    } else if (message == "") {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{trans('lang.please_enter_message')}}</p>");
                        window.scrollTo(0, 0);
                        return false;
                    } else {
                        var fd = new FormData();
                        fd.append('subject', subject);
                        fd.append('message', message);
                        fd.append('isSendToAdmin', isSendToAdmin ? 1 : 0);
                        $.ajax({ url: '{{ url('email-templates') }}' + '/' + id, method: 'POST', data: fd, processData: false, contentType: false, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                            .done(function(){ window.location.href = '{{ route('email-templates.index') }}'; })
                            .fail(function(xhr){ $(".error_top").show().html('<p>Failed ('+xhr.status+'): '+xhr.responseText+'</p>'); window.scrollTo(0,0); });
                    }
                });
            </script>
@endsection
