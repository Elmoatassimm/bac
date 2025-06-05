import { useEffect, useState } from 'react';
import { useParams } from 'react-router';
import Header from '~/components/layout/Header';
import Footer from '~/components/layout/Footer';
import BookingForm from '~/components/booking/BookingForm';
import { offersApi, type Offer } from '~/services/api';

export function meta() {
  return [
    { title: "Service Details - ClinicBook" },
    { name: "description", content: "View service details and book your appointment" },
  ];
}

export default function OfferDetail() {
  const { id } = useParams();
  const [offer, setOffer] = useState<Offer | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showBookingForm, setShowBookingForm] = useState(false);

  useEffect(() => {
    const fetchOffer = async () => {
      if (!id) return;

      try {
        const response = await offersApi.getById(parseInt(id));
        if (response.success) {
          setOffer(response.data);
        } else {
          setError(response.message);
        }
      } catch (err) {
        setError('Failed to load offer details');
        console.error('Error fetching offer:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchOffer();
  }, [id]);

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
        <Header />
        <main className="max-w-5xl mx-auto px-4 py-16">
          <div className="text-center py-20">
            <div className="inline-flex items-center space-x-2">
              <div className="w-2 h-2 bg-emerald-500 rounded-full animate-bounce"></div>
              <div className="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style={{animationDelay: '0.1s'}}></div>
              <div className="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style={{animationDelay: '0.2s'}}></div>
            </div>
            <p className="text-slate-500 mt-4">Loading service details...</p>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  if (error || !offer) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
        <Header />
        <main className="max-w-5xl mx-auto px-4 py-16">
          <div className="text-center py-20">
            <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg className="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <h2 className="text-xl font-semibold text-slate-900 mb-3">Service Not Found</h2>
            <p className="text-slate-600 mb-8 max-w-md mx-auto">{error || 'The requested service could not be found.'}</p>
            <a
              href="/offers"
              className="bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-emerald-700 transition-colors"
            >
              Back to Services
            </a>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      <Header />

      <main className="max-w-4xl mx-auto px-4 py-16">
        {/* Breadcrumb */}
        <nav className="mb-8">
          <div className="flex items-center space-x-2 text-sm text-slate-500">
            <a href="/offers" className="hover:text-emerald-600 transition-colors">Services</a>
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
            </svg>
            <span className="text-slate-900">{offer.title}</span>
          </div>
        </nav>

        <div className="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
          {/* Service Header */}
          <div className="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white p-8">
            <div className="flex justify-between items-start">
              <div>
                <h1 className="text-3xl font-bold mb-2">{offer.title}</h1>
                <div className="flex items-center space-x-4 text-emerald-100">
                  <div className="flex items-center space-x-2">
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{offer.user.name}</span>
                  </div>
                  <div className="flex items-center space-x-2">
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>{offer.user.email}</span>
                  </div>
                </div>
              </div>
              <div className="text-right">
                <div className="text-4xl font-bold">${offer.price}</div>
                <div className="text-emerald-200 text-sm">per session</div>
              </div>
            </div>
          </div>

          <div className="p-8">
            <div className="grid grid-cols-1 lg:grid-cols-5 gap-8">
              {/* Service Description */}
              <div className="lg:col-span-3 space-y-6">
                <div>
                  <h2 className="text-xl font-semibold text-slate-900 mb-4 flex items-center">
                    <svg className="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Service Description
                  </h2>
                  <p className="text-slate-600 leading-relaxed text-lg">{offer.description}</p>
                </div>

                <div className="bg-slate-50 rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <svg className="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    What's Included
                  </h3>
                  <ul className="space-y-2 text-slate-600">
                    <li className="flex items-center">
                      <svg className="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                      </svg>
                      Professional consultation
                    </li>
                    <li className="flex items-center">
                      <svg className="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                      </svg>
                      Detailed assessment
                    </li>
                    <li className="flex items-center">
                      <svg className="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                      </svg>
                      Follow-up recommendations
                    </li>
                  </ul>
                </div>
              </div>

              {/* Booking Section */}
              <div className="lg:col-span-2">
                {!showBookingForm ? (
                  <div className="bg-emerald-50 rounded-xl p-6 border border-emerald-200 max-w-md mx-auto">
                    <div className="text-center mb-6">
                      <div className="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <h3 className="text-xl font-semibold text-slate-900 mb-2">Ready to Book?</h3>
                      <p className="text-slate-600 mb-6">
                        Schedule your appointment and pay securely online
                      </p>
                    </div>
                    <button
                      onClick={() => setShowBookingForm(true)}
                      className="w-full bg-emerald-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:bg-emerald-700 transition-colors duration-200 shadow-lg hover:shadow-xl"
                    >
                      Book Now - ${offer.price}
                    </button>
                  </div>
                ) : (
                  <BookingForm
                    offer={offer}
                    onCancel={() => setShowBookingForm(false)}
                  />
                )}
              </div>
            </div>
          </div>

        </div>
      </main>

      <Footer />
    </div>
  );
}
