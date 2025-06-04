import React from 'react';

interface CardProps {
  children: React.ReactNode;
  className?: string;
  hover?: boolean;
  padding?: 'sm' | 'md' | 'lg';
}

const Card: React.FC<CardProps> = ({
  children,
  className = '',
  hover = false,
  padding = 'md'
}) => {
  const baseClasses = 'bg-white rounded-xl border border-slate-200 shadow-lg';
  const hoverClasses = hover ? 'hover:border-emerald-300 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2' : '';
  
  const paddingClasses = {
    sm: 'p-4',
    md: 'p-6',
    lg: 'p-8'
  };
  
  const classes = `${baseClasses} ${hoverClasses} ${paddingClasses[padding]} ${className}`;
  
  return (
    <div className={classes}>
      {children}
    </div>
  );
};

export default Card;
