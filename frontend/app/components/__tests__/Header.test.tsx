import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import { BrowserRouter } from 'react-router';
import userEvent from '@testing-library/user-event';
import Header from '../layout/Header';

// Helper function to render component with router
const renderWithRouter = (component: React.ReactElement) => {
  return render(
    <BrowserRouter>
      {component}
    </BrowserRouter>
  );
};

describe('Header Component', () => {
  it('renders the logo', () => {
    renderWithRouter(<Header />);
    
    const logo = screen.getByText('ClinicBook');
    expect(logo).toBeInTheDocument();
  });

  it('renders navigation links', () => {
    renderWithRouter(<Header />);
    
    const homeLink = screen.getByText('Home');
    const servicesLink = screen.getByText('Services');
    
    expect(homeLink).toBeInTheDocument();
    expect(servicesLink).toBeInTheDocument();
  });

  it('has correct navigation link hrefs', () => {
    renderWithRouter(<Header />);
    
    const homeLink = screen.getByText('Home').closest('a');
    const servicesLink = screen.getByText('Services').closest('a');
    
    expect(homeLink).toHaveAttribute('href', '/');
    expect(servicesLink).toHaveAttribute('href', '/offers');
  });

  it('renders mobile menu button', () => {
    renderWithRouter(<Header />);
    
    const mobileMenuButton = screen.getByRole('button');
    expect(mobileMenuButton).toBeInTheDocument();
  });

  it('applies correct CSS classes', () => {
    renderWithRouter(<Header />);

    const header = screen.getByRole('banner');
    expect(header).toHaveClass('bg-white', 'shadow-sm', 'border-b');
  });

  it('toggles mobile menu when button is clicked', async () => {
    const user = userEvent.setup();
    renderWithRouter(<Header />);

    const mobileMenuButton = screen.getByLabelText('Toggle mobile menu');

    // Mobile menu should not be visible initially
    expect(screen.queryByText('Home')).toBeInTheDocument(); // Desktop nav

    // Click to open mobile menu
    await user.click(mobileMenuButton);

    // Should now have mobile menu items (in addition to desktop nav)
    const homeLinks = screen.getAllByText('Home');
    const servicesLinks = screen.getAllByText('Services');

    expect(homeLinks).toHaveLength(2); // Desktop + mobile
    expect(servicesLinks).toHaveLength(2); // Desktop + mobile
  });
});
