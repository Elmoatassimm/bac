<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Payment Flow - ClinicBook</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2563eb;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #374151;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
        }
        button {
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background: #1d4ed8;
        }
        button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            white-space: pre-wrap;
            font-family: monospace;
        }
        .success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Payment Flow</h1>
        <p>This page helps test the booking and payment creation process.</p>

        <form id="bookingForm">
            <div class="form-group">
                <label for="offer_id">Offer ID:</label>
                <input type="number" id="offer_id" name="offer_id" value="1" required>
            </div>

            <div class="form-group">
                <label for="client_name">Client Name:</label>
                <input type="text" id="client_name" name="client_name" value="Test User" required>
            </div>

            <div class="form-group">
                <label for="client_email">Client Email:</label>
                <input type="email" id="client_email" name="client_email" value="test@example.com" required>
            </div>

            <div class="form-group">
                <label for="client_phone">Client Phone:</label>
                <input type="tel" id="client_phone" name="client_phone" value="123-456-7890" required>
            </div>

            <div class="form-group">
                <label for="appointment_date">Appointment Date:</label>
                <input type="date" id="appointment_date" name="appointment_date" required>
            </div>

            <div class="form-group">
                <label for="appointment_time">Appointment Time:</label>
                <input type="time" id="appointment_time" name="appointment_time" value="10:00" required>
            </div>

            <div class="form-group">
                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes" rows="3">Test booking for payment flow</textarea>
            </div>

            <button type="submit" id="createBookingBtn">Create Booking</button>
            <button type="button" id="testPaymentBtn" disabled>Test Payment Intent</button>
        </form>

        <div id="result"></div>
    </div>

    <script>
        const API_BASE = 'http://localhost:8000/api/v1';
        let currentBookingId = null;

        // Set default date to tomorrow
        document.getElementById('appointment_date').value = new Date(Date.now() + 86400000).toISOString().split('T')[0];

        document.getElementById('bookingForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            showResult('Creating booking...', 'info');
            
            try {
                const response = await fetch(`${API_BASE}/bookings`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    currentBookingId = result.data.id;
                    document.getElementById('testPaymentBtn').disabled = false;
                    showResult(`✅ Booking created successfully!\n\nBooking ID: ${result.data.id}\nTotal Amount: $${result.data.total_amount}\nStatus: ${result.data.status}\n\nYou can now test the payment intent creation.`, 'success');
                } else {
                    showResult(`❌ Failed to create booking:\n${result.message}`, 'error');
                }
            } catch (error) {
                showResult(`❌ Error creating booking:\n${error.message}`, 'error');
            }
        });

        document.getElementById('testPaymentBtn').addEventListener('click', async () => {
            if (!currentBookingId) {
                showResult('❌ No booking ID available. Create a booking first.', 'error');
                return;
            }
            
            showResult('Creating payment intent...', 'info');
            
            try {
                const response = await fetch(`${API_BASE}/create-payment-intent/${currentBookingId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showResult(`✅ Payment intent created successfully!\n\nPayment Intent ID: ${result.data.paymentIntentId}\nClient Secret: ${result.data.clientSecret.substring(0, 20)}...\n\nYou can now use this in your frontend payment form.`, 'success');
                } else {
                    showResult(`❌ Failed to create payment intent:\n${result.message}`, 'error');
                }
            } catch (error) {
                showResult(`❌ Error creating payment intent:\n${error.message}`, 'error');
            }
        });

        function showResult(message, type) {
            const resultDiv = document.getElementById('result');
            resultDiv.textContent = message;
            resultDiv.className = `result ${type}`;
        }
    </script>
</body>
</html>
