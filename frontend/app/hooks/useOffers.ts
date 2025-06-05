import { useState, useEffect } from 'react';
import { offersApi, type Offer } from '~/services/api';

interface UseOffersReturn {
  offers: Offer[];
  loading: boolean;
  error: string | null;
  refetch: () => Promise<void>;
}

export const useOffers = (): UseOffersReturn => {
  const [offers, setOffers] = useState<Offer[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchOffers = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await offersApi.getAll();
      if (response.success) {
        setOffers(response.data);
      } else {
        setError(response.message || 'Failed to fetch offers');
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'An error occurred');
      console.error('Error fetching offers:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchOffers();
  }, []);

  return {
    offers,
    loading,
    error,
    refetch: fetchOffers
  };
};
