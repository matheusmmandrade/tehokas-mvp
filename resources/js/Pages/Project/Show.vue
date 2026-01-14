<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    project: Object,
});

const form = useForm({
    title: '',
    description: '',
    deadline: '',
    status: 'pending', // Padrão
});

const submitTask = () => {
    form.post(route('tasks.store', props.project.id), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
    });
};

const onDragStart = (evt, task) => {
    evt.dataTransfer.dropEffect = 'move';
    evt.dataTransfer.effectAllowed = 'move';
    evt.dataTransfer.setData('taskId', task.id);
};

const onDrop = (evt, newStatus) => {
    const taskId = evt.dataTransfer.getData('taskId');
    
    router.patch(route('tasks.update', taskId), {
        status: newStatus
    }, {
        preserveScroll: true 
    });
};

const pendingTasks = computed(() => props.project.tasks.filter(t => t.status === 'pending'));
const progressTasks = computed(() => props.project.tasks.filter(t => t.status === 'in_progress'));
const completedTasks = computed(() => props.project.tasks.filter(t => t.status === 'completed'));
</script>

<template>
    <Head :title="project.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ project.title }}
                </h2>
                <span 
                    class="px-3 py-1 rounded-full text-sm font-bold shadow-sm transition-colors"
                    :class="{
                        'bg-green-100 text-green-700 border border-green-200': project.health_status === 'Saudável',
                        'bg-red-100 text-red-700 border border-red-200': project.health_status === 'Em Alerta'
                    }"
                >
                    {{ project.health_status }}
                </span>
            </div>
        </template>

        <div class="h-[calc(100vh-140px)] flex flex-col py-6">
            
            <div class="max-w-7xl w-full mx-auto sm:px-6 lg:px-8 flex-1 flex flex-col gap-6 overflow-hidden">

                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 shrink-0">
                    <form @submit.prevent="submitTask" class="flex flex-wrap gap-4 items-end">
                        
                        <div class="w-full md:flex-1">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Nova Tarefa</label>
                            <input 
                                v-model="form.title" 
                                type="text" 
                                placeholder="O que precisa ser feito?"
                                class="w-full h-11 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 px-4" 
                                required
                            >
                        </div>
                        
                        <div class="flex gap-4 w-full md:w-auto items-end">
                            
                            <div class="flex-1 md:w-40">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Prazo</label>
                                <input 
                                    v-model="form.deadline" 
                                    type="date" 
                                    class="w-full h-11 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 px-4" 
                                    required
                                >
                            </div>
                            
                            <button 
                                type="submit" 
                                class="h-11 px-6 min-w-[120px] bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-md hover:shadow-lg flex items-center justify-center shrink-0 whitespace-nowrap"
                            >
                                Adicionar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex-1 overflow-x-auto overflow-y-hidden pb-2">
                    <div class="flex h-full gap-6 min-w-full">
                        
                        <div 
                            class="flex-1 min-w-[320px] bg-gray-100 rounded-xl flex flex-col h-full border border-gray-200 shadow-inner"
                            @drop="onDrop($event, 'pending')"
                            @dragover.prevent
                            @dragenter.prevent
                        >
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl sticky top-0 z-10">
                                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                                    A Fazer
                                </h3>
                                <span class="bg-white text-gray-600 text-xs font-bold px-2 py-1 rounded shadow-sm border">
                                    {{ pendingTasks.length }}
                                </span>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                                <div 
                                    v-for="task in pendingTasks" 
                                    :key="task.id"
                                    draggable="true"
                                    @dragstart="onDragStart($event, task)"
                                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-gray-400 cursor-grab hover:shadow-md hover:border-indigo-300 transition group"
                                >
                                    <p class="font-semibold text-gray-800 text-sm mb-2">{{ task.title }}</p>
                                    <span class="text-xs text-gray-500 flex items-center gap-1 bg-gray-50 px-2 py-1 rounded w-fit">
                                         {{ new Date(task.deadline).toLocaleDateString() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div 
                            class="flex-1 min-w-[320px] bg-blue-50/50 rounded-xl flex flex-col h-full border border-blue-100 shadow-inner"
                            @drop="onDrop($event, 'in_progress')"
                            @dragover.prevent
                            @dragenter.prevent
                        >
                            <div class="p-4 border-b border-blue-100 flex justify-between items-center bg-blue-50 rounded-t-xl sticky top-0 z-10">
                                <h3 class="font-bold text-blue-800 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></div>
                                    Em Andamento
                                </h3>
                                <span class="bg-white text-blue-600 text-xs font-bold px-2 py-1 rounded shadow-sm border border-blue-100">
                                    {{ progressTasks.length }}
                                </span>
                            </div>

                            <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                                <div 
                                    v-for="task in progressTasks" 
                                    :key="task.id"
                                    draggable="true"
                                    @dragstart="onDragStart($event, task)"
                                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-blue-500 cursor-grab hover:shadow-md transition"
                                >
                                    <p class="font-semibold text-gray-800 text-sm mb-2">{{ task.title }}</p>
                                    <div class="flex items-center">
                                        <span v-if="new Date(task.deadline) < new Date()" class="text-xs text-red-600 font-bold bg-red-50 px-2 py-1 rounded border border-red-100 flex items-center gap-1">
                                            ⚠️ Atrasada
                                        </span>
                                        <span v-else class="text-xs text-gray-500 flex items-center gap-1 bg-gray-50 px-2 py-1 rounded">
                                             {{ new Date(task.deadline).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div 
                            class="flex-1 min-w-[320px] bg-green-50/50 rounded-xl flex flex-col h-full border border-green-100 shadow-inner"
                            @drop="onDrop($event, 'completed')"
                            @dragover.prevent
                            @dragenter.prevent
                        >
                            <div class="p-4 border-b border-green-100 flex justify-between items-center bg-green-50 rounded-t-xl sticky top-0 z-10">
                                <h3 class="font-bold text-green-800 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                    Concluído
                                </h3>
                                <span class="bg-white text-green-600 text-xs font-bold px-2 py-1 rounded shadow-sm border border-green-100">
                                    {{ completedTasks.length }}
                                </span>
                            </div>

                            <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                                <div 
                                    v-for="task in completedTasks" 
                                    :key="task.id"
                                    draggable="true"
                                    @dragstart="onDragStart($event, task)"
                                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 opacity-60 hover:opacity-100 transition cursor-grab"
                                >
                                    <p class="font-medium text-gray-500 line-through text-sm">{{ task.title }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>