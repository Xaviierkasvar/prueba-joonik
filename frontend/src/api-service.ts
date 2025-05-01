import { ApiResponse } from './location.types';

// API Key for authentication
const API_KEY = 'locations-api-key-12345';

// Base URL for API endpoints - usando ruta relativa directamente
const API_BASE_URL = '/api';

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
    
    console.log('Fetching from URL:', url); // Para depuración
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': API_KEY
      }
    });

    if (!response.ok) {
      const errorText = await response.text();
      console.error('Response not OK:', response.status, errorText);
      throw new Error(`Error: ${response.status} - ${errorText}`);
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
    const url = `${API_BASE_URL}/locations/${code}`;
    console.log('Fetching specific location from URL:', url); // Para depuración
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': API_KEY
      }
    });

    if (!response.ok) {
      const errorText = await response.text();
      console.error('Response not OK:', response.status, errorText);
      throw new Error(`Error: ${response.status} - ${errorText}`);
    }

    return await response.json();
  } catch (error) {
    console.error(`Error fetching location with code ${code}:`, error);
    throw error;
  }
};