import type { Route } from "./+types/home";
import { Link } from 'react-router';
import Header from '~/components/layout/Header';
import Footer from '~/components/layout/Footer';
import OffersList from '~/components/offers/OffersList';
import Button from '~/components/ui/Button';
import Icon from '~/components/ui/Icon';
import { useOffers } from '~/hooks/useOffers';

export function meta({}: Route.MetaArgs) {
  return [
    { title: "ClinicBook - Book Healthcare Appointments" },
    { name: "description", content: "Find and book appointments with trusted clinics in your area" },
  ];
}

export default function Home() {
  const { offers, loading } = useOffers();

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      <Header />

      <main className="max-w-5xl mx-auto px-4 py-16">
        {/* Hero Section */}
        <div className="text-center mb-20">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-6">
            <svg className="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
          </div>
          <h1 className="text-4xl md:text-5xl font-light text-slate-900 mb-6 tracking-tight">
            Quality Healthcare
            <span className="block text-emerald-600 font-medium">Made Simple</span>
          </h1>
          <p className="text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
            Discover trusted healthcare providers and book appointments with ease.
            Professional care, simplified booking.
          </p>
        </div>

        {/* Services Section */}
        <div className="space-y-12">
          {loading ? (
            <div className="text-center py-20">
              <div className="inline-flex items-center space-x-2">
                <div className="w-2 h-2 bg-emerald-500 rounded-full animate-bounce"></div>
                <div className="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style={{animationDelay: '0.1s'}}></div>
                <div className="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style={{animationDelay: '0.2s'}}></div>
              </div>
              <p className="text-slate-500 mt-4">Loading services...</p>
            </div>
          ) : offers.length === 0 ? (
            <div className="text-center py-20">
              <div className="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <h3 className="text-lg font-medium text-slate-900 mb-2">No Services Available</h3>
              <p className="text-slate-500">Check back soon for new healthcare services.</p>
            </div>
          ) : (
            <>
              <div className="text-center mb-12">
                <h2 className="text-2xl font-light text-slate-900 mb-3">Featured Services</h2>
                <div className="w-12 h-0.5 bg-emerald-500 mx-auto"></div>
              </div>

              <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                {offers.map((offer) => (
                  <div key={offer.id} className="group bg-white rounded-xl border border-slate-200 p-8 hover:border-emerald-300 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div className="flex justify-between items-start mb-6">
                      <h3 className="text-xl font-semibold text-slate-900 group-hover:text-emerald-700 transition-colors">
                        {offer.title}
                      </h3>
                      <div className="text-right">
                        <div className="text-2xl font-bold text-emerald-600">${offer.price}</div>
                        <div className="text-xs text-slate-500">per session</div>
                      </div>
                    </div>

                    <p className="text-slate-600 mb-6 leading-relaxed line-clamp-3">{offer.description}</p>

                    <div className="flex items-center mb-8">
                      <div className="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                        <svg className="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                      </div>
                      <div>
                        <div className="text-sm font-medium text-slate-900">{offer.user.name}</div>
                        <div className="text-xs text-slate-500">Healthcare Provider</div>
                      </div>
                    </div>

                    <Link
                      to={`/offers/${offer.id}`}
                      className="block w-full text-center bg-emerald-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-emerald-700 transition-colors duration-200 group-hover:shadow-md"
                    >
                      Book Appointment
                    </Link>
                  </div>
                ))}
              </div>
            </>
          )}

          {offers.length > 0 && (
            <div className="text-center pt-12">
              <Link
                to="/offers"
                className="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors group"
              >
                View all services
                <svg className="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
            </div>
          )}
        </div>
      </main>

      <Footer />
    </div>
  );
}
