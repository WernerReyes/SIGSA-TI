<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

interface Props {
    user: User;
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();


</script>

<template>

    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">

        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(`${user.firstname} ${user.lastname}`) }}
        </AvatarFallback>
    </Avatar>
    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium"></span>
        <span class="truncate text-xs text-muted-foreground">{{
            user.email
        }}</span>
    </div>
</template>
