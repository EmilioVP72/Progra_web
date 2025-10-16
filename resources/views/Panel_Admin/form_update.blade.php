<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>

    {{-- Inclusión de Bootstrap CSS vía CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- Axios desde CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    {{-- Estilos Neón / Tema Oscuro (Copiados del index/create para consistencia) --}}
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
</head>

<body class="bg-dark d-flex justify-content-center align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card card-dark shadow-lg p-4">
                    <div class="card-body">
                        <h1 class="h3 text-neon mb-4 border-bottom pb-3">
                            Editar Cliente (ID: <span id="client-id-display" class="text-secondary">...</span>)
                        </h1>

                        <div id="loading" class="text-center py-4 fs-5 text-secondary">Cargando datos del cliente... ⏳</div>
                        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>

                        <form id="update-client-form" class="d-none" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') {{-- Importante para que Laravel sepa que es una actualización --}}
                            <div id="messages" class="mb-4"></div>

                            <!-- Nombre y Apellido -->
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

                            <!-- Teléfono y Email -->
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

                            {{-- CAMBIO: Input de URL a FILE con previsualización --}}
                            <div class="mb-4">
                                <label class="form-label">Foto Actual</label>
                                <div class="mb-3">
                                    <img id="current-photo-preview" src="" alt="Foto actual del cliente" class="img-thumbnail rounded" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>

                                <label for="photo" class="form-label">Subir Nueva Foto (JPG/PNG, máx 5MB)</label>
                                <input type="file" id="photo" name="photo" class="form-control" accept=".jpg, .jpeg, .png">
                                <small class="form-text text-muted">Deja este campo vacío para conservar la foto actual. Archivos permitidos: JPG y PNG. Tamaño máximo: 5MB.</small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('admin.clients.index') }}" class="btn btn-link text-secondary">
                                    ← Volver al Listado
                                </a>
                                <button type="submit" class="btn btn-neon-primary btn-lg shadow-sm">
                                    Actualizar Cliente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const clientId = @json($id);
            const form = document.getElementById('update-client-form');
            const loading = document.getElementById('loading');
            const errorDiv = document.getElementById('error-message');
            const messagesDiv = document.getElementById('messages');
            const photoPreview = document.getElementById('current-photo-preview');

            document.getElementById('client-id-display').textContent = clientId;

            // Cargar datos del cliente
            try {
                const response = await axios.get(`/api/clients/${clientId}`);
                const client = response.data.client;

                // Llenar el formulario
                document.getElementById('name').value = client.name;
                document.getElementById('lastname').value = client.lastname;
                document.getElementById('phone').value = client.phone;
                document.getElementById('email').value = client.email;
                
                // Mostrar la foto actual
                // **IMPORTANTE**: Corrección de la URL para usar el path de almacenamiento correcto
                if (client.photo) {
                    photoPreview.src = `/storage/${client.photo}`;
                } else {
                    photoPreview.src = 'https://via.placeholder.com/150?text=No+Photo'; 
                }

                form.classList.remove('d-none');
            } catch (error) {
                console.error('Error al cargar el cliente:', error);
                errorDiv.textContent = error.response?.data?.message || 'No se pudo cargar el cliente.';
                errorDiv.classList.remove('d-none');
            } finally {
                loading.classList.add('d-none');
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(form);

                messagesDiv.innerHTML = '';

                try {
                    const response = await axios.post(`/api/clients/${clientId}`, formData);

                    messagesDiv.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <p class="mb-0"><strong>✅ Éxito:</strong> ${response.data.message}</p>
                            <p class="mb-0">Cliente actualizado correctamente.</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    // Opcional: Recargar la imagen de previsualización si se actualizó
                    if (response.data.client.photo) {
                         // Añadimos un timestamp para evitar problemas de caché del navegador
                         photoPreview.src = `/storage/${response.data.client.photo}?t=${new Date().getTime()}`; 
                    }
                } catch (error) {
                    console.error("Error al actualizar cliente:", error);

                    let html = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                    if (error.response?.data?.errors) {
                        html += '<strong>❌ Errores de Validación:</strong><ul>';
                        for (const field in error.response.data.errors) {
                            error.response.data.errors[field].forEach(msg => {
                                html += `<li>${msg}</li>`;
                            });
                        }
                        html += '</ul>';
                    } else {
                        html += `<p>${error.response?.data?.message || 'Error desconocido al actualizar el cliente.'}</p>`;
                    }
                    html += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    messagesDiv.innerHTML = html;
                }
            });
        });
    </script>
</body>
</html>
