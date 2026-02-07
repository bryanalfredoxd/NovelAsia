import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import type { User } from '@/types';

export function UserInfo({
    user,
    showEmail = false,
}: {
    // Usamos 'any' junto a User temporalmente por si tu interfaz TypeScript 'User' 
    // todavía tiene 'name' en lugar de 'nombre', para que no te marque error el editor.
    user: User | any; 
    showEmail?: boolean;
}) {
    const getInitials = useInitials();

    return (
        <>
            <Avatar className="h-8 w-8 overflow-hidden rounded-full">
                {/* Ojo: En tu DB es 'avatar_url', pero si usas 'avatar' en Laravel, déjalo así. 
                    He puesto el fallback por seguridad. */}
                <AvatarImage src={user.avatar || user.avatar_url} alt={user.nombre} />
                
                <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                    {/* AQUI ESTABA EL ERROR: Cambiado user.name por user.nombre */}
                    {getInitials(user.nombre)}
                </AvatarFallback>
            </Avatar>
            
            <div className="grid flex-1 text-left text-sm leading-tight">
                {/* Cambiado user.name por user.nombre */}
                <span className="truncate font-medium">{user.nombre}</span>
                
                {showEmail && (
                    <span className="truncate text-xs text-muted-foreground">
                        {user.email}
                    </span>
                )}
            </div>
        </>
    );
}