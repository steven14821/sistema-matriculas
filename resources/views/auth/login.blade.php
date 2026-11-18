<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="description" content="Ingresa al portal académico de Saint Victoria Academy con tus credenciales institucionales.">
    <title>Iniciar sesión | Saint Victoria Academy</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js'])
</head>
<body class="site-home site-login antialiased">
    <main class="site-home__frame">
        <div class="site-home__paper">
            <div class="site-home__scallop" aria-hidden="true"></div>
            <div class="site-home__inner">
                <header class="site-home__masthead">
                    <a href="{{ route('home') }}" class="site-home__brand" aria-label="Saint Victoria Academy, inicio">
                        <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="" class="site-home__seal">
                        <span class="site-home__brand-copy">
                            <span class="site-home__brand-name">Saint Victoria Academy</span>
                            <span class="site-home__brand-note">Portal académico</span>
                        </span>
                    </a>
                    <a href="{{ route('home') }}" class="site-home__access">Volver a la portada</a>
                </header>

                <div class="site-login__layout">
                    <section class="site-login__welcome" aria-labelledby="login-welcome-title">
                        <h1 id="login-welcome-title">Continúa tu<br><span>recorrido.</span></h1>
                        <p>
                            Ingresa con tus credenciales institucionales para consultar la oferta y continuar con las opciones de tu perfil.
                        </p>
                        <div class="site-login__note">
                            <span class="site-login__note-rule" aria-hidden="true"></span>
                            <span>El acceso está reservado para usuarios autorizados de la institución.</span>
                        </div>
                    </section>

                    <section class="site-login__form-section" aria-labelledby="login-title">
                        <div class="site-login__form-heading">
                            <h2 id="login-title">Iniciar sesión</h2>
                            <p>Usa tu correo y contraseña institucionales.</p>
                        </div>

                        <form method="POST" action="{{ route('login.store') }}" class="site-login__form">
                            @csrf

                            <div class="site-login__field">
                                <label for="email">Correo electrónico</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    inputmode="email"
                                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                    @if ($errors->has('email')) aria-describedby="email-error" @endif
                                    placeholder="nombre@institucion.edu.co"
                                >
                                @error('email')
                                    <p id="email-error" class="site-login__error" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="site-login__field">
                                <label for="password">Contraseña</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                    @if ($errors->has('password')) aria-describedby="password-error" @endif
                                    placeholder="Ingresa tu contraseña"
                                >
                                @error('password')
                                    <p id="password-error" class="site-login__error" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <label for="remember" class="site-login__remember">
                                <input
                                    id="remember"
                                    name="remember"
                                    type="checkbox"
                                    value="1"
                                >
                                <span>Recordarme en este dispositivo</span>
                            </label>

                            <button type="submit" class="site-home__primary site-login__submit">
                                <span>Ingresar</span>
                                <svg viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M3.5 10h12m-5-5 5 5-5 5"/>
                                </svg>
                            </button>
                        </form>

                        <p class="site-login__security">Tu sesión te mostrará únicamente las herramientas habilitadas para tu perfil.</p>
                    </section>
                </div>

                <footer class="site-home__footer site-login__footer">
                    <span>© {{ date('Y') }} Saint Victoria Academy</span>
                    <a href="{{ route('home') }}">Volver a la portada</a>
                </footer>
            </div>
        </div>
    </main>
</body>
</html>
