import { useState, useEffect } from 'react'
import { TextField, InputAdornment, IconButton } from '@mui/material'
import SearchIcon from '@mui/icons-material/Search'
import CloseIcon from '@mui/icons-material/Close'
import { designTokens } from '@/theme/tokens'

type SearchBarProps = {
  value: string
  onChange: (value: string) => void
  placeholder?: string
}

export function SearchBar({ value, onChange, placeholder = 'Buscar cafeterías...' }: SearchBarProps) {
  const [localValue, setLocalValue] = useState(value)

  useEffect(() => {
    setLocalValue(value)
  }, [value])

  const handleChange = (newValue: string) => {
    setLocalValue(newValue)
    onChange(newValue)
  }

  const handleClear = () => {
    setLocalValue('')
    onChange('')
  }

  return (
    <TextField
      fullWidth
      value={localValue}
      onChange={(e) => handleChange(e.target.value)}
      placeholder={placeholder}
      variant="outlined"
      size="small"
      data-search-bar
      InputProps={{
        startAdornment: (
          <InputAdornment position="start">
            <SearchIcon sx={{ color: designTokens.colors.gris }} />
          </InputAdornment>
        ),
        endAdornment: localValue ? (
          <InputAdornment position="end">
            <IconButton size="small" onClick={handleClear} sx={{ padding: 0.5 }}>
              <CloseIcon fontSize="small" />
            </IconButton>
          </InputAdornment>
        ) : null,
      }}
      sx={{
        backgroundColor: 'white',
        borderRadius: 1,
        '& .MuiOutlinedInput-root': {
          '& fieldset': {
            borderColor: designTokens.colors.grisClaro,
          },
          '&:hover fieldset': {
            borderColor: designTokens.colors.principal,
          },
          '&.Mui-focused fieldset': {
            borderColor: designTokens.colors.principal,
          },
        },
      }}
    />
  )
}
