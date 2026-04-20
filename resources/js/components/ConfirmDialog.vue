<script setup lang="ts">
import { nextTick, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useConfirm } from '@/composables/useConfirm';

const { isOpen, options, handleConfirm, handleCancel } = useConfirm();

const cancelBtnRef = ref<InstanceType<typeof Button> | null>(null);

// Auto-fokus ke tombol Batal saat dialog terbuka (lebih aman dari UX perspective)
watch(isOpen, async (val) => {
    if (val) {
        await nextTick();
        (cancelBtnRef.value?.$el as HTMLButtonElement)?.focus();
    }
});
</script>

<template>
    <Dialog :open="isOpen" @update:open="(val) => !val && handleCancel()">
        <DialogContent
            class="max-w-md"
            role="alertdialog"
            :aria-labelledby="'confirm-title'"
            :aria-describedby="'confirm-desc'"
        >
            <DialogHeader>
                <DialogTitle id="confirm-title">{{
                    options.title
                }}</DialogTitle>
                <DialogDescription id="confirm-desc">
                    {{ options.description }}
                </DialogDescription>
            </DialogHeader>

            <DialogFooter class="flex gap-2 sm:justify-end">
                <Button
                    ref="cancelBtnRef"
                    variant="outline"
                    @click="handleCancel"
                >
                    {{ options.cancelLabel }}
                </Button>
                <Button
                    :variant="
                        options.variant === 'destructive'
                            ? 'destructive'
                            : 'default'
                    "
                    @click="handleConfirm"
                >
                    {{ options.confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
