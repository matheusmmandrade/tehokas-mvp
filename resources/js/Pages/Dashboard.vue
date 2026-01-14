<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// Recebe os projetos enviados pelo ProjectController
const props = defineProps({
    projects: Array,
});

// Configuração do formulário para criar projeto
const form = useForm({
    title: '',
    description: '',
});

const submit = () => {
    form.post(route('projects.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Meus Projetos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Novo Projeto</h3>
                    
                    <form @submit.prevent="submit" class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Título do Projeto</label>
                            <input 
                                v-model="form.title" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                required
                            >
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Descrição (Opcional)</label>
                            <input 
                                v-model="form.description" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                            >
                        </div>
                        <button 
                            type="submit" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
                            :disabled="form.processing"
                        >
                            Criar
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="project in projects" :key="project.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-900">{{ project.title }}</h3>
                                
                                <span 
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800': project.health_status === 'Saudável' || project.health_status === 'Estável',
                                        'bg-red-100 text-red-800': project.health_status === 'Em Alerta'
                                    }"
                                >
                                    {{ project.health_status }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 mb-4">{{ project.description || 'Sem descrição' }}</p>
                            
                            <div class="flex justify-between items-center text-sm text-gray-500">
                                <span>{{ project.tasks.length }} Tarefas</span>
                                
                                <Link 
                                    :href="route('projects.show', project.id)" 
                                    class="text-indigo-600 hover:text-indigo-900 font-medium"
                                >
                                    Abrir Kanban →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="projects.length === 0" class="text-center text-gray-500 py-10">
                    Você ainda não tem projetos. Crie um acima!
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>