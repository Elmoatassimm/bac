import React, { useState, useEffect } from 'react';
import { Elements } from '@stripe/react-stripe-js';
import { useNavigate } from 'react-router';
import stripePromise from '~/services/stripe';
import CheckoutForm from './CheckoutForm';
import Toast from '../ui/Toast';
import { paymentsApi, type Booking } from '~/services/api';

interface PaymentWrapperProps {
  booking: Booking;
  onSuccess?: () => void;
  onCancel?: () => void;
}

const PaymentWrapper: React.FC<PaymentWrapperProps> = ({ 
  booking, 
  onSuccess, 
  onCancel 
}) => {
  const [clientSecret, setClientSecret] = useState('');
  const [message, setMessage] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    const createPaymentIntent = async () => {
      try {
        setIsLoading(true);
        const response = await paymentsApi.createPaymentIntent(booking.id);
        
        if (!response.success) {
          if (response.message === "Booking already paid") {
            setMessage("This booking has already been paid for.");
            setTimeout(() => {
              navigate("/offers");
            }, 2000);
          } else {
            setMessage(response.message || "An error occurred while creating the payment intent");
          }
          return;
        }

        setClientSecret(response.data.clientSecret);
      } catch (error) {
        console.error('Error creating payment intent:', error);
        setMessage("An error occurred while setting up payment");
      } finally {
        setIsLoading(false);
      }
    };

    createPaymentIntent();
  }, [booking.id, navigate]);
  
  const appearance = {
    theme: 'stripe' as const,
    variables: {
      colorPrimary: '#2563eb',
      colorBackground: '#ffffff',
      colorText: '#1f2937',
      colorDanger: '#dc2626',
      fontFamily: 'system-ui, sans-serif',
      spacingUnit: '4px',
      borderRadius: '8px',
    }
  };

  const options = {
    clientSecret,
    appearance,
  };

  if (isLoading) {
    return (
      <div className="min-h-screen flex justify-center items-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mx-auto mb-4"></div>
          <p className="text-gray-600">Setting up payment...</p>
        </div>
      </div>
    );
  }

  if (message && !clientSecret) {
    return (
      <div className="min-h-screen flex justify-center items-center">
        <Toast
          message={message}
          success={message.includes('paid')}
          onClose={() => setMessage(null)}
        />
        <div className="text-center">
          <div className="text-6xl mb-4">
            {message.includes('paid') ? '✅' : '❌'}
          </div>
          <p className="text-gray-600">{message}</p>
        </div>
      </div>
    );
  }

  return clientSecret ? (
    <div className="min-h-screen bg-gray-50 flex items-center justify-center p-4">
      <Elements stripe={stripePromise} options={options}>
        <CheckoutForm 
          booking={booking}
          onSuccess={onSuccess}
          onCancel={onCancel}
        />
      </Elements>
    </div>
  ) : null;
};

export default PaymentWrapper;
