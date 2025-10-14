<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración de Clientes</title>
    {{-- Asegúrate de que tu configuración de Vite esté corriendo (npm run dev) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="container mx-auto p-4 md:p-8">
        <div class="flex justify-between items-center mb-6 bg-white p-6 shadow-md rounded-lg">
            <h1 class="text-3xl font-extrabold text-indigo-800">Clientes de Suplementos</h1>
            <a href="{{ route('admin.clients.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-xl transition duration-300 ease-in-out transform hover:scale-105">
                ➕ Nuevo Cliente
            </a>
        </div>

        <div id="loading" class="text-center py-10 text-xl font-medium text-indigo-600">
            Cargando clientes... 🏋️‍♂️
        </div>
        
        {{-- Contenedor de la Tabla --}}
        <div class="bg-white shadow-xl rounded-lg overflow-x-auto hidden" id="clients-table-container">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="clients-table-body">
                    {{-- Los clientes se cargarán con JavaScript --}}
                </tbody>
            </table>
        </div>
        
        <p id="no-clients" class="text-center py-10 text-lg font-medium text-gray-500 hidden bg-white rounded-lg shadow-md">
            No hay clientes registrados. ¡Comienza creando uno!
        </p>
    </div>

    <script>
        // Uso de axios (ya incluido en resources/js/bootstrap.js)
        
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.getElementById('clients-table-body');
            const tableContainer = document.getElementById('clients-table-container');
            const loading = document.getElementById('loading');
            const noClients = document.getElementById('no-clients');

            async function fetchClients() {
                loading.classList.remove('hidden');
                tableContainer.classList.add('hidden');
                noClients.classList.add('hidden');
                tableBody.innerHTML = '';
                
                try {
                    const response = await axios.get('/api/clients');
                    const clients = response.data;

                    if (clients.length === 0) {
                        noClients.classList.remove('hidden');
                        return;
                    }

                    clients.forEach(client => {
                        const row = `
                            <tr id="client-${client.id}" class="hover:bg-indigo-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">${client.id}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">${client.name} ${client.lastname}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${client.phone}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${client.email}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="/admin/clientes/${client.id}/editar" class="text-indigo-600 hover:text-indigo-800 font-semibold transition duration-150 ease-in-out mr-4">
                                        Editar ✏️
                                    </a>
                                    <button onclick="deleteClient(${client.id})" class="text-red-600 hover:text-red-800 font-semibold transition duration-150 ease-in-out">
                                        Borrar 🗑️
                                    </button>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });

                    tableContainer.classList.remove('hidden');
                } catch (error) {
                    // Si el API devuelve 404, significa que no hay registros (basado en ClientController::All())
                    if (error.response && error.response.status === 404) {
                         noClients.classList.remove('hidden');
                    } else {
                        console.error("Error al obtener clientes:", error);
                        alert('Ocurrió un error al cargar los clientes.');
                    }
                } finally {
                    loading.classList.add('hidden');
                }
            }

            window.deleteClient = async function(id) {
                if (!confirm(`⚠️ ¿Estás seguro de que quieres eliminar al cliente con ID ${id}? Esta acción es irreversible.`)) {
                    return;
                }

                try {
                    const response = await axios.delete(`/api/clients/${id}`);
                    document.getElementById(`client-${id}`).remove();
                    alert(response.data.message || 'Cliente eliminado correctamente.');
                } catch (error) {
                    console.error("Error al eliminar cliente:", error);
                    alert('Error al eliminar el cliente. Por favor, verifica la consola.');
                }
            }
            
            fetchClients();
        });
    </script>
</body>
</html>