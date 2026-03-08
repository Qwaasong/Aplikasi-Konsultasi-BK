
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[var(--primary-color)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:bg-(var(--primary-color) active:bg-(var(--primary-color)) focus:outline-none focus:ring-2 focus:ring-(var(--primary-color) focus:ring-offset-2 transition ease-in-out duration-1050']) }}>
    {{ $slot }}
</button>
