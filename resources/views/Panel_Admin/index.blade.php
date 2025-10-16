<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Clientes</title>
    {{-- Inclusión de Bootstrap CSS vía CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    {{-- Para incluir iconos de Bootstrap (opcional, pero mejora la estética) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/js/app.js'])

    {{-- Estilos personalizados para el tema de Suplementos (Dark + Neon) --}}
    <style>
        :root {
            --bs-body-bg: #1C1C1C;
            /* Fondo oscuro */
            --bs-body-color: #F8F9FA;
            /* Texto claro */
            --bs-primary: #00FF7F;
            /* Verde Neón para acentos */
            --bs-info: #00BFFF;
            /* Azul Neón para botones */
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
            color: #1C1C1C; /* Aseguramos que el texto sea oscuro */
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

        .table-dark-custom {
            --bs-table-bg: #212529;
            --bs-table-border-color: #333333;
            color: #ffffff; /* 👈 fuerza el texto blanco */
        }

        .table-dark-custom thead th {
            color: #00FF7F; /* verde neón del encabezado */
        }

        .table-dark-custom td,
        .table-dark-custom th {
            color: #ffffff; /* 👈 texto blanco dentro del cuerpo */
        }

        .table-dark-custom tr:hover td {
            background-color: #2b2b2b; /* efecto hover más claro */
            color: #00FF7F; /* texto verde neón al pasar el mouse */
        }
    </style>
</head>

<body>
    <div class="container py-5">

        {{-- Cabecera del Panel --}}
        <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-3">
            <h1 class="text-white">
                Panel de Administración
                <span class="text-neon fw-bold">Clientes</span>
            </h1>

            {{-- Botón para Crear Nuevo Cliente --}}
            <a href="{{ route('admin.clients.create') }}" class="btn btn-neon-primary btn-lg shadow-lg">
                <i class="bi bi-plus-circle-fill me-2"></i> Crear Nuevo Cliente
            </a>
        </div>

        {{-- Contenedor de Mensajes (Alerts de Bootstrap) --}}
        <div id="messages" class="mb-4"></div>

        {{-- Contenedor de la Tabla (Card Oscuro) --}}
        <div class="card card-dark shadow-lg">
            <div class="card-body p-4">

                <h2 class="card-title h4 mb-4 text-neon">Listado de Clientes</h2>

                {{-- Barra de Búsqueda (Input de Bootstrap) --}}
                <div class="mb-4">
                    <input type="text" id="search-input"
                        class="form-control form-control-lg bg-dark text-light border-secondary"
                        placeholder="Buscar por Nombre, Email o Teléfono...">
                </div>

                {{-- Tabla de Clientes --}}
                <div class="table-responsive">
                    <table id="clients-table" class="table table-dark-custom table-striped table-hover align-middle">
                        <thead class="text-neon border-bottom border-primary">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Foto</th>
                                <th scope="col">Nombre Completo</th>
                                <th scope="col">Email</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        {{-- ID usado en el script para la carga de datos --}}
                        <tbody id="clients-list"> 
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Cargando clientes...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclusión de Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    @verbatim
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            // Se corrige el ID del tbody para que coincida con el HTML
            const clientsList = document.getElementById('clients-list'); 
            const messagesDiv = document.getElementById('messages');
            const searchInput = document.getElementById('search-input');
            let allClients = [];

            // Función para mostrar mensajes (Alerta de Bootstrap)
            function showMessage(message, type = 'success') {
                messagesDiv.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }

            // Función para cargar los datos de la API
            async function fetchClients() {
                try {
                    // Usamos axios, importado vía @vite(['resources/js/app.js'])
                    const response = await axios.get('/api/clients');
                    allClients = response.data;
                    renderClients(allClients);
                } catch (error) {
                    console.error("Error al cargar clientes:", error);
                    clientsList.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">
                        Error al cargar los datos. Verifique la conexión con la API.
                    </td></tr>`;
                    showMessage('No se pudo cargar la lista de clientes.', 'danger');
                }
            }

            // Función para renderizar clientes en la tabla
            function renderClients(clients) {
                if (clients.length === 0) {
                    clientsList.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">
                        No se encontraron clientes que coincidan con la búsqueda.
                    </td></tr>`;
                    return;
                }

                clientsList.innerHTML = clients.map(client => `
                    <tr id="client-row-${client.id}">
                        <th scope="row" class="text-neon">${client.id}</th>
                        <td>
                            <img src="${client.photo}" alt="${client.name}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                        </td>
                        <td>${client.name} ${client.lastname}</td>
                        <td>${client.email}</td>
                        <td>${client.phone}</td>
                        <td class="d-flex gap-2">
                            <a href="/admin/clientes/${client.id}/editar" class="btn btn-sm btn-info text-dark">
                                Editar
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteClient(${client.id})">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            // Lógica de Búsqueda
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                const filteredClients = allClients.filter(client =>
                    client.name.toLowerCase().includes(searchTerm) ||
                    client.lastname.toLowerCase().includes(searchTerm) ||
                    client.email.toLowerCase().includes(searchTerm) ||
                    client.phone.includes(searchTerm)
                );
                renderClients(filteredClients);
            });

            window.deleteClient = async function (id) {
                if (!confirm('¿Estás seguro de que quieres eliminar al cliente con ID: ' + id + '? Esta acción no se puede deshacer.')) {
                    return;
                }

                try {
                    // Eliminación de datos (usa axios)
                    await axios.delete(`/api/clients/${id}`);

                    // Eliminar la fila de la tabla
                    document.getElementById(`client-row-${id}`).remove();

                    // Actualizar la lista local
                    allClients = allClients.filter(c => c.id !== id);

                    showMessage(`Cliente con ID ${id} eliminado correctamente.`, 'danger');
                } catch (error) {
                    console.error("Error al eliminar cliente:", error);
                    showMessage(`Error al eliminar cliente con ID ${id}.`, 'danger');
                }
            };

            // Iniciar la carga de clientes
            fetchClients();
        });
    </script>
    @endverbatim
</body>

</html>