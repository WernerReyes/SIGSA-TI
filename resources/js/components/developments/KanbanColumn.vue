<template>
    <div class="w-full shrink-0 flex flex-col bg-background rounded-lg border border-border shadow-sm">
        <!-- Professional Header -->
        <div class="px-4 py-3.5 border-b bg-muted/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-1 h-8 rounded-full" :style="{ backgroundColor: headerColor }"></div>
                    <div>
                        <h3 class="font-semibold text-sm leading-none">{{ title }}</h3>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ devRequests.length }} {{ devRequests.length === 1 ? 'solicitud' : 'solicitudes' }}
                        </p>
                    </div>
                </div>

                <Button size="icon" variant="ghost" class="h-8 w-8 hover:bg-accent">
                    <Plus class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <!-- Clean Draggable Area -->
        <ScrollArea class="flex-1 p-3">
            <draggable v-model="devRequests" class="space-y-2.5 min-h-50"
                :group="{ name: 'dev-requests', pull: true, put: true }" item-key="id" animation="200">
                <!-- Professional Empty State -->
                <div v-if="devRequests.length === 0" :key="`empty-${title}`"
                    class="flex flex-col items-center justify-center h-60 text-center rounded-lg border-2 border-dashed bg-muted/20">
                    <Inbox class="h-10 w-10 text-muted-foreground/40 mb-3" />
                    <p class="text-sm font-medium text-muted-foreground">Sin solicitudes</p>
                    <p class="text-xs text-muted-foreground/60 mt-1">Arrastra aquí para agregar</p>
                </div>

                <!-- Clean Professional Cards -->
                <div 
                    v-for="devRequest in devRequests" 
                    :key="devRequest.id"    
               
                    class="bg-card rounded-lg border shadow-card p-3 transition-shadow">
                    <div class="flex items-start justify-between"><span
                            class="font-mono text-xs text-muted-foreground">DEV-{{ devRequest.id }}</span>
                            
                                                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button size="icon" 
                                            variant="ghost"
                                            class="h-7 w-7 ">
                                        <MoreVertical class="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem class="cursor-pointer">
                                        <Eye class="h-4 w-4 mr-2" />
                                        Ver detalles
                                    </DropdownMenuItem>
                                    <DropdownMenuItem class="cursor-pointer">
                                        <Pencil class="h-4 w-4 mr-2" />
                                        Editar
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem class="cursor-pointer text-destructive focus:text-destructive">
                                        <Trash2 class="h-4 w-4 mr-2" />
                                        Eliminar
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>


                        </div>
                    <h4 class="font-medium text-sm mt-2 line-clamp-1">{{ devRequest.title }}</h4>
                    <p class="text-xs text-muted-foreground mt-1 line-clamp-2">{{ devRequest.description }}</p>
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        <!-- <div
                            class="inline-flex items-center rounded-full border px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-xs bg-warning/10 text-warning border-warning/20">
                            Media</div> -->
                            <Badge :class="getPriorityOp(devRequest.priority).bg + ' text-xs font-medium flex items-center gap-1'">
                                <component :is="getPriorityOp(devRequest.priority).icon" class="h-3 w-3" />
                                {{ getPriorityOp(devRequest.priority).label }}
                            </Badge>
                        <div
                            class="inline-flex items-center rounded-full border px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80 text-xs">
                            {{ devRequest?.area?.descripcion_area }}    
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex justify-between text-xs mb-1"><span
                                class="text-muted-foreground">Progreso</span><span>75%</span></div>
                        <div aria-valuemax="100" aria-valuemin="0" role="progressbar" data-state="indeterminate"
                            data-max="100" class="relative w-full overflow-hidden rounded-full bg-secondary h-1.5">
                            <div data-state="indeterminate" data-max="100"
                                class="h-full w-full flex-1 bg-primary transition-all"
                                style="transform: translateX(-25%);"></div>
                        </div>
                    </div>

                    <!-- // TODO: Implement the real data below -->
                    <div class="mt-2 pt-2 border-t border-border space-y-1">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                            <!-- <svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-user w-3 h-3">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg> -->
                            <User class="size-3" />
                            
                            <span class="truncate">{{ devRequest?.requested_by?.full_name }}</span></div>
                        <div class="flex items-center gap-2 text-xs"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-calendar w-3 h-3 text-muted-foreground">
                                <path d="M8 2v4"></path>
                                <path d="M16 2v4"></path>
                                <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                <path d="M3 10h18"></path>
                            </svg><span class="text-muted-foreground truncate">{{ devRequest?.estimated_end_date }}</span></div>
                    </div>
                    <div class="mt-2 flex items-center gap-2 p-1.5 bg-muted/50 rounded"><button
                            class="flex items-center gap-1 hover:opacity-80"><svg xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-circle-check w-3.5 h-3.5 text-success">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg><span class="text-[10px] text-muted-foreground">Téc</span></button><button
                            class="flex items-center gap-1 hover:opacity-80"><svg xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-circle-check w-3.5 h-3.5 text-success">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg><span class="text-[10px] text-muted-foreground">Est</span></button><span
                            class="text-[10px] text-muted-foreground ml-auto">8 eventos</span></div>
                </div>
                
            </draggable>
        </ScrollArea>
    </div>
</template>

<script lang="ts" setup>
import { type DevelopmentRequest, getPriorityOp } from '@/interfaces/developmentRequest.interface';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Clock, Eye, Inbox, MoreVertical, Pencil, Plus, Trash2, User } from 'lucide-vue-next';
import { VueDraggableNext as draggable } from 'vue-draggable-next';

const devRequests = defineModel<Array<DevelopmentRequest>>('devRequests', {
    default: () => [],
});

withDefaults(defineProps<{
    title: string;
    headerColor?: string;
}>(), {
    headerColor: '#6366f1'
});


// <!-- <Card v-for="devRequest in devRequests" 
//                       :key="devRequest.id"
//                       class="group cursor-move hover:shadow-md transition-all duration-200 border-l-4"
//                       :style="{ borderLeftColor: headerColor }">
                    
//                     <CardHeader>
//                         <div class="flex items-start justify-between">
//                             <Badge variant="outline" class="font-mono text-[10px] px-2">
//                                 #{{ devRequest.id }}
//                             </Badge>
                            
                            // <DropdownMenu>
                            //     <DropdownMenuTrigger as-child>
                            //         <Button size="icon" 
                            //                 variant="ghost"
                            //                 class="h-7 w-7 opacity-0 group-hover:opacity-100 transition-opacity">
                            //             <MoreVertical class="h-4 w-4" />
                            //         </Button>
                            //     </DropdownMenuTrigger>
                            //     <DropdownMenuContent align="end">
                            //         <DropdownMenuItem class="cursor-pointer">
                            //             <Eye class="h-4 w-4 mr-2" />
                            //             Ver detalles
                            //         </DropdownMenuItem>
                            //         <DropdownMenuItem class="cursor-pointer">
                            //             <Pencil class="h-4 w-4 mr-2" />
                            //             Editar
                            //         </DropdownMenuItem>
                            //         <DropdownMenuSeparator />
                            //         <DropdownMenuItem class="cursor-pointer text-destructive focus:text-destructive">
                            //             <Trash2 class="h-4 w-4 mr-2" />
                            //             Eliminar
                            //         </DropdownMenuItem>
                            //     </DropdownMenuContent>
                            // </DropdownMenu>
//                         </div>
                        
//                         <CardTitle class="text-sm font-semibold leading-snug line-clamp-2">
//                             {{ devRequest.title }}
//                         </CardTitle>
                        
//                         <CardDescription class="text-xs line-clamp-2 leading-relaxed">
//                             {{ devRequest.description }}
//                         </CardDescription>
//                     </CardHeader>

//                     <CardContent class="pt-0 space-y-3">
//                         <!-- Priority Badge --
//                 <Badge :class="getPriorityOp(devRequest.priority).bg" class="text-[10px] font-medium">
//                     <component :is="getPriorityOp(devRequest.priority).icon" class="h-3 w-3 mr-1" />
//                     {{ getPriorityOp(devRequest.priority).label }}
//                 </Badge>

//                 <!-- Clean Divider -->
//                 <div class="border-t"></div>

//                 <!-- Metadata -->
//                 <div class="space-y-1.5">
//                     <div class="flex items-center gap-2 text-xs text-muted-foreground">
//                         <User class="h-3.5 w-3.5" />
//                         <span>Sin asignar</span>
//                     </div>
//                     <div class="flex items-center gap-2 text-xs text-muted-foreground">
//                         <Clock class="h-3.5 w-3.5" />
//                         <span>Pendiente</span>
//                     </div>
//                 </div>

//                 <!-- Progress Bar -->
//                 <div class="space-y-1.5 pt-1">
//                     <div class="flex items-center justify-between text-xs">
//                         <span class="text-muted-foreground">Progreso</span>
//                         <span class="font-semibold" :style="{ color: headerColor }">
//                             {{ devRequest.progress || 0 }}%
//                         </span>
//                     </div>
//                     <div class="h-1.5 bg-muted rounded-full overflow-hidden">
//                         <div class="h-full rounded-full transition-all duration-300" :style="{
//                             width: `${devRequest.progress || 0}%`,
//                             backgroundColor: headerColor
//                         }">
//                         </div>
//                     </div>
//                 </div>
//                 </CardContent>
//                 </Card> -->
// </script>