@extends('layouts.app')
@section('content')
<div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor restaurantTitle">{{trans('lang.vendor_document_details')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('vendors') !!}">{{trans('lang.vendor')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.vendor_document_details')}}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                                <li class="nav-item">
                                    <a class="nav-link active vendor-name"
                                       href="{!! url()->current() !!}">{{trans('lang.vendor_document_details')}}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10 doc-body"></div>
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document" style="max-width: 50%;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close"
                                                    data-dismiss="modal"
                                                    aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <embed id="docImage"
                                                       src=""
                                                       frameBorder="0"
                                                       scrolling="auto"
                                                       height="100%"
                                                       width="100%"
                                                       style="height: 540px;"
                                                ></embed>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">{{trans('lang.close')}}</button>
                                            </div>
                                        </div>
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
@section('scripts')
<script>
    // ✅ SQL API VERSION - No Firebase!
    console.log('✅ Vendor Document List using SQL API');

    var id = "<?php echo $id;?>";
    var fcmToken = "";
    var vendorData = null;
    var documentsData = [];
    var documentVerifications = [];

    $(document).ready(async function () {
        jQuery("#data-table_processing").show();

        // Modal for viewing document images
        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var img = button.data('image');
            var modal = $(this);
            modal.find('#docImage').attr('src', img);
        });

        // Load vendor document data from SQL API
        await loadVendorDocumentData();
    });

    async function loadVendorDocumentData() {
        try {
            console.log('✅ Loading vendor document data from SQL API');

            const response = await $.ajax({
                url: '/api/vendors/document-data/' + id,
                method: 'GET',
                dataType: 'json'
            });

            if (!response.success || !response.data) {
                console.error('❌ Failed to load vendor document data');
                jQuery("#data-table_processing").hide();
                $(".doc-body").html('<div class="alert alert-danger">Failed to load vendor document data</div>');
                return;
            }

            console.log('✅ Vendor document data loaded:', response);

            vendorData = response.data.vendor;
            documentsData = response.data.documents || [];
            documentVerifications = response.data.documentVerifications || [];

            // Set vendor name
            if (vendorData) {
                fcmToken = vendorData.fcmToken || '';
                $(".vendor-name").text(vendorData.firstName + ' ' + vendorData.lastName + "'s " + "{{trans('lang.vendor_document_details')}}");
            }

            // Build HTML table
            var html = '';
            html += '<table id="taxTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">';
            html += "<thead>";
            html += '<tr>';
            html += '<th>Name</th>';
            html += '<th>Status</th>';
            html += '<th>Action</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            // Process each document
            if (documentsData.length > 0) {
                documentsData.forEach(function(doc) {
                    // Find document verification for this document
                    var docVerification = documentVerifications.find(function(dv) {
                        return dv.documentId == doc.id;
                    });

                    var trhtml = '';
                    trhtml += '<tr>';

                    // Document name with image links
                    var docName = doc.title || '';
                    var imageLinks = '';

                    if (docVerification) {
                        var hasFront = docVerification.frontImage && docVerification.frontImage != '' && doc.frontSide;
                        var hasBack = docVerification.backImage && docVerification.backImage != '' && doc.backSide;

                        if (hasFront && hasBack) {
                            imageLinks = '&nbsp;&nbsp;<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="' + docVerification.frontImage + '" data-id="front" class="open-image">{{trans("lang.view_front_image")}}</a>&nbsp;<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="' + docVerification.backImage + '" data-id="back" class="open-image">{{trans("lang.view_back_image")}}</a>';
                        } else if (hasBack) {
                            imageLinks = '&nbsp;<a href="#" data-toggle="modal" class="badge badge-info" data-target="#exampleModal" data-id="back" data-image="' + docVerification.backImage + '" class="open-image">{{trans("lang.view_back_image")}}</a>';
                        } else if (hasFront) {
                            imageLinks = '&nbsp;<a href="#" data-toggle="modal" class="badge badge-info" data-target="#exampleModal" data-id="front" class="open-image" data-image="' + docVerification.frontImage + '">{{trans("lang.view_front_image")}}</a>';
                        }
                    }

                    trhtml += '<td>' + docName + imageLinks + '</td>';

                    // Status badge
                    var status = 'pending';
                    if (docVerification) {
                        status = docVerification.status || 'pending';
                    }

                    var display_status = '';
                    if (status == "approved") {
                        display_status = '<span class="badge badge-success py-2 px-3">' + status + '</span>';
                    } else if (status == "rejected") {
                        display_status = '<span class="badge badge-danger py-2 px-3">' + status + '</span>';
                    } else if (status == "uploaded") {
                        display_status = '<span class="badge badge-primary py-2 px-3">' + status + '</span>';
                    } else {
                        display_status = '<span class="badge badge-warning py-2 px-3">' + status + '</span>';
                    }
                    trhtml += '<td>' + display_status + '</td>';

                    // Action buttons
                    trhtml += '<td class="action-btn">';
                    trhtml += '<a href="/vendors/document/upload/' + id.trim() + '/' + doc.id.trim() + '" data-id="' + doc.id + '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>&nbsp;';

                    if (status !== 'pending') {
                        if (status == "rejected") {
                            trhtml += '&nbsp;<a href="javascript:void(0);" class="btn btn-sm btn-success direct-click-btn" id="approve-doc" data-title="' + doc.title + '" data-id="' + doc.id + '">{{trans("lang.approve")}}</a>';
                        } else if (status == "approved") {
                            trhtml += '&nbsp;<a href="javascript:void(0);" class="btn btn-sm btn-danger direct-click-btn" id="disapprove-doc" data-title="' + doc.title + '" data-id="' + doc.id + '">{{trans("lang.reject")}}</a>';
                        } else {
                            trhtml += '&nbsp;<a href="javascript:void(0);" class="btn btn-sm btn-success direct-click-btn" id="approve-doc" data-title="' + doc.title + '" data-id="' + doc.id + '">{{trans("lang.approve")}}</a>&nbsp;<a href="javascript:void(0);" class="btn btn-sm btn-danger direct-click-btn" id="disapprove-doc" data-title="' + doc.title + '" data-id="' + doc.id + '">{{trans("lang.reject")}}</a>';
                        }
                    }
                    trhtml += '</td>';
                    trhtml += '</tr>';

                    html += trhtml;
                });
            }

            html += '</tbody>';
            html += '</table>';

            $(".doc-body").html(html);

            // Initialize DataTable
            if (documentsData.length > 0) {
                $('#taxTable').DataTable({
                    order: [[0, 'asc']],
                    columnDefs: [
                        {orderable: false, targets: [1, 2]}
                    ],
                });
            }

            jQuery("#data-table_processing").hide();
        } catch (error) {
            console.error('❌ Error loading vendor document data:', error);
            jQuery("#data-table_processing").hide();
            $(".doc-body").html('<div class="alert alert-danger">Error loading vendor document data: ' + error.message + '</div>');
        }
    }

    // Handle approve/reject document clicks
    $(document).on('click', '.direct-click-btn', async function () {
        jQuery("#data-table_processing").show();

        var status = $(this).attr('id') == "approve-doc" ? "approved" : "rejected";
        var docId = $(this).attr('data-id');
        var docTitle = $(this).attr('data-title');

        try {
            console.log('✅ Updating document status via SQL API:', status, docId);

            const response = await $.ajax({
                url: '/api/vendors/document-status/' + id + '/' + docId,
                method: 'POST',
                dataType: 'json',
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'
                }
            });

            if (response.success) {
                console.log('✅ Document status updated successfully');

                // Reload page to show updated status
                window.location.reload();
            } else {
                console.error('❌ Failed to update document status:', response.message);
                alert('Failed to update document status: ' + (response.message || 'Unknown error'));
                jQuery("#data-table_processing").hide();
            }
        } catch (error) {
            console.error('❌ Error updating document status:', error);
            alert('Error updating document status. Please try again.');
            jQuery("#data-table_processing").hide();
        }
    });

    $(document.body).on('click', '.redirecttopage', function () {
        var url = $(this).attr('data-url');
        window.location.href = url;
    });
</script>
@endsection
