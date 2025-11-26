// composables/useEvents.js
export const useEvents = () => {
  const config = useRuntimeConfig()
  const API_URL = config.public.API_URL || 'http://localhost:8000/api'

  const getEvent = (id) => {
    return useFetch(`${API_URL}/events/${id}`, {
      method: 'GET',
      credentials: 'include',
    })
  }

  return { getEvent }
}
