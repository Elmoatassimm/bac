import React from 'react';

interface FormFieldProps {
  label: string;
  name: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
  error?: string;
  register?: any; // react-hook-form register function
  className?: string;
}

const FormField: React.FC<FormFieldProps> = ({
  label,
  name,
  type = 'text',
  placeholder,
  required = false,
  error,
  register,
  className = ''
}) => {
  return (
    <div className={className}>
      <label htmlFor={name} className="block text-sm font-medium text-slate-700 mb-1">
        {label} {required && '*'}
      </label>
      <input
        type={type}
        id={name}
        {...(register ? register(name, { required: required ? `${label} is required` : false }) : {})}
        className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 transition-colors text-sm"
        placeholder={placeholder}
      />
      {error && (
        <p className="text-red-600 text-xs mt-1 flex items-center">
          <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {error}
        </p>
      )}
    </div>
  );
};

export default FormField;
