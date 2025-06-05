import { useEffect, useState } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router';
import Header from '~/components/layout/Header';
import Footer from '~/components/layout/Footer';
import PaymentWrapper from '~/components/payment/PaymentWrapper';
import { type Booking } from '~/services/api';

export function meta() {
  return [
    { title: "Payment - ClinicBook" },
    { name: "description", content: "Complete your booking payment securely" },
  ];
}

export default function PaymentPage() {
  const { bookingId } = useParams();
  const navigate = useNavigate();
  const location = useLocation();
  const [booking, setBooking] = useState<Booking | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    // Try to get booking from navigation state first
    const stateBooking = location.state?.booking as Booking;

    if (stateBooking && stateBooking.id.toString() === bookingId) {
      setBooking(stateBooking);
      setLoading(false);
      return;
    }

    // If no booking in state, show error (in a real app, you'd fetch from API)
    if (!bookingId) {
      setError('No booking ID provided');
      setLoading(false);
      return;
    }

    // For now, redirect back to offers if no booking data
    // In a real implementation, you'd fetch the booking details here
    setError('Booking data not found. Please start the booking process again.');
    setLoading(false);
  }, [bookingId, location.state, navigate]);

  const handlePaymentSuccess = () => {
    // Handle successful payment
    console.log('Payment successful!');
    navigate('/offers');
  };

  const handleCancel = () => {
    // Handle payment cancellation
    navigate('/offers');
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <main className="flex items-center justify-center py-20">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mx-auto mb-4"></div>
            <p className="text-gray-600">Loading booking details...</p>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  if (error || !booking) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <main className="flex items-center justify-center py-20">
          <div className="text-center">
            <div className="text-6xl mb-4">❌</div>
            <h1 className="text-2xl font-bold text-gray-900 mb-2">Booking Not Found</h1>
            <p className="text-gray-600 mb-6">{error || 'The booking you\'re looking for doesn\'t exist.'}</p>
            <button
              onClick={() => navigate('/offers')}
              className="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
            >
              Browse Services
            </button>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <main className="py-8">
        <PaymentWrapper
          booking={booking}
          onSuccess={handlePaymentSuccess}
          onCancel={handleCancel}
        />
      </main>
      <Footer />
    </div>
  );
}
