<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Axios desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card shadow-lg p-4">
                    <div class="card-body">
                        <h1 class="h3 text-primary mb-4 border-bottom pb-3">
                            Editar Cliente (ID: <span id="client-id-display" class="text-secondary">...</span>)
                        </h1>

                        <div id="loading" class="text-center py-4 fs-5 text-secondary">Cargando datos del cliente... ⏳</div>
                        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>

                        <form id="update-client-form" class="d-none">
                            @csrf
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

                            <!-- Foto -->
                            <div class="mb-4">
                                <label for="photo" class="form-label">URL de la Foto</label>
                                <input type="url" id="photo" name="photo" required class="form-control">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('admin.clients.index') }}" class="btn btn-link text-secondary">
                                    ← Volver al Listado
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                    Actualizar Cliente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
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
                document.getElementById('photo').value = client.photo;

                form.classList.remove('d-none');
            } catch (error) {
                console.error('Error al cargar el cliente:', error);
                errorDiv.textContent = error.response?.data?.message || 'No se pudo cargar el cliente.';
                errorDiv.classList.remove('d-none');
            } finally {
                loading.classList.add('d-none');
            }

            // Enviar actualización
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                delete data._token; // No enviar el token

                messagesDiv.innerHTML = '';

                try {
                    const response = await axios.put(`/api/clients/${clientId}`, data);

                    messagesDiv.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <p class="mb-0"><strong>✅ Éxito:</strong> ${response.data.message}</p>
                            <p class="mb-0">Cliente actualizado correctamente.</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
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
