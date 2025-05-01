import React, { useState } from 'react';
import { Card, CardContent, CardMedia, Typography, Box } from '@mui/material';
import { Location } from './location.types';

interface LocationCardProps {
  location: Location;
}

const LocationCard: React.FC<LocationCardProps> = ({ location }) => {
  // Estado para controlar si la imagen ha fallado al cargar
  const [imageError, setImageError] = useState(false);
  // Generar un valor aleatorio único para esta carta
  const [randomSeed] = useState(() => Math.floor(Math.random() * 1000));
  
  // URL de imagen por defecto única para esta carta
  const defaultImage = `https://picsum.photos/800/400?random=${randomSeed}`;

  // Función para manejar el error de carga de la imagen
  const handleImageError = (e: React.SyntheticEvent<HTMLImageElement, Event>) => {
    // Prevenir que el error se propague a la consola
    e.preventDefault();
    e.stopPropagation();
    
    // Marcar la imagen como fallida
    if (!imageError) {
      setImageError(true);
    }
  };

  return (
    <Card sx={{ maxWidth: 345, height: '100%', display: 'flex', flexDirection: 'column' }}>
      <CardMedia
        component="img"
        height="140"
        image={imageError ? defaultImage : location.image}
        alt={location.name}
        onError={handleImageError}
        sx={{ objectFit: 'cover' }}
      />
      <CardContent sx={{ flexGrow: 1 }}>
        <Typography gutterBottom variant="h5" component="div">
          {location.name}
        </Typography>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
          <Typography variant="body2" color="text.secondary">
            Código:
          </Typography>
          <Typography variant="body2" color="text.secondary">
            {location.code}
          </Typography>
        </Box>
        <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
          <Typography variant="body2" color="text.secondary">
            Creada:
          </Typography>
          <Typography variant="body2" color="text.secondary">
            {location.creationDate}
          </Typography>
        </Box>
      </CardContent>
    </Card>
  );
};

export default LocationCard;
