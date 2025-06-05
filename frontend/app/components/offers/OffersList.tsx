import React from 'react';
import { type Offer } from '~/services/api';
import OfferCard from './OfferCard';
import LoadingSpinner from '~/components/ui/LoadingSpinner';
import Icon from '~/components/ui/Icon';

interface OffersListProps {
  offers: Offer[];
  loading: boolean;
  error: string | null;
}

const OffersList: React.FC<OffersListProps> = ({ offers, loading, error }) => {
  if (loading) {
    return (
      <div className="flex justify-center py-20">
        <LoadingSpinner size="lg" text="Loading services..." />
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-20">
        <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <Icon name="error" size="lg" className="text-red-600" />
        </div>
        <h3 className="text-xl font-semibold text-slate-900 mb-3">Error Loading Services</h3>
        <p className="text-slate-500 max-w-md mx-auto">{error}</p>
      </div>
    );
  }

  if (offers.length === 0) {
    return (
      <div className="text-center py-20">
        <div className="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
          <Icon name="services" size="lg" className="text-slate-400" />
        </div>
        <h3 className="text-xl font-semibold text-slate-900 mb-3">No Services Available</h3>
        <p className="text-slate-500 max-w-md mx-auto">
          We're working on adding new healthcare services. Please check back soon.
        </p>
      </div>
    );
  }

  return (
    <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      {offers.map((offer) => (
        <OfferCard key={offer.id} offer={offer} />
      ))}
    </div>
  );
};

export default OffersList;
