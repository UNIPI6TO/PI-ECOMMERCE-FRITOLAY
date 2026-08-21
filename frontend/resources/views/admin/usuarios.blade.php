@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="usuarios()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">GestiÃ³n de Usuarios Empleados</h1>
        <button @click="modal = true" class="bg-[#E3001B] text-white px-4 py-2 rounded font-medium">+ Nuevo Usuario</button>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">Nombre</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Rol</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="u in listado" :key="u.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-medium" x-text="u.nombre"></td>
                        <td class="p-4" x-text="u.email"></td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800" x-text="u.rol"></span>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs rounded" :class="u.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="u.activo ? 'Activo' : 'Inactivo'"></span>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button @click="toggleEstado(u)" class="text-sm font-medium" :class="u.activo ? 'text-red-600' : 'text-green-600'" x-text="u.activo ? 'Inactivar' : 'Activar'"></button>
                            <span class="text-gray-300">|</span>
                            <button @click="resetPass(u)" class="text-sm text-yellow-600 font-medium">Reset Pass</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Modal Crear -->
    <div x-show="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="font-bold text-lg mb-4">Crear Empleado</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Nombre Completo</label>
                    <input type="text" x-model="nuevo.nombre" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" x-model="nuevo.email" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-1">Rol</label>
                    <select x-model="nuevo.rol" class="w-full border rounded px-3 py-2">
                        <option value="OPERADOR">Operador</option>
                        <option value="CHOFER">Chofer</option>
                    </select>
                </div>
                <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded border">La contraseÃ±a por defecto serÃ¡: Fritolay2024*</div>
            </div>
            <div class="flex justify-end space-x-2 mt-6">
                <button @click="modal = false" class="px-4 py-2 border rounded">Cancelar</button>
                <button @click="guardar" class="px-4 py-2 bg-[#E3001B] text-white rounded">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('usuarios', () => ({
        modal: false,
        listado: [
            {id: 1, nombre: 'Admin Master', email: 'admin@fritolay.com', rol: 'ADMIN', activo: true},
            {id: 2, nombre: 'Luis Chofer', email: 'luis@fritolay.com', rol: 'CHOFER', activo: true}
        ],
        nuevo: {nombre: '', email: '', rol: 'OPERADOR'},

        guardar() {
            this.modal = false;
            // POST api/usuarios
        },
        toggleEstado(u) {
            if(confirm(`Â¿Seguro que desea ${u.activo ? 'inactivar' : 'activar'} a ${u.nombre}?`)) {
                u.activo = !u.activo;
            }
        },
        resetPass(u) {
            if(confirm(`Â¿Resetear contraseÃ±a de ${u.nombre}?`)) {
                Swal.fire('Éxito', 'Contraseña reseteada', 'success');
            }
        }
    }));
});
</script>
@endsection


