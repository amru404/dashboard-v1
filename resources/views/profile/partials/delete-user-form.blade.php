<div class="space-y-4">
    <p class="text-body-sm text-vd-muted leading-relaxed">
        Once your account is deleted, all resources and data will be permanently removed.
        Before deleting, please download any data you want to retain.
    </p>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Delete Account
    </x-danger-button>
</div>

<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
        @csrf
        @method('delete')

        <h2 class="text-headline-sm text-vd-on-surface mb-2">Delete your account?</h2>
        <p class="text-body-sm text-vd-muted leading-relaxed mb-6">
            This action is permanent and cannot be undone. Please enter your password to confirm.
        </p>

        <div class="mb-5">
            <x-input-label for="delete_password" value="Password" class="sr-only" />
            <x-text-input id="delete_password" name="password" type="password"
                class="w-full" placeholder="Your password" />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
        </div>

        <div class="flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-danger-button>Delete Account</x-danger-button>
        </div>
    </form>
</x-modal>
