<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Eagle Trip</title>
    <!-- Font Awesome Icon CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <style>
        @page {
            size: 200mm 1000mm;
            margin: 0;
        }

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
            max-width: 150px;
            border-radius: 8px;
        }

        .car-img img {
            width: 100%;
            max-width: 150px;
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
            text-align: left;
            font-size: 17px;
        }

        .inner-details-left {
            border-right: 2px solid #dee2e6;
        }

        .inner-details-rigth {
            padding-left: 20px;
        }

        .hotel-checkin-details p {
            text-transform: uppercase;
            color: #a6aaae;
            font-weight: 500;
            margin-top: 2px;
            margin-bottom: 5px;
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

        .hotel-separation {
            border-top: 4px dashed #dee2e6;
            padding-top: 10px;
            margin-top: 10px;
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
            width:100%
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

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>
    <table class="parent-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table class="header-table">
                    <tr>
                        <td>
                            <img src="{{ public_path('pdf/image/SKY-White-Logo.webp') }}" class="logo" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h1 class="bannertitle">Hey, {{ $lead->contact->name }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="subtitle">Your hassle-free holiday starts here</p>
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
                            <table class="travel-details" cellspacing="0">
                                <tr>
                                    <td width="15%"><strong>Visiting:</strong></td>
                                    <td>

                                        @foreach (json_decode($lead->destination, true) as $key => $destination)
                                            @if (count(json_decode($lead->destination, true)) == $key)
                                                {{ $destination['city'] }}
                                            @else
                                                {{ $destination['city'] . ',' }}
                                            @endif
                                        @endforeach
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
                                        @if (!empty($quotationFlight))
                                            @if ($quotationFlight->trip_type == 'one_way')
                                                1 Flight,
                                            @endif

                                            @if ($quotationFlight->trip_type == 'one_wayround_trip')
                                                2 Flights,
                                            @endif

                                            @if ($quotationFlight->trip_type == 'multi_city')
                                                {{ $quotationFlight->items->count() }} Flights,
                                            @endif
                                        @else
                                            <span style="color:red">No Flights</span>,
                                        @endif
                                        @if ($quotationHotels->count() > 0)
                                            {{ $quotationHotels->count() }} Hotels,
                                        @endif
                                        @if ($sightseeing->count() > 0)
                                            {{ $sightseeing->count() }} Activities
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="note">The total cost is inclusive of TCS, applicable taxes and GST as of the
                                date mentioned</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><span class="price"><img src="{{ public_path('pdf/image/rupee.png') }}"
                                    width="20px" height="20px" />{{ formatAmount($totalPrice) }}</span></td>
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
                                        <div class="icon"><i class="fa-solid fa-flag"></i></div>
                                        <p class="title">100%</p>
                                        <p class="desc">CUSTOMISED TRIPS</p>
                                    </td>
                                    <td>
                                        <div class="icon"><i class="fa-solid fa-headset"></i></div>
                                        <p class="title">24/7</p>
                                        <p class="desc">SUPPORT</p>
                                    </td>
                                    <td>
                                        <div class="icon"><i class="fa-brands fa-google"></i></div>
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
                                        <img src="{{ public_path('pdf/image/New-Zealand-Tourism.webp') }}"
                                            alt="New Zealand Tourism" class="partner-logo">
                                    </td>
                                    <td>
                                        <img src="{{ public_path('pdf/image/Aussie-Specialist.webp') }}"
                                            alt="Australia Aussie Specialist" class="partner-logo">
                                    </td>
                                    <td>
                                        <img src="{{ public_path('pdf/image/Dubai-Expert.webp') }}" alt="Dubai Expert"
                                            class="partner-logo">
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
                    @if ($sightseeing->count() > 0)
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
                                                <td width="25%" valign="top">
                                                    <div class="car-img">
                                                        <img src="{{ public_path('storage/' . $item->image) }}"
                                                            alt="Car Image">
                                                    </div>
                                                </td>
                                                <td valign="top">
                                                    <div class="car-details">
                                                        <h4>{{ $item->title }}</h4>
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
                    @endif
                </table>
            </td>
        </tr>
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
                                <table style="width: 100%;" class="{{ $hotel_key > 0 ? 'hotel-separation' : '' }}">
                                    <tr>
                                        <td width="20%" valign="top">
                                            <div class="hotel-image">
                                                <img src="{{ public_path('pdf/image/hotel-room.webp') }}"
                                                    alt="Hotel Image">
                                            </div>
                                        </td>
                                        <td valign="top">
                                            <div class="hotel-details">
                                                <h2>{{ $hotel->hotel->name }}</h2>
                                                @if (!empty($hotel->destination))
                                                    <p><strong>Destination:</strong> {{ $hotel->destination }}</p>
                                                @endif

                                                @if (!empty($hotel->room_type))
                                                    <p><strong>Room Type:</strong> {{ $hotel->room_type }}</p>
                                                @endif

                                                @if (!empty($hotel->room_type))
                                                    <p><strong>Meals:</strong> {{ $hotel->meals }}</p>
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
                                                                <span>2 Nights</span>
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
                <table class="main-content-table mt-0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td colspan="2" class="flight-checkin-details">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="50%" align="center" style="padding: 20px;">
                                        <div class="inner-details-left">
                                            <p>Date</p>
                                            <span>12 Aug, Tue</span>
                                        </div>
                                    </td>
                                    <td width="50%" align="center">
                                        <div class="inner-details-rigth">
                                            <p>CLASS</p>
                                            <span>Economy</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="flight-details">
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="35%">
                                                    <p>Default Cabin:</p>
                                                </td>
                                                <td width="65%">
                                                    <span>UnderSeat Bag 2 KG - Cabin 5KG/person</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="35%" class="td-top-border">
                                                    <p>Default Checkin:</p>
                                                </td>
                                                <td width="65%" class="td-top-border">
                                                    <span>0 KG/person</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" style="padding: 20px 0px 20px 0px;" width="100%">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="100%" colspan="2">
                                        <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:10px;">
                                            <tr>
                                                <td class="text-left">
                                                    <span class="time">19:55</span>
                                                </td>
                                                <td width="82%">
                                                    <div class="timeline">
                                                        <div class="line">
                                                            <div class="circle start"></div>
                                                            <div class="circle end"></div>
                                                            <div class="duration">1h 20m</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-rigth">
                                                    <span class="time">21:15</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top" class="text-left">
                                        <p class="p-black">12 Aug, Tue</p>
                                    </td>
                                    <td width="50%" valign="top" class="text-rigth">
                                        <p class="p-black">12 Aug, Tue</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top" class="text-left">
                                        <p class="p-grey">Jatayu Airlines</p>
                                    </td>
                                    <td width="50%" valign="top" class="text-rigth">
                                        <p class="p-grey">VJ - 1509</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top" class="text-left">
                                        <p class="p-black">HAN</p>
                                    </td>
                                    <td width="50%" valign="top" class="text-rigth">
                                        <p class="p-black">DAD</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top" class="text-left">
                                        <p class="p-grey">Hanoi</p>
                                    </td>
                                    <td width="50%" valign="top" class="text-rigth">
                                        <p class="p-grey">Da Nang</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
