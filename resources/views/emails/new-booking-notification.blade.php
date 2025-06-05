<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 28px;
        }
        .booking-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status.confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .client-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .client-info h3 {
            margin-top: 0;
            color: #007bff;
        }
        .alert {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .alert h3 {
            margin-top: 0;
            color: #0c5460;
        }
        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Booking Received!</h1>
            <p>You have received a new booking request</p>
        </div>

        <p>Dear {{ $provider->name }},</p>

        <p>Great news! You have received a new booking request for your service. Here are the details:</p>

        <div class="booking-details">
            <h3 style="margin-top: 0; color: #28a745;">Booking Information</h3>
            
            <div class="detail-row">
                <span class="detail-label">Booking ID:</span>
                <span class="detail-value">#{{ $booking->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Service:</span>
                <span class="detail-value">{{ $offer->title }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">{{ $offer->description }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Requested Date:</span>
                <span class="detail-value">{{ $booking->booking_date->format('l, F j, Y \a\t g:i A') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Service Amount:</span>
                <span class="detail-value amount">${{ number_format($booking->total_amount, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Current Status:</span>
                <span class="detail-value">
                    <span class="status {{ strtolower($booking->status) }}">{{ ucfirst($booking->status) }}</span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Booking Received:</span>
                <span class="detail-value">{{ $booking->created_at->format('F j, Y \a\t g:i A') }}</span>
            </div>
        </div>

        <div class="client-info">
            <h3>Client Information</h3>
            <p><strong>Name:</strong> {{ $client->name }}</p>
            <p><strong>Email:</strong> {{ $client->email }}</p>
            <p><strong>Phone:</strong> {{ $client->phone }}</p>
        </div>

        <div class="alert">
            <h3>Next Steps</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li>The booking is currently <strong>{{ strtolower($booking->status) }}</strong> and awaiting payment confirmation from the client.</li>
                <li>You will be notified once the client completes the payment process.</li>
                <li>You can contact the client directly using the information provided above if needed.</li>
                <li>Please prepare for the scheduled appointment on {{ $booking->booking_date->format('F j, Y') }}.</li>
            </ul>
        </div>

        <p>Thank you for using our platform to manage your bookings. We're here to help you grow your business!</p>

        <div class="footer">
            <p>This is an automated notification. You can manage your bookings through your dashboard.</p>
            <p>If you have any questions about this booking, please contact the client directly using the information provided above.</p>
        </div>
    </div>
</body>
</html>
