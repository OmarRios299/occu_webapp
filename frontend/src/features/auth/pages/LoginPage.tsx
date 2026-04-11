import { Alert, Box, Button, Paper, TextField, Typography } from '@mui/material'
import { useCallback } from 'react'
import { useLoginController } from '../hooks/useLoginController'
import { designTokens } from '@/theme/tokens'

export function LoginPage() {
  const { state, actions } = useLoginController()

  const handleSubmit = useCallback(
    async (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault()
      await actions.submit()
    },
    [actions]
  )

  const handleCorreoChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      actions.onCorreoChange(e.target.value)
    },
    [actions]
  )

  const handleContrasenaChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      actions.onContrasenaChange(e.target.value)
    },
    [actions]
  )

  return (
    <Box
      sx={{
        width: '100%',
        height: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '20px',
        margin: 0,
        background: `linear-gradient(135deg, ${designTokens.colors.principal} 0%, ${designTokens.colors.sidebar} 50%, #3a2e2a 100%)`,
        position: 'relative',
        overflow: 'hidden',
        boxSizing: 'border-box',
        '&::before': {
          content: '""',
          position: 'absolute',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          background: `
            radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.02) 0%, transparent 50%)
          `,
          pointerEvents: 'none',
        },
      }}
    >
      {/* Wrapper */}
      <Box
        sx={{
          width: '100%',
          maxWidth: '440px',
          position: 'relative',
          zIndex: 1,
        }}
      >
        {/* Card */}
        <Paper
          component="form"
          onSubmit={handleSubmit}
          elevation={0}
          sx={{
            background: designTokens.colors.blanco,
            borderRadius: '16px',
            padding: {
              xs: '32px 24px',
              sm: '40px',
            },
            boxShadow: '0 20px 60px rgba(0, 0, 0, 0.3), 0 0 1px rgba(0, 0, 0, 0.1)',
            width: '100%',
            maxHeight: '90vh',
            overflowY: 'auto',
            overflowX: 'hidden',
            '&::-webkit-scrollbar': {
              width: '8px',
            },
            '&::-webkit-scrollbar-track': {
              background: '#f1f1f1',
              borderRadius: '10px',
            },
            '&::-webkit-scrollbar-thumb': {
              background: '#c1c1c1',
              borderRadius: '10px',
              '&:hover': {
                background: '#a8a8a8',
              },
            },
          }}
        >
          {/* Logo */}
          <Box sx={{ textAlign: 'center', marginBottom: '20px' }}>
            <Box
              component="img"
              src="/logo_1.png"
              alt="OCCU"
              sx={{
                width: {
                  xs: '80px',
                  sm: '100px',
                },
                height: 'auto',
                maxWidth: '100%',
                marginBottom: '16px',
              }}
            />
          </Box>

          {/* Título */}
          <Box sx={{ textAlign: 'center', marginBottom: '20px' }}>
            <Typography
              variant="h4"
              sx={{
                fontSize: {
                  xs: '24px',
                  sm: '28px',
                },
                fontWeight: 700,
                color: designTokens.colors.texto,
                margin: '0 0 8px 0',
                letterSpacing: '-0.5px',
              }}
            >
              Bienvenido a OCCU
            </Typography>
            <Typography
              variant="body2"
              sx={{
                fontSize: {
                  xs: '14px',
                  sm: '15px',
                },
                fontWeight: 400,
                color: '#6b7280',
                margin: 0,
              }}
            >
              Accede a tu cuenta
            </Typography>
          </Box>

          {/* Error alert */}
          {state.errorMessage && (
            <Alert
              severity="error"
              sx={{
                marginBottom: '24px',
                borderRadius: '8px',
              }}
            >
              {state.errorMessage}
            </Alert>
          )}

          {/* Form fields */}
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
            <TextField
              label="Correo"
              value={state.correo}
              onChange={handleCorreoChange}
              autoComplete="email"
              inputMode="email"
              disabled={state.loading}
              fullWidth
              sx={{
                '& .MuiOutlinedInput-root': {
                  fontSize: '15px',
                  fontWeight: 400,
                  color: designTokens.colors.texto,
                  backgroundColor: designTokens.colors.blanco,
                  borderRadius: '8px',
                  '& fieldset': {
                    border: '1.5px solid #e5e7eb',
                  },
                  '&:hover fieldset': {
                    borderColor: '#d1d5db',
                  },
                  '&.Mui-focused fieldset': {
                    borderColor: designTokens.colors.principal,
                    boxShadow: '0 0 0 3px rgba(241, 108, 91, 0.1)',
                  },
                },
                '& .MuiInputLabel-root': {
                  fontSize: '14px',
                  fontWeight: 600,
                  color: designTokens.colors.texto,
                  '&.Mui-focused': {
                    color: designTokens.colors.principal,
                  },
                },
                '& .MuiInputBase-input::placeholder': {
                  color: '#9ca3af',
                  fontWeight: 400,
                  opacity: 1,
                },
                '& .MuiInputBase-input:focus::placeholder': {
                  color: '#d1d5db',
                },
              }}
            />

            <TextField
              label="Contraseña"
              value={state.contrasena}
              onChange={handleContrasenaChange}
              type="password"
              autoComplete="current-password"
              disabled={state.loading}
              fullWidth
              sx={{
                '& .MuiOutlinedInput-root': {
                  fontSize: '15px',
                  fontWeight: 400,
                  color: designTokens.colors.texto,
                  backgroundColor: designTokens.colors.blanco,
                  borderRadius: '8px',
                  '& fieldset': {
                    border: '1.5px solid #e5e7eb',
                  },
                  '&:hover fieldset': {
                    borderColor: '#d1d5db',
                  },
                  '&.Mui-focused fieldset': {
                    borderColor: designTokens.colors.principal,
                    boxShadow: '0 0 0 3px rgba(241, 108, 91, 0.1)',
                  },
                },
                '& .MuiInputLabel-root': {
                  fontSize: '14px',
                  fontWeight: 600,
                  color: designTokens.colors.texto,
                  '&.Mui-focused': {
                    color: designTokens.colors.principal,
                  },
                },
                '& .MuiInputBase-input::placeholder': {
                  color: '#9ca3af',
                  fontWeight: 400,
                  opacity: 1,
                },
                '& .MuiInputBase-input:focus::placeholder': {
                  color: '#d1d5db',
                },
              }}
            />

            <Button
              type="submit"
              variant="contained"
              disabled={state.loading}
              fullWidth
              sx={{
                width: '100%',
                padding: '14px 24px',
                fontSize: {
                  xs: '15px',
                  sm: '16px',
                },
                fontWeight: 600,
                color: designTokens.colors.blanco,
                backgroundColor: designTokens.colors.principal,
                border: 'none',
                borderRadius: '8px',
                marginTop: '8px',
                boxShadow: '0 2px 4px rgba(241, 108, 91, 0.2)',
                letterSpacing: '0.01em',
                textTransform: 'none',
                '&:hover': {
                  backgroundColor: '#e05a4a',
                  transform: 'translateY(-1px)',
                  boxShadow: '0 4px 8px rgba(241, 108, 91, 0.3)',
                },
                '&:active': {
                  transform: 'translateY(0)',
                  boxShadow: '0 1px 2px rgba(241, 108, 91, 0.2)',
                },
                '&:focus': {
                  outline: 'none',
                  boxShadow: '0 0 0 3px rgba(241, 108, 91, 0.2)',
                },
                '&:disabled': {
                  backgroundColor: designTokens.colors.principal,
                  opacity: 0.6,
                },
              }}
            >
              {state.loading ? 'Ingresando…' : 'Ingresar'}
            </Button>
          </Box>
        </Paper>
      </Box>
    </Box>
  )
}

