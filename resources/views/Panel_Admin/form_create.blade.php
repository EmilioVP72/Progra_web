<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Cliente</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex justify-center items-center min-h-screen">
    <div class="w-full max-w-xl bg-white p-8 rounded-xl shadow-2xl">
        <h1 class="text-3xl font-bold text-indigo-800 mb-8 border-b pb-4">Registro de Nuevo Cliente</h1>
        
        <form id="create-client-form">
            @csrf
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
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" id="password" name="password" required class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="space-y-1 mt-6">
                <label for="photo" class="block text-sm font-medium text-gray-700">URL de la Foto</label>
                <input type="url" id="photo" name="photo" required class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex justify-between items-center mt-8">
                <a href="{{ route('admin.clients.index') }}" class="text-gray-600 hover:text-indigo-800 font-medium transition duration-150 ease-in-out">
                    ← Volver al Listado
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    Guardar Cliente
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('create-client-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';
            
            // Eliminar el token CSRF para el envío del API
            delete data._token; 

            try {
                const response = await axios.post('/api/clients', data);
                
                messagesDiv.innerHTML = `
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                        <p class="font-bold">✅ Éxito: ${response.data.message}</p>
                        <p>Cliente ${response.data.client.name} creado con ID: ${response.data.client.id}.</p>
                    </div>
                `;
                
                form.reset(); 
            } catch (error) {
                console.error("Error al crear cliente:", error.response || error);
                
                let errorHtml = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">';
                
                if (error.response && error.response.data.errors) {
                    errorHtml += '<p class="font-bold">❌ Error de Validación:</p><ul>';
                    for (const field in error.response.data.errors) {
                        error.response.data.errors[field].forEach(msg => {
                            errorHtml += `<li>- ${msg}</li>`;
                        });
                    }
                    errorHtml += '</ul>';
                } else if (error.response && error.response.data.message) {
                    errorHtml += `<p class="font-bold">❌ Error: ${error.response.data.message}</p>`;
                } else {
                    errorHtml += `<p class="font-bold">❌ Error desconocido al crear el cliente.</p>`;
                }

                errorHtml += '</div>';
                messagesDiv.innerHTML = errorHtml;
            }
        });
    </script>
</body>
</html>