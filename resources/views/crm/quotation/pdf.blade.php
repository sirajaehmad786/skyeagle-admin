<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Eagle Trip</title>
    @php
        $isPdfPreview = filter_var($isPdfPreview ?? false, FILTER_VALIDATE_BOOLEAN);
        $pdfPublic = function (string $relativeToPublic) use ($isPdfPreview) {
            $relativeToPublic = ltrim(str_replace('\\', '/', $relativeToPublic), '/');
            return $isPdfPreview ? asset($relativeToPublic) : public_path($relativeToPublic);
        };
        $pdfStorageUrl = function (?string $pathUnderStorageDisk) use ($isPdfPreview) {
            if ($pathUnderStorageDisk === null || $pathUnderStorageDisk === '') {
                return null;
            }
            $pathUnderStorageDisk = ltrim(str_replace('\\', '/', $pathUnderStorageDisk), '/');
            if ($isPdfPreview) {
                $full = storage_path('app/public/' . $pathUnderStorageDisk);
                return is_file($full) ? asset('storage/' . $pathUnderStorageDisk) : null;
            }
            return public_path('storage/' . $pathUnderStorageDisk);
        };
    @endphp
    <!-- Font Awesome Icon CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <style>
        /* Single page: size set dynamically in controller; avoid breaks so all content stays on one page */
        @if ($isPdfPreview)
        @page {
            size: auto;
            margin: 0;
        }
        @else
        @page {
            size: 595pt {{ $pageHeightPt ?? 15000 }}pt;
            margin: 0;
        }
        @endif

        html,
        body {
            margin: 0;
            padding: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            page-break-inside: avoid;
        }

        .parent-table {
            page-break-inside: avoid;
        }

        h2 {
            margin-top: 28px;
        }

        .parent-table {
            width: 100%;
            padding: 0;
            margin: 0;
            border: none;
        }

        .header-table {
            width: 100%;
            background-color: #00273e;
            color: #ffffff;
            text-align: center;
        }

        .logo {
            width: 150px;
            margin: 50px auto 15px;
        }

        .bannertitle {
            font-size: 40px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 0;
            color: #ffffff;
        }

        .subtitle {
            font-size: 25px;
            margin-top: 13px;
            color: #2adf75;
            margin-bottom: 46px;
        }

        .main-content-table {
            width: 96%;
            margin: 15px;
        }

        .travel-details {
            border: 1px solid #e0e0e0;
            width: 100%;
            border-radius: 5px;
        }

        .travel-details tr td {
            padding: 17px 15px;
            font-size: 15px;
        }

        .travel-details tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        .note {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            color: #222;
            text-align: right;
            margin-top: 15px;
        }

        .bifurcation-line {
            margin: 4px 0;
            font-size: 15px;
            overflow: hidden;
        }

        .bifurcation-line .bifurcation-amount {
            float: right;
        }

        .bifurcation-sub {
            font-size: 14px;
            color: #555;
            padding-left: 12px;
        }

        .why-pick-us {
            width: 100%;
            text-align: center;
        }

        .why-pick-us h2 {
            margin-bottom: 15px;
            font-size: 1.8rem;
            font-weight: 700;
            color: #000;
        }

        .why-pick-us h2 span {
            color: #00835c;
        }

        .why-pick-us .icon img {
            width: 45px;
            margin-top: 25px;
        }

        .why-pick-us .icon {
            background: #f28b3f;
            color: #fff;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 15px;
            font-size: 30px;
            position: relative;
            border: 5px solid #ffb579;
        }

        .why-pick-us .title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #000;
            margin-bottom: 5px;
        }

        .why-pick-us .desc {
            color: #4a555e;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .partners-section-h2 {
            font-size: 24px;
            color: #000000;
            margin-top: 70px;
            margin-bottom: 15px;
        }

        .partners-section-h2 span {
            color: #0f9d58;
        }

        .travel-partner {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 5px;
            text-align: center;
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .partner-logo {
            max-width: 150px;
            height: auto;
        }

        .itinerary_title {
            color: #000;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
            border-bottom: 2px solid #d98f34;
            display: inline-block;
            padding-bottom: 5px;

        }

        .daywise_header {
            background-color: #00425F;
            color: white;
            padding: 12px 20px;
            font-weight: bold;
        }

        .car-img {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
        }

        .car-img img {
            width: 100%;
            max-width: 323px;
            border-radius: 8px;
        }

        .car-details h4 {
            margin: 0 0 10px;
            color: #000;
        }

        .car-details div {
            margin: 0;
            margin-right: 1rem;
            color: #555;
            font-size: 15px;
            line-height: 1.5;
        }

        .car-details ul {
            margin-left: 15px;
        }

        .car-details {
            margin-left: 20px;
        }

        .hotel-checkin-details {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            padding-top: 24px;
            margin-top: 16px;
            text-align: left;
            font-size: 17px;
        }

        .inner-details-left {
            border-right: 1px solid #dee2e6;
        }

        .inner-details-rigth {
            padding-left: 20px;
        }

        .hotel-checkin-details p {
            text-transform: uppercase;
            color: #a6aaae;
            margin-top: 2px;
            margin-bottom: 5px;
        }

        /* Check-in/out, Total Room, Nights – same style as flight section labels/values */
        .hotel-checkin-details .check-1 p,
        .hotel-checkin-details .check-2 p {
            color: #555;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 2px;
            margin-top: 0;
        }

        .hotel-checkin-details .check-1 span,
        .hotel-checkin-details .check-2 span {
            color: #222;
            font-weight: 500;
            font-size: 15px;
        }

        .check-2 {
            margin-top: 15px;
        }

        .hotel-checkin-details .stars {
            color: gold;
            font-size: 16px;
        }

        .hotel-image {
            margin-top: 20px;
        }

        .hotel-image img {
            width: 100%;
            max-width: 320px;
            height: auto;
            border-radius: 8px;
        }

        .hotel-details {
            padding-left: 15px;
            margin-top: 20px;
        }

        .hotel-details h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #000000;
            margin-top: 0;
        }

        .hotel-details p {
            font-size: 16px;
            color: #000;
            margin-bottom: 8px;
        }

        /* Hotel detail rows – label and value on one line, no separator between rows */
        .hotel-details .hotel-detail-row {
            padding: 4px 0;
            font-size: 15px;
        }

        .hotel-details .hotel-detail-label {
            color: #555;
            text-transform: uppercase;
            font-size: 12px;
            display: inline;
        }

        .hotel-details .hotel-detail-value {
            color: #222;
            font-weight: 500;
            display: inline;
        }

        .hotel-block {
            padding-bottom: 24px;
        }

        .hotel-block-last {
            padding-bottom: 0;
        }

        .hotel-separation {
            border-top: 4px dashed #dee2e6;
            padding-top: 10px;
            margin-top: 10px;
        }

        .visa-block {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 28px;
            font-size: 15px;
        }

        .visa-block-last {
            margin-bottom: 0;
        }

        .visa-block:last-child {
            margin-bottom: 0;
        }

        .visa-row-cell {
            padding-top: 24px;
        }

        .visa-block h3,
        .hotel-details h3,
        .car-details h3 {
            font-size: 18px;
            font-weight: bold;
            color: #00273e;
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #e8e8e8;
        }

        .visa-block .visa-row {
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .visa-block .visa-row:last-child {
            border-bottom: none;
        }

        .visa-block .visa-label {
            color: #555;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .visa-block .visa-value {
            color: #222;
            font-weight: 500;
        }

        .visa-separation {
            border-top: 4px dashed #dee2e6;
            padding-top: 16px;
            margin-top: 8px;
        }

        .visa-two-col-table {
            width: 100%;
            border-collapse: collapse;
        }

        .visa-two-col-table td {
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
            padding-bottom: 0;
        }

        .visa-two-col-table .visa-row {
            padding-bottom: 8px;
        }

        .mb-0 {
            margin-bottom: 0px !important;
        }

        .mt-0 {
            margin-top: 0px !important;
        }

        .flight-checkin-details {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            text-align: left;
            font-size: 17px;
        }

        .flight-details {
            background-color: #eff0f0;
            border-radius: 0px 0px 6px 6px;
        }

        .flight-details p {
            color: #555555;
            text-transform: uppercase;
            margin: 20px;
        }

        .td-top-border {
            border-top: 2px solid #dee2e6;
        }

        .flight-details span {
            color: #7b7d7f;
            font-size: 20px;
        }

        /* Flight section card (screenshot design: green header + light beige body) */
        .flight-section-card {
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
            margin-bottom: 15px;
        }

        .flight-section-header {
            background-color: #2B894E;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            padding: 12px 20px;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
        }

        .flight-section-body {
            background-color: #F5F5F0;
            padding: 18px 20px;
        }

        .flight-section-body .flight-row {
            padding: 8px 0;
            border-bottom: 1px solid #e8e8e8;
            font-size: 15px;
        }

        .flight-section-body .flight-row:last-child {
            border-bottom: none;
        }

        .flight-section-body .flight-label {
            color: #555;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .flight-section-body .flight-value {
            color: #222;
            font-weight: 500;
        }

        .flight-multi-segment {
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .flight-multi-segment:last-of-type {
            border-bottom: none;
        }

        .flight-multi-segment .flight-label {
            font-size: 11px;
        }

        .flight-multi-segment .flight-value {
            font-size: 15px;
        }

        .flight-arrow-icon {
            width: 24px;
            height: 14px;
            vertical-align: middle;
            margin: 0 4px;
        }

        .flight-two-col-table {
            width: 100%;
            border-collapse: collapse;
        }

        .flight-two-col-table td {
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
            padding-bottom: 0;
        }

        .flight-two-col-table .flight-row {
            padding-bottom: 8px;
        }

        .fligth-cmp-img {
            padding: 20px 20px 20px 0px;
            width: 15%;
        }

        .fligth-cmp-img img {
            border-radius: 5px;
            border: 2px solid #dee2e6;
            width: 200px;
            height: auto;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .text-left {
            text-align: left;
        }

        .text-rigth {
            text-align: right;
        }

        .p-black {
            margin: 0 0 10px 0;
            padding: 0;
            color: #000000;
            font-size: 20px;
            font-weight: 500;
        }

        .p-grey {
            margin: 0 0 10px 0;
            padding: 0;
            color: #7b7d7f;
            font-size: 20px;
            font-weight: 500;
        }

        .timeline {
            display: flex;
            align-items: center;
            gap: 2px;
            margin-bottom: 0px;
            width: 100%
        }

        .time {
            font-size: 20px;
            color: #000000;
            font-weight: 500;
        }

        .line {
            position: relative;
            height: 2px;
            background: #ccc;
            width: 100%;
        }

        .circle {
            width: 10px;
            height: 10px;
            background: #333333;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            transform: translateY(-75%);
            border: 3px solid #cdcfd1;
        }

        .circle.start {
            left: 0;
        }

        .circle.end {
            right: 0;
        }

        .duration {
            position: absolute;
            background: #fff3d1;
            color: #333333;
            font-size: 17px;
            padding: 3px 8px;
            border-radius: 10px;
            left: 50%;
            top: -13px;
            transform: translateX(-50%);
        }

        .inclusion-content {
            background: transparent;
            border-radius: 0;
            padding: 0 0 0 20px;
            width: 100%;
        }

        .inclusion-content p {
            margin: 15px 0 15px 0
        }

        .inclusion-content ul li,
        .inclusion-content ol li {
            margin: 18px 0 15px 15px;
        }

        /* Inclusion / Exclusion section cards (header + background as per screenshot) */
        .inclusion-section-card,
        .exclusion-section-card {
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
            margin-bottom: 15px;
        }

        .inclusion-section-header {
            background-color: #2B894E;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            padding: 12px 20px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .inclusion-section-body {
            background-color: #EAF7ED;
            padding: 18px 20px;
        }

        .inclusion-section-body .inclusion-content {
            background: transparent;
        }

        .inclusion-section-body ul li,
        .inclusion-section-body .inclusion-content ul li {
            color: #2B894E;
        }

        .exclusion-section-header {
            background-color: #C94C4C;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            padding: 12px 20px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .exclusion-section-body {
            background-color: #FDEEEE;
            padding: 18px 20px;
        }

        .exclusion-section-body .inclusion-content {
            background: transparent;
        }

        .exclusion-section-body ul li,
        .exclusion-section-body .inclusion-content ul li {
            color: #C94C4C;
        }

        /* Keep content on one page: avoid breaks inside sections */
        .main-content-table,
        .header-table,
        .travel-details,
        .hotel-info-card,
        .hotel-address-block,
        .flight-checkin-details,
        .inclusion-content {
            page-break-inside: avoid;
        }

        /* Package crafted by - bottom section */
        .crafted-by-section {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .crafted-by-card {
            background: #f7fafc;
            border: 1px solid #dbe4ee;
            border-radius: 10px;
            padding: 18px 22px;
            max-width: 460px;
            margin-left: 0;
        }

        .crafted-by-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .crafted-by-name {
            font-size: 18px;
            font-weight: bold;
            color: #00273e;
            margin-bottom: 12px;
        }

        .crafted-by-profile {
            margin-bottom: 12px;
        }

        .crafted-by-profile-img {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            border: 2px solid #dbe4ee;
            object-fit: cover;
            display: block;
        }

        .crafted-by-row {
            font-size: 14px;
            color: #444;
            margin: 8px 0;
            padding-left: 0;
        }

        .crafted-by-row strong {
            color: #555;
            font-weight: 600;
            min-width: 88px;
            display: inline-block;
        }

        .crafted-by-row .crafted-by-icon {
            width: 14px;
            height: 14px;
            vertical-align: middle;
            margin-right: 6px;
        }

        .crafted-by-divider {
            border-top: 1px solid #dbe4ee;
            margin: 8px 0 10px;
        }

        .crafted-by-link {
            color: #0f5d9c;
            text-decoration: none;
            word-break: break-all;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html,
            body {
                height: auto !important;
            }
        }

        @if ($isPdfPreview)
        body.pdf-preview-mode {
            background: #dee2e6;
            padding: 24px 16px 40px;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        .pdf-preview-shell {
            width: 595.28pt;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 6px 32px rgba(0, 0, 0, 0.14);
        }
        .pdf-preview-img-placeholder {
            min-height: 100px;
            background: #eef2f6;
            border: 1px dashed #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 12px;
            text-align: center;
            padding: 12px;
            box-sizing: border-box;
        }
        .pdf-preview-img-placeholder.hotel-ph {
            min-height: 140px;
        }
        @endif
    </style>
</head>

<body @if ($isPdfPreview) class="pdf-preview-mode" @endif>
    @if ($isPdfPreview)
        <div class="pdf-preview-shell">
    @endif
    <table class="parent-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table class="header-table">
                    <tr>
                        <td>
                            <img src="{{ $pdfPublic('pdf/image/SKY-White-Logo.webp') }}" class="logo" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h1 class="bannertitle">Hey, {{ $lead->contact->name ?? 'Guest' }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="subtitle">Your hassle-free holiday starts here</p>
                        </td>
                    </tr>
                    @if (!empty($bookingId))
                        <tr>
                            <td width="100%" align="left">
                                <p style="color:#ffffff; padding:0px 0px 20px 15px;">BookingID: {{ $bookingId }}</p>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="main-content-table">
                    <tr>
                        <td>
                            <table class="travel-details" cellspacing="0">
                                <tr>
                                    <td width="15%"><strong>Visiting:</strong></td>
                                    <td>
                                        @if (isset($lead->destination) && !empty($lead->destination))
                                            @php
                                                $destinations = is_array($lead->destination)
                                                    ? $lead->destination
                                                    : json_decode($lead->destination, true);
                                                $destinationCities = array_map(
                                                    function ($d) {
                                                        return $d['city'] ?? '';
                                                    },
                                                    $destinations ?: [],
                                                );
                                                $destinationCities = array_filter($destinationCities);
                                            @endphp
                                            {{ implode(', ', $destinationCities) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%"><strong>Travel Date:</strong></td>
                                    <td>{{ $quotation->start_date }} To {{ $quotation->end_date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ countDaysAndNights($quotation->start_date, $quotation->end_date, 1) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Travellers:</strong></td>
                                    <td>{{ $lead->no_of_adults }} Adults, {{ $lead->no_of_kids }} Child</td>
                                </tr>
                                <tr>
                                    <td><strong>Inclusions:</strong></td>
                                    <td>
                                        @if (($lead->flight_requirements ?? '') == 'Yes')
                                            @if (!empty($quotationFlight))
                                                @if ($quotationFlight->trip_type == 'one_way')
                                                    1 Flight,
                                                @endif

                                                @if ($quotationFlight->trip_type == 'round_trip')
                                                    2 Flights,
                                                @endif

                                                @if ($quotationFlight->trip_type == 'multi_city')
                                                    {{ $quotationFlight->items->count() }} Flights,
                                                @endif
                                            @else
                                                <span style="color:red">No Flights</span>,
                                            @endif
                                        @endif

                                        @if ($lead->hotel_requirements == 'Yes' && !empty($quotationHotels) && $quotationHotels->count() > 0)
                                            {{ $quotationHotels->count() }} Hotels,
                                        @endif

                                        @if ($lead->sightseeing_requirements == 'Yes' && !empty($sightseeing) && $sightseeing->count() > 0)
                                            {{ $sightseeing->count() }} Activities,
                                        @endif

                                        @if ($lead->travel_type == 'International' && ($lead->visa_requirements ?? '') == 'Yes' && !empty($quotation->visa) && $quotation->visa->count() > 0)
                                            Visa Included for
                                            {{ $quotation->visa->pluck('visa_country')->unique()->filter()->implode(', ') }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="note">
                                <span style="color:red; font-weight:bold;">Note:</span>
                                The total cost is inclusive of TCS, applicable taxes and GST as of the date mentioned
                            </p>
                        </td>
                    </tr>

                    @if (!empty($pdfBifurcation) && count($pdfBifurcation) > 0)
                        <tr>
                            <td style="padding-top:15px;">

                                <table width="100%" cellspacing="0" cellpadding="6" border="0"
                                    style="border-collapse:collapse; font-size:13px;">

                                    <tr style="background:#f2f6fb; border-bottom:1px solid #ddd;">
                                        <td width="70%" style="font-weight:bold;">Price Details</td>
                                        <td width="30%" align="right" style="font-weight:bold;">Amount</td>
                                    </tr>

                                    @foreach ($pdfBifurcation as $item)
                                    
                                        <tr style="border-bottom:1px solid #eee;">
                                            <td>
                                                <strong>{{ $item['label'] }}</strong>
                                                {{-- Per Person (Hotel etc) --}}
                                                @if (isset($item['per_person']) && $item['per_person'] !== null)
                                                    <br>
                                                    <span style="font-size:11px; color:#666;">
                                                        Per person :
                                                        <img src="{{ $pdfPublic('pdf/image/rupee.png') }}" width="10" height="10">
                                                        {{ formatAmount($item['per_person']) }}
                                                    </span>

                                                {{-- Adult / Child / Infant --}}
                                                @elseif(
                                                    (isset($item['per_person_adult']) && $item['per_person_adult'] !== null) ||
                                                    (isset($item['per_person_child']) && $item['per_person_child'] !== null) ||
                                                    (isset($item['per_person_infant']) && $item['per_person_infant'] !== null)
                                                )
                                                    {{-- Adult --}}
                                                    @if(isset($item['per_person_adult']))
                                                        <br>
                                                        <span style="font-size:11px; color:#666;">
                                                            Adult :
                                                            <img src="{{ $pdfPublic('pdf/image/rupee.png') }}" width="10" height="10">
                                                            {{ formatAmount($item['per_person_adult'] ?? 0) }}
                                                        </span>
                                                    @endif

                                                    {{-- Child --}}
                                                    @if(isset($item['per_person_child']))
                                                        <br>
                                                        <span style="font-size:11px; color:#666;">
                                                            Child :
                                                            <img src="{{ $pdfPublic('pdf/image/rupee.png') }}" width="10" height="10">
                                                            {{ formatAmount($item['per_person_child'] ?? 0) }}
                                                        </span>
                                                    @endif

                                                    {{-- ✅ NEW: Infant --}}
                                                    @if(isset($item['per_person_infant']) && $item['per_person_infant'] > 0)
                                                        <br>
                                                        <span style="font-size:11px; color:#666;">
                                                            Infant :
                                                            <img src="{{ $pdfPublic('pdf/image/rupee.png') }}" width="10" height="10">
                                                            {{ formatAmount($item['per_person_infant']) }}
                                                        </span>
                                                    @endif
                                                @endif

                                            </td>

                                            <td align="right">
                                                <img src="{{ $pdfPublic('pdf/image/rupee.png') }}" width="12" height="12">
                                                <strong>{{ formatAmount($item['amount']) }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach


                                    {{-- Discount --}}
                                    @if (!empty($quotation->discount) && $quotation->discount > 0)
                                        <tr style="background:#fff7f7;">
                                            <td style="color:#c0392b; font-weight:bold;">
                                                Discount
                                            </td>

                                            <td align="right" style="color:#c0392b; font-weight:bold;">
                                                - <img src="{{ $pdfPublic('pdf/image/rupee.png') }}" width="12"
                                                    height="12">
                                                {{ formatAmount($quotation->discount) }}
                                            </td>
                                        </tr>
                                    @endif


                                    {{-- Total --}}
                                    <tr style="background:#eef7ff; border-top:2px solid #2c7be5;">

                                        <td style="font-size:16px; font-weight:bold;">
                                            Total Package Cost
                                        </td>

                                        <td align="right" style="font-size:18px; font-weight:bold; color:#2c7be5;">

                                            <img src="{{ $pdfPublic('pdf/image/rupee.png') }}" width="16"
                                                height="16">
                                            {{ formatAmount($quotation->total_amount) }}

                                        </td>

                                    </tr>

                                </table>

                            </td>
                        </tr>

                    @endif
                    <tr>
                        <td align="right"><span class="price"><img src="{{ $pdfPublic('pdf/image/rupee.png') }}"
                                    width="20px" height="20px" />{{ formatAmount($quotation->total_amount) }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="main-content-table">
                    <tr>
                        <td>
                            <table class="why-pick-us">
                                <tr>
                                    <td colspan="3">
                                        <h2>WHY SHOULD YOU <span>PICK US</span></h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="icon">
                                            <img src="{{ $pdfPublic('pdf/image/customised-trip.png') }}"
                                                alt="Customised Trips">
                                        </div>
                                        <p class="title">100%</p>
                                        <p class="desc">CUSTOMISED TRIPS</p>
                                    </td>
                                    <td>
                                        <div class="icon"><img src="{{ $pdfPublic('pdf/image/support.png') }}"
                                                alt="Support"></div>
                                        <p class="title">24/7</p>
                                        <p class="desc">SUPPORT</p>
                                    </td>
                                    <td>
                                        <div class="icon">
                                            <img src="{{ $pdfPublic('pdf/image/review.png') }}" alt="Reviews">
                                        </div>
                                        <p class="title">4.6 <span class="star">★</span></p>
                                        <p class="desc">3800+ REVIEWS</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table class="main-content-table">
                    @if ($lead->sightseeing_requirements == 'Yes' && !empty($sightseeing) && $sightseeing->count() > 0)
                        <tr>
                            <td>
                                <h2 class="itinerary_title">Itinerary</h2>
                            </td>
                        </tr>
                        @foreach ($sightseeing as $itinery)
                            <tr>
                                <td>
                                    <div class="daywise_header">
                                        {{ formateDate($itinery->date, 'd M, D') }} | Day {{ $itinery->day_no }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    @foreach ($itinery->items as $item)
                                        <table class="main-content-table">
                                            <tr>
                                                @if ($isPdfPreview)
                                                    @php $sightItemImg = $pdfStorageUrl($item->image ?? null); @endphp
                                                    <td width="25%" valign="top">
                                                        <div class="car-img">
                                                            @if ($sightItemImg)
                                                                <img src="{{ $sightItemImg }}" alt="Sightseeing">
                                                            @else
                                                                <div class="pdf-preview-img-placeholder">Image not uploaded</div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @elseif (!empty($item->image))
                                                    <td width="25%" valign="top">
                                                        <div class="car-img">
                                                            <img src="{{ public_path('storage/' . $item->image) }}"
                                                                alt="Car Image">
                                                        </div>
                                                    </td>
                                                @endif
                                                <td valign="top">
                                                    <div class="car-details">
                                                        <h3>{{ $item->title }}</h3>
                                                        <div>
                                                            {!! $item->description !!}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        @if (!empty(trim(strip_tags($quotation->inclusion ?? ''))))
                            <tr>
                                <td>
                                    <div class="inclusion-section-card">
                                        <div class="inclusion-section-header">Inclusions</div>
                                        <div class="inclusion-section-body">
                                            <div class="inclusion-content">
                                                {!! $quotation->inclusion !!}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @if (!empty(trim(strip_tags($quotation->exclusion ?? ''))))
                            <tr>
                                <td>
                                    <div class="exclusion-section-card">
                                        <div class="exclusion-section-header">Exclusions</div>
                                        <div class="exclusion-section-body">
                                            <div class="inclusion-content">
                                                {!! $quotation->exclusion !!}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endif
                </table>
            </td>
        </tr>
        @if ($lead->hotel_requirements == 'Yes' && !empty($quotationHotels) && $quotationHotels->count() > 0)
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td>
                                <h2 class="itinerary_title mb-0">Hotel</h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="main-content-table mt-0">
                        @foreach ($quotationHotels as $hotel_key => $hotel)
                            <tr>
                                <td>
                                    <table style="width: 100%;"
                                        class="hotel-block {{ $hotel_key > 0 ? 'hotel-separation' : '' }} {{ $hotel_key === $quotationHotels->count() - 1 ? 'hotel-block-last' : '' }}">
                                        <tr>
                                            @if ($isPdfPreview)
                                                @php $hotelMainImg = $pdfStorageUrl($hotel->hotel->images ?? null); @endphp
                                                <td width="20%" valign="top">
                                                    <div class="hotel-image">
                                                        @if ($hotelMainImg)
                                                            <img src="{{ $hotelMainImg }}" alt="Hotel">
                                                        @else
                                                            <div class="pdf-preview-img-placeholder hotel-ph">Hotel image not uploaded</div>
                                                        @endif
                                                    </div>
                                                </td>
                                            @elseif (!empty($hotel->hotel->images))
                                                <td width="20%" valign="top">
                                                    <div class="hotel-image">
                                                        <img src="{{ public_path('storage/' . $hotel->hotel->images) }}"
                                                            alt="Hotel Image">
                                                    </div>
                                                </td>
                                            @endif
                                            <td valign="top">
                                                <div class="hotel-details">
                                                    <h3>{{ $hotel->hotel->name }}</h3>
                                                    @if (!empty($hotel->hotel?->address))
                                                        <div class="hotel-detail-row"><span
                                                                class="hotel-detail-label">Address:</span> <span
                                                                class="hotel-detail-value">{{ $hotel->hotel->address }}</span>
                                                        </div>
                                                    @endif
                                                    @if (!empty($hotel->destination))
                                                        <div class="hotel-detail-row"><span
                                                                class="hotel-detail-label">Destination:</span> <span
                                                                class="hotel-detail-value">{{ $hotel->destination }}</span>
                                                        </div>
                                                    @endif
                                                    @if (!empty($hotel->room_type))
                                                        <div class="hotel-detail-row"><span
                                                                class="hotel-detail-label">Room Type:</span> <span
                                                                class="hotel-detail-value">{{ $hotel->room_type }}</span>
                                                        </div>
                                                    @endif
                                                    @if (!empty($hotel->meals))
                                                        <div class="hotel-detail-row"><span
                                                                class="hotel-detail-label">Meals:</span> <span
                                                                class="hotel-detail-value">{{ $hotel->meals }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="hotel-checkin-details">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="50%">
                                                            <div class="inner-details-left">
                                                                <div class="check-1">
                                                                    <p>Check-in</p>
                                                                    <span>{{ formateDate($hotel->check_in, 'd M, D H:i') }}</span>
                                                                </div>
                                                                <div class="check-2">
                                                                    <p>Total Room</p>
                                                                    <span>{{ $hotel->total_room }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td width="50%">
                                                            <div class="inner-details-rigth">
                                                                <div class="check-1">
                                                                    <p>Check-out</p>
                                                                    <span>{{ formateDate($hotel->check_out, 'd M, D H:i') }}</span>
                                                                </div>
                                                                <div class="check-2">
                                                                    <p>Nights</p>
                                                                    <span>{{ $hotel->nights }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        @endif

        @if (($lead->flight_requirements ?? '') == 'Yes' && $quotationFlight)
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td>
                                <h2 class="itinerary_title mb-0">Flights</h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="main-content-table mt-0">
                        <tr>
                            <td>
                                <div class="flight-section-card">
                                    <div class="flight-section-body">
                                        @if ($quotationFlight->trip_type == 'multi_city' && $quotationFlight->items && $quotationFlight->items->count() > 0)
                                            
                                            @foreach ($quotationFlight->items as $idx => $item)
                                                <div class="flight-multi-segment">
                                                    <div class="flight-value">{{ $item->fromAirport->city ?? '—' }}
                                                        ({{ $item->fromAirport->airport_code ?? '' }})
                                                        - {{ $item->fromAirport->airport_name ?? '' }}
                                                        <img
                                                            src="{{ $pdfPublic('pdf/image/arrow-right.png') }}"
                                                            alt=""
                                                            class="flight-arrow-icon" />
                                                            {{ $item->toAirport->city ?? '—' }}
                                                            ({{ $item->toAirport->airport_code ?? '' }})
                                                            - {{ $item->toAirport->airport_name ?? '' }}
                                                        </div>
                                                    <div class="flight-label" style="margin-top: 4px;">Date</div>
                                                    <div class="flight-value">
                                                        {{ $item->date ? formateDate($item->date, 'd M, Y') : '—' }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            {{-- One Way / Round Trip: single from/to and dates (2 columns) --}}
                                            <table class="flight-two-col-table">
                                                <tr>
                                                    <td>
                                                        <div class="flight-row">
                                                            <div class="flight-label">Departure City</div>
                                                            <div class="flight-value">
                                                                {{ $quotationFlight->sourceAirport->city ?? '—' }}
                                                                ({{ $quotationFlight->sourceAirport->airport_code ?? '' }})
                                                                - {{ $quotationFlight->sourceAirport->airport_name ?? '' }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="flight-row">
                                                            <div class="flight-label">Destination City</div>
                                                            <div class="flight-value">
                                                                {{ $quotationFlight->destinationAirport->city ?? '—' }}
                                                                ({{ $quotationFlight->destinationAirport->airport_code ?? '' }})
                                                                - {{ $quotationFlight->destinationAirport->airport_name ?? '' }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flight-row">
                                                            <div class="flight-label">Departure Date</div>
                                                            <div class="flight-value">
                                                                {{ $quotationFlight->flight_start_date ? formateDate($quotationFlight->flight_start_date, 'd M, Y') : '—' }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($quotationFlight->trip_type == 'round_trip')
                                                            <div class="flight-row">
                                                                <div class="flight-label">Return Date</div>
                                                                <div class="flight-value">
                                                                    {{ $quotationFlight->flight_end_date ? formateDate($quotationFlight->flight_end_date, 'd M, Y') : '—' }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif
                                        <table class="flight-two-col-table">
                                            <tr>
                                                <td>
                                                    <div class="flight-row">
                                                        <div class="flight-label">Travel Mode</div>
                                                        <div class="flight-value">
                                                            {{ $quotationFlight->travel_mode ?? 'Flight' }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flight-row">
                                                        <div class="flight-label">Class</div>
                                                        <div class="flight-value">
                                                            {{ $quotationFlight->flight_class ?? '—' }}</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="flight-row">
                                                        <div class="flight-label">Passengers</div>
                                                        <div class="flight-value">
                                                            {{ $quotationFlight->flight_adults ?? 0 }} Adults,
                                                            {{ $quotationFlight->flight_child ?? 0 }} Child,
                                                            {{ $quotationFlight->flight_infant ?? 0 }} Infant</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flight-row">
                                                        <div class="flight-label">Trip Type</div>
                                                        <div class="flight-value">
                                                            {{ config('constant.trip_type.' . $quotationFlight->trip_type) ?? $quotationFlight->trip_type }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        @if (!empty($quotationFlight->flight_remarks))
                                            <div class="flight-row">
                                                <div class="flight-label">Remarks</div>
                                                <div class="flight-value">{{ $quotationFlight->flight_remarks }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif

        @if ($lead->travel_type == 'International' && ($lead->visa_requirements ?? '') == 'Yes' && !empty($quotation->visa) && $quotation->visa->count() > 0)
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td>
                                <h2 class="itinerary_title mb-0">Visa</h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="main-content-table mt-0">
                        @foreach ($quotation->visa as $visa_key => $visa)
                            <tr>
                                <td class="{{ $visa_key > 0 ? 'visa-row-cell' : '' }}"
                                    {{ $visa_key > 0 ? 'style="padding-top: 20px;"' : '' }}>
                                    <div
                                        class="visa-block {{ $visa_key === $quotation->visa->count() - 1 ? 'visa-block-last' : '' }}">
                                        <h3>{{ $visa->visa_country ?? 'Visa' }}</h3>
                                        <table class="visa-two-col-table">
                                            <tr>
                                                <td>
                                                    <div class="visa-row">
                                                        <div class="visa-label">Visa Category</div>
                                                        <div class="visa-value">{{ $visa->visa_category ?? '—' }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="visa-row">
                                                        <div class="visa-label">Visa Type</div>
                                                        <div class="visa-value">{{ $visa->visa_type ?? '—' }}</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="visa-row">
                                                        <div class="visa-label">Travel Date</div>
                                                        <div class="visa-value">
                                                            {{ $visa->visa_travel_date ? formateDate($visa->visa_travel_date, 'd M, Y') : '—' }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="visa-row">
                                                        <div class="visa-label">Travellers</div>
                                                        <div class="visa-value">{{ $visa->visa_adults ?? 0 }} Adults,
                                                            {{ $visa->visa_child ?? 0 }} Child,
                                                            {{ $visa->visa_infant ?? 0 }} Infant</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        @endif

        @if (
            $lead->travel_type &&
                $lead->travel_type == 'International' &&
                $lead->visa_requirements &&
                $lead->visa_requirements == 'Yes' &&
                !empty($visaPolicy))
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td>
                                <h2 class="itinerary_title mb-0">Visa Policy</h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td class="inclusion-content">{!! $visaPolicy !!}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif
        @if (!empty($paymentPolicy))
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td>
                                <h2 class="itinerary_title mb-0">Payment Policy</h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td class="inclusion-content">{!! $paymentPolicy !!}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif

        {{-- Terms & Condition --}}
        @if (!empty($terms))
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td>
                                <h2 class="itinerary_title mb-0">Terms & Conditions</h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="main-content-table">
                        <tr>
                            <td class="inclusion-content">
                                <p>{!! $terms !!}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif
        <tr>
            <td>
                <table class="main-content-table">
                    <tr>
                        <td align="center">
                            <h2 class="partners-section-h2">OUR TRAVEL <span>PARTNERS</span></h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="travel-partner">
                                <tr>
                                    <td>
                                        <img src="{{ $pdfPublic('pdf/image/villas-on-demand.png') }}"
                                            class="partner-logo">
                                    </td>
                                    <td>
                                        <img src="{{ $pdfPublic('pdf/image/travel-hawks.png') }}"
                                            class="partner-logo">
                                    </td>
                                    <td>
                                        <img src="{{ $pdfPublic('pdf/image/travel-on-demand.png') }}"
                                            class="partner-logo">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- Package Crafted By --}}
        @if ($quotation->user ?? null)
            <tr>
                <td>
                    <table class="main-content-table crafted-by-section">
                        <tr>
                            <td>
                                <div class="crafted-by-card">
                                    @php
                                        if ($isPdfPreview) {
                                            $profileImagePath = null;
                                            if (!empty($quotation->user->profile_image)) {
                                                $profileImagePath = $pdfStorageUrl('profileImage/' . $quotation->user->profile_image);
                                            }
                                            if ($profileImagePath === null) {
                                                $profileImagePath = $pdfPublic('images/users/avatar-1.jpg');
                                            }
                                        } else {
                                            $profileImage = !empty($quotation->user->profile_image)
                                                ? public_path('storage/profileImage/' . $quotation->user->profile_image)
                                                : public_path('images/users/avatar-1.jpg');
                                            $profileImagePath = is_file($profileImage)
                                                ? $profileImage
                                                : public_path('images/users/avatar-1.jpg');
                                        }
                                    @endphp
                                    <div class="crafted-by-title">Package Crafted By</div>
                                    <div class="crafted-by-profile">
                                        <img src="{{ $profileImagePath }}" alt="User Profile" class="crafted-by-profile-img" />
                                    </div>
                                    <div class="crafted-by-name">{{ $quotation->user->name ?? '—' }}</div>
                                    <div class="crafted-by-divider"></div>

                                    @if (!empty($quotation->user->phone) || !empty($quotation->user->al_phone))
                                        <div class="crafted-by-row">
                                            <strong>Phone:</strong>
                                            <img src="{{ $pdfPublic('pdf/image/icon-mobile.png') }}" alt=""
                                                class="crafted-by-icon" />
                                            {{ $quotation->user->phone ?? '—' }}
                                            @if (!empty($quotation->user->al_phone))
                                                <span> / {{ $quotation->user->al_phone }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if (!empty($quotation->user->email))
                                        <div class="crafted-by-row">
                                            <strong>Email:</strong>
                                            <img src="{{ $pdfPublic('pdf/image/icon-email.png') }}" alt=""
                                                class="crafted-by-icon" /> {{ $quotation->user->email }}
                                        </div>
                                    @endif
                                    @if (!empty($quotation->user->website) || !empty($quotation->user->company_website) || !empty($quotation->user->webset))
                                        @php
                                            $companyWebsite = $quotation->user->website ?? $quotation->user->company_website ?? $quotation->user->webset;
                                            $companyWebsiteUrl = preg_match('/^https?:\/\//i', $companyWebsite) ? $companyWebsite : 'https://' . $companyWebsite;
                                        @endphp
                                        <div class="crafted-by-row">
                                            <strong>Website:</strong>
                                            <a class="crafted-by-link" href="{{ $companyWebsiteUrl }}">
                                                {{ $companyWebsite }}
                                            </a>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif
    </table>
    @if ($isPdfPreview)
        </div>
    @endif
</body>

</html>
