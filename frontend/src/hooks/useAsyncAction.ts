import { useCallback, useState } from 'react'

export function useAsyncAction<TArgs extends unknown[], TResult>(
  action: (...args: TArgs) => Promise<TResult>
) {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<Error | null>(null)

  const run = useCallback(
    async (...args: TArgs) => {
      setLoading(true)
      setError(null)
      try {
        return await action(...args)
      } catch (e) {
        setError(e instanceof Error ? e : new Error('Error desconocido'))
        throw e
      } finally {
        setLoading(false)
      }
    },
    [action]
  )

  return { run, loading, error }
}

