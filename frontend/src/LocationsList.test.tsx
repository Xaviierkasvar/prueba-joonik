import React from 'react';
import { render, screen, waitFor } from '@testing-library/react';
import LocationsList from './LocationsList';
import * as ApiService from './api-service';

// Mock the API service
jest.mock('./api-service');
const mockGetLocations = ApiService.getLocations as jest.MockedFunction<typeof ApiService.getLocations>;

describe('LocationsList Component', () => {
  const mockLocations = [
    {
      code: 'LOC001',
      name: 'Sede Central',
      image: 'https://example.com/images/headquarters.jpg',
      creationDate: '2023-01-15'
    },
    {
      code: 'LOC002',
      name: 'Oficina Centro',
      image: 'https://example.com/images/downtown.jpg',
      creationDate: '2023-02-20'
    }
  ];

  beforeEach(() => {
    jest.clearAllMocks();
  });

  test('displays loading indicator initially', () => {
    mockGetLocations.mockImplementation(() => new Promise(() => {})); // Never resolves
    render(<LocationsList />);
    expect(screen.getByRole('progressbar')).toBeInTheDocument();
  });

  test('displays locations after loading', async () => {
    mockGetLocations.mockResolvedValue({
      success: true,
      data: mockLocations,
      meta: {
        current_page: 1,
        from: 1,
        last_page: 1,
        per_page: 10,
        to: 2,
        total: 2
      }
    });

    render(<LocationsList />);
    
    // Should show loading initially
    expect(screen.getByRole('progressbar')).toBeInTheDocument();
    
    // After loading, should show location names
    await waitFor(() => {
      expect(screen.getByText('Sede Central')).toBeInTheDocument();
      expect(screen.getByText('Oficina Centro')).toBeInTheDocument();
    });
  });

  test('displays error message when API call fails', async () => {
    mockGetLocations.mockRejectedValue(new Error('API Error'));

    render(<LocationsList />);
    
    await waitFor(() => {
      expect(screen.getByText('Error al conectar con el servidor')).toBeInTheDocument();
    });
  });
});
