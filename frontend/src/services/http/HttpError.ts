import type { ApiErrorResponse } from '@/types/api'

export class HttpError extends Error {
  public readonly status: number
  public readonly url: string
  public readonly payload?: ApiErrorResponse

  constructor(params: { status: number; url: string; message: string; payload?: ApiErrorResponse }) {
    super(params.message)
    this.name = 'HttpError'
    this.status = params.status
    this.url = params.url
    if (params.payload) {
      this.payload = params.payload
    }
  }
}

