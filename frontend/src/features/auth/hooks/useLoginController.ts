import { useCallback, useMemo, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAsyncAction } from '@/hooks/useAsyncAction'
import * as authService from '@/services/auth/authService'
import { useAuth } from '@/store/auth/AuthProvider'
import { HttpError } from '@/services/http/HttpError'

export function useLoginController() {
  const { setSessionFromLogin } = useAuth()
  const navigate = useNavigate()

  const [correo, setCorreo] = useState('')
  const [contrasena, setContrasena] = useState('')
  const [uiError, setUiError] = useState<string | null>(null)

  const doLogin = useCallback(async () => {
    if (!correo.trim() || !contrasena) {
      setUiError('Correo y contraseña son obligatorios.')
      return
    }

    const data = await authService.login({
      correo: correo.trim(),
      contrasena,
    })
    setSessionFromLogin(data)
    
    // Redirigir según el nivel del usuario
    const nivel = data.user.nivel
    if (nivel === 'Cliente' || nivel === 'Barista') {
      navigate('/cafeterias_lista', { replace: true })
    } else if (nivel === 'Propietario' || nivel === 'Administrador') {
      navigate('/cafeterias', { replace: true })
    } else {
      navigate('/inicio', { replace: true })
    }
  }, [correo, contrasena, setSessionFromLogin, navigate])

  const { run, loading, error } = useAsyncAction(doLogin)

  const errorMessage = useMemo(() => {
    if (uiError) return uiError
    if (!error) return null
    if (error instanceof HttpError) return error.payload?.message ?? error.message
    return error.message
  }, [error, uiError])

  const onCorreoChange = useCallback((value: string) => {
    setUiError(null)
    setCorreo(value)
  }, [])

  const onContrasenaChange = useCallback((value: string) => {
    setUiError(null)
    setContrasena(value)
  }, [])

  const submit = useCallback(async () => {
    setUiError(null)
    await run()
  }, [run])

  return {
    state: { correo, contrasena, loading, errorMessage },
    actions: { onCorreoChange, onContrasenaChange, submit },
  }
}

