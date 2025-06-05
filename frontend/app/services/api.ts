import axios from 'axios';

// Create axios instance with base configuration
const api = axios.create({
  baseURL: '/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Types
export interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
  updated_at: string;
}

export interface Offer {
  id: number;
  user_id: number;
  title: string;
  description: string;
  price: number;
  created_at: string;
  updated_at: string;
  user: User;
}

export interface Client {
  id: number;
  name: string;
  email: string;
  phone: string;
  created_at: string;
  updated_at: string;
}

export interface Payment {
  id: number;
  booking_id: number;
  payment_intent_id: string | null;
  amount: number;
  status: 'pending' | 'processing' | 'completed' | 'failed' | 'cancelled' | 'refunded';
  transaction_id: string | null;
  paid_at: string | null;
  failed_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface Booking {
  id: number;
  offer_id: number;
  client_id: number;
  booking_date: string;
  status: 'pending' | 'confirmed' | 'cancelled' | 'completed';
  total_amount: number;
  created_at: string;
  updated_at: string;
  offer: Offer;
  client: Client;
  payment: Payment;
}

export interface BookingRequest {
  offer_id: number;
  client_name: string;
  client_email: string;
  client_phone: string;
  booking_date: string;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message: string;
  error?: string;
}

// API functions
export const offersApi = {
  // Get all active offers
  getAll: async (): Promise<ApiResponse<Offer[]>> => {
    const response = await api.get('/offers');
    return response.data;
  },

  // Get specific offer by ID
  getById: async (id: number): Promise<ApiResponse<Offer>> => {
    const response = await api.get(`/offers/${id}`);
    return response.data;
  },
};

export const bookingsApi = {
  // Create a new booking
  create: async (bookingData: BookingRequest): Promise<ApiResponse<{
    booking: Booking;
    client_secret: string;
  }>> => {
    const response = await api.post('/bookings', bookingData);
    return response.data;
  },
};

export const paymentsApi = {
  // Create a payment intent for a booking
  createPaymentIntent: async (bookingId: number): Promise<ApiResponse<{
    clientSecret: string;
    paymentIntentId: string;
  }>> => {
    const response = await api.post(`/create-payment-intent/${bookingId}`);
    return response.data;
  },
};

// Error handling interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('API Error:', error);
    return Promise.reject(error);
  }
);

export default api;
