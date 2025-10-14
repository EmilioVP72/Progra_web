<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex justify-center items-center min-h-screen">
    <div class="w-full max-w-xl bg-white p-8 rounded-xl shadow-2xl">
        <h1 class="text-3xl font-bold text-indigo-800 mb-8 border-b pb-4">Editar Cliente (ID: <span id="client-id-display" class="text-indigo-600">...</span>)</h1>
        
        <div id="loading" class="text-center py-10 text-xl font-medium text-indigo-600">Cargando datos del cliente... ⏳</div>
        <div id="error-message" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert"></div>

        <form id="update-client-form" class="hidden">
            {{-- Usamos @method('PUT') para simular el método PUT para Laravel/Axios --}}
            @csrf 
            @method('PUT')
            <div id="messages" class="mb-4"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="name" name="name" required class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="space-y-1">
                    <label for="lastname" class="block text-sm font-medium text-gray-700">Apellido</label>
                    <input type="text" id="lastname" name="lastname" required class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                 <div class="space-y-1">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono (10 dígitos)</label>
                    <input type="text" id="phone" name="phone" required class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="space-y-1">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="space-y-1 mt-6">
                <label for="photo" class="block text-sm font-medium text-gray-700">URL de la Foto</label>
                <input type="url" id="photo" name="photo" required class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <p class="text-sm text-gray-500 mt-6 italic">
                Nota: Este formulario solo permite actualizar la información básica. La contraseña debe manejarse con una funcionalidad separada.
            </p>

            <div class="flex justify-between items-center mt-8">
                <a href="{{ route('admin.clients.index') }}" class="text-gray-600 hover:text-indigo-800 font-medium transition duration-150 ease-in-out">
                    ← Volver al Listado
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    Actualizar Cliente
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // El ID del cliente se inyecta desde el controlador de Blade
            const clientId = @json($id); 
            const form = document.getElementById('update-client-form');
            const messagesDiv = document.getElementById('messages');
            const loading = document.getElementById('loading');
            const errorMessageDiv = document.getElementById('error-message');

            document.getElementById('client-id-display').textContent = clientId;

            async function fetchClientData() {
                try {
                    const response = await axios.get(`/api/clients/${clientId}`);
                    // Los datos del cliente vienen dentro de la propiedad 'client' de la respuesta
                    const client = response.data.client; 

                    // Llenar el formulario con los datos actuales
                    document.getElementById('name').value = client.name;
                    document.getElementById('lastname').value = client.lastname;
                    document.getElementById('phone').value = client.phone;
                    document.getElementById('email').value = client.email;
                    document.getElementById('photo').value = client.photo;

                    form.classList.remove('hidden');
                } catch (error) {
                    console.error("Error al obtener datos del cliente:", error);
                    
                    let message = 'Error desconocido al cargar los datos del cliente.';
                    if (error.response && error.response.data.message) {
                        message = 'Cliente no encontrado. ' + error.response.data.message;
                    } 
                    
                    errorMessageDiv.textContent = message;
                    errorMessageDiv.classList.remove('hidden');
                } finally {
                    loading.classList.add('hidden');
                }
            }

            await fetchClientData();

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                // Excluimos los campos de Blade (como _token y _method) que no son parte de la carga útil de la API
                const data = Object.fromEntries([...formData.entries()].filter(([key]) => key !== '_token' && key !== '_method'));
                
                messagesDiv.innerHTML = '';
                
                try {
                    // Usamos el método PUT de la API
                    const response = await axios.put(`/api/clients/${clientId}`, data);
                    
                    messagesDiv.innerHTML = `
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                            <p class="font-bold">✅ Éxito: ${response.data.message}</p>
                            <p>Cliente actualizado correctamente.</p>
                        </div>
                    `;
                } catch (error) {
                    console.error("Error al actualizar cliente:", error.response || error);
                    
                    let errorHtml = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">';
                    
                    if (error.response && error.response.data.errors) {
                        errorHtml += '<p class="font-bold">❌ Errores de Validación:</p><ul>';
                        for (const field in error.response.data.errors) {
                            error.response.data.errors[field].forEach(msg => {
                                errorHtml += `<li>- ${msg}</li>`;
                            });
                        }
                        errorHtml += '</ul>';
                    } else if (error.response && error.response.data.message) {
                        errorHtml += `<p class="font-bold">❌ Error: ${error.response.data.message}</p>`;
                    } else {
                        errorHtml += `<p class="font-bold">❌ Error desconocido al actualizar el cliente.</p>`;
                    }

                    errorHtml += '</div>';
                    messagesDiv.innerHTML = errorHtml;
                }
            });
        });
    </script>
</body>
</html>