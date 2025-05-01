import React, { useState, useEffect, useCallback } from 'react';
import { 
  Container, 
  Typography, 
  Box, 
  CircularProgress, 
  Alert, 
  Pagination, 
  Select, 
  MenuItem, 
  FormControl, 
  InputLabel, 
  TextField, 
  SelectChangeEvent,
  Stack,
  Divider,
  Paper,
  useMediaQuery,
  useTheme
} from '@mui/material';
import LocationCard from './LocationCard';
import { getLocations } from './api-service';
import { ApiListResponse, Location } from './location.types';

const LocationsList: React.FC = () => {
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down('sm'));
  const isTablet = useMediaQuery(theme.breakpoints.between('sm', 'md'));
  const isDesktop = useMediaQuery(theme.breakpoints.up('md'));

  const [locations, setLocations] = useState<Location[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  
  // Paginación
  const [page, setPage] = useState<number>(1);
  const [perPage, setPerPage] = useState<number>(8);
  const [totalPages, setTotalPages] = useState<number>(1);
  const [totalItems, setTotalItems] = useState<number>(0);
  
  // Ordenamiento y filtrado
  const [sortBy, setSortBy] = useState<string>('creation_date');
  const [sortDirection, setSortDirection] = useState<string>('desc');
  const [searchTerm, setSearchTerm] = useState<string>('');
  const [searchQuery, setSearchQuery] = useState<string>(''); // Término real que se enviará al API
  
  // Calcular el número de tarjetas por fila según el tamaño de pantalla
  const getCardsPerRow = (): 1 | 2 | 4 => {
    if (isMobile) return 1;    // 1 tarjeta en móviles
    if (isTablet) return 2;    // 2 tarjetas en tablets
    return 4;                  // 4 tarjetas en desktop
  };
  
  const cardsPerRow = getCardsPerRow();

  // Función para cargar las sedes con paginación, ordenamiento y filtrado
  const fetchLocations = async () => {
    try {
      setLoading(true);
      
      // Construir parámetros de la consulta
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: perPage.toString(),
        sort_by: sortBy,
        sort_direction: sortDirection
      });
      
      // Añadir término de búsqueda si existe
      if (searchQuery.trim()) {
        params.append('search', searchQuery);
      }
      
      const response = await getLocations(params);
      
      // Verificar que la respuesta es para una lista
      if ('meta' in response && response.success) {
        setLocations(response.data);
        setTotalPages(response.meta.last_page);
        setTotalItems(response.meta.total);
        setError(null);
      } else {
        setError('Error al cargar las sedes');
      }
    } catch (err) {
      setError('Error al conectar con el servidor');
      // Error silenciado para mantener la consola limpia
    } finally {
      setLoading(false);
    }
  };

  // Aplicar debounce al término de búsqueda
  // Solo enviar la consulta al API cuando el usuario deja de escribir
  useEffect(() => {
    const timer = setTimeout(() => {
      setSearchQuery(searchTerm);
    }, 500); // Esperar 500ms después de que el usuario deje de escribir
    
    return () => clearTimeout(timer);
  }, [searchTerm]);

  // Cargar sedes cuando cambian los parámetros
  useEffect(() => {
    fetchLocations();
  }, [page, perPage, sortBy, sortDirection, searchQuery]); // Ahora depende de searchQuery, no searchTerm

  // Manejadores de eventos
  const handlePageChange = (_event: React.ChangeEvent<unknown>, value: number) => {
    setPage(value);
  };

  const handlePerPageChange = (event: SelectChangeEvent<number>) => {
    setPerPage(Number(event.target.value));
    setPage(1); // Volver a la primera página al cambiar items por página
  };

  const handleSortByChange = (event: SelectChangeEvent<string>) => {
    setSortBy(event.target.value);
  };

  const handleSortDirectionChange = (event: SelectChangeEvent<string>) => {
    setSortDirection(event.target.value);
  };

  const handleSearchChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSearchTerm(event.target.value);
    // Ya no necesitamos setPage(1) aquí, lo haremos cuando cambie searchQuery
  };

  // Resetear la página cuando cambia la búsqueda
  useEffect(() => {
    setPage(1);
  }, [searchQuery]);

  // Calcular el ancho de las tarjetas según el número por fila
  const getCardWidth = (): string => {
    switch (cardsPerRow) {
      case 1: return '100%';
      case 2: return '50%';
      case 4: 
      default: return '25%';
    }
  };

  return (
    <Container maxWidth="lg" sx={{ mt: 4, mb: 6 }}>
      <Paper elevation={2} sx={{ p: 3, mb: 4 }}>
        <Box sx={{ mb: 3 }}>
          <Typography variant="h4" component="h1" gutterBottom sx={{ fontWeight: 'bold', color: '#1976d2' }}>
            Sedes
          </Typography>
          <Typography variant="subtitle1" color="text.secondary">
            Explora nuestras sedes en diferentes ubicaciones
          </Typography>
        </Box>

        <Divider sx={{ mb: 3 }} />

        {/* Filtros y ordenamiento */}
        <Stack 
          direction={{ xs: 'column', sm: 'row' }} 
          spacing={2} 
          sx={{ mb: 4 }} 
          justifyContent="space-between"
          alignItems={{ xs: 'stretch', sm: 'flex-end' }}
        >
          <TextField
            label="Buscar sedes"
            variant="outlined"
            size="small"
            value={searchTerm}
            onChange={handleSearchChange}
            sx={{ flexGrow: 1, maxWidth: { sm: 300 } }}
          />
          
          <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
            <FormControl size="small" sx={{ minWidth: 120 }}>
              <InputLabel>Ordenar por</InputLabel>
              <Select
                value={sortBy}
                onChange={handleSortByChange}
                label="Ordenar por"
              >
                <MenuItem value="name">Nombre</MenuItem>
                <MenuItem value="code">Código</MenuItem>
                <MenuItem value="creation_date">Fecha de creación</MenuItem>
              </Select>
            </FormControl>
            
            <FormControl size="small" sx={{ minWidth: 120 }}>
              <InputLabel>Dirección</InputLabel>
              <Select
                value={sortDirection}
                onChange={handleSortDirectionChange}
                label="Dirección"
              >
                <MenuItem value="asc">Ascendente</MenuItem>
                <MenuItem value="desc">Descendente</MenuItem>
              </Select>
            </FormControl>
            
            <FormControl size="small" sx={{ minWidth: 80 }}>
              <InputLabel>Por página</InputLabel>
              <Select
                value={perPage}
                onChange={handlePerPageChange}
                label="Por página"
              >
                <MenuItem value={4}>4</MenuItem>
                <MenuItem value={8}>8</MenuItem>
                <MenuItem value={12}>12</MenuItem>
                <MenuItem value={16}>16</MenuItem>
              </Select>
            </FormControl>
          </Box>
        </Stack>
      </Paper>

      {loading && (
        <Box sx={{ display: 'flex', justifyContent: 'center', my: 8 }}>
          <CircularProgress />
        </Box>
      )}

      {error && (
        <Alert severity="error" sx={{ mb: 3 }}>
          {error}
        </Alert>
      )}

      {!loading && !error && locations.length === 0 && (
        <Alert severity="info" sx={{ mb: 3 }}>
          No se encontraron sedes
        </Alert>
      )}

      {!loading && !error && locations.length > 0 && (
        <>
          {/* Mostrar el total de resultados */}
          <Box sx={{ mb: 2 }}>
            <Typography variant="body2" color="text.secondary">
              Mostrando {locations.length} de {totalItems} sedes
            </Typography>
          </Box>

          {/* Grid de tarjetas adaptativo con la sintaxis actualizada para MUI v7 */}
          <Box sx={{ display: 'flex', flexWrap: 'wrap', margin: -1.5 }}>
            {locations.map((location) => (
              <Box 
                key={location.code}
                sx={{ 
                  width: { 
                    xs: '100%', 
                    sm: isTablet ? '50%' : '100%', 
                    md: '33.333%', 
                    lg: '25%' 
                  },
                  padding: 1.5
                }}
              >
                <LocationCard location={location} />
              </Box>
            ))}
          </Box>

          {/* Controles de paginación */}
          <Box sx={{ display: 'flex', justifyContent: 'center', mt: 4, mb: 2 }}>
            <Pagination 
              count={totalPages} 
              page={page} 
              onChange={handlePageChange} 
              color="primary"
              showFirstButton
              showLastButton
              size={isMobile ? "small" : "medium"}
            />
          </Box>
        </>
      )}
    </Container>
  );
};

export default LocationsList;
