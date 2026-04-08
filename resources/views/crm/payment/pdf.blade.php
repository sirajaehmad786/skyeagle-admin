<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sky Eagle Trip</title>

<style>
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; margin: 0; color: #000; }
    h2 { margin-top: 28px; }
    .parent-table { width: 100%; border: none; padding: 0; margin: 0; }
    .header-table { width: 100%; text-align: center; }
    .logo { width: 300px; margin: 30px auto 15px; }
    .subtitle { font-size: 25px; margin: 0 0 10px 0; color: #000; }
    .main-content-table { width: 96%; margin: 15px 15px 15px 10px; }
    .main-content-table p { color: #767474; margin-top: 40px; }
    .user-details p label { font-weight: 600; letter-spacing: 0.5px; color: #000;font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    .user-details p { margin: 0 0 5px 0; }
    .travel-details { border: 1px solid #e0e0e0; width: 100%; border-collapse: collapse; }
    .travel-details tr td { padding: 17px 15px; font-size: 15px; border-bottom: 1px solid #e0e0e0; }
    .travel-details tr td:first-child { background-color: #f2f2f2; width: 45%; font-weight: 600; }
    .travel-details tr:last-child td { border-bottom: none; }
    .payment-img { width: 80px; height: 50px; border-radius: 5px; object-fit: cover; }
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
                <!-- Header Section -->
                <table class="header-table">
                    <tr>
                        <td>
                           <img 
                                src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" 
                                class="logo"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="subtitle">Payment Receipt</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td width="100%">
                <!-- User / Booking Details -->
                <table class="main-content-table">
                    <tr>
                        <td colspan="2" width="100%">
                            <table cellspacing="0" cellpadding="0" width="100%" class="user-details">
                                <tr>
                                    <td>
                                        <p>
                                            <label>Customer Name:</label>
                                            {{ optional($payment->booking->quotation->contact)->first_name 
                                                ? optional($payment->booking->quotation->contact)->first_name . ' ' . optional($payment->booking->quotation->contact)->last_name 
                                                : 'N/A' }}
                                        </p>
                                    </td>
                                    <td width="40%" align="right">
                                        <p>
                                            <label>Booking ID:</label>
                                            {{ $payment->booking->booking_id ?? 'N/A' }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td >
                                        <p>
                                            <label>Phone:</label>
                                            {{ optional($payment->booking->quotation->contact)->mobile_no ?? 'N/A' }}
                                        </p>
                                    </td>
                                    <td width="40%" align="right">
                                        <p>
                                            <label>Payment ID:</label>
                                            {{ $payment->payment_id ?? 'N/A' }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p>
                                            <label>Email:</label>
                                            {{ optional($payment->booking->quotation->contact)->email ?? 'N/A' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Payment Details Table -->
                    <tr>
                        <td width="100%">
                            <table class="travel-details" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><strong>Amount</strong></td>
                                    <td>{{ config('constant.rupee_symbol') . ' ' . number_format($payment->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method</strong></td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Date</strong></td>
                                    <td>{{ formateDate($payment->payment_date) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td><strong style="color:green;">Paid</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Remarks</strong></td>
                                    <td>{{ $payment->remarks ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center">
                            <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
