<script setup lang="ts">
import { useConfirm } from '@/composables/useConfirm';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

const { isOpen, options, handleConfirm, handleCancel } = useConfirm();
</script>

<template>
    <Dialog :open="isOpen" @update:open="(val) => !val && handleCancel()">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>{{ options.title }}</DialogTitle>
                <DialogDescription>{{ options.description }}</DialogDescription>
            </DialogHeader>

            <DialogFooter class="flex gap-2 sm:justify-end">
                <Button variant="outline" @click="handleCancel">
                    {{ options.cancelLabel }}
                </Button>
                <Button
                    :variant="options.variant === 'destructive' ? 'destructive' : 'default'"
                    @click="handleConfirm"
                >
                    {{ options.confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
