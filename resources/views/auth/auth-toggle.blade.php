<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{ 
        tab: '{{ $initialState }}',
        regEmail: '{{ old('email') }}',
        regPassword: '',
        regConfirmPassword: '',
        emailError: '',
        passwordError: '',
        
        async checkEmail() {
            if (!this.regEmail) return;
            
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(this.regEmail)) {
                this.emailError = 'Invalid email address format.';
                return;
            }

            try {
                const response = await fetch('{{ route('check.email') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: this.regEmail })
                });
                const data = await response.json();
                if (data.exists) {
                    this.emailError = 'This email is already registered.';
                } else {
                    this.emailError = '';
                }
            } catch (error) {
                console.error('Error checking email:', error);
            }
        },

        checkPasswords() {
            if (this.regPassword && this.regConfirmPassword) {
                if (this.regPassword !== this.regConfirmPassword) {
                    this.passwordError = 'Passwords do not match.';
                } else {
                    this.passwordError = '';
                }
            }
        }
    }">
        <!-- Toggles -->
        <div class="flex justify-around mb-6 border-b border-gray-200 dark:border-gray-700">
            <button @click="tab = 'login'" 
                :class="{ 'border-b-2 border-indigo-600 text-indigo-600': tab === 'login', 'text-gray-500 hover:text-gray-700': tab !== 'login' }" 
                class="w-1/2 py-2 font-semibold text-lg transition-colors duration-300 focus:outline-none">
                Login
            </button>
            <button @click="tab = 'register'" 
                :class="{ 'border-b-2 border-indigo-600 text-indigo-600': tab === 'register', 'text-gray-500 hover:text-gray-700': tab !== 'register' }" 
                class="w-1/2 py-2 font-semibold text-lg transition-colors duration-300 focus:outline-none">
                Register
            </button>
        </div>

        <!-- Login Form -->
        <div x-show="tab === 'login'" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100">
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="login_email" :value="__('Email')" />
                    <x-text-input id="login_email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="login_password" :value="__('Password')" />

                    <x-text-input id="login_password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a class="text-indigo-600 hover:text-indigo-800 transition-colors duration-300" href="{{ url('/') }}" title="{{ __('Back to Home') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>

                    <button type="submit" class="ml-3 inline-flex items-center justify-center px-4 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white bg-indigo-600 rounded-md hover:bg-white hover:text-indigo-600 hover:border-indigo-600 focus:outline-none focus:text-indigo-600 focus:border-indigo-600 transition duration-150 ease-in-out shadow-sm hover:shadow-none">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Register Form -->
        <div x-show="tab === 'register'" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100"
             style="display: none;"> <!-- Hide initially to avoid flash if JS hasn't loaded, though x-show handles it mostly -->
            
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Role -->
                <div class="mt-4">
                    <x-input-label for="role" :value="__('Register As')" />
                    <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="customer">Customer</option>
                        <option value="seller">Seller</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="register_email" :value="__('Email')" />
                    <x-text-input id="register_email" class="block mt-1 w-full" type="email" name="email" x-model="regEmail" @blur="checkEmail()" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    <span x-show="emailError" x-text="emailError" class="text-sm text-red-600 mt-2 block"></span>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="register_password" :value="__('Password')" />

                    <x-text-input id="register_password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    x-model="regPassword"
                                    @blur="checkPasswords()"
                                    required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" 
                                    x-model="regConfirmPassword"
                                    @blur="checkPasswords()"
                                    required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    <span x-show="passwordError" x-text="passwordError" class="text-sm text-red-600 mt-2 block"></span>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a class="text-indigo-600 hover:text-indigo-800 transition-colors duration-300" href="{{ url('/') }}" title="{{ __('Back to Home') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>

                    <button type="submit" class="ml-4 inline-flex items-center justify-center px-4 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white bg-indigo-600 rounded-md hover:bg-white hover:text-indigo-600 hover:border-indigo-600 focus:outline-none focus:text-indigo-600 focus:border-indigo-600 transition duration-150 ease-in-out shadow-sm hover:shadow-none">
                        {{ __('Register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
