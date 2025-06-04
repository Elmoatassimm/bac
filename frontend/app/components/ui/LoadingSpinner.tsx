import React from 'react';

interface LoadingSpinnerProps {
  size?: 'sm' | 'md' | 'lg';
  color?: 'primary' | 'secondary';
  text?: string;
  className?: string;
}

const LoadingSpinner: React.FC<LoadingSpinnerProps> = ({
  size = 'md',
  color = 'primary',
  text,
  className = ''
}) => {
  const sizeClasses = {
    sm: 'h-4 w-4',
    md: 'h-8 w-8',
    lg: 'h-12 w-12'
  };
  
  const colorClasses = {
    primary: 'border-emerald-600 border-t-transparent',
    secondary: 'border-slate-600 border-t-transparent'
  };
  
  const spinnerClasses = `animate-spin rounded-full border-4 ${sizeClasses[size]} ${colorClasses[color]}`;
  
  return (
    <div className={`flex flex-col items-center justify-center ${className}`}>
      <div className={spinnerClasses}></div>
      {text && (
        <p className="text-slate-600 mt-2 text-sm">{text}</p>
      )}
    </div>
  );
};

export default LoadingSpinner;
