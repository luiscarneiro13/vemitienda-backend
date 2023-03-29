<a href="{{ @$url }}"
    class="flex justify-between items-center item text-xs
    lg:text-base py-2 px-3 mx-1
    lg:mr-4 last:mr-0 inline-block whitespace-nowrap
    rounded bg-gray-50 border-solid border border-gray-300
    nuxt-link-exact-active
    nuxt-link-active {{ @$active ? 'bg-' . @$theme . ' border-' . @$theme : '' }}">
    <span data-v-dc4763d4=""
        class="font-normal relative z-10 font-medium  {{ @$active ? 'text-white' : 'text-black' }}"">
        {{ $title }}
    </span>
</a>
