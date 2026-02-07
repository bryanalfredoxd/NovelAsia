import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';

// --- ESTA LÍNEA ARREGLA EL ERROR DE "route" (Imagen 3) ---
declare function route(name: string, params?: any, absolute?: boolean): string;

export default function Register() {
    // --- ESTO ELIMINA LA DEPENDENCIA DE '@/routes/register' (Imagen 2) ---
    // Usamos useForm directamente aquí
    const { data, setData, post, processing, errors, reset } = useForm({
        username: '',
        nombre: '',
        email: '',
        pais: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <AuthLayout
            title="Crear cuenta"
            description="Ingresa tus detalles para registrarte en NovelAsia"
        >
            <Head title="Registro" />

            <form onSubmit={submit} className="flex flex-col gap-6">
                <div className="grid gap-6">
                    
                    {/* Username */}
                    <div className="grid gap-2">
                        <Label htmlFor="username">Username</Label>
                        <Input
                            id="username"
                            type="text"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="username"
                            value={data.username}
                            onChange={(e) => setData('username', e.target.value)}
                            placeholder="Usuario123"
                        />
                        <InputError message={errors.username} className="mt-2" />
                    </div>

                    {/* Nombre */}
                    <div className="grid gap-2">
                        <Label htmlFor="nombre">Nombre Completo</Label>
                        <Input
                            id="nombre"
                            type="text"
                            required
                            tabIndex={2}
                            autoComplete="name"
                            value={data.nombre}
                            onChange={(e) => setData('nombre', e.target.value)}
                            placeholder="Tu nombre real"
                        />
                        <InputError message={errors.nombre} className="mt-2" />
                    </div>

                    {/* Email */}
                    <div className="grid gap-2">
                        <Label htmlFor="email">Correo Electrónico</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            tabIndex={3}
                            autoComplete="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="email@ejemplo.com"
                        />
                        <InputError message={errors.email} className="mt-2" />
                    </div>

                    {/* País */}
                    <div className="grid gap-2">
                        <Label htmlFor="pais">País</Label>
                        <Input
                            id="pais"
                            type="text"
                            required
                            tabIndex={4}
                            autoComplete="country-name"
                            value={data.pais}
                            onChange={(e) => setData('pais', e.target.value)}
                            placeholder="Venezuela"
                        />
                        <InputError message={errors.pais} className="mt-2" />
                    </div>

                    {/* Password */}
                    <div className="grid gap-2">
                        <Label htmlFor="password">Contraseña</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            tabIndex={5}
                            autoComplete="new-password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="********"
                        />
                        <InputError message={errors.password} className="mt-2" />
                    </div>

                    {/* Confirm Password */}
                    <div className="grid gap-2">
                        <Label htmlFor="password_confirmation">Confirmar Contraseña</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            required
                            tabIndex={6}
                            autoComplete="new-password"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            placeholder="********"
                        />
                        <InputError message={errors.password_confirmation} className="mt-2" />
                    </div>

                    <Button type="submit" className="mt-2 w-full" tabIndex={7} disabled={processing}>
                        {processing && <Spinner className="mr-2" />}
                        Registrarse
                    </Button>
                </div>

                <div className="text-center text-sm text-muted-foreground">
                    ¿Ya tienes una cuenta?{' '}
                    <TextLink href={route('login')} tabIndex={8}>
                        Iniciar sesión
                    </TextLink>
                </div>
            </form>
        </AuthLayout>
    );
}