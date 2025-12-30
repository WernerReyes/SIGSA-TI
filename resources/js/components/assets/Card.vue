<template>
    <div
        class="bg-card rounded-xl border shadow-card p-5 hover:shadow-card-hover transition-all duration-200 animate-fade-in">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center">
                    <Laptop />
                </div>
                <div>
                    <p class="font-mono text-xs text-muted-foreground">AST-{{ asset.id }}</p>
                    <h3 class="font-semibold text-sm mt-0.5">{{ asset.name }}</h3>
                </div>
            </div>


            <!-- <button
                class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8"
                type="button" id="radix-:r91:" aria-haspopup="menu" aria-expanded="false" data-state="closed">


                <EllipsisIcon />

            </button> -->

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline">
                        <EllipsisIcon />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-48" align="start">
                    <!-- <DropdownMenuLabel class="flex gap-2 items-center">
                            <InfoIcon class="size-4" />
                        Ver Detalles</DropdownMenuLabel> -->
                    <!-- <DropdownMenuLabel class="flex gap-2 items-center" @click="() => {
                        emit('update:asset', asset);
                    }">
                        <Pencil class="size-4" />
                        Editar
                    </DropdownMenuLabel> -->
                    <DropdownMenuItem class="flex gap-2 items-center cursor-pointer"
                        @select="emit('detail:asset', asset)">
                        <Info class="size-4" />
                        Ver Detalles
                    </DropdownMenuItem>
                    <DropdownMenuItem class="flex gap-2 items-center cursor-pointer"
                        @select="emit('update:asset', asset)">
                        <Pencil class="size-4" />
                        Editar
                    </DropdownMenuItem>
                    <DropdownMenuItem class="flex gap-2 items-center cursor-pointer" @select="emit('assign:asset', asset)">
                        <!-- <Info class="size-4" /> -->
                         <UserPlus class="size-4" />
                        Asignar
                    </DropdownMenuItem>
                    <DropdownMenuGroup>
                        <!-- <DropdownMenuItem>
                            Profile
                            <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem>
                            Billing
                            <DropdownMenuShortcut>⌘B</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem>
                            Settings
                            <DropdownMenuShortcut>⌘S</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem>
                            Keyboard shortcuts
                            <DropdownMenuShortcut>⌘K</DropdownMenuShortcut>
                        </DropdownMenuItem> -->
                    </DropdownMenuGroup>

                </DropdownMenuContent>
            </DropdownMenu>
        </div>
        <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">Marca/Modelo</span>
                <span class="font-medium max-w-52">{{ asset.brand }} / {{ asset.model }}
                </span>
            </div>

            <div class="flex justify-between"><span class="text-muted-foreground">Serial</span><span
                    class="font-mono text-xs">{{ asset.serial_number }}</span></div>
            <div class="flex justify-between"><span class="text-muted-foreground">Asignado
                    a</span><span v-if="asset.assigned_to" class="font-medium">
                    {{ asset.assigned_to.full_name }}
                </span>
                <Badge v-else variant="secondary">Sin asignar</Badge>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-border flex items-center justify-between">
            <div
                class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent text-xs border-0 bg-success/10 text-success">
                Garantía</div>

            <span class="text-xs font-medium" :class="{
                'text-green-500': isWarrantyValid(),
                'text-red-500': !isWarrantyValid()
            }">

                {{
                    isWarrantyValid() ? 'Vence el ' + format(parseISO(asset.warranty_expiration.split('T')[0]),
                        'dd/MM/yyyy') : 'Venció el ' + format(parseISO(asset.warranty_expiration.split('T')[0]), 'dd/MM/yyyy')
                }}
            </span>
        </div>
    </div>

</template>

<script setup lang="ts">
import { type Asset } from '@/interfaces/asset.interface';
import { format, isAfter, parseISO } from 'date-fns';
import { EllipsisIcon, Info, InfoIcon, Laptop, Pencil, UserPlus } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    // DropdownMenuAction,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

const { asset } = defineProps<{
    asset: Asset;
}>();

const emit = defineEmits<{
    (e: 'detail:asset', asset: Asset): void;
    (e: 'update:asset', asset: Asset): void;
    (e: 'assign:asset', asset: Asset): void;
}>();

const isWarrantyValid = (): boolean => {
    const warrantyEndDate = asset.warranty_expiration;

    const endDate = parseISO(warrantyEndDate.split('T')[0])
    const today = new Date()
    const todayDateOnly = parseISO(today.toISOString().split('T')[0])

    return isAfter(endDate, todayDateOnly)
}
</script>