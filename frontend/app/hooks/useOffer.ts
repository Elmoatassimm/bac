import { useState, useEffect } from 'react';
import { offersApi, type Offer } from '~/services/api';

interface UseOfferReturn {
  offer: Offer | null;
  loading: boolean;
  error: string | null;
  refetch: () => Promise<void>;
}

export const useOffer = (id: number): UseOfferReturn => {
  const [offer, setOffer] = useState<Offer | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchOffer = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await offersApi.getById(id);
      if (response.success) {
        setOffer(response.data);
      } else {
        setError(response.message || 'Failed to fetch offer');
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'An error occurred');
      console.error('Error fetching offer:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (id) {
      fetchOffer();
    }
  }, [id]);

  return {
    offer,
    loading,
    error,
    refetch: fetchOffer
  };
};
