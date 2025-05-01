import React from 'react';
import { render, screen } from '@testing-library/react';
import LocationCard from './LocationCard';

describe('LocationCard Component', () => {
  const mockLocation = {
    code: 'LOC001',
    name: 'Headquarters',
    image: 'https://example.com/images/headquarters.jpg',
    creationDate: '2023-01-15'
  };

  test('renders location information correctly', () => {
    render(<LocationCard location={mockLocation} />);
    
    // Check if the name is displayed
    expect(screen.getByText('Headquarters')).toBeInTheDocument();
    
    // Check if the code is displayed
    expect(screen.getByText('LOC001')).toBeInTheDocument();
    
    // Check if the creation date is displayed
    expect(screen.getByText('2023-01-15')).toBeInTheDocument();
    
    // Check if the image is rendered with correct alt text
    const image = screen.getByAltText('Headquarters');
    expect(image).toBeInTheDocument();
    expect(image).toHaveAttribute('src', 'https://example.com/images/headquarters.jpg');
  });
});
