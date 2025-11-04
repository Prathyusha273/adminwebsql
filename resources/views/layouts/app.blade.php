<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?> dir="rtl" <?php } ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <title>{{ config('app.name', 'Laravel') }}</title> -->
    <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
    <link rel="icon" id="favicon" type="image/x-icon"
          href="<?php echo str_replace('images/', 'images%2F', @$_COOKIE['favicon']); ?>">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Styles -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap-rtl.min.css')}}" rel="stylesheet">
    <?php } ?>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
    <link href="{{asset('css/style_rtl.css')}}" rel="stylesheet">
    <?php } ?>
    <link href="{{ asset('css/icons/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet">
    <link href="{{ asset('css/colors/blue.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chosen.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet">

    <link href="{{ asset('assets/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <!-- jQuery - Load early to avoid $ not defined errors -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Global Activity Logger - Load after jQuery -->
    <script src="{{ asset('js/global-activity-logger.js') }}"></script>

    <!--
    ========================================
    FIREBASE COMPLETELY DISABLED
    ========================================
    All Firebase code has been commented out.
    The application now uses MySQL exclusively.
    ========================================
    -->

    <!--
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-storage-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-database-compat.js"></script>

    <script>
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_APIKEY', 'AIzaSyAf_lICoxPh8qKE1QnVkmQYTFJXKkYmRXU') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN', 'jippymart-27c08.firebaseapp.com') }}",
            databaseURL: "{{ env('FIREBASE_DATABASE_URL', 'https://jippymart-27c08-default-rtdb.firebaseio.com') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID', 'jippymart-27c08') }}",
            storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET', 'jippymart-27c08.firebasestorage.app') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAAGING_SENDER_ID', '592427852800') }}",
            appId: "{{ env('FIREBASE_APP_ID', '1:592427852800:web:f74df8ceb2a4b597d1a4e5') }}",
            measurementId: "{{ env('FIREBASE_MEASUREMENT_ID', 'G-ZYBQYPZWCF') }}"
        };

        if (!firebase.apps.length) {
            try {
                firebase.initializeApp(firebaseConfig);
                console.log('✅ Firebase initialized successfully');
                window.database = firebase.firestore();
                window.storage = firebase.storage();
                console.log('✅ Firebase services initialized (Auth disabled temporarily)');
            } catch (error) {
                console.error('❌ Firebase initialization error:', error);
            }
        } else {
            console.log('✅ Firebase already initialized');
            window.database = firebase.firestore();
            window.storage = firebase.storage();
        }
    </script>
    -->

    <!-- Enhanced Notification Bell Styles -->
    <style>
        .nav-item .fa-bell {
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-item .fa-bell:hover {
            color: #ff6849 !important;
            transform: scale(1.1);
        }

        .badge-counter {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .toast-notification {
            z-index: 9999;
        }

        /* Enhanced Notification Container */
        #notification-container {
            position: relative;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-bell:hover {
            color: #ff6849 !important;
        }

        /* Custom Tooltip Styles */
        .notification-tooltip-content {
            position: absolute;
            top: 100%;
            right: 0;
            width: 300px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            margin-top: 5px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .notification-tooltip-content.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .notification-tooltip-content::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 15px;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid white;
        }

        .tooltip-header {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            color: #333;
        }

        .tooltip-header i {
            margin-right: 8px;
            color: #ff6849;
        }

        .tooltip-body {
            max-height: 200px;
            overflow-y: auto;
            padding: 0;
        }

        .recent-order-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .recent-order-item:hover {
            background-color: #f8f9fa;
        }

        .recent-order-item:last-child {
            border-bottom: none;
        }

        .order-id {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .order-customer {
            color: #666;
            font-size: 12px;
            margin-top: 2px;
        }

        .order-time {
            color: #999;
            font-size: 11px;
            margin-top: 2px;
        }

        .no-orders {
            padding: 20px 15px;
            text-align: center;
            color: #999;
            font-style: italic;
        }

        /* Sound notification indicator */
        .sound-indicator {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            animation: soundPulse 1s infinite;
        }

        @keyframes soundPulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Notification sound controls */
        .sound-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: none;
        }

        .sound-controls.show {
            display: block;
        }

        .sound-toggle {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .sound-toggle:hover {
            background: #f0f0f0;
            color: #333;
        }

        .sound-toggle.muted {
            color: #dc3545;
        }
    </style>

    <?php if (isset($_COOKIE['admin_panel_color'])) { ?>

    <style type="text/css">


        .sidebar-nav ul li a {
            border-bottom: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .sidebar-nav ul li a:hover i {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .vendor_payout_create-inner fieldset legend {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .restaurant_payout_create-inner fieldset legend {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        a {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        a:hover, a:focus {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        a.link:hover, a.link:focus {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        html body blockquote {
            border-left: 5px solid<?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .text-warning {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>  !important;
        }

        .text-info {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>  !important;
        }

        .sidebar-nav ul li a:hover {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .btn-primary {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            border: 1px solid<?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .sidebar-nav > ul > li.active > a,.sidebar-nav ul li ul li.active a,.sidebar-nav ul li ul li a:hover {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            border-right: 3px solid<?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .sidebar-nav > ul > li.active > a i {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .bg-info {
            background-color: <?php    echo $_COOKIE['admin_panel_color']; ?>  !important;
        }

        .bellow-text ul li > span {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>


        }

        .table tr td.redirecttopage {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>


        }

        ul.rating {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .nav-tabs.card-header-tabs .nav-link.active, .nav-tabs.card-header-tabs .nav-link:hover {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            border-color: <?php    echo $_COOKIE['admin_panel_color']; ?> <?php    echo $_COOKIE['admin_panel_color']; ?> #fff;
        }

        .btn-warning, .btn-warning.disabled {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            border: 1px solid<?php    echo $_COOKIE['admin_panel_color']; ?>;
            box-shadow: none;
        }

        .payment-top-tab .nav-tabs.card-header-tabs .nav-link.active, .payment-top-tab .nav-tabs.card-header-tabs .nav-link:hover {
            border-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .nav-tabs.card-header-tabs .nav-link span.badge-success {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .nav-tabs.card-header-tabs .nav-link.active span.badge-success, .nav-tabs.card-header-tabs .nav-link:hover span.badge-success, .sidebar-nav ul li a.active, .sidebar-nav ul li a.active:hover, .sidebar-nav ul li.active a.has-arrow:hover, .topbar ul.dropdown-user li a:hover {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .sidebar-nav ul li a.has-arrow:hover::after, .sidebar-nav .active > .has-arrow::after, .sidebar-nav li > .has-arrow.active::after, .sidebar-nav .has-arrow[aria-expanded="true"]::after, .sidebar-nav ul li a:hover {
            border-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        [type="checkbox"]:checked + label::before {
            border-right: 2px solid <?php    echo $_COOKIE['admin_panel_color']; ?>;
            border-bottom: 2px solid <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }
        .edit-form-group .form-check [type="checkbox"]:checked + label::after{border-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;background: <?php    echo $_COOKIE['admin_panel_color']; ?>}

        .btn-primary:hover, .btn-primary.disabled:hover {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            border: 1px solid<?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .btn-primary.active, .btn-primary:active, .btn-primary:focus, .btn-primary.disabled.active, .btn-primary.disabled:active, .btn-primary.disabled:focus, .btn-primary.active.focus, .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary.focus:active, .btn-primary:active:focus, .btn-primary:active:hover, .open > .dropdown-toggle.btn-primary.focus, .open > .dropdown-toggle.btn-primary:focus, .open > .dropdown-toggle.btn-primary:hover, .btn-primary.focus, .btn-primary:focus, .btn-primary:not(:disabled):not(.disabled).active:focus, .btn-primary:not(:disabled):not(.disabled):active:focus, .show > .btn-primary.dropdown-toggle:focus, .btn-warning:hover, .btn-warning:hover, .btn-warning.disabled:hover, .btn-warning.active.focus, .btn-warning.active:focus, .btn-warning.active:hover, .btn-warning.focus:active, .btn-warning:active:focus, .btn-warning:active:hover, .open > .dropdown-toggle.btn-warning.focus, .open > .dropdown-toggle.btn-warning:focus, .open > .dropdown-toggle.btn-warning:hover, .btn-warning.focus, .btn-warning:focus {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            border-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            box-shadow: 0 0 0 0.2rem<?php    echo $_COOKIE['admin_panel_color']; ?>;
            color: #fff;
        }

        .pagination > li > a.page-link:hover {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .mini-sidebar .sidebar-nav #sidebarnav > li:hover a i, .mini-sidebar .sidebar-nav ul li a, .sidebar-nav ul li a.active i, .sidebar-nav ul li a.active:hover i, .sidebar-nav ul li.active a:hover i {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .cat-slider .cat-item a.cat-link:hover, .cat-slider .cat-item.section-selected a.cat-link {
            border-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .cat-slider .cat-item a.cat-link {
            border-bottom-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .cat-slider .cat-item.section-selected a.cat-link:after {
            border-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .cat-slider {
            border-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .business-analytics .card-box i {
            background: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .order-status .data i, .order-status span.count {
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        .print-btn button {
            border-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
            color: <?php    echo $_COOKIE['admin_panel_color']; ?>;
        }

        [type="radio"]:checked + label::after, [type="radio"].with-gap:checked + label::after {background-color: <?php    echo $_COOKIE['admin_panel_color']; ?>;}
        [type="radio"]:checked + label::after, [type="radio"].with-gap:checked + label::before, [type="radio"].with-gap:checked + label::after {border: 2px solid <?php    echo $_COOKIE['admin_panel_color']; ?>;}

        .card-header-tab ul.nav-tab li.active a,.card-header-tab ul.nav-tab li a:hover{background: <?php echo $_COOKIE['admin_panel_color']; ?>;}
        .edit-form-group .form-check [type="radio"]:checked + label::before,.edit-form-group .form-check [type="checkbox"]:checked + label::after {background: <?php echo $_COOKIE['admin_panel_color']; ?>; border-color: <?php echo $_COOKIE['admin_panel_color']; ?>;}
        .pricing-card-btm .btn:hover{background: <?php echo $_COOKIE['admin_panel_color']; ?>;border-color: <?php echo $_COOKIE['admin_panel_color']; ?>;}
        @media screen and ( max-width: 767px ) {

            .mini-sidebar .sidebar-nav ul li a:hover, .sidebar-nav > ul > li.active > a {
                color: <?php    echo $_COOKIE['admin_panel_color']; ?>  !important;
            }

            .mini-sidebar .sidebar-nav #sidebarnav > li:hover a i, .mini-sidebar .sidebar-nav ul li a, .sidebar-nav ul li a.active i, .sidebar-nav ul li a.active:hover i, .sidebar-nav ul li.active a:hover i {
                color: #fff;
            }

            .sidebar-nav > ul > li.active > a, .sidebar-nav > ul > li.active > a i, .sidebar-nav > ul > li > a:hover i {
                color: <?php    echo $_COOKIE['admin_panel_color']; ?>  !important;
            }
        }
    </style>
    <?php } ?>

</head>
<body>

<div id="app" class="fix-header fix-sidebar card-no-border">
    <div id="main-wrapper">
        <div id="data-table_processing" class="page-overlay" style="display:none;">
            <div class="overlay-text">
                <img src="{{asset('images/spinner.gif')}}">
            </div>
        </div>
        <header class="topbar">

            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                @include('layouts.header')
            </nav>

        </header>
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                @include('layouts.menu')
            </div>
            <!-- End Sidebar scroll-->
        </aside>
    </div>
    <main class="py-4">
        @yield('content')
    </main>
</div>

<!-- Sound Controls -->
<div class="sound-controls" id="sound-controls">
    <button class="sound-toggle" id="sound-toggle" title="Toggle Sound Notifications">
        <i class="fa fa-volume-up"></i>
    </button>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-editable/0.7.3/leaflet.editable.min.js"></script>
<script src="https://unpkg.com/leaflet-draw@0.4.14/dist/leaflet.draw-src.js"></script>
<script src="https://unpkg.com/leaflet-geojson-layer/src/leaflet.geojson.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
<!-- jQuery already loaded in head section -->

<script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('js/waves.js') }}"></script>
<script src="{{ asset('js/sidebarmenu.js') }}"></script>
<script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('assets/plugins/summernote/summernote-bs4.js')}}"></script>

<script src="{{ asset('js/jquery.resizeImg.js') }}"></script>
<script src="{{ asset('js/mobileBUGFix.mini.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

<script type="text/javascript">
    jQuery(window).scroll(function () {
        var scroll = jQuery(window).scrollTop();
        if (scroll <= 60) {
            jQuery("body").removeClass("sticky");
        } else {
            jQuery("body").addClass("sticky");
        }
    });

</script>
<script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<!-- Firebase 9.0.0 will be loaded in individual pages to avoid conflicts -->
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('js/chosen.jquery.js') }}"></script>
<script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript"
        src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.24/jspdf.plugin.autotable.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="{{ asset('js/jquery.masking.js') }}"></script>
<script src="{{ asset('assets/plugins/toast-master/js/jquery.toast.js') }}"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script type="text/javascript">

    var database = firebase.firestore();
    var geoFirestore = new GeoFirestore(database);
    var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());
    var createdAt = {_nanoseconds: createdAtman.nanoseconds, _seconds: createdAtman.seconds};

    var ref = database.collection('settings').doc("globalSettings");
    ref.get().then(async function (snapshots) {
        try {
            var globalSettings = snapshots.data();
            $("#logo_web").attr('src', globalSettings.appLogo);

            if (getCookie('meta_title') == undefined || getCookie('meta_title') == null || getCookie('meta_title') == "") {
                document.title = globalSettings.meta_title;

                setCookie('meta_title', globalSettings.meta_title, 365);
            }

        } catch (error) {

        }
    });
    var refDistance = database.collection('settings').doc("RestaurantNearBy");
    refDistance.get().then(async function (snapshots) {
        try {
            var data = snapshots.data();
            var distanceType=data.distanceType.charAt(0).toUpperCase() + data.distanceType.slice(1);
            $('#distanceType').val(distanceType);
            $('.global_distance_type').html(distanceType);

        } catch (error) {

        }
    });

    var langcount = 0;
    var languages_list = database.collection('settings').doc('languages');
    languages_list.get().then(async function (snapshotslang) {
        snapshotslang = snapshotslang.data();
        if (snapshotslang != undefined) {
            snapshotslang = snapshotslang.list;
            languages_list_main = snapshotslang;
            snapshotslang.forEach((data) => {
                if (data.isActive == true) {
                    langcount++;
                    $('#language_dropdown').append($("<option></option>").attr("value", data.slug).text(data.title));
                }
            });
            if (langcount > 1) {
                $("#language_dropdown_box").css('visibility', 'visible');
            }
            <?php if (session()->get('locale')) { ?>
            $("#language_dropdown").val("<?php    echo session()->get('locale'); ?>");
            <?php } ?>

        }
    });

    var url = "{{ route('changeLang') }}";

    $(".changeLang").change(function () {
        var slug = $(this).val();
        languages_list_main.forEach((data) => {
            if (slug == data.slug) {
                if (data.is_rtl == undefined) {
                    setCookie('is_rtl', 'false', 365);
                } else {
                    setCookie('is_rtl', data.is_rtl.toString(), 365);
                }
                window.location.href = url + "?lang=" + slug;
            }
        });
    });

    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    var version = database.collection('settings').doc("Version");
    version.get().then(async function (snapshots) {
        var version_data = snapshots.data();
        if (version_data == undefined) {
            database.collection('settings').doc('Version').set({});
        }
        try {
            $('.web_version').html("V:" + version_data.web_version);
        } catch (error) {
        }
    });

    // Email sending function disabled - email notifications removed
    async function sendEmail(url, subject, message, recipients) {
        console.log('Email sending disabled - notifications removed');
        return false;
    }

    // Original sendEmail function code removed - email notifications disabled
    /*
    async function sendEmail(url, subject, message, recipients) {
        await $.ajax({
            type: 'POST',
            data: {
                subject: subject,
                message: message,
                recipients: recipients
            },
            url: url,
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                checkFlag = true;
            },
            error: function (xhr, status, error) {
                checkFlag = true;
            }
        });
        return checkFlag;
    }
    */
    function exportData(dt, format, config) {
        const {
            columns,
            fileName = 'Export',
        } = config;

        const filteredRecords = dt.ajax.json().filteredData;

        const fieldTypes = {};
        const dataMapper = (record) => {
            return columns.map((col) => {
                const value = record[col.key];
                if (!fieldTypes[col.key]) {
                    if (value === true || value === false) {
                        fieldTypes[col.key] = 'boolean';
                    } else if (value && typeof value === 'object' && value.seconds) {
                        fieldTypes[col.key] = 'date';
                    } else if (typeof value === 'number') {
                        fieldTypes[col.key] = 'number';
                    } else if (typeof value === 'string') {
                        fieldTypes[col.key] = 'string';
                    } else {
                        fieldTypes[col.key] = 'string';
                    }
                }

                switch (fieldTypes[col.key]) {
                    case 'boolean':
                        return value ? 'Yes' : 'No';
                    case 'date':
                        return value ? new Date(value.seconds * 1000).toLocaleString() : '-';
                    case 'number':
                        return typeof value === 'number' ? value : 0;
                    case 'string':
                    default:
                        return value || '-';
                }
            });
        };

        const tableData = filteredRecords.map(dataMapper);

        const data = [columns.map(col => col.header), ...tableData];

        const columnWidths = columns.map((_, colIndex) =>
            Math.max(...data.map(row => row[colIndex]?.toString().length || 0))
        );

        if (format === 'csv') {
            const csv = data.map(row => row.map(cell => {
                if (typeof cell === 'string' && (cell.includes(',') || cell.includes('\n') || cell.includes('"'))) {
                    return `"${cell.replace(/"/g, '""')}"`;
                }
                return cell;
            }).join(',')).join('\n');

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            saveAs(blob, `${fileName}.csv`);
        } else if (format === 'excel') {
            const ws = XLSX.utils.aoa_to_sheet(data, { cellDates: true });

            ws['!cols'] = columnWidths.map(width => ({ wch: Math.min(width + 5, 30) }));

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Data');
            XLSX.writeFile(wb, `${fileName}.xlsx`);
        } else if (format === 'pdf') {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const totalLength = columnWidths.reduce((sum, length) => sum + length, 0);
            const columnStyles = {};
            columnWidths.forEach((length, index) => {
                columnStyles[index] = {
                    cellWidth: (length / totalLength) * 180,
                };
            });

            doc.setFontSize(16);
            doc.text(fileName, 14, 16);

            doc.autoTable({
                head: [columns.map(col => col.header)],
                body: tableData,
                startY: 20,
                theme: 'striped',
                styles: {
                    cellPadding: 2,
                    fontSize: 10,
                },
                columnStyles,
                margin: { top: 30, bottom: 30 },
                didDrawPage: function (data) {
                    doc.setFontSize(10);
                    doc.text(fileName, data.settings.margin.left, 10);
                }
            });
            doc.save(`${fileName}.pdf`);
        } else {
            console.error('Unsupported format');
        }
    }


    var mapType = 'ONLINE';
    database.collection('settings').doc('DriverNearBy').get().then(async function (snapshots) {
        var data = snapshots.data();
        if (data && data.selectedMapType && data.selectedMapType == "osm") {
            mapType = "OFFLINE"
        }
    });

    async function loadGoogleMapsScript() {
        var googleMapKeySnapshotsHeader = await database.collection('settings').doc("googleMapKey").get();
        var placeholderImageHeaderData = googleMapKeySnapshotsHeader.data();
        googleMapKey = placeholderImageHeaderData.key;
        const script = document.createElement('script');
        if (mapType == "OFFLINE" ){
            script.src = "https://unpkg.com/leaflet@1.7.1/dist/leaflet.js";
            script.src = "https://unpkg.com/leaflet-draw/dist/leaflet.draw.js";
            script.src = "https://cdnjs.cloudflare.com/ajax/libs/leaflet-editable/0.7.3/leaflet.editable.min.js";
            script.src = "https://unpkg.com/leaflet-draw@0.4.14/dist/leaflet.draw-src.js";
            script.src = "https://unpkg.com/leaflet-ajax/dist/leaflet.ajax.min.js";
            script.src = "https://unpkg.com/leaflet-geojson-layer/src/leaflet.geojson.js";
            script.src = "https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js";

        }else{
            script.src = "https://maps.googleapis.com/maps/api/js?key=" + googleMapKey + "&libraries=places,drawing";
        }
        script.onload = function () {
            navigator.geolocation.getCurrentPosition(GeolocationSuccessCallback,GeolocationErrorCallback);
            if(typeof window['InitializeGodsEyeMap'] === 'function') {
                InitializeGodsEyeMap();
            }
        };
        document.head.appendChild(script);
    }

    const GeolocationSuccessCallback = (position) => {
        if(position.coords != undefined){
            default_latitude = position.coords.latitude
            default_longitude = position.coords.longitude
            setCookie('default_latitude', default_latitude, 365);
            setCookie('default_longitude', default_longitude, 365);
        }
    };

    const GeolocationErrorCallback = (error) => {
        console.log('Error: You denied for your default Geolocation',error.message);
        setCookie('default_latitude', '23.022505', 365);
        setCookie('default_longitude','72.571365', 365);
    };

    loadGoogleMapsScript();

    async function sendNotification(fcmToken = '', title, body) {
        var checkFlag = false;
        var sendNotificationUrl = "{{ route('send-notification') }}";

        if (fcmToken !== '') {
            await $.ajax({
                type: 'POST',
                url: sendNotificationUrl,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    'fcm': fcmToken,
                    'title': title,
                    'message': body
                },
                success: function (data) {
                    checkFlag = true;
                },
                error: function (error) {
                    checkFlag = true;
                }
            });
        } else {
            checkFlag = true;
        }

        return checkFlag;
    }

    database.collection('settings').doc("notification_setting").get().then(async function (snapshots) {
        var data = snapshots.data();
        if(data != undefined){
            serviceJson = data.serviceJson;
            if(serviceJson != '' && serviceJson != null){
                $.ajax({
                    type: 'POST',
                    data: {
                        serviceJson: btoa(serviceJson),
                    },
                    url: "{{ route('store-firebase-service') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                    }
                });
            }
        }
    });

    // 🧠 Smart Coupon Deletion Function - Preserves Global Coupons & Active Orders
    const smartDeleteCouponsForVendor = async (vendorId) => {
        console.log(`🔍 Smart coupon deletion for vendor: ${vendorId}`);

        try {
            // Get all coupons for this vendor (including global ones)
            const couponsSnapshot = await database.collection('coupons')
                .where('resturant_id', 'in', [vendorId, 'ALL'])
                .get();

            if (couponsSnapshot.empty) {
                console.log(`📝 No coupons found for vendor: ${vendorId}`);
                return { deleted: 0, preserved: 0, protected: 0 };
            }

            let deletedCount = 0;
            let preservedCount = 0;
            let protectedCount = 0;
            const deletedCoupons = [];
            const preservedCoupons = [];
            const protectedCoupons = [];

            // Process each coupon
            for (const doc of couponsSnapshot.docs) {
                const couponData = doc.data();
                const couponId = doc.id;

                // Only delete vendor-specific coupons, preserve global ones
                if (couponData.resturant_id === vendorId) {
                    // Check if coupon has active orders before deletion
                    const hasActiveOrders = await checkCouponActiveOrders(couponData.code, vendorId);

                    if (hasActiveOrders) {
                        // Protect coupon with active orders
                        protectedCount++;
                        protectedCoupons.push(couponData.code || 'Unknown');
                        console.log(`🛡️ Protected coupon with active orders: ${couponData.code}`);
                    } else {
                        // Safe to delete vendor-specific coupon
                        await deleteDocumentWithImage('coupons', couponId, 'image');
                        deletedCount++;
                        deletedCoupons.push(couponData.code || 'Unknown');
                        console.log(`🗑️ Deleted vendor-specific coupon: ${couponData.code}`);
                    }
                } else if (couponData.resturant_id === 'ALL') {
                    // This is a global coupon - always preserve it
                    preservedCount++;
                    preservedCoupons.push(couponData.code || 'Unknown');
                    console.log(`✅ Preserved global coupon: ${couponData.code}`);
                }
            }

            // Log the smart deletion results
            console.log(`📊 Smart Coupon Deletion Results:`);
            console.log(`   🗑️ Deleted: ${deletedCount} vendor-specific coupons`);
            console.log(`   ✅ Preserved: ${preservedCount} global coupons`);
            console.log(`   🛡️ Protected: ${protectedCount} coupons with active orders`);

            if (deletedCoupons.length > 0) {
                console.log(`   Deleted coupons: ${deletedCoupons.join(', ')}`);
            }
            if (preservedCoupons.length > 0) {
                console.log(`   Preserved coupons: ${preservedCoupons.join(', ')}`);
            }
            if (protectedCoupons.length > 0) {
                console.log(`   Protected coupons: ${protectedCoupons.join(', ')}`);
            }

            // Show user-friendly notification
            if (preservedCount > 0 || protectedCount > 0) {
                showSmartDeletionNotification(deletedCount, preservedCount, protectedCount, deletedCoupons, preservedCoupons, protectedCoupons);
            }

            return {
                deleted: deletedCount,
                preserved: preservedCount,
                protected: protectedCount,
                deletedCoupons: deletedCoupons,
                preservedCoupons: preservedCoupons,
                protectedCoupons: protectedCoupons
            };

        } catch (error) {
            console.error(`❌ Error in smart coupon deletion:`, error);
            throw error;
        }
    };

    // 🔍 Check Coupon Active Orders Function
    const checkCouponActiveOrders = async (couponCode, vendorId) => {
        try {
            // Check for active orders with this coupon code for this vendor
            const activeOrdersSnapshot = await database.collection('restaurant_orders')
                .where('couponCode', '==', couponCode)
                .where('vendor_id', '==', vendorId)
                .where('status', 'in', ['pending', 'confirmed', 'preparing', 'ready_for_pickup', 'out_for_delivery'])
                .limit(1)
                .get();

            return !activeOrdersSnapshot.empty;
        } catch (error) {
            console.error(`❌ Error checking active orders for coupon ${couponCode}:`, error);
            // If we can't check, assume there might be active orders and protect the coupon
            return true;
        }
    };

    // 📢 Smart Deletion Notification Function
    const showSmartDeletionNotification = (deletedCount, preservedCount, protectedCount, deletedCoupons, preservedCoupons, protectedCoupons) => {
        let message = `
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5><i class="fas fa-brain"></i> Smart Coupon Deletion Completed</h5>`;

        if (preservedCount > 0) {
            message += `<p><strong>✅ Preserved ${preservedCount} global coupon(s):</strong> ${preservedCoupons.join(', ')}</p>`;
        }

        if (deletedCount > 0) {
            message += `<p><strong>🗑️ Deleted ${deletedCount} vendor-specific coupon(s):</strong> ${deletedCoupons.join(', ')}</p>`;
        }

        if (protectedCount > 0) {
            message += `<p><strong>🛡️ Protected ${protectedCount} coupon(s) with active orders:</strong> ${protectedCoupons.join(', ')}</p>`;
        }

        message += `<p class="mb-0"><small>Global coupons work for all restaurants and are automatically preserved. Coupons with active orders are protected to maintain data integrity.</small></p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`;

        // Show notification at the top of the page
        $('body').prepend(message);

        // Auto-dismiss after 10 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 10000);
    };

    //On delete item delete image also from bucket general code
    const deleteDocumentWithImage = async (collection, id, singleImageField, arrayImageField) => {
        // Reference to the Firestore document
        const docRef = database.collection(collection).doc(id);
        try {
            const doc = await docRef.get();
            if (!doc.exists) {
                console.log("No document found for deletion");
                return;
            }

            const data = doc.data();

            // Deleting single image field with smart media deletion
            if (singleImageField) {
                if (Array.isArray(singleImageField)) {
                    for (const field of singleImageField) {
                        const imageUrl = data[field];
                        if (imageUrl) await smartDeleteImageFromBucket(imageUrl, collection, id);
                    }
                } else {
                    const imageUrl = data[singleImageField];
                    if (imageUrl) await smartDeleteImageFromBucket(imageUrl, collection, id);
                }
            }
            // Deleting array image field with smart media deletion
            if (arrayImageField) {
                if (Array.isArray(arrayImageField)) {
                    for (const field of arrayImageField) {
                        const arrayImages = data[field];
                        if (arrayImages && Array.isArray(arrayImages)) {
                            for (const imageUrl of arrayImages) {
                                if (imageUrl) await smartDeleteImageFromBucket(imageUrl, collection, id);
                            }
                        }
                    }
                } else {
                    const arrayImages = data[arrayImageField];
                    if (arrayImages && Array.isArray(arrayImages)) {
                        for (const imageUrl of arrayImages) {
                            if (imageUrl) await smartDeleteImageFromBucket(imageUrl, collection, id);
                        }
                    }
                }
            }

            // Deleting images in variants array within item_attribute
            const item_attribute = data.item_attribute || {};  // Access item_attribute
            const variants = item_attribute.variants || [];    // Access variants array inside item_attribute
            if (variants.length > 0) {
                for (const variant of variants) {
                    const variantImageUrl = variant.variant_image;
                    if (variantImageUrl) {
                        await smartDeleteImageFromBucket(variantImageUrl, collection, id);
                    }
                }
            }

            // Optionally delete the Firestore document after image deletion
            await docRef.delete();
            console.log("Document and images deleted successfully.");
        } catch (error) {
            console.error("Error deleting document and images:", error);
        }
    };

    // Smart media deletion with reference counting
    const smartDeleteImageFromBucket = async (imageUrl, currentCollection, currentId) => {
        try {
            console.log(`🔍 Checking if image ${imageUrl} is still referenced by other documents...`);

            // Check if this image is still referenced by other documents
            const isStillReferenced = await checkImageReferences(imageUrl, currentCollection, currentId);

            if (isStillReferenced) {
                console.log(`✅ Image ${imageUrl} is still referenced by other documents. Keeping the image.`);
                return;
            }

            console.log(`🗑️ Image ${imageUrl} is no longer referenced. Safe to delete.`);
            await deleteImageFromBucket(imageUrl);

        } catch (error) {
            console.error("Error in smart media deletion:", error);
            // Fallback to old behavior if smart deletion fails
            await deleteImageFromBucket(imageUrl);
        }
    };

    // Check if an image is still referenced by other documents
    const checkImageReferences = async (imageUrl, currentCollection, currentId) => {
        try {
            // Collections that might reference media images
            const collectionsToCheck = [
                'mart_categories',
                'mart_subcategories',
                'mart_items',
                'vendor_categories',
                'vendor_products',
                'media' // Check if it's still in media collection
            ];

            for (const collectionName of collectionsToCheck) {
                // Skip the current collection and document
                if (collectionName === currentCollection) {
                    continue;
                }

                console.log(`🔍 Checking ${collectionName} for image references...`);

                const snapshot = await database.collection(collectionName).get();

                for (const doc of snapshot.docs) {
                    // Skip the current document being deleted
                    if (collectionName === currentCollection && doc.id === currentId) {
                        continue;
                    }

                    const data = doc.data();

                    // Check single photo field
                    if (data.photo === imageUrl) {
                        console.log(`✅ Found reference in ${collectionName}/${doc.id} (photo field)`);
                        return true;
                    }

                    // Check photos array field
                    if (data.photos && Array.isArray(data.photos) && data.photos.includes(imageUrl)) {
                        console.log(`✅ Found reference in ${collectionName}/${doc.id} (photos array)`);
                        return true;
                    }

                    // Check image_path field (for media collection)
                    if (data.image_path === imageUrl) {
                        console.log(`✅ Found reference in ${collectionName}/${doc.id} (image_path field)`);
                        return true;
                    }
                }
            }

            console.log(`❌ No other references found for image ${imageUrl}`);
            return false;

        } catch (error) {
            console.error("Error checking image references:", error);
            // If we can't check references, assume it's still referenced (safer)
            return true;
        }
    };

    // Get media reference count for debugging/display purposes
    const getMediaReferenceCount = async (imageUrl) => {
        try {
            const collectionsToCheck = [
                'mart_categories',
                'mart_subcategories',
                'mart_items',
                'vendor_categories',
                'vendor_products',
                'media'
            ];

            let referenceCount = 0;
            const references = [];

            for (const collectionName of collectionsToCheck) {
                const snapshot = await database.collection(collectionName).get();

                for (const doc of snapshot.docs) {
                    const data = doc.data();

                    // Check single photo field
                    if (data.photo === imageUrl) {
                        referenceCount++;
                        references.push(`${collectionName}/${doc.id} (photo)`);
                    }

                    // Check photos array field
                    if (data.photos && Array.isArray(data.photos) && data.photos.includes(imageUrl)) {
                        referenceCount++;
                        references.push(`${collectionName}/${doc.id} (photos array)`);
                    }

                    // Check image_path field (for media collection)
                    if (data.image_path === imageUrl) {
                        referenceCount++;
                        references.push(`${collectionName}/${doc.id} (image_path)`);
                    }
                }
            }

            return {
                count: referenceCount,
                references: references
            };

        } catch (error) {
            console.error("Error getting media reference count:", error);
            return { count: 0, references: [] };
        }
    };

    const deleteImageFromBucket = async (imageUrl) => {
        try {
            const storageRef = firebase.storage().ref();

            // Check if the imageUrl is a full URL or just a child path
            let oldImageUrlRef;
            if (imageUrl.includes('https://')) {
                // Full URL
                oldImageUrlRef = storageRef.storage.refFromURL(imageUrl);
            } else {
                // Child path, use ref instead of refFromURL
                oldImageUrlRef = storageRef.storage.ref(imageUrl);
            }
            var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
            var imageBucket = oldImageUrlRef.bucket;
            // Check if the bucket name matches
            if (imageBucket === envBucket) {
                // Delete the image
                await oldImageUrlRef.delete();
                console.log("Image deleted successfully.");
            }
        } catch (error) {

        }
    };

    // Enhanced Global Real-time restaurantorders Notification System
    (function() {
        // Initialize enhanced notification system
        let knownOrderIds = new Set();
        let pageLoadTime = Date.now();
        let isInitialized = false;

        // Connection management for shared hosting optimization
        let connectionCount = 0;
        const MAX_CONNECTIONS = 3; // Reduced limit for shared hosting

        function checkConnectionLimit() {
            if (connectionCount >= MAX_CONNECTIONS) {
                console.warn('⚠️ Connection limit reached, skipping request to prevent 503 errors...');
                return false;
            }
            connectionCount++;
            return true;
        }

        function releaseConnection() {
            if (connectionCount > 0) {
                connectionCount--;
            }
        }

        // Auto-release connections after timeout to prevent leaks
        function autoReleaseConnection() {
            setTimeout(() => {
                releaseConnection();
            }, 5000); // Auto-release after 5 seconds
        }






        // Manual email function removed - email notifications disabled

        // Function to clear known orders cache and reset notification system
        window.clearKnownOrders = function() {
            knownOrderIds.clear();
            localStorage.removeItem('knownOrderIds');
            localStorage.removeItem('knownOrderIdsTimestamp');

            // Clear notification badge
            const badge = document.getElementById('new-orders-badge');
            if (badge) {
                badge.style.display = 'none';
                badge.textContent = '0';
            }

            console.log('🗑️ Cleared known orders cache and reset notification badge. Next orders will be treated as new.');
        };

        // Function to show current system status
        window.showNotificationStatus = function() {
            console.log('📊 Notification System Status:');
            console.log('- Known order IDs:', knownOrderIds.size);
            console.log('- Page load time:', new Date(pageLoadTime));
            console.log('- Is initialized:', isInitialized);
            console.log('- Current time:', new Date());
            console.log('- System start time:', new Date(pageLoadTime - (2 * 60 * 1000)));
            console.log('- Available functions: clearKnownOrders(), showNotificationStatus()');
            console.log('- Test functions disabled in production');
        };



        // Debug: Log that functions are available
        console.log('🔧 Debug functions loaded:', {
            clearKnownOrders: typeof window.clearKnownOrders,
            sendEmailForOrder: 'DISABLED',
            showNotificationStatus: typeof window.showNotificationStatus
        });

        // Alternative: Define functions globally for easier access
        window.debugClearCache = function() {
            if (typeof knownOrderIds !== 'undefined') {
                knownOrderIds.clear();
                localStorage.removeItem('knownOrderIds');
                localStorage.removeItem('knownOrderIdsTimestamp');

                // Clear notification badge
                const badge = document.getElementById('new-orders-badge');
                if (badge) {
                    badge.style.display = 'none';
                    badge.textContent = '0';
                }

                console.log('🗑️ Cleared known orders cache and reset badge (alternative method)');
            } else {
                console.error('❌ knownOrderIds not available');
            }
        };


        console.log('🔧 Debug functions loaded: clearKnownOrders(), debugClearCache(), showNotificationStatus(), removeSpecificTestOrder()');
        console.log('🔧 Test functions disabled in production');
        let notificationSound = null;
        let customRingtone = null;
        let soundEnabled = localStorage.getItem('notificationSoundEnabled') !== 'false';
        let recentOrders = [];
        let tooltipTimeout = null;

        // Enhanced load known order IDs from localStorage with better validation
        function loadKnownOrderIds() {
            try {
                const savedOrderIds = localStorage.getItem('knownOrderIds');
                const savedTimestamp = localStorage.getItem('knownOrderIdsTimestamp');

                if (savedOrderIds && savedTimestamp) {
                    const timestamp = parseInt(savedTimestamp);
                    const now = Date.now();

                    // Only use saved IDs if they're from the last 2 hours (very strict to prevent stale cache issues)
                    if (now - timestamp < 2 * 60 * 60 * 1000) {
                        const orderIds = JSON.parse(savedOrderIds);

                        // Validate that we have an array of strings
                        if (Array.isArray(orderIds) && orderIds.every(id => typeof id === 'string' && id.length > 0)) {
                            knownOrderIds = new Set(orderIds);
                            console.log('📋 Loaded known order IDs from localStorage:', knownOrderIds.size, 'orders');
                            console.log('📅 Cache timestamp:', new Date(timestamp));
                            console.log('📅 Cache age:', Math.round((now - timestamp) / (1000 * 60)), 'minutes');
                        } else {
                            console.log('📋 Invalid order IDs format in localStorage, starting fresh');
                            localStorage.removeItem('knownOrderIds');
                            localStorage.removeItem('knownOrderIdsTimestamp');
                        }
                    } else {
                        console.log('📋 Saved order IDs are too old (older than 2 hours), starting fresh');
                        localStorage.removeItem('knownOrderIds');
                        localStorage.removeItem('knownOrderIdsTimestamp');
                    }
                } else {
                    console.log('📋 No saved order IDs found, starting fresh');
                }
            } catch (error) {
                console.error('❌ Error loading known order IDs:', error);
                // Clear corrupted data
                localStorage.removeItem('knownOrderIds');
                localStorage.removeItem('knownOrderIdsTimestamp');
                knownOrderIds = new Set(); // Reset to empty set
            }
        }

        // Enhanced save known order IDs to localStorage with better error handling
        function saveKnownOrderIds() {
            try {
                const orderIdsArray = Array.from(knownOrderIds);

                // Validate data before saving
                if (Array.isArray(orderIdsArray) && orderIdsArray.length > 0) {
                    localStorage.setItem('knownOrderIds', JSON.stringify(orderIdsArray));
                    localStorage.setItem('knownOrderIdsTimestamp', Date.now().toString());
                    console.log('💾 Saved known order IDs to localStorage:', orderIdsArray.length, 'orders');
                } else {
                    console.log('💾 No order IDs to save or invalid data');
                }
            } catch (error) {
                console.error('❌ Error saving known order IDs:', error);
                // Try to clear corrupted data
                try {
                    localStorage.removeItem('knownOrderIds');
                    localStorage.removeItem('knownOrderIdsTimestamp');
                } catch (clearError) {
                    console.error('❌ Error clearing corrupted localStorage:', clearError);
                }
            }
        }

        // Load custom ringtone from global settings
        function loadCustomRingtone() {
            const settingsRef = database.collection('settings').doc('globalSettings');
            settingsRef.get().then((snapshot) => {
                const globalSettings = snapshot.data();
                if (globalSettings && globalSettings.order_ringtone_url) {
                    customRingtone = globalSettings.order_ringtone_url;
                    console.log('Custom ringtone loaded:', customRingtone);
                }
            }).catch((error) => {
                console.log('Error loading custom ringtone:', error);
            });
        }

        // Create notification sound
        function createNotificationSound() {
            if (!notificationSound) {
                // Use custom ringtone if available
                if (customRingtone) {
                    notificationSound = new Audio(customRingtone);
                    notificationSound.volume = 0.5;
                    console.log('Using custom ringtone for notifications');
                } else {
                    // Create a pleasant notification sound using Web Audio API
                    try {
                        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();

                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);

                        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                        oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.2);

                        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);

                        notificationSound = { audioContext, oscillator, gainNode };
                    } catch (e) {
                        console.log('Web Audio API not supported, using fallback sound');
                        // Fallback to simple beep
                        notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
                        notificationSound.volume = 0.3;
                    }
                }
            }
        }

        // Play notification sound
        function playNotificationSound() {
            console.log('🔊 playNotificationSound called');
            console.log('🔊 soundEnabled:', soundEnabled);

            if (!soundEnabled) {
                console.log('🔊 Sound is disabled, skipping audio');
                return;
            }

            console.log('🔊 Creating notification sound...');
            createNotificationSound();

            try {
                if (customRingtone && notificationSound.src) {
                    console.log('🔊 Using custom ringtone');
                    // Custom ringtone audio
                    notificationSound.currentTime = 0;
                    notificationSound.play().then(() => {
                        console.log('🔊 Custom ringtone played successfully');
                    }).catch(e => {
                        console.log('🔊 Custom ringtone play failed:', e);
                        // Try fallback sound
                        playFallbackSound();
                    });
                } else if (notificationSound.audioContext) {
                    console.log('🔊 Using Web Audio API sound');
                    // Web Audio API sound
                    const audioContext = notificationSound.audioContext;
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                    oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.2);

                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.3);
                    console.log('🔊 Web Audio API sound played');
                } else {
                    console.log('🔊 Using fallback audio');
                    // Fallback audio
                    notificationSound.currentTime = 0;
                    notificationSound.play().then(() => {
                        console.log('🔊 Fallback audio played successfully');
                    }).catch(e => {
                        console.log('🔊 Fallback audio play failed:', e);
                        // Try browser beep as last resort
                        playBrowserBeep();
                    });
                }
            } catch (e) {
                console.log('🔊 Sound play failed with error:', e);
                // Try browser beep as last resort
                playBrowserBeep();
            }
        }

        // Fallback sound function
        function playFallbackSound() {
            console.log('🔊 Playing fallback sound...');
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.setValueAtTime(1000, audioContext.currentTime);
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.1);
                oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.2);

                gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.3);
                console.log('🔊 Fallback sound played successfully');
            } catch (e) {
                console.log('🔊 Fallback sound failed:', e);
                playBrowserBeep();
            }
        }

        // Browser beep as last resort
        function playBrowserBeep() {
            console.log('🔊 Playing browser beep as last resort...');
            try {
                // Try multiple methods to play a sound
                const beep = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
                beep.volume = 0.3;
                beep.play().then(() => {
                    console.log('🔊 Browser beep played successfully');
                }).catch(e => {
                    console.log('🔊 Browser beep failed:', e);
                    // Final fallback - try to create a simple beep using document
                    document.body.innerHTML += '<audio autoplay><source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT" type="audio/wav"></audio>';
                });
            } catch (e) {
                console.log('🔊 Browser beep creation failed:', e);
            }
        }

        let toastOffset = 0;

        function showNewOrderNotification(orderData) {
            // Enhanced debugging for order notifications
            console.log('🔔 showNewOrderNotification called for order:', orderData.id);
            console.log('🔔 restaurantorders data:', {
                id: orderData.id,
                status: orderData.status,
                vendorID: orderData.vendorID,
                vendor: orderData.vendor,
                author: orderData.author,
                createdAt: orderData.createdAt
            });

            // Check if this is a mart order by looking at vendor data
            const isMartOrder = orderData.vendor && orderData.vendor.vType === 'mart';
            console.log('🏪 Is Mart restaurantorders:', isMartOrder);

            // Play notification sound with enhanced debugging
            console.log('🔊 Attempting to play notification sound...');
            console.log('🔊 Sound enabled:', soundEnabled);
            console.log('🔊 Notification sound object:', notificationSound);

            playNotificationSound();

            // Email notifications have been completely removed

            // Fallback: Check for toast plugin
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert not loaded!');
                alert(`New Order: #${orderData.id}`);
                return;
            }

            // Create a unique wrapper per toast
            const wrapper = document.createElement('div');
            wrapper.style.marginTop = `${toastOffset}px`;
            document.getElementById('toast-container').appendChild(wrapper);

            Swal.fire({
                title: 'New restaurantorders Received!',
                html: `<strong>Order #${orderData.id}</strong><br>From: ${orderData.vendor ? orderData.vendor.title : 'Restaurant'}`,
                icon: 'info',
                toast: true,
                position: 'top',
                showConfirmButton: false,
                showCloseButton: true,
                timer: 10000,
                timerProgressBar: true,
                target: wrapper,
                didOpen: (toast) => {
                    toast.addEventListener('click', () => {
                        window.location.href = '{{ route("orders") }}';
                    });
                },
                willClose: () => {
                    wrapper.remove();
                    toastOffset -= 90; // adjust spacing after toast disappears
                }
            });

            toastOffset += 90; // adjust spacing for next toast (approx height)

            // Optional: reset offset if too many
            if (toastOffset > 500) toastOffset = 0;

            updateOrderCount();
            updateNotificationBadge();
        }

        // Email notification function removed to prevent resource issues on shared hosting
        // Get time ago string
        function getTimeAgo(date) {
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            return `${diffDays}d ago`;
        }

        // Update order count on dashboard
        function updateOrderCount() {
            database.collection('restaurant_orders').get().then((snapshot) => {
                const orderCount = snapshot.docs.length;
                const orderCountElement = document.getElementById('order_count');
                if (orderCountElement) {
                    orderCountElement.textContent = orderCount;
                }
            });
        }

        // Update notification badge
        function updateNotificationBadge() {
            const badge = document.getElementById('new-orders-badge');
            if (badge) {
                // Reset badge count to prevent accumulation
                badge.textContent = '1';
                badge.style.display = 'block';

                // Auto-hide badge after 30 seconds
                setTimeout(() => {
                    badge.style.display = 'none';
                    badge.textContent = '0';
                }, 30000);
            }
        }

        // Initialize real-time listener for new orders
        function initializeOrderListener() {
            // Guard: disable if Firebase/database not available
            if (typeof database === 'undefined' || !database || typeof database.collection !== 'function') {
                return;
            }
            // console.log('Initializing enhanced global order notification listener...');

            // Get existing orders to populate knownOrderIds and recent orders
            database.collection('restaurant_orders')
                .orderBy('createdAt', 'desc')
                .limit(50) // Get last 50 orders
                .get()
                .then((snapshot) => {
                    if (!snapshot.empty) {
                        snapshot.docs.forEach(doc => {
                            knownOrderIds.add(doc.id);
                        });
                        // console.log('Known order IDs populated:', knownOrderIds.size, 'orders');

                        // Save known order IDs to localStorage
                        saveKnownOrderIds();

                        // Populate recent orders for tooltip
                        snapshot.docs.slice(0, 5).forEach(doc => {
                            const orderData = doc.data();
                            addToRecentOrders({
                                id: doc.id,
                                author: orderData.author,
                                vendor: orderData.vendor,
                                toPayAmount: orderData.toPayAmount
                            });
                        });
                    } else {
                        // console.log('No existing orders found, will show notifications for all new orders');
                    }

                    // Start real-time listener
                    startRealtimeListener();
                })
                .catch((error) => {
                    // console.error('Error getting existing orders:', error);
                    // Start listener anyway
                    startRealtimeListener();
                });
        }

        // Start real-time listener
        function startRealtimeListener() {
            console.log('🚀 Starting enhanced real-time listener for restaurant_orders collection...');
            console.log('📊 System Status:', {
                knownOrderIds: knownOrderIds.size,
                isInitialized: isInitialized,
                pageLoadTime: new Date(pageLoadTime),
                currentTime: new Date()
            });

            const ordersRef = database.collection('restaurant_orders');

            // Listen for new documents
            ordersRef.onSnapshot((snapshot) => {
                console.log('📡 Snapshot received, changes:', snapshot.docChanges().length, 'Total docs:', snapshot.docs.length);
                console.log('🔍 Snapshot metadata:', {
                    fromCache: snapshot.metadata.fromCache,
                    hasPendingWrites: snapshot.metadata.hasPendingWrites,
                    isEqual: snapshot.metadata.isEqual
                });

                // Skip processing if this is a cache snapshot (not real-time data)
                if (snapshot.metadata.fromCache) {
                    return;
                }

                snapshot.docChanges().forEach((change) => {

                    if (change.type === 'added') {
                        const orderData = change.doc.data();
                        orderData.id = change.doc.id;

                        // Check if this is a truly new order
                        if (!knownOrderIds.has(orderData.id)) {
                            // STRICT order age validation - only process truly new orders
                            const orderCreatedAt = orderData.createdAt ? new Date(orderData.createdAt.seconds * 1000) : new Date();
                            const currentTime = Date.now();
                            const orderAge = currentTime - orderCreatedAt.getTime();

                            // Only process orders created within the last 2 minutes (very strict)
                            const maxOrderAge = 2 * 60 * 1000; // 2 minutes
                            const isVeryRecentOrder = orderAge <= maxOrderAge;

                            // Additional check: order must be created after page load
                            const isOrderCreatedAfterPageLoad = orderCreatedAt.getTime() > pageLoadTime;

                            // Only process if order is very recent AND created after page load
                            const shouldProcessOrder = isVeryRecentOrder && isOrderCreatedAfterPageLoad;


                            // Only process orders that pass age validation
                            if (shouldProcessOrder) {

                                // Enhanced debugging for order type detection
                                const isMartOrder = orderData.vendor && orderData.vendor.vType === 'mart';
                                const isRestaurantOrder = orderData.vendor && orderData.vendor.vType === 'restaurant';

                                console.log('🏪 restaurantorders type detection:', {
                                    isMartOrder: isMartOrder,
                                    isRestaurantOrder: isRestaurantOrder,
                                    vendorType: orderData.vendor ? orderData.vendor.vType : 'unknown',
                                    vendorTitle: orderData.vendor ? orderData.vendor.title : 'unknown'
                                });

                                // ENHANCED FILTERING: Additional checks to prevent false notifications
                                const isTestOrder = orderData.id.includes('Restaurant_') ||
                                                   orderData.id.includes('test_') ||
                                                   orderData.id.includes('debug') ||
                                                   orderData.id.includes('TEST-ORDER') ||
                                                   orderData.id.includes('TEST_') ||
                                                   orderData.id.toLowerCase().includes('test');
                                const isAdminOrder = orderData.author && (orderData.author.name === 'admin' || orderData.author.name === 'Admin');
                                const hasValidStatus = ['restaurantorders Placed', 'restaurantorders Accepted', 'restaurantorders Rejected', 'restaurantorders Completed'].includes(orderData.status);

                                console.log('🔍 Enhanced filtering checks:', {
                                    isTestOrder: isTestOrder,
                                    isAdminOrder: isAdminOrder,
                                    hasValidStatus: hasValidStatus,
                                    orderStatus: orderData.status
                                });

                                // Skip test orders, admin orders, and orders with invalid status
                                if (isTestOrder || isAdminOrder || !hasValidStatus) {
                                    console.log('⏭️ Skipping notification - Test/Admin order or invalid status:', orderData.id);
                                    return;
                                }

                                // Enhanced debugging for mart orders specifically
                                if (isMartOrder) {
                                    console.log('🏪 MART ORDER DETECTED - Enhanced Debug Info:');
                                    console.log('   - restaurantorders ID:', orderData.id);
                                    console.log('   - Vendor ID:', orderData.vendorID);
                                    console.log('   - Vendor Title:', orderData.vendor.title);
                                    console.log('   - restaurantorders Status:', orderData.status);
                                    console.log('   - Created At:', orderCreatedAt);
                                    console.log('   - System Initialized:', isInitialized);
                                    console.log('   - Age Validation (DISABLED):', shouldProcessOrder);
                                }

                                // Only show notification if system is initialized (to avoid showing old orders on page load)
                                if (isInitialized) {
                                    console.log('🔔 Showing notification for new order:', orderData.id);
                                    console.log('🔔 restaurantorders type:', isMartOrder ? 'MART ORDER' : isRestaurantOrder ? 'RESTAURANT ORDER' : 'UNKNOWN TYPE');
                                    showNewOrderNotification(orderData);
                                } else {
                                    console.log('⏳ System not initialized yet, skipping visual notification for:', orderData.id);
                                }
                            } else {
                                console.log('❌ restaurantorders failed age validation - skipping notification:', orderData.id);
                                console.log('   - Is very recent (≤2 minutes):', isVeryRecentOrder);
                                console.log('   - Created after page load:', isOrderCreatedAfterPageLoad);
                                console.log('   - restaurantorders age:', Math.round(orderAge / 1000), 'seconds');
                                console.log('   - Max allowed age:', Math.round(maxOrderAge / 1000), 'seconds');
                            }

                            // Only add to known orders if the order was actually processed
                            if (shouldProcessOrder) {
                                knownOrderIds.add(orderData.id);
                                saveKnownOrderIds();
                                console.log('✅ Added order to known set:', orderData.id);
                            } else {
                                console.log('⏭️ Skipped adding order to known set (failed validation):', orderData.id);
                            }
                        } else {
                            // restaurantorders is already known, but let's check if it's a status change that needs notification
                            console.log('📋 restaurantorders already known (ID in known set):', orderData.id, 'Status:', orderData.status);

                            // Check if this is a status change that should trigger notification
                            const shouldNotifyStatus = ['restaurantorders Accepted', 'restaurantorders Rejected', 'restaurantorders Completed'].includes(orderData.status);

                            if (shouldNotifyStatus && isInitialized) {
                                console.log('🔔 Status change detected for known order (email notifications disabled):', orderData.id);
                                // Show notification for status changes (email notifications disabled)
                                showNewOrderNotification(orderData);
                            } else {
                                console.log('📋 Status change for known order but no notification needed:', orderData.id, 'Status:', orderData.status);
                            }
                        }
                    }
                });

                // Mark as initialized after first snapshot
                if (!isInitialized) {
                    isInitialized = true;
                    // console.log('Enhanced notification system initialized, will show notifications for new orders');
                }
            }, (error) => {
                console.error('Error in order listener:', error);
                // Retry after 5 seconds
                setTimeout(initializeOrderListener, 5000);
            });
        }
        // Initialize tooltip functionality
        function initializeTooltip() {
            const notificationBell = document.querySelector('.notification-bell');
            const tooltipContent = document.querySelector('.notification-tooltip-content');

            if (!notificationBell || !tooltipContent) return;

            // Show tooltip on hover
            notificationBell.addEventListener('mouseenter', () => {
                if (tooltipTimeout) {
                    clearTimeout(tooltipTimeout);
                }
                tooltipContent.classList.add('show');
            });
            // Hide tooltip on mouse leave
            notificationBell.addEventListener('mouseleave', () => {
                tooltipTimeout = setTimeout(() => {
                    tooltipContent.classList.remove('show');
                }, 300);
            });

            // Prevent tooltip from hiding when hovering over it
            tooltipContent.addEventListener('mouseenter', () => {
                if (tooltipTimeout) {
                    clearTimeout(tooltipTimeout);
                }
            });

            tooltipContent.addEventListener('mouseleave', () => {
                tooltipContent.classList.remove('show');
            });
        }

        // Initialize sound controls
        function initializeSoundControls() {
            const soundToggle = document.getElementById('sound-toggle');
            const soundControls = document.getElementById('sound-controls');

            if (!soundToggle) return;

            // Show sound controls after first notification
            let controlsShown = false;

            // Load sound preference from localStorage
            const savedSoundPreference = localStorage.getItem('notificationSoundEnabled');
            if (savedSoundPreference !== null) {
                soundEnabled = savedSoundPreference === 'true';
                updateSoundToggleIcon();
            }

            soundToggle.addEventListener('click', () => {
                soundEnabled = !soundEnabled;
                localStorage.setItem('notificationSoundEnabled', soundEnabled.toString());
                updateSoundToggleIcon();

                // Show controls if not shown yet
                if (!controlsShown) {
                    soundControls.classList.add('show');
                    controlsShown = true;

                    // Hide after 5 seconds
                    setTimeout(() => {
                        soundControls.classList.remove('show');
                    }, 5000);
                }
            });

            function updateSoundToggleIcon() {
                const icon = soundToggle.querySelector('i');
                if (soundEnabled) {
                    icon.className = 'fa fa-volume-up';
                    soundToggle.classList.remove('muted');
                    soundToggle.title = 'Mute Sound Notifications';
                } else {
                    icon.className = 'fa fa-volume-off';
                    soundToggle.classList.add('muted');
                    soundToggle.title = 'Unmute Sound Notifications';
                }
            }

            updateSoundToggleIcon();
        }

        // Global toggle to enable/disable realtime Firebase listener (MySQL build keeps this OFF)
        if (typeof window.ENABLE_REALTIME_NOTIFICATIONS === 'undefined') {
            window.ENABLE_REALTIME_NOTIFICATIONS = false;
        }

        // Initialize the enhanced notification system when DOM is ready
        $(document).ready(function() {
            // Clear notification badge on page load to prevent accumulation
            const badge = document.getElementById('new-orders-badge');
            if (badge) {
                badge.style.display = 'none';
                badge.textContent = '0';
            }

            // Load custom ringtone first
            loadCustomRingtone();
            loadKnownOrderIds(); // Load known order IDs on page load

            // Small delay to ensure Firebase is fully initialized
            setTimeout(() => {
                // Only initialize order listener on pages that need it (not on settings pages)
                const currentPath = window.location.pathname;
                const isSettingsPage = currentPath.includes('/settings/') ||
                                     currentPath.includes('/zone/bonus-settings') ||
                                     currentPath.includes('/test/');

                if (!isSettingsPage && window.ENABLE_REALTIME_NOTIFICATIONS === true) {
                    initializeOrderListener();
                }

                initializeTooltip();
                initializeSoundControls();
                initializeImpersonationAutoLogin(); // Initialize impersonation auto-login
            }, 1000);

            // // Add debug info to console
            // setTimeout(() => {
            //     console.log('=== ENHANCED NOTIFICATION SYSTEM DEBUG INFO ===');
            //     console.log('Firebase database object:', typeof database !== 'undefined' ? 'Available' : 'Not available');
            //     console.log('jQuery toast plugin:', typeof $.toast !== 'undefined' ? 'Available' : 'Not available');
            //     console.log('Sound enabled:', soundEnabled);
            //     console.log('Recent orders count:', recentOrders.length);
            //     console.log('Known order IDs in localStorage:', knownOrderIds.size);
            //
            //     // Test toast plugin if available
            //     if (typeof $.toast !== 'undefined') {
            //         console.log('Toast plugin is working!');
            //     } else {
            //         console.error('Toast plugin is NOT loaded!');
            //     }
            // }, 2000);
        });

        // Clear notification badge
        function clearNotificationBadge() {
            const badge = document.getElementById('new-orders-badge');
            if (badge) {
                badge.style.display = 'none';
                badge.textContent = '0';
            }
        }

        // Clear badge when user visits orders page
        $(document).ready(function() {
            if (window.location.pathname.includes('/orders')) {
                clearNotificationBadge();
            }
        });

        // // Make functions globally available for debugging and testing
        // window.orderNotificationSystem = {
        //     initializeOrderListener: initializeOrderListener,
        //     showNewOrderNotification: showNewOrderNotification,
        //     updateOrderCount: updateOrderCount,
        //     clearNotificationBadge: clearNotificationBadge,
        //     playNotificationSound: playNotificationSound,
        //     refreshCustomRingtone: loadCustomRingtone,
        //     toggleSound: function() {
        //         soundEnabled = !soundEnabled;
        //         localStorage.setItem('notificationSoundEnabled', soundEnabled.toString());
        //         const soundToggle = document.getElementById('sound-toggle');
        //         if (soundToggle) {
        //             const icon = soundToggle.querySelector('i');
        //             if (soundEnabled) {
        //                 icon.className = 'fa fa-volume-up';
        //                 soundToggle.classList.remove('muted');
        //             } else {
        //                 icon.className = 'fa fa-volume-off';
        //                 soundToggle.classList.add('muted');
        //             }
        //         }
        //     },
        //     testNotification: function() {
        //         const testOrderData = {
        //             id: 'TEST_' + Date.now(),
        //             vendor: { title: 'Test Restaurant' },
        //             author: { name: 'Test Customer' },
        //             toPayAmount: 25.00,
        //             status: 'restaurantorders Placed'
        //         };
        //         showNewOrderNotification(testOrderData);
        //     },
        //     testCustomRingtone: function() {
        //         if (customRingtone) {
        //             const audio = new Audio(customRingtone);
        //             audio.volume = 0.5;
        //             audio.play().catch(e => console.log('Custom ringtone test failed:', e));
        //             console.log('Testing custom ringtone:', customRingtone);
        //         } else {
        //             console.log('No custom ringtone available');
        //         }
        //     },
        //     getDebugInfo: function() {
        //         return {
        //             knownOrderIds: Array.from(knownOrderIds),
        //             knownOrderCount: knownOrderIds.size,
        //             isInitialized: isInitialized,
        //             pageLoadTime: pageLoadTime,
        //             currentTime: Date.now(),
        //             firebaseInitialized: typeof database !== 'undefined',
        //             soundEnabled: soundEnabled,
        //             customRingtone: customRingtone,
        //             recentOrdersCount: recentOrders.length,
        //             recentOrders: recentOrders
        //         };
        //     }
        // };
    })();

</script>

@yield('scripts')

<!-- Auto-login script for Admin Impersonation -->
<script>
    // Auto-login function for Admin Impersonation
    function initializeImpersonationAutoLogin() {
        console.log('🔍 Auto-login script started');

        // Check URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const impersonationToken = urlParams.get('impersonation_token');
        const restaurantUid = urlParams.get('restaurant_uid');
        const autoLogin = urlParams.get('auto_login');

        console.log('🔍 Parameters:', {
            token: !!impersonationToken,
            uid: !!restaurantUid,
            autoLogin: autoLogin
        });

        // Only proceed if we have all required parameters
        if (impersonationToken && restaurantUid && autoLogin === 'true') {
            console.log('🔐 Starting auto-login process...');

            // Show loading immediately
            showImpersonationLoading();

            // Wait for Firebase to be ready
            setTimeout(function() {
                if (typeof firebase !== 'undefined' && firebase.auth) {
                    startImpersonationAutoLogin();
                } else {
                    console.error('❌ Firebase not available');
                    showImpersonationError('Firebase not loaded. Please refresh the page.');
                }
            }, 2000); // Wait for Firebase to be ready
        } else {
            console.log('ℹ️ No impersonation parameters, showing normal page');
        }

        function startImpersonationAutoLogin() {
            console.log('🚀 Starting auto-login...');

            const auth = firebase.auth();

            // Sign in with custom token
            auth.signInWithCustomToken(impersonationToken)
                .then(function(userCredential) {
                    console.log('✅ Login successful!');
                    console.log('User UID:', userCredential.user.uid);
                    console.log('Expected UID:', restaurantUid);

                    // Verify UID matches
                    if (userCredential.user.uid !== restaurantUid) {
                        throw new Error('UID mismatch - security violation');
                    }

                    // Store impersonation info
                    localStorage.setItem('restaurant_impersonation', JSON.stringify({
                        isImpersonated: true,
                        restaurantUid: restaurantUid,
                        impersonatedAt: new Date().toISOString()
                    }));

                    console.log('🔄 Impersonation successful, cleaning URL...');

                    // Clean URL and show success
                    setTimeout(function() {
                        window.history.replaceState({}, document.title, window.location.pathname);
                        showImpersonationSuccess();
                    }, 1000);
                })
                .catch(function(error) {
                    console.error('❌ Login failed:', error);
                    showImpersonationError('Auto-login failed: ' + error.message);

                    // Clean URL
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
        }

        function showImpersonationLoading() {
            const loading = document.createElement('div');
            loading.id = 'impersonation-loading';
            loading.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;">
                    <div style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 400px;">
                        <div style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                        <h3>🔐 Admin Impersonation</h3>
                        <p>Logging you in as restaurant owner...</p>
                        <p style="font-size: 12px; color: #666;">Please wait while we authenticate you.</p>
                    </div>
                </div>
                <style>
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            `;
            document.body.appendChild(loading);
        }

        function showImpersonationSuccess() {
            // Remove loading first
            const loading = document.getElementById('impersonation-loading');
            if (loading) {
                loading.remove();
            }

            const success = document.createElement('div');
            success.innerHTML = `
                <div style="position: fixed; top: 20px; right: 20px; background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <strong>✅ Impersonation Successful!</strong><br>
                    You are now logged in as the restaurant owner.
                    <button onclick="this.parentElement.parentElement.remove()" style="float: right; background: none; border: none; font-size: 18px; cursor: pointer; margin-left: 10px;">&times;</button>
                </div>
            `;
            document.body.appendChild(success);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (success.parentNode) {
                    success.remove();
                }
            }, 5000);
        }

        function showImpersonationError(message) {
            // Remove loading first
            const loading = document.getElementById('impersonation-loading');
            if (loading) {
                loading.remove();
            }

            const error = document.createElement('div');
            error.innerHTML = `
                <div style="position: fixed; top: 20px; right: 20px; background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <strong>❌ Auto-login Failed:</strong><br>
                    ${message}
                    <button onclick="this.parentElement.parentElement.remove()" style="float: right; background: none; border: none; font-size: 18px; cursor: pointer; margin-left: 10px;">&times;</button>
                </div>
            `;
            document.body.appendChild(error);
        }
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
