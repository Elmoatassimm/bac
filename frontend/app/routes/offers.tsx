import Header from '~/components/layout/Header';
import Footer from '~/components/layout/Footer';
import OffersList from '~/components/offers/OffersList';
import Icon from '~/components/ui/Icon';
import { useOffers } from '~/hooks/useOffers';

export function meta() {
  return [
    { title: "Available Services - ClinicBook" },
    { name: "description", content: "Browse available healthcare services and book your appointment" },
  ];
}

export default function Offers() {
  const { offers, loading, error } = useOffers();

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
            <p className="text-slate-500 mt-4">Loading services...</p>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  if (error) {
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
            <h2 className="text-xl font-semibold text-slate-900 mb-3">Error Loading Services</h2>
            <p className="text-slate-600 mb-8 max-w-md mx-auto">{error}</p>
            <button
              onClick={() => window.location.reload()}
              className="bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-emerald-700 transition-colors"
            >
              Try Again
            </button>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      <Header />

      <main className="max-w-5xl mx-auto px-4 py-16">
        <div className="text-center mb-16">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-6">
            <Icon name="services" size="lg" className="text-emerald-600" />
          </div>
          <h1 className="text-4xl font-light text-slate-900 mb-4 tracking-tight">
            All Healthcare Services
          </h1>
          <p className="text-lg text-slate-600 max-w-2xl mx-auto">
            Browse our complete catalog of professional healthcare services and book your appointment today.
          </p>
        </div>

        <OffersList offers={offers} loading={loading} error={error} />
      </main>

      <Footer />
    </div>
  );
}
