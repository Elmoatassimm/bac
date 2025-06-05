import { useState } from 'react';
import { useNavigate } from 'react-router';
import { bookingsApi, type BookingRequest, type Booking } from '~/services/api';

interface UseBookingReturn {
  createBooking: (data: BookingRequest) => Promise<void>;
  loading: boolean;
  error: string | null;
}

export const useBooking = (): UseBookingReturn => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const navigate = useNavigate();

  const createBooking = async (data: BookingRequest) => {
    try {
      setLoading(true);
      setError(null);
      
      const response = await bookingsApi.create(data);
      
      if (!response.success) {
        throw new Error(response.message);
      }

      const { booking } = response.data;
      
      // Navigate to payment page with booking data
      navigate(`/payment/${booking.id}`, {
        state: { booking }
      });
      
    } catch (err) {
      setError(err instanceof Error ? err.message : 'An error occurred');
      throw err; // Re-throw to allow component to handle if needed
    } finally {
      setLoading(false);
    }
  };

  return {
    createBooking,
    loading,
    error
  };
};
