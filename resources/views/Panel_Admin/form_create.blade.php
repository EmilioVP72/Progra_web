<?php
// emiliovp72/progra_web/Progra_web-c8642178cbdb8ffa2616855da2e9dacdc956cf41/resources/views/Panel_Admin/form_create.blade.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Cliente</title>
    {{-- Inclusión de Bootstrap CSS vía CDN para usar los componentes --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    {{-- Estilos Neón / Tema Oscuro --}}
    <style>
        :root {
            --bs-body-bg: #1C1C1C;
            --bs-body-color: #F8F9FA;
            --bs-primary: #00FF7F; /* Verde Neón para acentos */
        }

        body {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        .text-neon {
            color: var(--bs-primary) !important;
        }

        .btn-neon-primary {
            background-color: var(--bs-primary);
            color: #1C1C1C;
            border: 1px solid var(--bs-primary);
            transition: all 0.3s;
        }

        .btn-neon-primary:hover {
            background-color: transparent;
            color: var(--bs-primary);
        }

        .card-dark {
            background-color: #242424;
            border: 1px solid #333333;
            color: var(--bs-body-color);
        }

        /* Ajustes para inputs en tema oscuro */
        .form-control {
            background-color: #2b2b2b;
            border-color: #444444;
            color: var(--bs-body-color);
        }
        .form-control:focus {
            background-color: #2b2b2b;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.25rem rgba(0, 255, 127, 0.25);
            color: var(--bs-body-color);
        }
        .form-label {
            color: var(--bs-body-color);
        }
    </style>
    
    @vite(['resources/js/app.js'])
</head>
{{-- CAMBIO: bg-light a bg-dark --}}
<body class="bg-dark d-flex justify-content-center align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                {{-- CAMBIO: Card normal a card-dark --}}
                <div class="card card-dark shadow-lg p-4">
                    <div class="card-body">
                        {{-- CAMBIO: text-primary a text-neon --}}
                        <h1 class="h3 text-neon mb-4 border-bottom pb-3">Registro de Nuevo Cliente</h1>
                        
                        {{-- CAMBIO: Añadir enctype para subir archivos --}}
                        <form id="create-client-form" enctype="multipart/form-data">
                            @csrf
                            <div id="messages" class="mb-4"></div>

                            {{-- Fila 1: Nombre y Apellido --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text" id="name" name="name" required class="form-control">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Apellido</label>
                                    <input type="text" id="lastname" name="lastname" required class="form-control">
                                </div>
                            </div>

                            {{-- Fila 2: Teléfono y Email --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Teléfono (10 dígitos)</label>
                                    <input type="text" id="phone" name="phone" required class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" required class="form-control">
                                </div>
                            </div>
                            
                            {{-- Contraseña --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" id="password" name="password" required class="form-control">
                            </div>
                            
                            {{-- CAMBIO: Input de URL a FILE con validación en el front (acepta tipos) --}}
                            <div class="mb-4">
                                <label for="photo" class="form-label">Subir Foto (JPG/PNG, máx 5MB)</label>
                                <input type="file" id="photo" name="photo" required class="form-control" accept=".jpg, .jpeg, .png">
                                <small class="form-text text-muted">Archivos permitidos: JPG y PNG. Tamaño máximo: 5MB.</small>
                            </div>

                            {{-- Botones de acción --}}
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('admin.clients.index') }}" class="btn btn-link text-secondary">
                                    ← Volver al Listado
                                </a>
                                {{-- CAMBIO: btn-success a btn-neon-primary --}}
                                <button type="submit" class="btn btn-neon-primary btn-lg shadow-sm">
                                    Guardar Cliente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclusión de Bootstrap JS (para el funcionamiento de las alertas) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        document.getElementById('create-client-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form); // Obtiene FormData con el archivo
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';
            
            // Envío de FormData con axios.post para subir archivos.

            try {
                const response = await axios.post('/api/clients', formData); 
                
                messagesDiv.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <p class="mb-0"><strong>✅ Éxito: ${response.data.message}</strong></p>
                        <p class="mb-0">Cliente ${response.data.client.name} creado con ID: ${response.data.client.id}.</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                form.reset(); 
            } catch (error) {
                console.error("Error al crear cliente:", error.response || error);
                
                let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                
                if (error.response && error.response.data.errors) {
                    errorHtml += '<p class="mb-0"><strong>❌ Error de Validación:</strong></p><ul>';
                    for (const field in error.response.data.errors) {
                        error.response.data.errors[field].forEach(msg => {
                            errorHtml += `<li>- ${msg}</li>`;
                        });
                    }
                    errorHtml += '</ul>';
                } else if (error.response && error.response.data.message) {
                    errorHtml += `<p class="mb-0"><strong>❌ Error: ${error.response.data.message}</strong></p>`;
                } else {
                    errorHtml += `<p class="mb-0"><strong>❌ Error desconocido al crear el cliente.</strong></p>`;
                }

                errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                messagesDiv.innerHTML = errorHtml;
            }
        });
    </script>
</body>
</html>