@section('title', __('Order Payment'))
<div class="">
    <div class="w-11/12 p-12 mx-auto mt-20 border rounded shadow md:w-6/12 lg:w-4/12">
        <x-heroicon-o-exclamation class="w-12 h-12 mx-auto {{ $error ? 'text-yellow-400':'text-primary-400' }} md:h-24 md:w-24" />
        <p class="text-xl font-medium text-center">{{__('Already processed')}}</p>
        <p class="text-sm text-center">{{  !empty($errorMessage) ? $errorMessage : __('Order payment has already been processed. Please check your orders to see more details about the order')}}</p>
    </div>

    {{-- close --}}
    <p class="w-full p-4 text-sm text-center text-gray-500">{{__('You may close this window')}}</p>
    <x-buttons.close />
</div>
