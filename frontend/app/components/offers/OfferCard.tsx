import React from 'react';
import { Link } from 'react-router';
import { type Offer } from '~/services/api';
import Card from '~/components/ui/Card';

interface OfferCardProps {
  offer: Offer;
}

const OfferCard: React.FC<OfferCardProps> = ({ offer }) => {
  return (
    <Link to={`/offers/${offer.id}`} className="block group">
      <Card hover className="h-full">
        <div className="mb-4">
          <h3 className="text-xl font-semibold text-slate-900 group-hover:text-emerald-700 transition-colors mb-2">
            {offer.title}
          </h3>
          <div className="flex items-baseline gap-2">
            <div className="text-2xl font-bold text-emerald-600">${offer.price}</div>
            <div className="text-sm text-slate-500">per session</div>
          </div>
        </div>

        <p className="text-slate-600 mb-6 line-clamp-3 leading-relaxed">
          {offer.description}
        </p>
        
        <div className="flex items-center justify-between pt-4 border-t border-slate-100">
          <div className="flex items-center text-sm text-slate-500">
            <div className="w-6 h-6 bg-slate-200 rounded-full flex items-center justify-center mr-2">
              <svg className="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            {offer.user.name}
          </div>
          
          <div className="text-emerald-600 font-medium text-sm group-hover:text-emerald-700 transition-colors">
            View Details →
          </div>
        </div>
      </Card>
    </Link>
  );
};

export default OfferCard;
