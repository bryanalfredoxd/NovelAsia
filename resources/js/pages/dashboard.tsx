import { Head, usePage, router } from '@inertiajs/react';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Button } from '@/components/ui/button'; // Importamos el botón
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

// Solución para el error de TypeScript con route()
declare function route(name: string): string;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard() {
    // 1. Obtenemos el usuario de las props compartidas
    const { auth } = usePage().props as any; 
    const user = auth.user;

    // 2. Función para cerrar sesión
    const handleLogout = () => {
        router.post(route('logout'));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    
                    {/* TARJETA 1: INFORMACIÓN DEL USUARIO Y LOGOUT */}
                    <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border bg-sidebar/50">
                        <h3 className="text-lg font-bold">Bienvenido, {user.nombre}</h3>
                        <p className="text-sm text-muted-foreground mb-4">
                            Usuario: {user.username} <br />
                            País: {user.pais}
                        </p>
                        
                        <Button variant="destructive" onClick={handleLogout}>
                            Cerrar Sesión
                        </Button>
                    </div>

                    {/* TARJETA 2 (Placeholder) */}
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>

                    {/* TARJETA 3 (Placeholder) */}
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                </div>

                {/* ÁREA INFERIOR (Placeholder) */}
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}