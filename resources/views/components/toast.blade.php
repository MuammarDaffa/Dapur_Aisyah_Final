{{-- Toast notification component using SweetAlert2 --}}
{{-- Usage: Include <x-toast /> in your layout to auto-show session flash messages --}}

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: @json(session('success')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showClass: { popup: 'animate__animated animate__slideInRight' },
            hideClass: { popup: 'animate__animated animate__slideOutRight' }
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: @json(session('error')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            showClass: { popup: 'animate__animated animate__slideInRight' },
            hideClass: { popup: 'animate__animated animate__slideOutRight' }
        });
</script>
@endif

@if(session('event_conflict_error'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: '{!! session("event_conflict_error") !!}',
            showConfirmButton: true,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#f97316'
        });
    });
</script>
@endif

@if(session('info'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: @json(session('info')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
@endif

@if(session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: @json(session('warning')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    });
</script>
@endif
