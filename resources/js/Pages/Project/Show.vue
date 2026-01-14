<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    project: Object,
});

const statusFilter = ref('all');
const priorityFilter = ref('all'); 

const form = useForm({
    title: '',
    description: '',
    deadline: '',
    status: 'pending',
    priority: 'medium',
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
    }, { preserveScroll: true });
};

const pendingTasks = computed(() => props.project.tasks.filter(t => t.status === 'pending'));
const progressTasks = computed(() => props.project.tasks.filter(t => t.status === 'in_progress'));
const completedTasks = computed(() => props.project.tasks.filter(t => t.status === 'completed'));

const showTask = (task) => {
    const matchesStatus = statusFilter.value === 'all' || statusFilter.value === task.status; 
    const matchesPriority = priorityFilter.value === 'all' || task.priority === priorityFilter.value;
    return matchesPriority;
};

const getPriorityColor = (priority) => {
    switch(priority) {
        case 'high': return 'bg-red-100 text-red-700 border-red-200';
        case 'medium': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case 'low': return 'bg-blue-100 text-blue-700 border-blue-200';
        default: return 'bg-gray-100 text-gray-700';
    }
};
const getPriorityLabel = (priority) => {
    switch(priority) {
        case 'high': return 'Alta';
        case 'medium': return 'Média';
        case 'low': return 'Baixa';
        default: return priority;
    }
};
</script>

<template>
    <Head :title="project.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ project.title }}
                </h2>
                
                <div class="flex items-center gap-2 flex-wrap justify-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Filtrar:</span>
                    
                    <select v-model="statusFilter" class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 py-1 pl-2 pr-8">
                        <option value="all">Todas as Colunas</option>
                        <option value="pending">A Fazer</option>
                        <option value="in_progress">Em Andamento</option>
                        <option value="completed">Concluído</option>
                    </select>

                    <select v-model="priorityFilter" class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 py-1 pl-2 pr-8">
                        <option value="all">Todas Prioridades</option>
                        <option value="high">Alta</option>
                        <option value="medium">Média</option>
                        <option value="low">Baixa</option>
                    </select>

                    <div class="h-6 w-px bg-gray-300 mx-2"></div>

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
            </div>
        </template>

        <div class="h-[calc(100vh-140px)] flex flex-col py-6">
            <div class="max-w-7xl w-full mx-auto sm:px-6 lg:px-8 flex-1 flex flex-col gap-6 overflow-hidden">

                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 shrink-0">
                    <form @submit.prevent="submitTask" class="flex flex-wrap gap-4 items-end">
                        
                        <div class="w-full md:flex-1">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Nova Tarefa</label>
                            <input 
                                v-model="form.title" type="text" placeholder="O que precisa ser feito?"
                                class="w-full h-11 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-indigo-500 px-4" 
                                required
                            >
                        </div>
                        
                        <div class="flex gap-4 w-full md:w-auto items-end shrink-0">
                            
                            <div class="w-28">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Prioridade</label>
                                <select 
                                    v-model="form.priority" 
                                    class="w-full h-11 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-indigo-500 px-2"
                                >
                                    <option value="low">Baixa</option>
                                    <option value="medium">Média</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>

                            <div class="flex-1 md:w-36">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Prazo</label>
                                <input 
                                    v-model="form.deadline" type="date" 
                                    class="w-full h-11 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-indigo-500 px-4" 
                                    required
                                >
                            </div>
                            
                            <button type="submit" class="h-11 px-6 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-md flex items-center justify-center shrink-0">
                                Adicionar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex-1 overflow-x-auto overflow-y-hidden pb-2">
                    <div class="flex h-full gap-6 min-w-full">
                        
                        <div 
                            v-show="statusFilter === 'all' || statusFilter === 'pending'"
                            class="flex-1 min-w-[320px] bg-gray-100 rounded-xl flex flex-col h-full border border-gray-200 shadow-inner"
                            @drop="onDrop($event, 'pending')" @dragover.prevent @dragenter.prevent
                        >
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl sticky top-0 z-10">
                                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-gray-400"></div> A Fazer
                                </h3>
                                <span class="bg-white text-gray-600 text-xs font-bold px-2 py-1 rounded shadow-sm border">{{ pendingTasks.length }}</span>
                            </div>
                            <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                                <div 
                                    v-for="task in pendingTasks" :key="task.id"
                                    v-show="showTask(task)"
                                    draggable="true" @dragstart="onDragStart($event, task)"
                                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 cursor-grab hover:shadow-md hover:border-indigo-300 transition group"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="font-semibold text-gray-800 text-sm">{{ task.title }}</p>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border uppercase" :class="getPriorityColor(task.priority)">
                                            {{ getPriorityLabel(task.priority) }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500 flex items-center gap-1 bg-gray-50 px-2 py-1 rounded w-fit">
                                         {{ new Date(task.deadline).toLocaleDateString('pt-BR') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div 
                            v-show="statusFilter === 'all' || statusFilter === 'in_progress'"
                            class="flex-1 min-w-[320px] bg-blue-50/50 rounded-xl flex flex-col h-full border border-blue-100 shadow-inner"
                            @drop="onDrop($event, 'in_progress')" @dragover.prevent @dragenter.prevent
                        >
                            <div class="p-4 border-b border-blue-100 flex justify-between items-center bg-blue-50 rounded-t-xl sticky top-0 z-10">
                                <h3 class="font-bold text-blue-800 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></div> Em Andamento
                                </h3>
                                <span class="bg-white text-blue-600 text-xs font-bold px-2 py-1 rounded shadow-sm border border-blue-100">{{ progressTasks.length }}</span>
                            </div>
                            <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                                <div 
                                    v-for="task in progressTasks" :key="task.id"
                                    v-show="showTask(task)"
                                    draggable="true" @dragstart="onDragStart($event, task)"
                                    class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500 cursor-grab hover:shadow-md transition"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="font-semibold text-gray-800 text-sm">{{ task.title }}</p>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border uppercase" :class="getPriorityColor(task.priority)">
                                            {{ getPriorityLabel(task.priority) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <span v-if="new Date(task.deadline) < new Date()" class="text-xs text-red-600 font-bold bg-red-50 px-2 py-1 rounded border border-red-100 flex items-center gap-1">⚠️ Atrasada</span>
                                        <span v-else class="text-xs text-gray-500 flex items-center gap-1 bg-gray-50 px-2 py-1 rounded"> {{ new Date(task.deadline).toLocaleDateString('pt-BR') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div 
                            v-show="statusFilter === 'all' || statusFilter === 'completed'"
                            class="flex-1 min-w-[320px] bg-green-50/50 rounded-xl flex flex-col h-full border border-green-100 shadow-inner"
                            @drop="onDrop($event, 'completed')" @dragover.prevent @dragenter.prevent
                        >
                            <div class="p-4 border-b border-green-100 flex justify-between items-center bg-green-50 rounded-t-xl sticky top-0 z-10">
                                <h3 class="font-bold text-green-800 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-green-500"></div> Concluído
                                </h3>
                                <span class="bg-white text-green-600 text-xs font-bold px-2 py-1 rounded shadow-sm border border-green-100">{{ completedTasks.length }}</span>
                            </div>
                            <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                                <div 
                                    v-for="task in completedTasks" :key="task.id"
                                    v-show="showTask(task)"
                                    draggable="true" @dragstart="onDragStart($event, task)"
                                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 opacity-60 hover:opacity-100 transition cursor-grab"
                                >
                                    <div class="flex justify-between items-start">
                                        <p class="font-medium text-gray-500 line-through text-sm">{{ task.title }}</p>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border uppercase" :class="getPriorityColor(task.priority)">
                                            {{ getPriorityLabel(task.priority) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>