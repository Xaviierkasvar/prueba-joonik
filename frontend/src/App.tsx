import React from 'react';
import { CssBaseline, AppBar, Toolbar, Typography, Container, ThemeProvider, createTheme } from '@mui/material';
import LocationsList from './LocationsList';

// Create a theme
const theme = createTheme({
  palette: {
    primary: {
      main: '#1976d2',
    },
    secondary: {
      main: '#dc004e',
    },
  },
});

function App() {
  return (
    <ThemeProvider theme={theme}>
      <CssBaseline />
      <AppBar position="static">
        <Toolbar>
          <Typography variant="h6" component="div" sx={{ flexGrow: 1 }}>
            Aplicación de Sedes
          </Typography>
        </Toolbar>
      </AppBar>
      <Container component="main" sx={{ mt: 4, mb: 4 }}>
        <LocationsList />
      </Container>
    </ThemeProvider>
  );
}

export default App;
