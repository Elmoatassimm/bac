import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { type Offer } from '~/services/api';
import { useBooking } from '~/hooks/useBooking';
import Button from '~/components/ui/Button';
import FormField from '~/components/ui/FormField';
import Card from '~/components/ui/Card';
import Icon from '~/components/ui/Icon';

interface BookingFormProps {
  offer: Offer;
  onCancel: () => void;
}

interface FormData {
  client_name: string;
  client_email: string;
  client_phone: string;
  booking_date: string;
}

export default function BookingForm({ offer, onCancel }: BookingFormProps) {
  const [success, setSuccess] = useState(false);
  const { createBooking, loading, error } = useBooking();

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>();

  const onSubmit = async (data: FormData) => {
    try {
      await createBooking({
        offer_id: offer.id,
        client_name: data.client_name,
        client_email: data.client_email,
        client_phone: data.client_phone,
        booking_date: data.booking_date,
      });
    } catch (err) {
      // Error is handled by the hook
    }
  };

  if (success) {
    return (
      <div className="bg-emerald-50 border border-emerald-200 rounded-xl p-6 text-center max-w-md mx-auto">
        <div className="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h3 className="text-lg font-semibold text-slate-900 mb-2">Booking Confirmed!</h3>
        <p className="text-sm text-slate-600 mb-6 leading-relaxed">
          Your appointment has been successfully booked. You will receive a confirmation email shortly.
        </p>
        <button
          onClick={() => window.location.href = '/'}
          className="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-emerald-700 transition-colors text-sm"
        >
          Back to Home
        </button>
      </div>
    );
  }

  return (
    <Card className="max-w-lg mx-auto shadow-xl border-0">
      {/* Header Section */}
      <div className="text-center mb-8">
        <div className="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
          <Icon name="calendar" size="lg" className="text-white" />
        </div>
        <h3 className="text-2xl font-bold text-slate-900 mb-2">Book Your Appointment</h3>
        <p className="text-slate-600">Complete the form below to secure your booking</p>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        {/* Personal Information Section */}
        <div className="space-y-5">
          <div className="space-y-1">
            <label htmlFor="client_name" className="block text-sm font-semibold text-slate-800 mb-2">
              Full Name *
            </label>
            <div className="relative">
              <input
                type="text"
                id="client_name"
                {...register('client_name', { required: 'Name is required' })}
                className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 text-slate-900 placeholder-slate-400"
                placeholder="Enter your full name"
              />
              <div className="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg className="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
            </div>
            {errors.client_name && (
              <p className="text-red-500 text-sm mt-2 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                <Icon name="error" size="sm" className="text-red-500 mr-2" />
                {errors.client_name.message}
              </p>
            )}
          </div>

          <div className="space-y-1">
            <label htmlFor="client_email" className="block text-sm font-semibold text-slate-800 mb-2">
              Email Address *
            </label>
            <div className="relative">
              <input
                type="email"
                id="client_email"
                {...register('client_email', {
                  required: 'Email is required',
                  pattern: {
                    value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                    message: 'Invalid email address'
                  }
                })}
                className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 text-slate-900 placeholder-slate-400"
                placeholder="Enter your email address"
              />
              <div className="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg className="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
              </div>
            </div>
            {errors.client_email && (
              <p className="text-red-500 text-sm mt-2 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                <Icon name="error" size="sm" className="text-red-500 mr-2" />
                {errors.client_email.message}
              </p>
            )}
          </div>

          <div className="space-y-1">
            <label htmlFor="client_phone" className="block text-sm font-semibold text-slate-800 mb-2">
              Phone Number *
            </label>
            <div className="relative">
              <input
                type="tel"
                id="client_phone"
                {...register('client_phone', { required: 'Phone number is required' })}
                className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 text-slate-900 placeholder-slate-400"
                placeholder="Enter your phone number"
              />
              <div className="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg className="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
            </div>
            {errors.client_phone && (
              <p className="text-red-500 text-sm mt-2 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                <Icon name="error" size="sm" className="text-red-500 mr-2" />
                {errors.client_phone.message}
              </p>
            )}
          </div>

          <div className="space-y-1">
            <label htmlFor="booking_date" className="block text-sm font-semibold text-slate-800 mb-2">
              Preferred Date & Time *
            </label>
            <div className="relative">
              <input
                type="datetime-local"
                id="booking_date"
                {...register('booking_date', { required: 'Booking date is required' })}
                className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 text-slate-900"
                min={new Date().toISOString().slice(0, 16)}
              />
              <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <Icon name="calendar" size="sm" className="text-slate-400" />
              </div>
            </div>
            {errors.booking_date && (
              <p className="text-red-500 text-sm mt-2 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                <Icon name="error" size="sm" className="text-red-500 mr-2" />
                {errors.booking_date.message}
              </p>
            )}
          </div>
        </div>

        {/* Error Message */}
        {error && (
          <div className="bg-red-50 border-l-4 border-red-400 rounded-lg p-4">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <Icon name="error" className="text-red-400" />
              </div>
              <div className="ml-3">
                <p className="text-red-800 text-sm font-medium">{error}</p>
              </div>
            </div>
          </div>
        )}

        {/* Booking Summary */}
        <div className="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-2xl p-6 border border-emerald-200/50">
          <h4 className="text-lg font-bold text-slate-900 mb-4 flex items-center">
            <div className="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
              <svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            Booking Summary
          </h4>
          <div className="space-y-3">
            <div className="flex justify-between items-center py-2">
              <span className="text-slate-700 font-medium">Service:</span>
              <span className="font-semibold text-slate-900 text-right max-w-xs">{offer.title}</span>
            </div>
            <div className="flex justify-between items-center py-2">
              <span className="text-slate-700 font-medium">Provider:</span>
              <span className="font-semibold text-slate-900">{offer.user.name}</span>
            </div>
            <div className="border-t border-emerald-200 pt-3 mt-3">
              <div className="flex justify-between items-center">
                <span className="text-lg font-bold text-slate-900">Total Amount:</span>
                <div className="text-right">
                  <span className="text-2xl font-bold text-emerald-600">${offer.price}</span>
                  <div className="text-sm text-slate-600">per session</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="flex flex-col gap-4 pt-6">
          <Button
            type="submit"
            loading={loading}
            size="lg"
            className="w-full shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
          >
            {loading ? 'Processing...' : 'Continue to Payment'}
          </Button>
          <Button
            type="button"
            variant="outline"
            onClick={onCancel}
            disabled={loading}
            size="lg"
            className="w-full border-2 hover:bg-slate-50"
          >
            Cancel Booking
          </Button>
        </div>
      </form>
    </Card>
  );
}
