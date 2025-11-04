@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.print_order')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <?php if (isset($_GET['eid']) && $_GET['eid'] != ''){ ?>
                    <li class="breadcrumb-item"><a
                            href="{{route('restaurants.orders',$_GET['eid'])}}">{{trans('lang.order_plural')}}</a></li>
                    <?php }else{ ?>
                    <li class="breadcrumb-item"><a href="{!! route('orders') !!}">{{trans('lang.order_plural')}}</a>
                    </li>
                    <?php } ?>
                    <li class="breadcrumb-item">{{trans('lang.print_order')}}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card" id="printableArea" style="font-family: emoji;">
                <div class="col-md-12">
                    <div class="print-top non-printable mt-3">
                        <div class="text-right print-btn non-printable">
                            <button type="button" class="fa fa-print non-printable"
                                    onclick="printDiv('printableArea')"></button>
                        </div>
                    </div>
                    <hr class="non-printable">
                </div>
                <div class="col-12">
                    <div class="text-center pt-4 mb-3">
                        <h2 style="line-height: 1"><label class="storeName"></label></h2>
                        <h5 style="font-size: 20px;font-weight: lighter;line-height: 1">
                            <label class="storeAddress"></label>
                        </h5>
                        <h5 style="font-size: 16px;font-weight: lighter;line-height: 1">
                            {{trans('lang.phone')}} :
                            <label class="storePhone"></label>
                        </h5>
                    </div>
                    <span class="dashed-line"></span>
                    <div class="row mt-3">
                        <div class="col-6">
                            <h5>{{trans('lang.order_id')}} : <label class="orderId"></label></h5>
                        </div>
                        <div class="col-6">
                            <h5 style="font-weight: lighter">
                                <label class="orderDate"></label>
                            </h5>
                        </div>
                        <div class="col-12">
                            <h5>
                                {{trans('lang.customer_name')}} :
                                <label class="customerName"></label>
                            </h5>
                            <h5>
                                {{trans('lang.phone')}} :
                                <label class="customerPhone"></label>
                            </h5>
                            <h5 class="text-break">
                                {{trans('lang.address')}} :
                                <label class="customerAddress"></label>
                            </h5>
                        </div>
                    </div>
                    <h5 class="text-uppercase"></h5>
                    <span class="dashed-line"></span>
                    <table class="table table-bordered mt-3" style="width: 92%">
                        <thead>
                        <tr>
                            <th>{{trans('lang.item')}}</th>
                            <th>{{trans('lang.price')}}</th>
                            <th>{{trans('lang.qty')}}</th>
                            <th>{{trans('lang.extras')}}</th>
                            <th>{{trans('lang.total')}}</th>
                        </tr>
                        </thead>
                        <tbody id="order_products">
                        </tbody>
                    </table>
                    <span class="dashed-line"></span>
                    <div class="row justify-content-md-end mb-3" style="width: 97%">
                        <div class="col-md-7 col-lg-7">
                            <table class="order-summary-table" style="width:100%; font-size:16px; margin-bottom:0;">
                                <tr style="background:#eaffea;">
                                    <td><b>Subtotal</b></td>
                                    <td style="text-align:right; color:green;"><b><span
                                                class="sub_total_val"></span></b></td>
                                </tr>
                                <tr>
                                    <td colspan="2"
                                        style="font-size:13px; color:#888; margin-top:12px; height:18px;"></td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td style="text-align:right; color:#e74c3c;"><span class="discount_val"></span></td>
                                </tr>
                                <tr>
                                    <td>Special Offer Discount</td>
                                    <td style="text-align:right; color:#e74c3c;"><span
                                            class="special_discount_val"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"
                                        style="font-size:13px; color:#888; margin-top:12px; height:18px;"></td>
                                </tr>
                                <tr>
                                    <td>SGST (<span class="sgst_rate"></span>%)</td>
                                    <td style="text-align:right; color:#27ae60;">+<span class="sgst_val"></span></td>
                                </tr>
                                <tr>
                                    <td>GST (<span class="gst_rate"></span>%)</td>
                                    <td style="text-align:right; color:#27ae60;">+<span class="gst_val"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"
                                        style="font-size:13px; color:#888; margin-top:12px; height:18px;"></td>
                                </tr>
                                <tr>
                                    <td>Delivery Charge</td>
                                    <td style="text-align:right; color:#2980b9;">+<span
                                            class="delivery_charge_val"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"
                                        style="font-size:13px; color:#888; margin-top:12px; height:18px;"></td>
                                </tr>
                                <tr>
                                    <td>Tip Amount</td>
                                    <td style="text-align:right; color:#2980b9;">+<span class="tip_amount_val"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <hr>
                                    </td>
                                </tr>
                                <tr style="font-size:20px;">
                                    <td><b>Total Amount</b></td>
                                    <td style="text-align:right; color:#2c3e50;"><b><span
                                                class="total_amount_val"></span></b></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size:12px; color:#888;">
                                        Admin Commission (<span class="admin_commission_rate"></span>) <span
                                            style="color:red;"><span style="margin:right;"
                                                                     class="admin_commission_val"></span></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <span class="dashed-line"></span>
                    <h5 class="text-center pt-3">
                        {{trans('lang.thank_you')}}
                    </h5>
                    <span class="dashed-line"></span>
                </div>
            </div>
        </div>
        @endsection
        @section('style')
            <style type="text/css">
                .dashed-line {
                    display: block; /* Make the span behave like a block element */
                    width: 100%; /* Make it span the full width of the page */
                    border-bottom: 2px dashed black; /* Create the dotted line */
                    margin: 20px 0; /* Optional: add some space above and below the line */
                }

                #printableArea * {
                    color: black !important;
                }

                /* Promotional Price Styles for Print */
                .promotional-price {
                    color: #28a745 !important;
                    font-weight: bold;
                }

                .original-price {
                    text-decoration: line-through;
                    color: #6c757d;
                }

                .promotional_savings {
                    color: #28a745 !important;
                    font-weight: bold;
                }

                .badge-success {
                    background-color: #28a745;
                    color: white;
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-size: 10px;
                }

                /* Promotional Item Badge Styles for Print */
                .promotional-badge {
                    background: linear-gradient(45deg, #ff6b6b, #ff8e8e) !important;
                    color: white !important;
                    padding: 4px 10px !important;
                    border-radius: 15px !important;
                    font-size: 9px !important;
                    font-weight: bold !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.5px !important;
                    box-shadow: 0 2px 6px rgba(255, 107, 107, 0.4) !important;
                    display: inline-block !important;
                    margin-top: 4px !important;
                    text-align: center !important;
                    width: fit-content !important;
                    border: none !important;
                    outline: none !important;
                }

                /* Promotional item row styling for print */
                .promotional-item-row {
                    background: linear-gradient(90deg, rgba(255, 107, 107, 0.05), rgba(255, 142, 142, 0.05));
                    border-left: 3px solid #ff6b6b;
                }

                .promotional-item-row td {
                    position: relative;
                }

                @media print {
                    @page {
                        size: portrait;
                    }

                    .non-printable {
                        display: none;
                    }

                    .printable {
                        display: block;
                        font-family: emoji !important;
                    }

                    #printableArea {
                        width: 400px;
                    }

                    body {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                        font-family: emoji !important;
                    }
                }
            </style>
            <style type="text/css" media="print">
                @page {
                    size: portrait;
                }

                @page {
                    size: auto;
                    /* auto is the initial value */
                    margin: 2px;
                    /* this affects the margin in the printer settings */
                    font-family: emoji !important;
                }
            </style>
            @section('scripts')
                <script>
                    // MySQL-based: Order data passed from controller
                    var id = "<?php echo $id; ?>";
                    var order = @json($order);
                    var currency = @json($currency ?? (object)[]);

                    // Currency settings
                    var currentCurrency = currency?.symbol || '₹';
                    var currencyAtRight = currency?.symbolAtRight || false;
                    var decimal_degits = currency?.decimal_degits || 2;
                    var place_image = '{{ asset("images/placeholder.png") }}';
                    // Initialize accumulators used in builders
                    var total_price = 0;
                    var total_addon_price = 0;
                    var total_item_price = 0;

                    // Populate order details
                    var customerName = (order.user_first_name || '') + ' ' + (order.user_last_name || '');
                    if (!customerName.trim() && order.author && order.author.firstName) {
                        customerName = (order.author.firstName || '') + ' ' + (order.author.lastName || '');
                    }
                    $(".customerName").text(customerName.trim() || 'N/A');
                    $(".orderId").text(id);

                    // Order date
                    if (order.createdAt) {
                        try {
                            var date = new Date(order.createdAt);
                            if (!isNaN(date.getTime())) {
                                var dateStr = date.toDateString();
                                var time = date.toLocaleTimeString('en-US');
                                $(".orderDate").text(dateStr + " " + time);
                            } else {
                                $(".orderDate").text(order.createdAt);
                            }
                        } catch(e) {
                            $(".orderDate").text(order.createdAt);
                        }
                    }

                    // Billing address
                            var billingAddressstring = '';
                    if (order.address && order.address.address) {
                        billingAddressstring = order.address.address;
                            }
                    if (order.address && order.address.locality) {
                        billingAddressstring = billingAddressstring + (billingAddressstring ? ', ' : '') + order.address.locality;
                            }
                    if (order.address && order.address.landmark) {
                                billingAddressstring = billingAddressstring + " " + order.address.landmark;
                            }
                            $(".customerAddress").text(billingAddressstring);

                    // Customer phone
                    var customerPhone = order.user_phone || (order.author && order.author.phoneNumber) || '';
                    $(".customerPhone").text(customerPhone ? shortEditNumber(customerPhone) : "");

                    // Store/vendor information
                    if (order.vendor_title) {
                        $('.storeName').html(order.vendor_title);
                    }
                    if (order.vendor_phone) {
                        $('.storePhone').text(shortEditNumber(order.vendor_phone));
                    }
                    if (order.vendor_location) {
                        $('.storeAddress').text(order.vendor_location);
                    }
                    if (order.vendor_photo) {
                        $('.resturant-img').attr('src', order.vendor_photo);
                                } else {
                        $('.resturant-img').attr('src', place_image);
                    }

                    // Product list
                    var append_procucts_list = document.getElementById('order_products');
                            append_procucts_list.innerHTML = '';

                    // Build product list
                    var productsListHTML = buildHTMLProductsList(order.products || []);
                            if (productsListHTML != '') {
                                append_procucts_list.innerHTML = productsListHTML;
                            }

                    // Fill order summary
                            fillPrintOrderSummary(order);

                    function buildHTMLProductsList(snapshotsProducts) {
                        var html = '';
                        var alldata = [];
                        var number = [];
                        var totalProductPrice = 0;
                        snapshotsProducts.forEach((product) => {
                            var val = product;
                            html = html + '<tr>';
                            var extra_html = '';
                            if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                                extra_html = extra_html + '<span>';
                                var extra_count = 1;
                                try {
                                    product.extras.forEach((extra) => {
                                        if (extra_count > 1) {
                                            extra_html = extra_html + ',' + extra;
                                        } else {
                                            extra_html = extra_html + extra;
                                        }
                                        extra_count++;
                                    })
                                } catch (error) {
                                }
                                extra_html = extra_html + '</span>';
                            }
                            html = html + '<td class="order-product"><div class="order-product-box">';
                            if (val.photo != '' && val.photo != null) {
                                html = html + '<img onerror="this.onerror=null;this.src=\'' + place_image + '\'" class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + val.photo + '" alt="image">';
                            } else {
                                html = html + '<img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + place_image + '" alt="image">';
                            }
                            html = html + '</div><div class="orders-tracking"><h6>' + val.name + '</h6><div class="orders-tracking-item-details">';
                            if (extra_count > 1 || product.size) {
                            }
                            if (extra_count > 1) {
                                html = html + '<div class="extra"><span>{{trans("lang.extras")}} :</span><span class="ext-item">' + extra_html + '</span></div>';
                            }
                            if (product.size) {
                                html = html + '<div class="type"><span>{{trans("lang.type")}} :</span><span class="ext-size">' + product.size + '</span></div>';
                            }
                            if (product.variant_info) {
                                html += '<div class="variant-info">';
                                html += '<ul>';
                                $.each(product.variant_info.variant_options, function (label, value) {
                                    html += '<li class="variant"><span class="label">' + label + '</span><span class="value">' + value + '</span></li>';
                                });
                                html += '</ul>';
                                html += '</div>';
                            }
                            // HIERARCHY: 1. Promo price (handled by getPromotionalPrice), 2. discountPrice (>0), 3. price
                            var final_price = '';
                            if (val.discountPrice != 0 && val.discountPrice != "" && val.discountPrice != null && !isNaN(val.discountPrice) && parseFloat(val.discountPrice) > 0) {
                                final_price = parseFloat(val.discountPrice);
                                console.log('🎯 Using discountPrice (Hierarchy 2):', final_price);
                            } else {
                                final_price = parseFloat(val.price);
                                console.log('🎯 Using regular price (Hierarchy 3):', final_price);
                            }
                            price_item = final_price.toFixed(decimal_degits);
                            totalProductPrice = final_price * parseInt(val.quantity);
                            var extras_price = 0;
                            if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                                extras_price_item = (parseFloat(val.extras_price) * parseInt(val.quantity)).toFixed(2);
                                if (parseFloat(extras_price_item) != NaN && val.extras_price != undefined) {
                                    extras_price = extras_price_item;
                                }
                                totalProductPrice = parseFloat(extras_price) + parseFloat(totalProductPrice);
                            }
                            totalProductPrice = parseFloat(totalProductPrice).toFixed(decimal_degits);
                            if (currencyAtRight) {
                                price_val = parseFloat(price_item).toFixed(decimal_degits) + "" + currentCurrency;
                                extras_price_val = parseFloat(extras_price).toFixed(decimal_degits) + "" + currentCurrency;
                                totalProductPrice_val = parseFloat(totalProductPrice).toFixed(decimal_degits) + "" + currentCurrency;
                            } else {
                                price_val = currentCurrency + "" + parseFloat(price_item).toFixed(decimal_degits);
                                extras_price_val = currentCurrency + "" + parseFloat(extras_price).toFixed(decimal_degits);
                                totalProductPrice_val = currentCurrency + "" + parseFloat(totalProductPrice).toFixed(decimal_degits);
                            }
                            html = html + '</div></div></td>';
                            html = html + '<td>' + price_val + '</td><td>' + val.quantity + '</td><td> + ' + extras_price_val + '</td><td>  ' + totalProductPrice_val + '</td>';
                            html = html + '</tr>';
                            total_price += parseFloat(totalProductPrice);
                            total_addon_price += parseFloat(extras_price);
                            total_item_price += parseFloat(price_item);
                        });
                        totalProductPrice = 0;
                        if (currencyAtRight) {
                            total_item_price = parseFloat(total_item_price).toFixed(decimal_degits) + "" + currentCurrency;
                            total_addon_price = parseFloat(total_addon_price).toFixed(decimal_degits) + "" + currentCurrency;
                            $('.total_price').text(parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency);
                        } else {
                            total_item_price = currentCurrency + "" + parseFloat(total_item_price).toFixed(decimal_degits);
                            total_addon_price = currentCurrency + "" + parseFloat(total_addon_price).toFixed(decimal_degits);
                            $('.total_price').text(currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits));
                        }
                        $('.total_item_price').text(total_item_price);
                        $('.total_addon_price').text(total_addon_price);
                        return html;
                    }

                    // --- PATCH: Sync print total logic with edit page ---
                    function buildHTMLProductstotal(snapshotsProducts) {
                        var html = '';
                        var alldata = [];
                        var number = [];
                        var adminCommission = snapshotsProducts.adminCommission;
                        var adminCommissionType = snapshotsProducts.adminCommissionType;
                        var discount = snapshotsProducts.discount;
                        var couponCode = snapshotsProducts.couponCode;
                        var extras = snapshotsProducts.extras;
                        var extras_price = snapshotsProducts.extras_price;
                        var rejectedByDrivers = snapshotsProducts.rejectedByDrivers;
                        var takeAway = snapshotsProducts.takeAway;
                        var tip_amount = snapshotsProducts.tip_amount;
                        var notes = snapshotsProducts.notes;
                        var tax_amount = snapshotsProducts.vendor.tax_amount;
                        var status = snapshotsProducts.status;
                        var products = snapshotsProducts.products;
                        var deliveryCharge = snapshotsProducts.deliveryCharge;
                        var specialDiscount = snapshotsProducts.specialDiscount;
                        var intRegex = /^\d+$/;
                        var floatRegex = /^((\d+(\.\d+)?)|((\d+\.)?\d+))$/;
                        var baseDeliveryCharge = 23; // default, override with settings if available
                        var gstRate = 18;
                        var sgstRate = 5;
                        var subtotal = 0;
                        var decimal_degits = decimal_degits || 2;
                        var currencyAtRight = currencyAtRight || false;
                        var currentCurrency = currentCurrency || '₹';

                        // Calculate subtotal from products
                        if (products) {
                            products.forEach((product) => {
                                var price = (product.discountPrice && parseFloat(product.discountPrice) > 0)
                                    ? parseFloat(product.discountPrice)
                                    : parseFloat(product.price);
                                subtotal += price * (parseInt(product.quantity) || 1);
                            });
                        }

                        // Calculate total price including extras
                        var total_price = subtotal;
                        if (intRegex.test(extras_price) || floatRegex.test(extras_price)) {
                            total_price += parseFloat(extras_price);
                        }
                        var priceWithCommision = total_price;

                        // Apply discounts
                        if (intRegex.test(discount) || floatRegex.test(discount)) {
                            discount = parseFloat(discount).toFixed(decimal_degits);
                            total_price -= parseFloat(discount);
                        }
                        if (specialDiscount && specialDiscount.special_discount) {
                            var special_discount = parseFloat(specialDiscount.special_discount).toFixed(decimal_degits);
                            total_price -= parseFloat(special_discount);
                        }

                        // Calculate taxes
                        var sgst = subtotal * (sgstRate / 100); // SGST on subtotal only
                        var gst = 0;
                        if (parseFloat(deliveryCharge) > 0) {
                            // If delivery charge equals base delivery charge (₹23), only calculate GST once
                            if (parseFloat(deliveryCharge) === baseDeliveryCharge) {
                                gst = baseDeliveryCharge * (gstRate / 100); // 18% of base delivery charge only
                            } else {
                                // If delivery charge is different from base delivery charge, calculate GST on actual delivery charge + base delivery charge
                                gst = (parseFloat(deliveryCharge) * (gstRate / 100)) + (baseDeliveryCharge * (gstRate / 100)); // 18% of delivery charge + 18% of base delivery charge
                            }
                        } else {
                            gst = baseDeliveryCharge * (gstRate / 100); // 18% of base delivery charge only
                        }

                        var total_tax_amount = sgst + gst;

                        // Calculate final total
                        var totalAmount = parseFloat(total_price) + parseFloat(total_tax_amount);

                        // Add delivery charge to total
                        if (intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) {
                            deliveryCharge = parseFloat(deliveryCharge).toFixed(decimal_degits);
                            totalAmount += parseFloat(deliveryCharge);
                        }

                        // Add tip amount to total
                        if (intRegex.test(tip_amount) || floatRegex.test(tip_amount)) {
                            tip_amount = parseFloat(tip_amount).toFixed(decimal_degits);
                            totalAmount += parseFloat(tip_amount);
                        }
                        html += '<tr><td class="label">Items Price :</td><td>' + subtotal.toFixed(decimal_degits) + currentCurrency + '</td></tr>';
                        html += '<tr><td class="label">Addon Cost :</td><td>' + (extras_price ? parseFloat(extras_price).toFixed(decimal_degits) : '0.00') + currentCurrency + '</td></tr>';
                        html += '<tr><td class="label">Subtotal :</td><td>' + subtotal.toFixed(decimal_degits) + currentCurrency + '</td></tr>';
                        if (intRegex.test(discount) || floatRegex.test(discount)) {
                            var discount_val = currencyAtRight ? discount + currentCurrency : currentCurrency + discount;
                            html += '<tr><td class="label">Discount :</td><td>-' + discount_val + '</td></tr>';
                        }
                        if (specialDiscount && specialDiscount.special_discount) {
                            var special_discount_val = currencyAtRight ? parseFloat(specialDiscount.special_discount).toFixed(decimal_degits) + currentCurrency : currentCurrency + parseFloat(specialDiscount.special_discount).toFixed(decimal_degits);
                            html += '<tr><td class="label">Special Offer Discount :</td><td>-' + special_discount_val + '</td></tr>';
                        }
                        html += '<tr><td class="label">GST (18%) :</td><td>₹ ' + gst.toFixed(decimal_degits) + '</td></tr>';
                        html += '<tr><td class="label">SGST (5%) :</td><td>₹ ' + sgst.toFixed(decimal_degits) + '</td></tr>';
                        if (intRegex.test(tip_amount) || floatRegex.test(tip_amount)) {
                            html += '<tr><td class="label">DM Tips :</td><td>+ ' + tip_amount + currentCurrency + '</td></tr>';
                        }
                        if (intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) {
                            html += '<tr><td class="label">Delivery Fee :</td><td>+ ' + deliveryCharge + currentCurrency + '</td></tr>';
                        }
                        html += '<tr><td class="label"><b>Total :</b></td><td><b>' + currentCurrency + parseFloat(totalAmount).toFixed(decimal_degits) + '</b></td></tr>';
                        // Admin Commission (optional, for reference)
                        var adminCommHtml = "";
                        var adminCommission_val = 0;
                        var basePrice = 0;
                        if (adminCommissionType == "Percent") {
                            basePrice = (priceWithCommision / (1 + (parseFloat(adminCommission) / 100)));
                            adminCommission = parseFloat(priceWithCommision - basePrice);
                            adminCommHtml = "(" + adminCommissionType + "%)";
                        } else {
                            basePrice = priceWithCommision - adminCommission;
                            adminCommission = parseFloat(priceWithCommision - basePrice);
                        }
                        if (currencyAtRight) {
                            adminCommission_val = parseFloat(adminCommission).toFixed(decimal_degits) + "" + currentCurrency;
                        } else {
                            adminCommission_val = currentCurrency + "" + parseFloat(adminCommission).toFixed(decimal_degits);
                        }
                        if (adminCommission) {
                            html += '<tr><td class="label"><small>Admin Commission ' + adminCommHtml + '</small></td><td style="color:red"><small>( ' + adminCommission_val + ' )</small></td></tr>';
                        }
                        if (notes) {
                            html += '<tr><td class="label">Notes</td><td class="adminCommission_val">' + notes + '</td></tr>';
                        }
                        return html;
                    }

                    // --- END PATCH ---
                    function printDiv(divName) {
                        var css = '@page { size: portrait; }',
                            head = document.head || document.getElementsByTagName('head')[0],
                            style = document.createElement('style');
                        style.type = 'text/css';
                        style.media = 'print';
                        if (style.styleSheet) {
                            style.styleSheet.cssText = css;
                        } else {
                            style.appendChild(document.createTextNode(css));
                        }
                        head.appendChild(style);
                        var printContents = document.getElementById(divName).innerHTML;
                        var originalContents = document.body.innerHTML;
                        document.body.innerHTML = printContents;
                        window.print();
                        document.body.innerHTML = originalContents;
                    }

                    // --- Fill values dynamically using the same logic as edit.blade.php ---
                    function fillPrintOrderSummary(order) {
                        // Reference: buildHTMLProductstotal from edit.blade.php
                        var intRegex = /^\d+$/;
                        var floatRegex = /^((\d+(\.\d+)?)|((\d+\.)?\d+))$/;
                        var baseDeliveryCharge = 23; // default, same as edit.blade.php
                        var gstRate = 18;
                        var sgstRate = 5;
                        var subtotal = 0;
                        var decimal_degits = decimal_degits || 2;
                        var currencyAtRight = currencyAtRight || false;
                        var currentCurrency = currentCurrency || '₹';
                        var products = order.products;

                        // Calculate subtotal from products (MySQL) – no dependency on promotionalTotals
                        if (products && products.length) {
                            products.forEach(function (product) {
                                var price = (product.discountPrice && parseFloat(product.discountPrice) > 0)
                                    ? parseFloat(product.discountPrice)
                                    : parseFloat(product.price || 0);
                                var qty = parseInt(product.quantity) || 1;
                                var extras = parseFloat(product.extras_price || 0) * qty;
                                subtotal += (price * qty) + (isNaN(extras) ? 0 : extras);
                            });
                        }

                        // Calculate total price including extras
                        var total_price = subtotal;
                        if (intRegex.test(order.extras_price) || floatRegex.test(order.extras_price)) {
                            total_price += parseFloat(order.extras_price);
                        }
                        var priceWithCommision = total_price;

                        // Apply discounts
                        var discount = order.discount;
                        if (intRegex.test(discount) || floatRegex.test(discount)) {
                            discount = parseFloat(discount).toFixed(decimal_degits);
                            total_price -= parseFloat(discount);
                        }

                        var specialDiscount = order.specialDiscount;
                        var special_discount = 0;
                        if (specialDiscount && specialDiscount.special_discount) {
                            special_discount = parseFloat(specialDiscount.special_discount).toFixed(decimal_degits);
                            total_price -= parseFloat(special_discount);
                        }

                        // Calculate taxes
                        var sgst = subtotal * (sgstRate / 100); // SGST on subtotal only

                        // Use delivery charge from order data (same as edit.blade.php)
                        var deliveryCharge = order.deliveryCharge || 0;

                        var gst = 0;

                        // Debug delivery charge
                        console.log('=== fillPrintOrderSummary Debug ===');
                        console.log('restaurantorders deliveryCharge:', order.deliveryCharge);
                        console.log('Final deliveryCharge:', deliveryCharge);
                        console.log('Base delivery charge:', baseDeliveryCharge);
                        console.log('GST calculation logic:');
                        console.log('- Delivery charge:', deliveryCharge);
                        console.log('- Base delivery charge:', baseDeliveryCharge);
                        console.log('- GST rate:', gstRate + '%');

                        if (parseFloat(deliveryCharge) > 0) {
                            // If delivery charge equals base delivery charge (₹23), only calculate GST once
                            if (parseFloat(deliveryCharge) === baseDeliveryCharge) {
                                gst = baseDeliveryCharge * (gstRate / 100); // 18% of base delivery charge only
                            } else {
                                // If delivery charge is different from base delivery charge, calculate GST on actual delivery charge + base delivery charge
                                gst = (parseFloat(deliveryCharge) * (gstRate / 100)) + (baseDeliveryCharge * (gstRate / 100)); // 18% of delivery charge + 18% of base delivery charge
                            }
                        } else {
                            gst = baseDeliveryCharge * (gstRate / 100); // 18% of base delivery charge only
                        }

                        var total_tax_amount = sgst + gst;
                        console.log('GST calculation result:');
                        console.log('- SGST (5% on subtotal):', sgst.toFixed(2));
                        console.log('- GST (18% on delivery):', gst.toFixed(2));
                        console.log('- Total tax amount:', total_tax_amount.toFixed(2));

                        // Calculate final total
                        var totalAmount = parseFloat(total_price) + parseFloat(total_tax_amount);
                        console.log('Total after taxes:', totalAmount);
                        console.log('Subtotal:', subtotal);
                        console.log('Total price after discounts:', total_price);
                        console.log('SGST:', sgst);
                        console.log('GST:', gst);
                        console.log('Total tax amount:', total_tax_amount);

                        // Add delivery charge to total
                        console.log('Delivery charge before adding:', deliveryCharge);
                        console.log('Delivery charge type:', typeof deliveryCharge);
                        console.log('Is delivery charge valid number?', intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge));

                        // Check if delivery charge is a valid number (either by regex or parseFloat)
                        var deliveryChargeNum = parseFloat(deliveryCharge);
                        if ((intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) && !isNaN(deliveryChargeNum) && deliveryChargeNum > 0) {
                            deliveryCharge = deliveryChargeNum.toFixed(decimal_degits);
                            totalAmount += deliveryChargeNum;
                            console.log('Total after adding delivery charge:', totalAmount);
                        } else {
                            console.log('Delivery charge not added - invalid format or zero');
                        }

                        // Add tip amount to total
                        var tip_amount = order.tip_amount;
                        console.log('Tip amount before adding:', tip_amount);
                        console.log('Tip amount type:', typeof tip_amount);
                        console.log('Is tip amount valid number?', intRegex.test(tip_amount) || floatRegex.test(tip_amount));

                        // Check if tip amount is a valid number
                        var tipAmountNum = parseFloat(tip_amount);
                        if ((intRegex.test(tip_amount) || floatRegex.test(tip_amount)) && !isNaN(tipAmountNum) && tipAmountNum > 0) {
                            tip_amount = tipAmountNum.toFixed(decimal_degits);
                            totalAmount += tipAmountNum;
                            console.log('Total after adding tip amount:', totalAmount);
                        } else {
                            console.log('Tip amount not added - invalid format or zero');
                        }

                        // Format helpers
                        function fmt(val) {
                            return currencyAtRight ? (val + currentCurrency) : (currentCurrency + val);
                        }

                        // Fill values
                        document.querySelector('.sub_total_val').textContent = fmt(parseFloat(subtotal).toFixed(decimal_degits));
                        document.querySelector('.discount_val').textContent = '-' + fmt(discount || '0.00');
                        document.querySelector('.special_discount_val').textContent = '-' + fmt(special_discount || '0.00');
                        document.querySelector('.sgst_rate').textContent = sgstRate;
                        document.querySelector('.sgst_val').textContent = sgst.toFixed(decimal_degits);
                        document.querySelector('.gst_rate').textContent = gstRate;
                        document.querySelector('.gst_val').textContent = gst.toFixed(decimal_degits);
                        document.querySelector('.delivery_charge_val').textContent = fmt(deliveryCharge || '0.00');
                        document.querySelector('.tip_amount_val').textContent = fmt(tip_amount || '0.00');
                        document.querySelector('.total_amount_val').textContent = fmt(parseFloat(totalAmount).toFixed(decimal_degits));
                        console.log('Final total amount displayed:', totalAmount);
                        console.log('Final total amount formatted:', fmt(parseFloat(totalAmount).toFixed(decimal_degits)));
                        // Admin Commission
                        var adminCommission = order.adminCommission;
                        var adminCommissionType = order.adminCommissionType;
                        var adminCommHtml = '';
                        var adminCommission_val = 0;
                        var basePrice = 0;
                        if (adminCommissionType == "Percent") {
                            basePrice = (priceWithCommision / (1 + (parseFloat(adminCommission) / 100)));
                            adminCommission = parseFloat(priceWithCommision - basePrice);
                            adminCommHtml = "(" + adminCommission + "%)";
                        } else {
                            basePrice = priceWithCommision - adminCommission;
                            adminCommission = parseFloat(priceWithCommision - basePrice);
                        }
                        adminCommission_val = fmt(parseFloat(adminCommission).toFixed(decimal_degits));
                        document.querySelector('.admin_commission_rate').textContent = adminCommissionType == "Percent" ? order.adminCommission + "%" : '';
                        document.querySelector('.admin_commission_val').textContent = '( ' + adminCommission_val + ' )';
                    }

                    // Usage: after fetching order data, call fillPrintOrderSummary(order)

                    // ========== PROMOTIONAL PRICING FUNCTIONS ==========

                    // Clean and robust promotional price checking function with proper hierarchy
                    async function getPromotionalPrice(product, vendorID) {
                        // MySQL version: simply use discountPrice (>0) else price
                        var price = (product.discountPrice && parseFloat(product.discountPrice) > 0)
                            ? parseFloat(product.discountPrice)
                            : parseFloat(product.price);
                        return {
                            price: price,
                            isPromotional: false,
                            promotionId: null,
                            originalPrice: price
                        };
                    }

                    // Enhanced function to build product list with promotional pricing
                    async function buildHTMLProductsListWithPromotions(snapshotsProducts, vendorID) {
                        try {
                            console.log('🎯 ===== BUILDING PRODUCT LIST WITH PROMOTIONS (PRINT) =====');
                            console.log('🎯 Products:', snapshotsProducts.length);
                            console.log('🎯 Vendor ID:', vendorID);

                            var html = '';
                            var alldata = [];
                            var number = [];
                            var totalProductPrice = 0;

                            for (const product of snapshotsProducts) {
                                try {
                                    console.log('🎯 ===== PROCESSING PRODUCT FOR LIST (PRINT) =====');
                                    console.log('🎯 Product:', product.name, 'ID:', product.id);

                                    // Get promotional price for this product
                                    const priceInfo = await getPromotionalPrice(product, vendorID);
                                    console.log('🎯 Price Info Result:', priceInfo);

                                    var val = product;
                                    html = html + '<tr data-product-id="' + val.id + '">';
                                    var extra_html = '';
                                    if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                                        extra_html = extra_html + '<span>';
                                        var extra_count = 1;
                                        try {
                                            product.extras.forEach((extra) => {
                                                if (extra_count > 1) {
                                                    extra_html = extra_html + ',' + extra;
                                                } else {
                                                    extra_html = extra_html + extra;
                                                }
                                                extra_count++;
                                            })
                                        } catch (error) {
                                        }
                                        extra_html = extra_html + '</span>';
                                    }
                                    html = html + '<td class="order-product"><div class="order-product-box">';
                                    if (val.photo != '' && val.photo != null) {
                                        html = html + '<img onerror="this.onerror=null;this.src=\'' + place_image + '\'" class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + val.photo + '" alt="image">';
                                    } else {
                                        html = html + '<img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + place_image + '" alt="image">';
                                    }
                                    html = html + '</div><div class="orders-tracking"><h6>' + val.name + '</h6><div class="orders-tracking-item-details">';
                                    if (extra_count > 1 || product.size) {
                                    }
                                    if (extra_count > 1) {
                                        html = html + '<div class="extra"><span>{{trans("lang.extras")}} :</span><span class="ext-item">' + extra_html + '</span></div>';
                                    }
                                    if (product.size) {
                                        html = html + '<div class="type"><span>{{trans("lang.type")}} :</span><span class="ext-size">' + product.size + '</span></div>';
                                    }
                                    if (product.variant_info) {
                                        html += '<div class="variant-info">';
                                        html += '<ul>';
                                        $.each(product.variant_info.variant_options, function (label, value) {
                                            html += '<li class="variant"><span class="label">' + label + '</span><span class="value">' + value + '</span></li>';
                                        });
                                        html += '</ul>';
                                        html += '</div>';
                                    }

                                    // Use promotional price if available, otherwise use original price
                                    var final_price = priceInfo.price;
                                    console.log('🎯 Using final price:', final_price, 'for product:', product.name);
                                    console.log('🎯 Is promotional:', priceInfo.isPromotional);

                                    price_item = final_price.toFixed(decimal_degits);
                                    totalProductPrice = parseFloat(price_item) * parseInt(val.quantity);
                                    var extras_price = 0;
                                    if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                                        extras_price_item = (parseFloat(val.extras_price) * parseInt(val.quantity)).toFixed(2);
                                        if (parseFloat(extras_price_item) != NaN && val.extras_price != undefined) {
                                            extras_price = extras_price_item;
                                        }
                                        totalProductPrice = parseFloat(extras_price) + parseFloat(totalProductPrice);
                                    }
                                    totalProductPrice = parseFloat(totalProductPrice).toFixed(decimal_degits);
                                    if (currencyAtRight) {
                                        price_val = parseFloat(price_item).toFixed(decimal_degits) + "" + currentCurrency;
                                        extras_price_val = parseFloat(extras_price).toFixed(decimal_degits) + "" + currentCurrency;
                                        totalProductPrice_val = parseFloat(totalProductPrice).toFixed(decimal_degits) + "" + currentCurrency;
                                    } else {
                                        price_val = currentCurrency + "" + parseFloat(price_item).toFixed(decimal_degits);
                                        extras_price_val = currentCurrency + "" + parseFloat(extras_price).toFixed(decimal_degits);
                                        totalProductPrice_val = currentCurrency + "" + parseFloat(totalProductPrice).toFixed(decimal_degits);
                                    }

                                    // Add promotional badge and styling if this is a promotional item
                                    var promotionalBadge = '';
                                    var rowClass = '';
                                    if (priceInfo.isPromotional) {
                                        promotionalBadge = '<div class="promotional-badge" style="background: linear-gradient(45deg, #ff6b6b, #ff8e8e); color: white; padding: 4px 10px; border-radius: 15px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(255, 107, 107, 0.4); display: inline-block; margin-top: 4px; text-align: center; width: fit-content; border: none; outline: none;">🎯 PROMO</div>';
                                        rowClass = ' promotional-item-row';
                                        console.log('🎯 Adding promotional badge for:', product.name);
                                    }

                                    html = html + '</div></div></td>';
                                    html = html + '<td>' + price_val + '</td><td>' + val.quantity + '</td><td> + ' + extras_price_val + '</td><td>  ' + totalProductPrice_val + '</td>';
                                    html = html + '</tr>';

                                    // Update the product name with promotional badge UNDER the name
                                    if (priceInfo.isPromotional) {
                                        html = html.replace(
                                            '<h6>' + val.name + '</h6>',
                                            '<h6>' + val.name + '</h6>' + promotionalBadge
                                        );
                                        // Add promotional row class to the tr element
                                        html = html.replace(
                                            '<tr data-product-id="' + val.id + '">',
                                            '<tr data-product-id="' + val.id + '" class="' + rowClass + '">'
                                        );
                                    }

                                    total_price += parseFloat(totalProductPrice);
                                    total_addon_price += parseFloat(extras_price);
                                    total_item_price += parseFloat(price_item);
                                } catch (error) {
                                    console.error('❌ Error processing product:', product.name, error);
                                    // Fallback to original pricing if promotional pricing fails
                                    console.log('🔄 Falling back to original pricing for:', product.name);
                                }
                            }
                            totalProductPrice = 0;
                            if (currencyAtRight) {
                                total_item_price = parseFloat(total_item_price).toFixed(decimal_degits) + "" + currentCurrency;
                                total_addon_price = parseFloat(total_addon_price).toFixed(decimal_degits) + "" + currentCurrency;
                                $('.total_price').text(parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency);
                            } else {
                                total_item_price = currentCurrency + "" + parseFloat(total_item_price).toFixed(decimal_degits);
                                total_addon_price = currentCurrency + "" + parseFloat(total_addon_price).toFixed(decimal_degits);
                                $('.total_price').text(currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits));
                            }
                            $('.total_item_price').text(total_item_price);
                            $('.total_addon_price').text(total_addon_price);
                            console.log('🎯 ===== PRODUCT LIST BUILD COMPLETE (PRINT) =====');
                            return html;
                        } catch (error) {
                            console.error('❌ Error in buildHTMLProductsListWithPromotions:', error);
                            // Fallback to original function if promotional function fails
                            console.log('🔄 Falling back to original buildHTMLProductsList function');
                            return buildHTMLProductsList(snapshotsProducts);
                        }
                    }

                    // Enhanced function to calculate totals with promotional pricing
                    async function calculatePromotionalTotals(products, vendorID) {
                        console.log('💰 ===== CALCULATING PROMOTIONAL TOTALS (PRINT) =====');
                        console.log('💰 Products:', products.length);
                        console.log('💰 Vendor ID:', vendorID);

                        let promotionalSubtotal = 0;
                        let originalSubtotal = 0;
                        let promotionalSavings = 0;
                        let promotionalItems = [];
                        let regularItems = [];

                        for (const product of products) {
                            console.log('💰 ===== CALCULATING PRODUCT TOTAL (PRINT) =====');
                            console.log('💰 Product:', product.name, 'ID:', product.id);

                            const priceInfo = await getPromotionalPrice(product, vendorID);
                            console.log('💰 Price Info Result:', priceInfo);

                            const quantity = parseInt(product.quantity) || 1;
                            // Use discountPrice only if it exists and is greater than 0, otherwise use price
                            const originalPrice = (product.discountPrice && parseFloat(product.discountPrice) > 0)
                                ? parseFloat(product.discountPrice)
                                : parseFloat(product.price);
                            const promotionalPrice = priceInfo.price;

                            if (priceInfo.isPromotional) {
                                const itemTotal = promotionalPrice * quantity;
                                const originalTotal = originalPrice * quantity;
                                const savings = originalTotal - itemTotal;

                                promotionalSubtotal += itemTotal;
                                originalSubtotal += originalTotal;
                                promotionalSavings += savings;

                                promotionalItems.push({
                                    name: product.name,
                                    originalPrice: originalPrice,
                                    promotionalPrice: promotionalPrice,
                                    quantity: quantity,
                                    originalTotal: originalTotal,
                                    promotionalTotal: itemTotal,
                                    savings: savings
                                });

                                console.log('💰 ===== PROMOTIONAL ITEM CALCULATION (PRINT) =====');
                                console.log('💰 Product:', product.name);
                                console.log('💰 Original Price:', originalPrice);
                                console.log('💰 Promotional Price:', promotionalPrice);
                                console.log('💰 Quantity:', quantity);
                                console.log('💰 Original Total:', originalTotal);
                                console.log('💰 Promotional Total:', itemTotal);
                                console.log('💰 Savings:', savings);
                                console.log('💰 Running Promotional Subtotal:', promotionalSubtotal);
                                console.log('💰 Running Promotional Savings:', promotionalSavings);
                            } else {
                                const itemTotal = originalPrice * quantity;
                                promotionalSubtotal += itemTotal;
                                originalSubtotal += itemTotal;

                                regularItems.push({
                                    name: product.name,
                                    price: originalPrice,
                                    quantity: quantity,
                                    total: itemTotal
                                });

                                console.log('💰 ===== REGULAR ITEM CALCULATION (PRINT) =====');
                                console.log('💰 Product:', product.name);
                                console.log('💰 Price:', originalPrice);
                                console.log('💰 Quantity:', quantity);
                                console.log('💰 Total:', itemTotal);
                                console.log('💰 Running Promotional Subtotal:', promotionalSubtotal);
                            }
                        }

                        console.log('💰 ===== FINAL CALCULATION SUMMARY (PRINT) =====');
                        console.log('💰 Original Subtotal:', originalSubtotal);
                        console.log('💰 Promotional Subtotal:', promotionalSubtotal);
                        console.log('💰 Total Promotional Savings:', promotionalSavings);
                        console.log('💰 Promotional Items:', promotionalItems.length);
                        console.log('💰 Regular Items:', regularItems.length);

                        return {
                            promotionalSubtotal: promotionalSubtotal,
                            originalSubtotal: originalSubtotal,
                            promotionalSavings: promotionalSavings,
                            promotionalItems: promotionalItems,
                            regularItems: regularItems
                        };
                    }
                </script>
@endsection
