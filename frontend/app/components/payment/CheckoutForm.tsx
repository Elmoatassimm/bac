import React, { useState } from 'react';
import {
  PaymentElement,
  useStripe,
  useElements
} from '@stripe/react-stripe-js';
import { useNavigate } from 'react-router';
import Toast from '../ui/Toast';
import type { Booking } from '~/services/api';

interface CheckoutFormProps {
  booking: Booking;
  onSuccess?: () => void;
  onCancel?: () => void;
}

const CheckoutForm: React.FC<CheckoutFormProps> = ({ 
  booking, 
  onSuccess, 
  onCancel 
}) => {
  const navigate = useNavigate();
  const stripe = useStripe();
  const elements = useElements();

  const [message, setMessage] = useState<string | null>(null);
  const [isProcessing, setIsProcessing] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!stripe || !elements) {
      return;
    }

    setIsProcessing(true);

    const { error, paymentIntent } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: `${window.location.origin}/offers`,
      },
      redirect: 'if_required'
    });

    if (error) {
      setMessage(error.message || 'An error occurred during payment');
    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
      setMessage('Payment succeeded! Your booking is confirmed.');
      
      // Call success callback if provided
      if (onSuccess) {
        onSuccess();
      }
      
      // Navigate after a delay to show success message
      setTimeout(() => {
        navigate('/offers');
      }, 2000);
      
    } else {
      setMessage('Unexpected payment state. Please contact support.');
    }

    setIsProcessing(false);
  };

  return (
    <div className="max-w-md mx-auto bg-white rounded-xl shadow-lg p-8">
      {/* Toast Notification for payment status */}
      <Toast
        message={message}
        success={message?.includes('succeeded') || false}
        onClose={() => setMessage(null)}
      />

      <div className="mb-6">
        <h2 className="text-2xl font-bold text-gray-900 mb-2">Complete Payment</h2>
        <div className="bg-gray-50 p-4 rounded-lg">
          <p className="text-sm text-gray-600 mb-1">Service: {booking.offer.title}</p>
          <p className="text-sm text-gray-600 mb-1">Date: {new Date(booking.booking_date).toLocaleDateString()}</p>
          <p className="text-lg font-semibold text-gray-900">Total: ${booking.total_amount}</p>
        </div>
      </div>
      
      <div className="bg-blue-50 p-4 rounded-lg mb-6">
        <h3 className="font-semibold mb-2 text-blue-900">Test Card Numbers:</h3>
        <p className="text-sm text-blue-800">✅ Success: 4242 4242 4242 4242</p>
        <p className="text-sm text-blue-800">❌ Decline: 4000 0000 0000 0002</p>
        <p className="text-sm text-blue-600">Use any future date and CVC</p>
      </div>

      <form onSubmit={handleSubmit}>
        <PaymentElement />
        
        <div className="flex gap-3 mt-6">
          {onCancel && (
            <button 
              type="button"
              onClick={onCancel}
              className="flex-1 py-3 px-4 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
          )}
          
          <button 
            type="submit"
            disabled={isProcessing || !stripe || !elements}
            className={`
              flex-1 py-3 px-4 rounded-lg font-medium transition-colors
              ${isProcessing 
                ? 'bg-gray-400 cursor-not-allowed text-white' 
                : 'bg-blue-600 hover:bg-blue-700 text-white'} 
            `}
          >
            {isProcessing ? 'Processing...' : `Pay $${booking.total_amount}`}
          </button>
        </div>
      </form>
    </div>
  );
};

export default CheckoutForm;
