@if(!empty(\Helper::getSetting()))
    <div>
        @if(!empty(\Helper::getSetting()['software_logo_white']) && is_string(\Helper::getSetting()['software_logo_white']))
            <img src="{{ asset('storage/'. \Helper::getSetting()['software_logo_white']) }}" alt="LucrativaBet" class="show-in-dark h-8">
        @endif

        @if(!empty(\Helper::getSetting()['software_logo_black']) && is_string(\Helper::getSetting()['software_logo_black']))
            <img src="{{ asset('storage/'. \Helper::getSetting()['software_logo_black']) }}" alt="LucrativaBet" class="show-in-light h-8">
        @endif
    </div>
@else
    <div class="flex items-center gap-2">
        <i class="fas fa-coins text-green-500 text-xl"></i>
        <span class="text-xl font-bold text-gray-900 dark:text-white">LucrativaBet Admin</span>
    </div>
@endif
