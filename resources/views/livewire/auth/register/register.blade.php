@section('title', 'Become A Partner')
<div>
    @if ( setting('partnersCanRegister',true) )
    <div class="items-center min-h-screen bg-gray-50 md:flex ">
        <div class="flex flex-col overflow-y-auto md:flex-row">
            {{-- image --}}
            <div class="hidden h-screen md:block md:w-1/2">
                <div class="w-full h-full">
                    <img aria-hidden="true" class="object-cover w-full h-full" src="{{ setting('registerImage', asset('images/login-office.jpeg')) }}" alt="Office" />
                </div>
            </div>
            {{-- form --}}
            <div class="w-11/12 h-full max-w-xl mx-auto my-12 overflow-hidden bg-white rounded-lg shadow-xl md:my-auto md:max-w-2xl ">
                <div class="flex flex-col overflow-y-auto md:flex-row">
                    <div class="flex items-center justify-center w-full p-6 sm:p-12 ">
                        <div class="w-full">
                            <h1 class="mb-4 text-3xl font-bold text-gray-700 uppercase">
                                {{ __('Become a partner') }} </h1>

                            {{-- tabs  --}}
                            <div x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'driver' }" id="tab_wrapper">

                                @if(false)
                                <nav class="mb-8">
                                    <a :class="tab==='driver' ? 'bg-primary-800 text-white' : 'bg-primary-500 text-gray-500'" class="p-2 font-semibold text-white rounded-t-lg text-md" @click.prevent="tab = 'driver'; window.location.hash = 'driver'" href="#">{{ __('Driver') }}</a>
                                    <a :class="tab==='vendor' ? 'bg-primary-800 text-white' : 'bg-primary-500 text-gray-500'" class="p-2 font-semibold text-white rounded-t-lg text-md" @click.prevent="tab = 'vendor'; window.location.hash = 'vendor'" href="#">{{ __('Vendor') }}</a>
                                </nav>
                                @endif


                                {{-- tabs section  --}}
                                {{-- driver account registration  --}}
                                <div x-show="tab === 'driver'">
                                    @include('livewire.auth.register.partials.driver')
                                </div>
                                {{-- vendor account registration  --}}
                                <div x-show="tab === 'vendor'">
                                    @include('livewire.auth.register.partials.vendor')
                                </div>
                                <p class="my-4 text-center">
                                    {{ __('Already have an account?') }} <a href="{{ route('login') }}" class="ml-2 font-bold text-primary-500 text-md">{{ __('Login') }}</a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    {{--  Registration disabled  --}}
    <div class="w-full p-12 mx-auto my-12 rounded-sm shadow md:w-5/12 lg:w-4/12 ">
        <p class="mb-2 text-2xl font-semibold">{{ __('Registration Page Not available') }}</p>
        <p class="text-sm">
            {{ __('Partner account registration is currently unavailable. Please stay tune/contact support regarding further instruction about registering for a partners account. Thank you') }}
        </p>
        <p class='mt-4 text-center'><a href="{{ route('contact') }}" class="underline text-primary-600">{{ __('Contact Us') }}</a></p>
    </div>
    @endif
    {{-- loading --}}
    <x-loading />
</div>
