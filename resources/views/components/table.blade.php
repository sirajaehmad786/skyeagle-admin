<table id="{{ $id ?? 'my-table' }}" 
       class="{{ $class ?? 'table table-bordered table-centered mb-0 dt-responsive w-100 no-footer' }}">
    <thead>
        {{ $slot }}
    </thead>
</table>
