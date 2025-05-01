import React from 'react';
import { render, screen } from '@testing-library/react';
import App from './App';

test('renders Aplicación de Sedes header', () => {
  render(<App />);
  const headerElement = screen.getByText(/Aplicación de Sedes/i);
  expect(headerElement).toBeInTheDocument();
});
