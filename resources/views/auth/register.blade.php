<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-black text-neutral-950">Create your LaundryLink account</h1>
        <p class="mt-2 text-sm text-neutral-600">Register as a customer or cleaner. Admin accounts are created by the platform owner.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="role" :value="__('Account type')" />
            <select id="role" name="role" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-teal-700 focus:ring-teal-700">
                <option value="customer" @selected(old('role', 'customer') === 'customer')>Customer</option>
                <option value="cleaner" @selected(old('role') === 'cleaner')>Cleaner</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" class="mt-1 block w-full" type="text" name="phone" :value="old('phone')" autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="city" :value="__('Cleaner city')" />
                <x-text-input id="city" class="mt-1 block w-full" type="text" name="city" :value="old('city')" />
                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input id="address" class="mt-1 block w-full" type="text" name="address" :value="old('address')" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="rounded-lg border border-neutral-200 bg-stone-50 p-4">
            <h2 class="font-black text-neutral-950">Cleaner profile details</h2>
            <p class="mt-1 text-sm text-neutral-600">Required only when account type is Cleaner. New cleaner profiles wait for admin approval.</p>

            <div class="mt-4 space-y-4">
                <div>
                    <x-input-label for="business_name" :value="__('Business name')" />
                    <x-text-input id="business_name" class="mt-1 block w-full" type="text" name="business_name" :value="old('business_name')" />
                    <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="turnaround_time" :value="__('Turnaround time')" />
                    <x-text-input id="turnaround_time" class="mt-1 block w-full" type="text" name="turnaround_time" :value="old('turnaround_time')" placeholder="24-48 hours" />
                    <x-input-error :messages="$errors->get('turnaround_time')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-teal-700 focus:ring-teal-700">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <a class="text-sm text-neutral-600 underline hover:text-teal-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
