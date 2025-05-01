export interface Location {
  code: string;
  name: string;
  image: string;
  creationDate: string;
}

/**
 * Metadatos de paginación devueltos por la API
 */
export interface PaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  per_page: number;
  to: number | null;
  total: number;
}

/**
 * Respuesta completa de la API para una lista de ubicaciones
 */
export interface ApiListResponse {
  success: boolean;
  data: Location[];
  meta: PaginationMeta;
}

/**
 * Respuesta de la API para una única ubicación
 */
export interface ApiSingleResponse {
  success: boolean;
  data: Location;
}

/**
 * Tipo unión para las posibles respuestas de la API
 */
export type ApiResponse = ApiListResponse | ApiSingleResponse;
