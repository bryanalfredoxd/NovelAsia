import { useCallback } from 'react';

// Modificamos el tipo para aceptar string, undefined o null
export type GetInitialsFn = (fullName?: string | null) => string;

export function useInitials(): GetInitialsFn {
    return useCallback((fullName?: string | null): string => {
        // 1. SEGURIDAD: Si es undefined, null o vacío, retornamos string vacío y paramos aquí.
        // Esto arregla el error "Cannot read properties of undefined (reading 'trim')"
        if (!fullName) return '';

        const names = fullName.trim().split(' ');

        if (names.length === 0) return '';
        if (names.length === 1) return names[0].charAt(0).toUpperCase();

        const firstInitial = names[0].charAt(0);
        const lastInitial = names[names.length - 1].charAt(0);

        return `${firstInitial}${lastInitial}`.toUpperCase();
    }, []);
}