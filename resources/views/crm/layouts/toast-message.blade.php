@if(session('success'))
    <script>
        window.addEventListener('load', function() {
            showToastmessage("sdfasdf 123", "error");
        });
    </script>
@endif
