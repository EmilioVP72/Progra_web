{{-- emiliovp72/progra_web/Progra_web-c8642178cbdb8ffa2616855da2e9dacdc956cf41/resources/views/Panel_Admin/index.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Clientes</title>

    {{-- Inclusión de Bootstrap CSS vía CDN --}}
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

        .border-neon {
            border-color: var(--bs-primary) !important;
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
        
        /* Estilos específicos para la tabla oscura */
        .table-dark {
            --bs-table-bg: #242424;
            --bs-table-color: var(--bs-body-color);
            --bs-table-striped-bg: #2b2b2b;
            --bs-table-hover-bg: #363636;
            border: 1px solid #333333;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    @vite(['resources/js/app.js'])
</head>
<body class="bg-dark">
    <div class="container py-5">
        <header class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h2 text-neon">Gestión de Clientes</h1>
            <a href="{{ route('admin.clients.create') }}" class="btn btn-neon-primary btn-lg shadow-sm">
                ➕ Crear Nuevo Cliente
            </a>
        </header>

        <div id="messages" class="mb-4"></div>
        
        <div id="loading" class="text-center py-5 fs-4 text-secondary d-none">
            Cargando datos... ⏳
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover rounded overflow-hidden">
                        <thead class="bg-dark text-neon border-bottom border-neon">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Foto</th> {{-- Nueva columna para la Foto --}}
                                <th scope="col">Nombre</th>
                                <th scope="col">Apellido</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Email</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="clients-table-body">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclusión de Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    @verbatim
    <script>
        const API_URL = '/api/clients';
        
        document.addEventListener('DOMContentLoaded', fetchClients);

        async function fetchClients() {
            const tableBody = document.getElementById('clients-table-body');
            const loading = document.getElementById('loading');
            
            loading.classList.remove('d-none');
            tableBody.innerHTML = '';
            
            try {
                const response = await axios.get(API_URL);
                const clients = response.data.clients || response.data;
                renderClients(clients);
            } catch (error) {
                console.error("Error al obtener clientes:", error);
                document.getElementById('messages').innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        Error al cargar los clientes. Por favor, revise la consola.
                    </div>
                `;
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger py-4">
                            Error al cargar los datos.
                        </td>
                    </tr>
                `;
            } finally {
                loading.classList.add('d-none');
            }
        }

        function renderClients(clients) {
            const tableBody = document.getElementById('clients-table-body');
            
            if (clients.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay clientes registrados.
                        </td>
                    </tr>
                `;
                return;
            }

            clients.forEach(client => {
                // Lógica para determinar la URL de la foto. Usamos '/storage/' para acceder al disco público.
                const photoUrl = client.photo 
                    ? `/storage/${client.photo}` 
                    : 'https://via.placeholder.com/50?text=No+Foto';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${client.id}</td>
        
                    <td>
                        <img src="${photoUrl}" alt="Foto de ${client.name}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                    </td>
                    
                    <td>${client.name}</td>
                    <td>${client.lastname}</td>
                    <td>${client.phone}</td>
                    <td>${client.email}</td>
                    <td>
                        <a href="/admin/clientes/${client.id}/editar" class="btn btn-sm btn-outline-info me-2">Editar</a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteClient(${client.id})">Eliminar</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        async function deleteClient(clientId) {
            if (!confirm(`¿Estás seguro de que quieres eliminar al cliente con ID ${clientId}?`)) {
                return;
            }
            
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';

            try {
                const response = await axios.delete(`${API_URL}/${clientId}`);
                
                messagesDiv.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                fetchClients(); // Recargar la lista después de la eliminación
            } catch (error) {
                console.error("Error al eliminar cliente:", error);
                messagesDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al eliminar el cliente: ${error.response?.data?.message || 'Error desconocido'}.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }
        }
    </script>
    @endverbatim
</body>
</html>