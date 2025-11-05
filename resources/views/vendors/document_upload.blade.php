@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.upload_document')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('vendors') !!}">{{trans('lang.vendor')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.upload_document')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card  pb-4">
            <div class="card-body">
                <div class="error_top" style="display:none;"></div>
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner doc-body">
                        <!-- Loading indicator -->
                        <div id="document-loading" style="text-align: center; padding: 40px;">
                            <i class="fa fa-spinner fa-spin" style="font-size: 48px; color: #007cff;"></i>
                            <p style="margin-top: 15px; color: #666;">Loading document details...</p>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary save-form-btn"><i class="fa fa-save"></i> {{ trans('lang.save')}}
                    </button>
                    <a href="{{url('vendors/document-list/' . $vendorId)}}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('scripts')
    <script>
        // ✅ SQL API VERSION - No Firebase!
        console.log('✅ Vendor Document Upload using SQL API');

        var docId = "{{$id}}";
        var id = "{{$vendorId}}";
        var placeholderImage = '{{ asset('images/placeholder.png') }}';
        var back_photo = '';
        var front_photo = '';
        var backFileName = '';
        var frontFileName = '';
        var backFileOld = '';
        var frontFileOld = '';
        var documentData = null;
        var documentVerification = null;
        var keydata = -1;
        var isAdd = true;

        $(document).ready(async function () {
            jQuery("#data-table_processing").show();

            // Load document upload data from SQL API
            await loadDocumentUploadData();
        });

        async function loadDocumentUploadData() {
            try {
                console.log('✅ Loading document upload data from SQL API');

                const response = await $.ajax({
                    url: '/api/vendors/document-upload-data/' + id + '/' + docId,
                    method: 'GET',
                    dataType: 'json'
                });

                if (!response.success || !response.data) {
                    console.error('❌ Failed to load document upload data');
                    jQuery("#data-table_processing").hide();
                    $('#document-loading').html('<div class="alert alert-danger">Failed to load document data: ' + (response.message || 'Unknown error') + '</div>');
                    return;
                }

                console.log('✅ Document upload data loaded:', response);

                documentData = response.data.document;
                documentVerification = response.data.documentVerification;
                keydata = response.data.keydata;
                isAdd = response.data.isAdd;

                if (!documentData || !documentData.enable) {
                    $('#document-loading').html('<div class="alert alert-danger">Document not found or disabled</div>');
                    jQuery("#data-table_processing").hide();
                    return;
                }

                // Build HTML form
                var html = '';
                html += '<fieldset><legend>' + (documentData.title || 'Document') + '</legend>';

                if (documentData.backSide) {
                    html += '<div class="form-group row width-50">';
                } else {
                    html += '<div class="form-group row width-100">';
                }

                if (documentData.frontSide) {
                    html += '<input type="hidden" name="frontSide" id="frontSide" value="true">';
                    front_photo = documentVerification && documentVerification.frontImage ? documentVerification.frontImage : '';
                    frontFileOld = front_photo;
                    html += '<label class="col-3 control-label">' + "{{trans('lang.front_image')}}" + '<span class="required-field"></span></label><div class="col-7"><input type="file" onChange="handleFrontFileSelect(event)" class="form-control image"><div class="placeholder_img_thumb front_image"><span class="image-item"><span class="remove-btn" id="front_image"><i class="fa fa-remove"></i></span><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:200px; height:auto" src="' + (front_photo || placeholderImage) + '" alt="image"></span></div><div id="uploding_image"></div></div></div>';
                }

                if (documentData.backSide) {
                    html += '<input type="hidden" name="backSide" id="backSide" value="true">';
                    back_photo = documentVerification && documentVerification.backImage ? documentVerification.backImage : '';
                    backFileOld = back_photo;
                    html += '<div class="form-group row width-50"><label class="col-3 control-label">' + "{{trans('lang.back_image')}}" + '<span class="required-field"></span></label><div class="col-7"><input type="file" onChange="handleBackFileSelect(event)" class="form-control image"><div class="placeholder_img_thumb back_image"><span class="image-item"><span class="remove-btn" id="back_image"><i class="fa fa-remove"></i></span><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:200px; height:auto" src="' + (back_photo || placeholderImage) + '" alt="image"></span></div><div id="uploding_image"></div></div></div>';
                }

                html += '<input type="hidden" name="docId" id="docId" value="' + documentData.id + '">';
                html += '<input type="hidden" name="keydata" id="keydata" value="' + keydata + '">';
                html += '<input type="hidden" name="isAdd" id="isAdd" value="' + (isAdd ? 'true' : 'false') + '">';
                html += '</fieldset>';

                $('#document-loading').hide();
                $(".doc-body").html(html);
                jQuery("#data-table_processing").hide();
            } catch (error) {
                console.error('❌ Error loading document upload data:', error);
                jQuery("#data-table_processing").hide();
                $('#document-loading').html('<div class="alert alert-danger">Error loading document data: ' + error.message + '</div>');
            }
        }

        function handleFrontFileSelect(evt) {
            var f = evt.target.files[0];
            var validExtensions = ['jpg', 'jpeg', 'png'];
            var fileExtension = f.name.split('.').pop().toLowerCase();
            if (validExtensions.indexOf(fileExtension) === -1) {
                alert("{{trans('lang.invalid_file_extension')}}");
                return;
            }
            var reader = new FileReader();
            reader.onload = (function (theFile) {
                return function (e) {
                    var filePayload = e.target.result;
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    front_photo = filePayload;
                    frontFileName = filename;
                    $(".front_image").empty();
                    $(".front_image").append('<span class="image-item"><span class="remove-btn" id="front_image"><i class="fa fa-remove"></i></span><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:200px; height:auto" src="' + filePayload + '" alt="image"></span>');
                };
            })(f);
            reader.readAsDataURL(f);
        }

        function handleBackFileSelect(evt) {
            var f = evt.target.files[0];
            var validExtensions = ['jpg', 'jpeg', 'png'];
            var fileExtension = f.name.split('.').pop().toLowerCase();
            if (validExtensions.indexOf(fileExtension) === -1) {
                alert("{{trans('lang.invalid_file_extension')}}");
                return;
            }
            var reader = new FileReader();
            reader.onload = (function (theFile) {
                return function (e) {
                    var filePayload = e.target.result;
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    back_photo = filePayload;
                    backFileName = filename;
                    $(".back_image").empty();
                    $(".back_image").append('<span class="image-item"><span class="remove-btn" id="back_image"><i class="fa fa-remove"></i></span><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:200px; height:auto" src="' + filePayload + '" alt="image"></span>');
                };
            })(f);
            reader.readAsDataURL(f);
        }

        $(document).on('click', '.save-form-btn', async function () {
            var docId = $("#docId").val();
            var backSide = $("#backSide").val();
            var frontSide = $("#frontSide").val();

            if (backSide && back_photo == "") {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.document_back_side_help')}}</p>");
                window.scrollTo(0, 0);
                return;
            }

            if (frontSide && front_photo == "") {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.document_front_side_help')}}</p>");
                window.scrollTo(0, 0);
                return;
            }

            jQuery("#data-table_processing").show();
            $(".error_top").hide();

            try {
                console.log('✅ Uploading vendor document via SQL API');

                // Prepare data
                var uploadData = {
                    _token: '{{ csrf_token() }}'
                };

                // Handle front image
                if (frontSide) {
                    if (front_photo && front_photo != frontFileOld) {
                        // New image uploaded (base64)
                        if (front_photo.startsWith('data:')) {
                            uploadData.frontImage = front_photo;
                        } else {
                            // Already a URL
                            uploadData.frontImageUrl = front_photo;
                        }
                    } else if (frontFileOld) {
                        // Keep old image
                        uploadData.frontImageUrl = frontFileOld;
                    }
                }

                // Handle back image
                if (backSide) {
                    if (back_photo && back_photo != backFileOld) {
                        // New image uploaded (base64)
                        if (back_photo.startsWith('data:')) {
                            uploadData.backImage = back_photo;
                        } else {
                            // Already a URL
                            uploadData.backImageUrl = back_photo;
                        }
                    } else if (backFileOld) {
                        // Keep old image
                        uploadData.backImageUrl = backFileOld;
                    }
                }

                const response = await $.ajax({
                    url: '/api/vendors/document-upload/' + id + '/' + docId,
                    method: 'POST',
                    dataType: 'json',
                    data: uploadData
                });

                if (response.success) {
                    console.log('✅ Document uploaded successfully');
                    window.location.href = "/vendors/document-list/" + id;
                } else {
                    console.error('❌ Failed to upload document:', response.message);
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + (response.message || 'Failed to upload document') + "</p>");
                    window.scrollTo(0, 0);
                    jQuery("#data-table_processing").hide();
                }
            } catch (error) {
                console.error('❌ Error uploading document:', error);
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>Error uploading document. Please try again.</p>");
                window.scrollTo(0, 0);
                jQuery("#data-table_processing").hide();
            }
        });

        $(document).on('click', '.remove-btn', function () {
            var currentId = $(this).attr('id')
            if (currentId == "back_image") {
                $(".back_image").empty();
                back_photo = '';
                backFileName = '';
            }
            if (currentId == "front_image") {
                $(".front_image").empty();
                front_photo = '';
                frontFileName = '';
            }
        });
    </script>
    @endsection
