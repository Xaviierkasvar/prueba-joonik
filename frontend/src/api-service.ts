import { ApiResponse } from './location.types';

// API Key for authentication
const API_KEY = 'locations-api-key-12345';

// Base URL for API endpoints
const API_BASE_URL = 'http://localhost:8000/api';

/**
 * Service for fetching locations from the API
 * @param params URLSearchParams opcional con parámetros de paginación, ordenamiento y búsqueda
 */
export const getLocations = async (params?: URLSearchParams): Promise<ApiResponse> => {
  try {
    // Construir la URL con los parámetros si existen
    let url = `${API_BASE_URL}/locations`;
    if (params && params.toString()) {
      url += `?${params.toString()}`;
    }

    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': API_KEY
      }
    });

    if (!response.ok) {
      throw new Error(`Error: ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error('Error fetching locations:', error);
    throw error;
  }
};

/**
 * Service for fetching a specific location by code
 * @param code Código de la sede a obtener
 */
export const getLocationByCode = async (code: string): Promise<ApiResponse> => {
  try {
    const response = await fetch(`${API_BASE_URL}/locations/${code}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': API_KEY
      }
    });

    if (!response.ok) {
      throw new Error(`Error: ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error(`Error fetching location with code ${code}:`, error);
    throw error;
  }
};
